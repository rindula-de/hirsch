<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Paypalme;
use App\Model\Table\PaypalmesTable;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Datasource\ResultSetInterface;
use Cake\Http\Response;

/**
 * Paypalmes Controller
 *
 * @property PaypalmesTable $Paypalmes
 * @method Paypalme[]|ResultSetInterface paginate($object = null, array $settings = [])
 */
class PaypalmesController extends AppController
{
    /**
     * Index method
     *
     * @return Response|null|void Renders view
     */
    public function index()
    {
        $paypalmes = $this->paginate($this->Paypalmes);

        $active = $this->Paypalmes->findActivePayer();

        $this->set(compact('paypalmes', 'active'));
    }

    /**
     * Add method
     *
     * @return Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $paypalme = $this->Paypalmes->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            if (strpos(strtolower($data['link']), 'paypal.me') === false) {

                // Ein "/" vor oder hinter dem Namen entfernen
                if (strpos($data['link'], "/") === 0) {
                    $data['link'] = substr($data['link'], 1);
                } elseif (strpos($data['link'], "/") === strlen($data['link']) - 1) {
                    $data['link'] = substr($data['link'], 0, -1);
                }

                $data['link'] = "https://paypal.me/{$data['link']}/";
                $paypalme = $this->Paypalmes->patchEntity($paypalme, $data);
                if ($this->Paypalmes->save($paypalme)) {
                    $this->Flash->success(__('The paypalme has been saved.'));

                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('The paypalme could not be saved. Please, try again.'));
            } else {
                $this->Flash->error(__('You have entered the Linkname in a wrong format. If your link is for example "https://paypal.me/rindulalp/" you have to enter "rindulalp"'));
            }
        }
        $this->set(compact('paypalme'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Paypalme id.
     * @return Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $paypalme = $this->Paypalmes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $paypalme = $this->Paypalmes->patchEntity($paypalme, $this->request->getData());
            if ($this->Paypalmes->save($paypalme)) {
                $this->Flash->success(__('The paypalme has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The paypalme could not be saved. Please, try again.'));
        }
        $this->set(compact('paypalme'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Paypalme id.
     * @return Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws RecordNotFoundException When record not found.
     */
    public function pay()
    {
        $id = base64_decode($this->request->getData()['id']);
        $ppm = $this->Paypalmes->get($id);
        if ($ppm) {
            $this->Paypalmes->Payhistory->save($this->Paypalmes->Payhistory->newEntity([
                "paypalme_id" => $id
            ]));
            return $this->redirect($ppm->link . "3.5");
        }
        return $this->redirect(['action' => 'index']);
    }

}
