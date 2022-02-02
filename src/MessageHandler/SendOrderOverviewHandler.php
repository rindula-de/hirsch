<?php

namespace App\MessageHandler;

use App\Entity\Orders;
use App\Entity\Payhistory;
use App\Entity\Paypalmes;
use App\Message\SendOrderOverview;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;

final class SendOrderOverviewHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $entityManager;
    private MailerInterface $mailer;
    private KernelInterface $kernel;

    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer, KernelInterface $kernel)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->kernel = $kernel;
    }

    public function __invoke(SendOrderOverview $message)
    {

        // get all orders for today
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
            ->setParameter('date', strftime('%Y-%m-%d'))
            ->getQuery()
            ->getResult();

        // make a textual summary of the orders
        $text = "Heutige Bestellungen:\n\n";
        $path = $this->kernel->getProjectDir().'/public/favicon.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/'.$type.';base64,'.base64_encode($data);
        $html = '<h1><img src="'.$base64.'" style="width: 50px;"> Heutige Hirsch Bestellung ('.(new DateTime())->format('d.m.Y').')</h1>';
        foreach ($orders as $order) {
            $text .= $order['cnt'].'x '.$order['name'].(!empty($order['note']) ? "\nSonderwunsch:".$order['note'] : '')."\n\n";
            $html .= '<li>'.$order['cnt'].'x '.$order['name'].(!empty($order['note']) ? '<li>Sonderwunsch: '.$order['note'].'</li>' : '').'</li>';
        }

        // get active payer
        $activePayer = $this->entityManager->getRepository(Payhistory::class)->findActivePayer();
        /** @var Paypalmes */
        $activePayer = $this->entityManager->getRepository(Paypalmes::class)->find($activePayer['id']);
        // prepare symfony mailer
        $email = (new Email())
            ->from('essen@hochwarth-e.com')
            ->to($activePayer->getEmail())
            ->subject('Bestellübersicht')
            ->text($text)
            ->html($html);

        $this->mailer->send($email);
    }
}
