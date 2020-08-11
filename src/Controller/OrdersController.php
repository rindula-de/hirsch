<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Holiday;
use App\Model\Entity\Order;
use App\Model\Table\OrdersTable;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Datasource\ResultSetInterface;
use Cake\Http\Response;
use Cake\I18n\Date;
use Cake\I18n\Time;
use Cake\Utility\Security;

/**
 * Orders Controller
 *
 * @property OrdersTable $Orders
 * @method Order[]|ResultSetInterface paginate($object = null, array $settings = [])
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

        $holidaysTable = $this->getTableLocator()->get('holidays');

        /** @var Holiday $holiday */
        $holiday = $holidaysTable->find()->where(['end >=' => new Date()])->order(['start', 'end'])->first();

        $cookiedName = '';
        if (!empty($_COOKIE['lastOrderedName'])) {
            $cookiedName = Security::decrypt($_COOKIE['lastOrderedName'], 'ordererNameDecryptionKeyLongVersion');
        }

        $this->set(compact('meal', 'order', 'cookiedName'));

        if (!Configure::read('debug') && (($future == 0 && ($now->hour > 10 || ($now->hour == 10 && $now->minute > 55))) || $future < 0)) {
            $this->Flash->error("Die Zeit zum bestellen ist abgelaufen!");
            return $this->redirect(['_name' => 'karte']);
        } elseif ($data['for']->isWeekend()) {
            $this->Flash->error("Am Wochenende wird dir keiner deine Bestellung abholen! Bitte wähle einen anderen tag aus!");
            return $this->redirect(['_name' => 'karte']);
        } elseif ($holiday && $data['for']->between($holiday->start, $holiday->end)) {
            $this->Flash->error("An diesem Tag sind Betriebsferien. Bitte wähle einen anderen Tag!");
            return $this->redirect(['_name' => 'karte']);
        }

        if ($this->request->is('post')) {
            if (!empty($data)) {
                setcookie('lastOrderedName', Security::encrypt($data['orderedby'], 'ordererNameDecryptionKeyLongVersion'), time() + (60 * 60 * 24 * 30), '/');
                if ($orders->save($order)) {
                    $this->Flash->success("Bestellung aufgegeben, Zahlung ausstehend");
                    return $this->redirect(['controller' => 'paypalmes', 'action' => 'index']);
                } else {
                    $this->Flash->error("Konnte Bestellung nicht aufgeben! Bitte versuche es erneut!");
                    return;
                }
            }
            return;
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
                'orderedby' => Security::decrypt($_COOKIE['lastOrderedName'], 'ordererNameDecryptionKeyLongVersion')
            ])->contain(['Hirsch'])->order(['for'])->first();
            if ($lastOrder) {
                $this->Flash->set('Deine heutige Bestellung: ' . $lastOrder->hirsch->name . ((!empty($lastOrder->note)) ? " ({$lastOrder->note})" : ""));
            }
        }

        $this->set(['orders' => $o, 'ordersGrouped' => $oG]);
        $this->set(compact('preorders'));
    }


    /**
     * Delete method
     *
     * @param string|null $id Order id.
     * @return Response|null|void Redirects to index.
     * @throws RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        if ((new Time())->hour >= 11 && !Configure::read('debug')) {
            $this->Flash->error(__('It\'s too late to revoke your order!'));
            return $this->redirect(['action' => 'list']);
        }
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
