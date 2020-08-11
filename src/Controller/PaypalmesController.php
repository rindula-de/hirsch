<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Paypalmes Controller
 *
 * @property \App\Model\Table\PaypalmesTable $Paypalmes
 * @method \App\Model\Entity\Paypalme[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PaypalmesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $paypalmes = $this->paginate($this->Paypalmes);

        $this->set(compact('paypalmes'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
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
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
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

}
