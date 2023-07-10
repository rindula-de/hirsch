<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\Command;

use App\Entity\Orders;
use App\Entity\Payhistory;
use App\Entity\Paypalmes;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'order:send:mail',
    description: 'Send daily order Mail',
)]
class SendOrderOverview extends Command
{
    private EntityManagerInterface $entityManager;
    private MailerInterface $mailer;
    private KernelInterface $kernel;

    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer, KernelInterface $kernel)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->kernel = $kernel;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // get all orders for today
        /** @var mixed[] */
        $orders = $this->entityManager
            ->getRepository(Orders::class)
            ->createQueryBuilder('o')
            ->select('o.for_date')
            ->addSelect('o.note')
            ->addSelect('count(o.id) as cnt')
            ->addSelect('count(o.orderedby) as personen')
            ->innerJoin('o.hirsch', 'h')
            ->addSelect('h.name')
            ->where('o.for_date = :date')
            ->groupBy('h.name')
            ->addGroupBy('o.note')
            ->setParameter('date', date('Y-m-d'))
            ->getQuery()
            ->getResult();

        // if there are no orders for today, exit
        if (0 === count($orders)) {
            $output->writeln('No orders for today');

            return Command::SUCCESS;
        }

        // make a textual summary of the orders
        $text = "Heutige Bestellungen:\n\n";
        $path = $this->kernel->getProjectDir().'/public/favicon.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path) ?: '';
        $base64 = 'data:image/'.$type.';base64,'.base64_encode($data);
        $html = '<h1><img src="'.$base64.'" style="width: 50px;"> Heutige Hirsch Bestellung ('.(new DateTime())->format('d.m.Y').')</h1>';
        /** @var mixed[] $order */
        foreach ($orders as $order) {
            $text .= $order['cnt'].'x '.$order['name'].(!empty($order['note']) ? "\nSonderwunsch:".$order['note'] : '')."\n\n";
            $html .= '<li>'.$order['cnt'].'x '.$order['name'].(!empty($order['note']) ? ' - '.$order['note'] : '').'</li>';
        }

        // get active payer
        $activePayer = $this->entityManager->getRepository(Payhistory::class)->findActivePayer();
        /** @var Paypalmes|null $activePayer */
        $activePayer = $activePayer?$this->entityManager->getRepository(Paypalmes::class)->find($activePayer['id']):null;
        if ($activePayer && $activePayer->getEmail()) {
            // prepare symfony mailer
            $email = (new Email())
                ->from('essen@hochwarth-e.com')
                ->to($activePayer->getEmail())
                ->subject('Bestellübersicht')
                ->text($text)
                ->html($html);

            $this->mailer->send($email);
            $output->writeln('Mail sent to '.$activePayer->getEmail());
        }

        return Command::SUCCESS;
    }
}
