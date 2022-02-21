<?php

namespace App\Controller;

use App\Repository\HirschRepository;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Exception;
use Smalot\PdfParser\Parser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
        return $this->render('menu/index.html.twig', []);
    }

    /**
     * Get the Hirsch to Go menu.
     *
     * @return JsonResponse
     */
    #[Route('/api/get-menu', name: 'api_menu', methods: ['GET'])]
    public function getMenu(HirschRepository $hirschRepository): JsonResponse
    {
        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->eq('display', true))->andWhere(Criteria::expr()->neq('slug', 'tagesessen'));

        $htg = $hirschRepository->matching($criteria)->toArray();

        return $this->json($htg);
    }

    /**
     * Get a list of all menu items this week.
     */
    #[Route('/api/get-tagesessen', name: 'tagesessen', methods: ['GET'])]
    #[Route('/api/get-tagesessen-karte', name: 'tagesessenkarte', methods: ['GET'])]
    public function getTagesessen(Request $request, TranslatorInterface $translator): JsonResponse|RedirectResponse
    {
        $file = '';
        $message = '';
        $regexLunch = '([\w\s\-\,öäüÄÜÖß!@#$%^&*)(\'`„“]+?)( (\d+,\d{2}) Euro?)?';

        try {
            $server = $_ENV['MailAccess_host'];
            $adresse = $_ENV['MailAccess_username'];
            $password = $_ENV['MailAccess_password'];
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
                'BEFORE "'.(new DateTime('-6 days'))->format('d F Y').'"');
            $emails = imap_sort(
                $mbox,
                SORTDATE,
                true,
                0,
                'SINCE "'.(new DateTime('-6 days'))->format('d F Y').'"');

            $displayData = [];

            if ($emailsToDelete) {
                foreach ($emailsToDelete as $emailId) {
                    // Markiert die E-Mails zum löschen
                    imap_delete($mbox, (explode('.', phpversion())[0] == 8 ? $emailId.'' : $emailId));
                }

                // Löscht die markierten Mails endgültig
                imap_expunge($mbox);
                imap_close($mbox);

                // Die Mailbox muss nochmal neu initialisiert werden, da die IDs anders sind ... Also ... RELOAD!
                return $this->redirect('tagesessen');
            }

            if ($emails) {
                foreach ($emails as $emailId) {
                    $structure = imap_fetchstructure($mbox, $emailId);

                    if (isset($structure->parts) && count($structure->parts)) {
                        for ($i = 0; $i < count($structure->parts); $i++) {
                            $attachments[$i] = [
                                'is_attachment' => false,
                                'filename'      => '',
                                'name'          => '',
                                'attachment'    => '',
                            ];

                            if ($structure->parts[$i]->ifdparameters) {
                                foreach ($structure->parts[$i]->dparameters as $object) {
                                    if (strtolower($object->attribute) == 'filename') {
                                        $attachments[$i]['is_attachment'] = true;
                                        $attachments[$i]['filename'] = $object->value;
                                    }
                                }
                            }

                            if ($structure->parts[$i]->ifparameters) {
                                foreach ($structure->parts[$i]->parameters as $object) {
                                    if (strtolower($object->attribute) == 'name') {
                                        $attachments[$i]['is_attachment'] = true;
                                        $attachments[$i]['name'] = $object->value;
                                    }
                                }
                            }

                            if ($attachments[$i]['is_attachment']) {
                                $attachments[$i]['attachment'] = imap_fetchbody($mbox, $emailId, ($i + 1).'');

                                if (is_string($attachments[$i]['attachment'])) {
                                    if ($structure->parts[$i]->encoding == 3) { // 3 = BASE64
                                        $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                                    } elseif ($structure->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
                                        $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                                    }
                                }
                            }
                        }
                    }

                    if (isset($attachments) && count($attachments) != 0) {
                        foreach ($attachments as $at) {
                            if ($at['is_attachment'] == 1) {
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
                                            '/Montag ' . $regexLunch . ' Dienstag/',
                                            $text,
                                            $matches);
                                        $displayData[] = [
                                            'gericht' => trim($matches[1]),
                                            'date' => (new DateTime('monday noon this week'))
                                        ];

                                        preg_match(
                                            '/Dienstag ' . $regexLunch . ' Mittwoch/',
                                            $text,
                                            $matches);
                                        $displayData[] = [
                                            'gericht' => trim($matches[1]),
                                            'date' => (new DateTime('tuesday noon this week'))
                                        ];

                                        preg_match(
                                            '/Mittwoch ' . $regexLunch . ' Donnerstag/',
                                            $text,
                                            $matches);
                                        $displayData[] = [
                                            'gericht' => trim($matches[1]),
                                            'date' => (new DateTime('wednesday noon this week'))
                                        ];

                                        preg_match(
                                            '/Donnerstag ' . $regexLunch . ' Freitag/',
                                            $text,
                                            $matches);
                                        $displayData[] = [
                                            'gericht' => trim($matches[1]),
                                            'date' => (new DateTime('thursday noon this week'))
                                        ];

                                        preg_match(
                                            '/Freitag ' . $regexLunch . ' Restaurant/',
                                            $text,
                                            $matches);
                                        $displayData[] = [
                                            'gericht' => trim($matches[1]),
                                            'date' => (new DateTime('friday noon this week'))
                                        ];

                                        unlink($filename);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            imap_close($mbox);
        } catch (Exception $e) {
            $displayData = false;
            $message = $e->getMessage();
        }

        if ($request->attributes->get('_route') == 'tagesessenkarte') {
            return $this->json(['file' => $file, 'message' => $message]);
        }

        if ($request->attributes->get('_route') == 'tagesessen') {
            return $this->json(['displayData' => $displayData, 'message' => $message]);
        }

        return $this->json(['message' => $translator->trans('defaults.route_not_found')]);
    }
}
