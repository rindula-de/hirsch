<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Order;
use App\Model\Table\OrdersTable;
use Cake\Core\Configure;
use Cake\I18n\Date;
use Cake\I18n\Time;

/**
 * Orders Controller
 *
 * @property \App\Model\Table\OrdersTable $Orders
 * @method \App\Model\Entity\Order[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class OrdersController extends AppController
{

    public function order($future = 0, $mealSlug = null)
    {
        $now = new Time();
        /** @var OrdersTable $orders */
        $orders = $this->getTableLocator()->get('Orders');
        $data = $this->request->getData();
        $data['for'] = new Date('+' . $future . ' days');
        $order = $orders->newEntity($data);
        $meal = $this->Orders->Hirsch->findBySlug($mealSlug)->first();

        $cookiedName = '';
        if (!empty($_COOKIE['lastOrderedName'])) {
            $cookiedName = $_COOKIE['lastOrderedName'];
        }

        $this->set(compact('meal', 'order', 'cookiedName'));

        if ($this->request->is('post')) {
            if (!empty($data)) {
                setcookie('lastOrderedName', $data['orderedby'], time() + (60 * 60 * 24 * 30), '/');
                if ($orders->save($order)) {
                    $this->Flash->success("Bestellung aufgegeben, Zahlung ausstehend");
                    return $this->redirect(['controller' => 'paypalmes', 'action' => 'index']);
                } else {
                    $this->Flash->error("Konnte Bestellung nicht aufgeben! Bitte versuche es erneut!");
                    return;
                }
            }
            return;
        } elseif (!Configure::read('debug') && (($future == 0 && ($now->hour > 10 || ($now->hour == 10 && $now->minute > 55))) || $future < 0)) {
            $this->Flash->error("Die Zeit zum bestellen ist abgelaufen!");
            return $this->redirect(['controller' => 'hirsch', 'action' => 'index']);
        }
    }

    public function list()
    {
        $botd = new Date();
        $o = $this->Orders->find()->where([
            'for' => $botd->toIso8601String()
        ])->contain(['Hirsch']);

        $preorders = $this->Orders->find()->where([
            'for >' => $botd->toIso8601String()
        ])->group(['Hirsch.name', 'note', 'for'])->select(['Hirsch.name', 'for', 'note', 'cnt' => 'count(Hirsch.name)'])->contain(['Hirsch']);

        $oG = $this->Orders->find()->where([
            'for' => $botd->toIso8601String()
        ])->group(['Hirsch.name', 'note'])->select(['Hirsch.name', 'for', 'note', 'cnt' => 'count(Hirsch.name)'])->contain(['Hirsch']);

        // Order Notification
        if (isset($_COOKIE['lastOrderedName'])) {
            /** @var Order $lastOrder */
            $lastOrder = $this->Orders->find()->where([
                'for' => $botd->toIso8601String(),
                'orderedby' => $_COOKIE['lastOrderedName']
            ])->contain(['Hirsch'])->order(['for'])->first();
            if ($lastOrder) {
                $this->Flash->set('Deine heutige Bestellung: ' . $lastOrder->hirsch->name);
            }
        }

        $this->set(['orders' => $o, 'ordersGrouped' => $oG]);
        $this->set(compact('preorders'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Order id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $id = base64_decode($id);
        $order = $this->Orders->get($id);
        if ($this->Orders->delete($order)) {
            $this->Flash->success(__('The order has been deleted.'));
        } else {
            $this->Flash->error(__('The order could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'list']);
    }
}
