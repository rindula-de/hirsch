<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Holiday;
use App\Model\Entity\Order;
use App\Model\Table\OrdersTable;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\Event\EventInterface;
use Cake\Http\Cookie\Cookie;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Response;
use Cake\I18n\Date;
use Cake\I18n\FrozenTime;
use Cake\I18n\Time;
use Cake\Utility\Security;
use http\Exception\BadUrlException;

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
        if ($mealSlug == null) {
            throw new BadUrlException();
        }
        $now = new Time();
        /** @var OrdersTable $orders */
        $orders = $this->getTableLocator()->get('Orders');
        $data = $this->request->getData();
        $data['for'] = new Date('+' . $future . ' days');
        $order = $orders->newEntity($data);
        $meal = $this->Orders->Hirsch->findBySlug($mealSlug)->first();

        $extended = Cache::read('settings.extended', 'extended') ?? false;

        $holidaysTable = $this->getTableLocator()->get('holidays');

        /** @var Holiday $holiday */
        $holiday = $holidaysTable->find()->where(['end >=' => new Date()])->order(['start', 'end'])->first();

        $cookiedName = '';
        if (!empty($_COOKIE['lastOrderedName'])) {
            $cookiedName = Security::decrypt($_COOKIE['lastOrderedName'], 'ordererNameDecryptionKeyLongVersion');
        }

        $this->set(compact('meal', 'order', 'cookiedName'));

        if ($this->request->is('post')) {
            if (!empty($data)) {
                if (empty($data['orderedby'])) {
                    throw new BadRequestException();
                }
                $this->setResponse($this->getResponse()->withCookie(new Cookie('lastOrderedName', Security::encrypt($data['orderedby'], 'ordererNameDecryptionKeyLongVersion'), FrozenTime::now()->modify('+1 years'))));
                if ($orders->save($order)) {
                    $this->Flash->success('Bestellung aufgegeben, Zahlung ausstehend');

                    return $this->redirect(['_name' => 'bezahlen']);
                } else {
                    $this->Flash->error('Konnte Bestellung nicht aufgeben! Bitte versuche es erneut!');

                    return;
                }
            }
        }

        if (!Configure::read('debug') && ((($future == 0 && ($now->hour > 10 || ($now->hour == 10 && $now->minute > 55)) && !$extended) || $future < 0) || (($future == 0 && ($now->hour > 11 || ($now->hour == 11 && $now->minute > 20)) && $extended) || $future < 0))) {
            $this->Flash->error('Die Zeit zum bestellen ist abgelaufen!');

            return $this->redirect(['_name' => 'karte']);
        } elseif (!Configure::read('debug') && $data['for']->isWeekend()) {
            $this->Flash->error('Am Wochenende wird dir keiner deine Bestellung abholen! Bitte wähle einen anderen Tag aus!');

            return $this->redirect(['_name' => 'karte']);
        } elseif (!Configure::read('debug') && $holiday && $data['for']->between($holiday->start, $holiday->end)) {
            $this->Flash->error('An diesem Tag sind Betriebsferien. Bitte wähle einen anderen Tag!');

            return $this->redirect(['_name' => 'karte']);
        }

        return;
    }

    public function list()
    {
        $botd = new Date();
        $o = $this->Orders->find()->where([
            'for' => $botd->toIso8601String(),
        ])->orderAsc('Orders.created')->contain(['Hirsch']);

        $preorders = $this->Orders->find()->where([
            'for >' => $botd->toIso8601String(),
        ])->orderAsc('Orders.created')->group(['Hirsch.name', 'note', 'for'])->select(['Hirsch.name', 'for', 'note', 'cnt' => 'count(Hirsch.name)'])->contain(['Hirsch']);

        $oG = $this->Orders->find()->where([
            'for' => $botd->toIso8601String(),
        ])->orderAsc('Orders.created')->group(['Hirsch.name', 'note'])->select(['Hirsch.name', 'for', 'note', 'cnt' => 'count(Hirsch.name)'])->contain(['Hirsch']);

        // Order Notification
        if (isset($_COOKIE['lastOrderedName'])) {
            /** @var Order $lastOrder */
            $lastOrder = $this->Orders->find()->where([
                'for' => $botd->toIso8601String(),
                'orderedby' => Security::decrypt($_COOKIE['lastOrderedName'], 'ordererNameDecryptionKeyLongVersion') ?? '',
            ])->contain(['Hirsch'])->order(['for'])->first();
            if ($lastOrder) {
                $this->Flash->set('Deine heutige Bestellung: ' . $lastOrder->hirsch->name . (!empty($lastOrder->note) ? " ({$lastOrder->note})" : ''));
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

    public function extend()
    {
        if ((new Time())->hour >= 12 && !Configure::read('debug')) {
            $this->Flash->error('Die Bestellungen sind bereits aufgegeben!');

            return $this->redirect($this->referer());
        }

        Cache::write('settings.extended', true, 'extended');
        $this->Flash->success('Die Bestellzeit wurde auf 11:20 Uhr verlängert');

        return $this->redirect($this->referer());
    }
}
