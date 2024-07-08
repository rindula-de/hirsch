<?php

namespace App\MessageHandler;

use App\Entity\DailyFood;
use App\Message\SyncMenuMessage;
use App\Repository\DailyFoodRepository;
use DateTime;
use Smalot\PdfParser\Parser;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\RecoverableMessageHandlingException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

#[AsMessageHandler]
final class SyncMenuMessageHandler
{

    public function __construct(
        private readonly DailyFoodRepository $dailyFoodRepository,
        private readonly MessageBusInterface $messageBus,
    ) {}

    public function __invoke(SyncMenuMessage $message): void
    {
        $server = $_ENV['MAIL_ACCESS_HOST'];
        $adresse = $_ENV['MAIL_ACCESS_USERNAME'];
        $password = $_ENV['MAIL_ACCESS_PASSWORD'];
        $mbox = @imap_open($server, $adresse, $password);

        if (!$mbox) {
            $error = '';
            if (imap_last_error()) {
                $error = imap_last_error();
            }

            throw new InternalErrorException($error);
        }
        $emails = imap_sort(
            $mbox,
            SORTDATE,
            true,
            0,
            'SINCE "'.(new DateTime('-6 days'))->format('d F Y').'"'
        );

        $displayData = [];

        if ($emails) {
            $regexLunch = '(([\w\s\-\,éèáàíìóòúùÁÀÉÈÍÌÓÒÚÙöäüÄÜÖß!@#$%^&*)(\'`´„“\/]+?)(?: (\d+,\d{2}))|\-?\s?Ruhetag\s?\-?)';
            foreach ($emails as $emailId) {
                $structure = imap_fetchstructure($mbox, $emailId);

                if (isset($structure->parts) && count($structure->parts)) {
                    for ($i = 0; $i < count($structure->parts); ++$i) {
                        $attachments[$i] = [
                            'is_attachment' => false,
                            'filename' => '',
                            'name' => '',
                            'attachment' => '',
                        ];

                        if ($structure->parts[$i]->ifdparameters) {
                            foreach ($structure->parts[$i]->dparameters as $object) {
                                if ('filename' == strtolower($object->attribute)) {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['filename'] = $object->value;
                                }
                            }
                        }

                        if ($structure->parts[$i]->ifparameters) {
                            foreach ($structure->parts[$i]->parameters as $object) {
                                if ('name' == strtolower($object->attribute)) {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['name'] = $object->value;
                                }
                            }
                        }

                        if ($attachments[$i]['is_attachment']) {
                            $attachments[$i]['attachment'] = imap_fetchbody($mbox, $emailId, ($i + 1).'');

                            if (is_string($attachments[$i]['attachment'])) {
                                if (3 == $structure->parts[$i]->encoding) { // 3 = BASE64
                                    $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                                } elseif (4 == $structure->parts[$i]->encoding) { // 4 = QUOTED-PRINTABLE
                                    $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                                }
                            }
                        }
                    }
                }

                if (isset($attachments)) {
                    foreach ($attachments as $at) {
                        if (1 == $at['is_attachment']) {
                            if (str_contains(strtolower($at['filename']), 'mittagstisch') && is_string($at['attachment'])) {
                                $filename = tempnam(sys_get_temp_dir(), 'hi_');

                                if ($filename) {
                                    $file = base64_encode($at['attachment']);
                                    file_put_contents($filename, $at['attachment']);

                                    $parser = new Parser();
                                    $pdf = $parser->parseFile($filename);

                                    $text = str_replace("\t", '', $pdf->getText());
                                    $text = preg_replace('/\s+/', ' ', $text);
                                    $text = trim($text ?? '');

                                    preg_match(
                                        '/Montag '.$regexLunch.'/',
                                        $text,
                                        $matches
                                    );
                                    $displayData[] = [
                                        'name' => trim($matches[2]),
                                        'date' => (new DateTime('monday 2pm this week')),
                                    ];

                                    preg_match(
                                        '/Dienstag '.$regexLunch.'/',
                                        $text,
                                        $matches
                                    );
                                    $displayData[] = [
                                        'name' => trim($matches[2]),
                                        'date' => (new DateTime('tuesday 2pm this week')),
                                    ];

                                    preg_match(
                                        '/Mittwoch '.$regexLunch.'/',
                                        $text,
                                        $matches
                                    );
                                    $displayData[] = [
                                        'name' => trim($matches[2]),
                                        'date' => (new DateTime('wednesday 2pm this week')),
                                    ];

                                    preg_match(
                                        '/Donnerstag '.$regexLunch.'/',
                                        $text,
                                        $matches
                                    );
                                    $displayData[] = [
                                        'name' => trim($matches[2]),
                                        'date' => (new DateTime('thursday 2pm this week')),
                                    ];

                                    preg_match(
                                        '/Freitag '.$regexLunch.'/',
                                        $text,
                                        $matches
                                    );
                                    $displayData[] = [
                                        'name' => trim($matches[2]),
                                        'date' => (new DateTime('friday 2pm this week')),
                                    ];

                                    unlink($filename);

                                    // save the data to the database
                                    foreach ($displayData as $data) {
                                        $food = new DailyFood();
                                        $food
                                            ->setDate($data['date'])
                                            ->setFile($file)
                                            ->setName($data['name']);
                                        $this->dailyFoodRepository->save($food, true);
                                    }
                                }
                            }
                        }
                    }
                    break;
                }
                // delete the mails so the inbox is empty again
                imap_delete($mbox, $emailId);
            }
        }

        imap_close($mbox);

        if (empty($displayData)) {
            // retry in an hour
            $this->messageBus->dispatch($message, [
                new DelayStamp(3600000)
            ]);
        }
    }
}
