<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\OrdersTable;
use Cake\I18n\Time;
use Exception;
use Smalot\PdfParser\Parser;

/**
 * Hirsch Controller
 *
 * @method \App\Model\Entity\Hirsch[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
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
        $server = '{rindula.de/imap/novalidate-cert}INBOX.Essen';
        $adresse = 'essen@rindula.de';
        $password = 'foodwars';
        $mbox = imap_open($server, $adresse, $password) or die("Error: " . imap_last_error());

        $emails = imap_sort($mbox, SORTDATE, 1, 0, 'SINCE "' . (new Time('-1 weeks'))->format('d F Y') . '"');

        $displayData = [];

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

                $htg = [
                    'Schweizer Wurstsalat mit Pommes',
                    'Bunte Blattsalate mit Hühnerbrust',
                    'Bunte Blattsalate mit gegrillten Garnelen',
                    'Gebackener Camembert, Preiselbeeren und Salat',
                    'Paniertes Schweineschnitzel, Pommes und Salat ',
                    'Jägerschnitzel, Spätzle und Salat',
                    'Zigeunerschnitzel mit Kroketten und Salat',
                    'Cordon Bleu mit Pommes und Salat',
                    'Schweinefilet in Pilzrahmsauce, Spätzle und Salat',
                    'Schweinesteak mit Kräuterbutter, Pommes kleiner Salat',
                    'Käsespätzle mit buntem Salat',
                    'Salbeignocchi mit Grillgemüse, Parmesan und Ruccola',
                ];
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
                                        preg_match('/M\s*o\s*n\s*t\s*a\s*g[^a-zA-Z0-9\-]*([^\n\d]*)/', $text, $matches);
                                        $displayData[] = ['gericht' => $matches[1], 'date' => (new Time())];
                                    case 2:
                                        preg_match('/D\s*i\s*e\s*n\s*s\s*t\s*a\s*g[^a-zA-Z0-9\-]*([^\n\d]*)/', $text, $matches);
                                        if ($dow == 1) {
                                            $daysAdd = 1;
                                        }
                                        $displayData[] = ['gericht' => $matches[1], 'date' => (new Time("+" . $daysAdd . " days"))];
                                        $dow++;
                                    case 3:
                                        preg_match('/M\s*i\s*t\s*t\s*w\s*o\s*c\s*h[^a-zA-Z0-9\-]*([^\n\d]*)/', $text, $matches);
                                        if ($dow == 1) {
                                            $daysAdd = 2;
                                        }
                                        if ($dow == 2) {
                                            $daysAdd = 1;
                                        }
                                        $displayData[] = ['gericht' => $matches[1], 'date' => (new Time("+" . $daysAdd . " days"))];
                                    case 4:
                                        preg_match('/D\s*o\s*n\s*n\s*e\s*r\s*s\s*t\s*a\s*g[^a-zA-Z0-9\-]*([^\n\d]*)/', $text, $matches);
                                        if ($dow == 1) {
                                            $daysAdd = 3;
                                        }
                                        if ($dow == 2) {
                                            $daysAdd = 2;
                                        }
                                        if ($dow == 3) {
                                            $daysAdd = 1;
                                        }
                                        $displayData[] = ['gericht' => $matches[1], 'date' => (new Time("+" . $daysAdd . " days"))];
                                    case 5:
                                        preg_match('/F\s*r\s*e\s*i\s*t\s*a\s*g[^a-zA-Z0-9\-]*([^\n\d]*)/', $text, $matches);
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
                                        $displayData[] = ['gericht' => $matches[1], 'date' => (new Time("+" . $daysAdd . " days"))];
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

    public function order($meal = '')
    {
        if ($this->request->is('post') && !empty($meal) && ((new Time())->hour < 10 || ((new Time())->hour == 10 && (new Time())->minute <= 45))) {
            /** @var OrdersTable $orders */
            $orders = $this->getTableLocator()->get('Orders');
            $paypalmes = $this->getTableLocator()->get('Paypalmes')->find('list', [
                'keyField' => 'id',
                'valueField' => 'name'
            ])->toArray();
            $data = $this->request->getData();
            $order = $orders->newEntity($data);
            $this->set(compact('meal', 'order', 'paypalmes'));
            if (!empty($data)) {
                if ($orders->save($order)) {
                    $link = $this->getTableLocator()->get('Paypalmes')->get($order->paypalme)->get('link');
                    $this->Flash->success("Bestellung aufgegeben (".(new Time())->nice().")");
                    return $this->redirect($link . "3.5");
                } else {
                    $this->Flash->error("Konnte Bestellung nicht aufgeben! Bitte versuche es erneut!");
                    return;
                }
            }
            return;
        }
        $this->Flash->error("Die Zeit zum bestellen ist abgelaufen!");
        return $this->redirect(['controller' => 'hirsch', 'action' => 'index']);
    }

    public function orders() {
        $orders = $this->getTableLocator()->get('Orders');
        $time = (new Time());
        $o = $orders->find()->where([
            'created >' => (new Time(($time->timestamp + $time->secondsUntilEndOfDay()) - 86399))->toIso8601String()
        ]);
        $this->set(['orders' => $o]);
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
