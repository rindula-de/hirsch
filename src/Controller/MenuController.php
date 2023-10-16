<?php

/*
 * (c) Sven Nolting, 2023
 */

namespace App\Controller;

use App\Repository\HirschRepository;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Exception;
use Smalot\PdfParser\Parser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MenuController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->redirectToRoute('menu');
    }

    #[Route('/karte', name: 'menu', methods: ['GET'])]
    public function menu(): Response
    {
        $cache = new FilesystemAdapter();
        $menuDisabled = $cache->get('menu_disabled', function (ItemInterface $item) {
            $item->expiresAfter(0);

            return false;
        });

        return $this->render('menu/index.html.twig', [
            'menu_disabled' => $menuDisabled,
        ]);
    }

    /**
     * Get the Hirsch to Go menu.
     */
    #[Route('/api/get-menu', name: 'api_menu', methods: ['GET'])]
    public function getMenu(Request $request, HirschRepository $hirschRepository): Response
    {
        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->eq('display', true))->andWhere(Criteria::expr()->neq('slug', 'tagesessen'));

        $htg = $hirschRepository->matching($criteria)->toArray();

        $frameId = $request->headers->get('Turbo-Frame');

        $cache = new FilesystemAdapter();
        $menuDisabled = $cache->get('menu_disabled', function (ItemInterface $item) {
            $item->expiresAfter(0);

            return false;
        });

        if (null === $frameId) {
            return $this->json($menuDisabled ? [] : $htg);
        }

        return $this->render('menu/htgframe.html.twig', [
            'htg' => $htg,
            'menu_disabled' => $menuDisabled,
        ]);
    }

    /**
     * Get a list of all menu items this week.
     */
    #[Route('/api/get-tagesessen', name: 'tagesessen', methods: ['GET'])]
    #[Route('/api/get-tagesessen-karte', name: 'tagesessenkarte', methods: ['GET'])]
    public function getTagesessen(Request $request, TranslatorInterface $translator, HirschRepository $hirschRepository): Response
    {
        $file = '';
        $message = '';

        try {
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

            $emailsToDelete = imap_sort(
                $mbox,
                SORTDATE,
                true,
                0,
                'BEFORE "'.(new DateTime('-6 days'))->format('d F Y').'"'
            );
            $emails = imap_sort(
                $mbox,
                SORTDATE,
                true,
                0,
                'SINCE "'.(new DateTime('-6 days'))->format('d F Y').'"'
            );

            $displayData = [];

            if ($emailsToDelete) {
                foreach ($emailsToDelete as $emailId) {
                    // Markiert die E-Mails zum löschen
                    imap_delete($mbox, 8 == explode('.', phpversion())[0] ? $emailId.'' : $emailId);
                }

                // Löscht die markierten Mails endgültig
                imap_expunge($mbox);
                imap_close($mbox);

                // Die Mailbox muss nochmal neu initialisiert werden, da die IDs anders sind ... Also ... RELOAD!
                return $this->redirectToRoute('tagesessen');
            }

            if ($emails) {
                $regexLunch = '(\w[\w\s\-\,éèáàíìóòúùÁÀÉÈÍÌÓÒÚÙöäüÄÜÖß!@#$%^&*)(\'`´„“\/]+?(?: (\d+,\d{2}))|\-\s?Ruhetag\s?\-)';
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
                                            'gericht' => trim($matches[1]),
                                            'date' => (new DateTime('monday 2pm this week')),
                                        ];

                                        preg_match(
                                            '/Dienstag '.$regexLunch.'/',
                                            $text,
                                            $matches
                                        );
                                        $displayData[] = [
                                            'gericht' => trim($matches[1]),
                                            'date' => (new DateTime('tuesday 2pm this week')),
                                        ];

                                        preg_match(
                                            '/Mittwoch '.$regexLunch.'/',
                                            $text,
                                            $matches
                                        );
                                        $displayData[] = [
                                            'gericht' => trim($matches[1]),
                                            'date' => (new DateTime('wednesday 2pm this week')),
                                        ];

                                        preg_match(
                                            '/Donnerstag '.$regexLunch.'/',
                                            $text,
                                            $matches
                                        );
                                        $displayData[] = [
                                            'gericht' => trim($matches[1]),
                                            'date' => (new DateTime('thursday 2pm this week')),
                                        ];

                                        preg_match(
                                            '/Freitag '.$regexLunch.'/',
                                            $text,
                                            $matches
                                        );
                                        $displayData[] = [
                                            'gericht' => trim($matches[1]),
                                            'date' => (new DateTime('friday 2pm this week')),
                                        ];

                                        unlink($filename);
                                    }
                                }
                            }
                        }
                        break;
                    }
                }
            }

            imap_close($mbox);
        } catch (Exception $e) {
            $displayData = false;
            $message = $e->getMessage();
        }

        $frameId = $request->headers->get('Turbo-Frame');

        if (null === $frameId) {
            if ('tagesessenkarte' == $request->attributes->get('_route')) {
                return $this->json(['file' => $file, 'message' => $message]);
            }

            if ('tagesessen' == $request->attributes->get('_route')) {
                return $this->json(['displayData' => $displayData, 'message' => $message]);
            }

            return $this->json(['message' => $translator->trans('defaults.route_not_found')]);
        } else {
            if ('dailymenu' === $frameId) {
                if (is_array($displayData)) {
                    $displayData = array_filter($displayData, function ($d) {
                        return $d['date'] >= new DateTime('today');
                    });
                }

                return $this->render('menu/frame.html.twig', [
                    'dailyfood' => $displayData,
                ]);
            }

            return $this->json(['message' => $translator->trans('defaults.route_not_found')]);
        }
    }
}
