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
}
