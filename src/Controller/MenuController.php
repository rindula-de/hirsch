<?php

namespace App\Controller;

use App\Repository\HirschRepository;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Smalot\PdfParser\Parser;

class MenuController extends AbstractController
{

    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->redirectToRoute("menu");
    }

    /**
     * @Route("/karte", name="menu")
     */
    public function menu(HirschRepository $hirschRepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->eq('display', true))->andWhere(Criteria::expr()->neq('slug', 'tagesessen'));

        $htg = $hirschRepository->matching($criteria)->toArray();
        return $this->render('menu/index.html.twig', [
            'htg' => $htg,
        ]);
    }

    /**
     * @Route("/hirsch/get-tagesessen", name="tagesessen")
     */
    public function getTagesessen(): JsonResponse
    {
        $file = '';
        $message = '';
        try {
            $server = $_ENV['MailAccess_host'];
            $adresse = $_ENV['MailAccess_username'];
            $password = $_ENV['MailAccess_password'];
            $mbox = @imap_open($server, $adresse, $password);
            if (!$mbox) {
                throw new InternalErrorException(imap_last_error());
            }

            $emailsToDelete = imap_sort($mbox, SORTDATE, (explode(".", phpversion())[0] == 8?true:1), 0, 'BEFORE "' . (new DateTime('-6 days'))->format('d F Y') . '"');
            $emails = imap_sort($mbox, SORTDATE, (explode(".", phpversion())[0] == 8?true:1), 0, 'SINCE "' . (new DateTime('-6 days'))->format('d F Y') . '"');

            $displayData = [];

            if ($emailsToDelete) {
                foreach ($emailsToDelete as $emailId) {
                    // Markiert die E-Mails zum löschen
                    imap_delete($mbox, (explode(".", phpversion())[0] == 8?$emailId."":$emailId));
                }
                // Löscht die markierten Mails endgültig
                imap_expunge($mbox);
                imap_close($mbox);
                // Die Mailbox muss nochmal neu initialisiert werden, da die IDs anders sind ... Also ... RELOAD!
                return $this->redirect("tagesessen");
            }

            if ($emails) {
                foreach ($emails as $emailId) {
                    $structure = imap_fetchstructure($mbox, $emailId);

                    if (isset($structure->parts) && count($structure->parts)) {
                        for ($i = 0; $i < count($structure->parts); $i++) {
                            $attachments[$i] = [
                                'is_attachment' => false,
                                'filename' => '',
                                'name' => '',
                                'attachment' => ''];

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
                                $attachments[$i]['attachment'] = imap_fetchbody($mbox, $emailId, ($i + 1) . '');
                                if ($structure->parts[$i]->encoding == 3) { // 3 = BASE64
                                    $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                                } elseif ($structure->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
                                    $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                                }
                            }
                        }
                    }

                    if (count($attachments) != 0) {
                        foreach ($attachments as $at) {
                            if ($at['is_attachment'] == 1) {
                                if (strtolower($at['filename']) == 'mittagstisch.pdf') {
                                    $filename = tempnam(sys_get_temp_dir(), 'hi_');
                                    $file = base64_encode($at['attachment']);
                                    file_put_contents($filename, $at['attachment']);
                                    $parser = new Parser();
                                    $pdf = $parser->parseFile($filename);
                                    $text = str_replace("\t", '', $pdf->getText());
                                    $dow = date( "w");
                                    $daysAdd = 0;
                                    switch ($dow) {
                                        case 1:
                                            preg_match('/M\s*o\s*n\s*t\s*a\s*g[^a-zA-Z0-9\-]+([^\d\n]*)/', $text, $matches);
                                            $displayData[] = ['gericht' => trim($matches[1]), 'date' => (new DateTime())];
                                        case 2:
                                            preg_match('/D\s*i\s*e\s*n\s*s\s*t\s*a\s*g[^a-zA-Z0-9\-]+([^\d\n]*)/', $text, $matches);
                                            if ($dow < 2) {
                                                $daysAdd++;
                                            }
                                            $displayData[] = ['gericht' => trim($matches[1]), 'date' => (new DateTime('+' . $daysAdd . ' days'))];
                                        case 3:
                                            preg_match('/M\s*i\s*t\s*t\s*w\s*o\s*c\s*h[^a-zA-Z0-9\-]+([^\d\n]*)/', $text, $matches);
                                            if ($dow < 3) {
                                                $daysAdd++;
                                            }
                                            $displayData[] = ['gericht' => trim($matches[1]), 'date' => (new DateTime('+' . $daysAdd . ' days'))];
                                        case 4:
                                            preg_match('/D\s*o\s*n\s*n\s*e\s*r\s*s\s*t\s*a\s*g[^a-zA-Z0-9\-]+([^\d\n]*)/', $text, $matches);
                                            if ($dow < 4) {
                                                $daysAdd++;
                                            }
                                            $displayData[] = ['gericht' => trim($matches[1]), 'date' => (new DateTime('+' . $daysAdd . ' days'))];
                                        case 5:
                                            preg_match('/F\s*r\s*e\s*i\s*t\s*a\s*g[^a-zA-Z0-9\-]+([^\d\n]*)/', $text, $matches);
                                            if ($dow < 5) {
                                                $daysAdd++;
                                            }
                                            $displayData[] = ['gericht' => trim($matches[1]), 'date' => (new DateTime('+' . $daysAdd . ' days'))];
                                    }
                                    unlink($filename);
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

        return $this->json(['displayData' => $displayData, 'file' => $file, "message" => $message]);
    }
}
