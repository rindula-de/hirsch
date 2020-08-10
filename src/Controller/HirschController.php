<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\HirschTable;
use App\Model\Table\OrdersTable;
use Cake\Core\Configure;
use Cake\Http\Exception\InternalErrorException;
use Cake\I18n\Date;
use Cake\I18n\Time;
use Exception;
use Smalot\PdfParser\Parser;

/**
 * Hirsch Controller
 *
 * @method \App\Model\Entity\Hirsch[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 * @property HirschTable Hirsch
 * @property \Cake\Http\Cookie\Cookie Cookie
 */
class HirschController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     * @throws Exception
     */
    public function index()
    {
        $server = Configure::readOrFail("MailAccess.host");
        $adresse = Configure::readOrFail("MailAccess.username");
        $password = Configure::readOrFail("MailAccess.password");
        $mbox = @imap_open($server, $adresse, $password);
        if (!$mbox) throw new InternalErrorException(imap_last_error());

        $emailsToDelete = imap_sort($mbox, SORTDATE, 1, 0, 'BEFORE "' . (new Time('-6 days'))->format('d F Y') . '"');
        $emails = imap_sort($mbox, SORTDATE, 1, 0, 'SINCE "' . (new Time('-6 days'))->format('d F Y') . '"');

        $displayData = [];

        $htg = $this->Hirsch->find()->where(['slug !=' => 'tagesessen']);

        if ($emailsToDelete) {
            foreach ($emailsToDelete as $emailId) {
                // Markiert die E-Mails zum löschen
                imap_delete($mbox, $emailId);
            }
            // Löscht die markierten Mails endgültig
            imap_expunge($mbox);
            imap_close($mbox);
            // Die Mailbox muss nochmal neu initialisiert werden, da die IDs anders sind ... Also ... RELOAD!
            return $this->redirect(['_name' => 'karte']);
        }

        if ($emails) {
            foreach ($emails as $emailId) {
                $structure = imap_fetchstructure($mbox, $emailId);
                $overview = imap_fetch_overview($mbox, "" . $emailId)[0];
                $body = utf8_encode(quoted_printable_decode(imap_fetchbody($mbox, $emailId, '1.1')));


                if (isset($structure->parts) && count($structure->parts)) {
                    for ($i = 0; $i < count($structure->parts); $i++) {
                        $attachments[$i] = array(
                            'is_attachment' => false,
                            'filename' => '',
                            'name' => '',
                            'attachment' => '');

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
                            $attachments[$i]['attachment'] = imap_fetchbody($mbox, $emailId, ($i + 1) . "");
                            if ($structure->parts[$i]->encoding == 3) { // 3 = BASE64
                                $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                            } elseif ($structure->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
                                $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                            }
                        }
                    } // for($i = 0; $i < count($structure->parts); $i++)
                } // if(isset($structure->parts) && count($structure->parts))

                if (count($attachments) != 0) {
                    foreach ($attachments as $at) {
                        if ($at['is_attachment'] == 1) {
                            $filename = tempnam(ROOT . DIRECTORY_SEPARATOR . 'tmp', 'hi_');
                            file_put_contents($filename, $at['attachment']);
                            $parser = new Parser();
                            $pdf = $parser->parseFile($filename);
                            $text = str_replace("\t", '', $pdf->getText());
                            if (strtolower($at['name']) == 'mittagstisch.pdf') {
                                $now = new Time();
                                $dow = $now->dayOfWeek;
                                $daysAdd = 0;
                                switch ($dow) {
                                    case 1:
                                        preg_match('/M\s*o\s*n\s*t\s*a\s*g[^a-zA-Z0-9\-]*([^\d\n]*)/', $text, $matches);
                                        $displayData[] = ['gericht' => trim($matches[1]), 'date' => (new Date())];
                                    case 2:
                                        preg_match('/D\s*i\s*e\s*n\s*s\s*t\s*a\s*g[^a-zA-Z0-9\-]*([^\d]*)/', $text, $matches);
                                        if ($dow == 1) {
                                            $daysAdd = 1;
                                        }
                                        $displayData[] = ['gericht' => trim($matches[1]), 'date' => (new Date("+" . $daysAdd . " days"))];
                                    case 3:
                                        preg_match('/M\s*i\s*t\s*t\s*w\s*o\s*c\s*h[^a-zA-Z0-9\-]*([^\d]*)/', $text, $matches);
                                        if ($dow == 1) {
                                            $daysAdd = 2;
                                        }
                                        if ($dow == 2) {
                                            $daysAdd = 1;
                                        }
                                        $displayData[] = ['gericht' => trim($matches[1]), 'date' => (new Date("+" . $daysAdd . " days"))];
                                    case 4:
                                        preg_match('/D\s*o\s*n\s*n\s*e\s*r\s*s\s*t\s*a\s*g[^a-zA-Z0-9\-]*([^\d]*)/', $text, $matches);
                                        if ($dow == 1) {
                                            $daysAdd = 3;
                                        }
                                        if ($dow == 2) {
                                            $daysAdd = 2;
                                        }
                                        if ($dow == 3) {
                                            $daysAdd = 1;
                                        }
                                        $displayData[] = ['gericht' => trim($matches[1]), 'date' => (new Date("+" . $daysAdd . " days"))];
                                    case 5:
                                        preg_match('/F\s*r\s*e\s*i\s*t\s*a\s*g[^a-zA-Z0-9\-]*([^\d]*)/', $text, $matches);
                                        if ($dow == 1) {
                                            $daysAdd = 4;
                                        }
                                        if ($dow == 2) {
                                            $daysAdd = 3;
                                        }
                                        if ($dow == 3) {
                                            $daysAdd = 2;
                                        }
                                        if ($dow == 4) {
                                            $daysAdd = 1;
                                        }
                                        $displayData[] = ['gericht' => trim($matches[1]), 'date' => (new Date("+" . $daysAdd . " days"))];
                                }
                            }
                            unlink($filename);
                        }
                    }
                }
            }
        }
        imap_close($mbox);

        $this->set(compact('displayData', 'htg'));
    }

    public function modalText() {
        $this->viewBuilder()->setLayout('ajax');
        $lastShowed = $this->request->getSession()->read('lastShowed');
        if (!$lastShowed) {
            $lastShowed = new Time(0);
            $this->request->getSession()->write('lastShowed', $lastShowed);
        }

        $holidaysTable = $this->getTableLocator()->get('holidays');

        $holiday = $holidaysTable->find()->where(['end >=' => new Date()])->order(['start', 'end'])->first();

        $this->set(compact('lastShowed', 'holiday'));
    }

    private function read_docx($filename)
    {
        $content = '';

        $zip = zip_open($filename);
        if (!$zip || is_numeric($zip)) return false;

        while ($zip_entry = zip_read($zip)) {

            if (zip_entry_open($zip, $zip_entry) == FALSE) continue;

            if (zip_entry_name($zip_entry) != "word/document.xml") continue;

            $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

            zip_entry_close($zip_entry);
        }
        zip_close($zip);
        $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
        $content = str_replace('</w:r></w:p>', "\r\n", $content);
        $striped_content = strip_tags($content);

        return $striped_content;
    }
}
