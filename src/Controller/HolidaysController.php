<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\Holiday;
use App\Model\Table\HolidaysTable;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Response;

/**
 * Holidays Controller
 *
 * @property HolidaysTable $Holidays
 * @method Holiday[]|ResultSetInterface paginate($object = null, array $settings = [])
 */
class HolidaysController extends AppController
{
    /**
     * Index method
     *
     * @return Response|null|void Renders view
     */
    public function index()
    {
        $holidays = $this->paginate($this->Holidays);

        $this->set(compact('holidays'));
    }

    /**
     * View method
     *
     * @param string|null $id Holiday id.
     * @return Response|null|void Renders view
     * @throws RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $holiday = $this->Holidays->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('holiday'));
    }

    /**
     * Add method
     *
     * @return Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $holiday = $this->Holidays->newEmptyEntity();
        if ($this->request->is('post')) {
            $holiday = $this->Holidays->patchEntity($holiday, $this->request->getData());
            if ($this->Holidays->save($holiday)) {
                $this->Flash->success(__('The holiday has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The holiday could not be saved. Please, try again.'));
        }
        $this->set(compact('holiday'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Holiday id.
     * @return Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $holiday = $this->Holidays->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $holiday = $this->Holidays->patchEntity($holiday, $this->request->getData());
            if ($this->Holidays->save($holiday)) {
                $this->Flash->success(__('The holiday has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The holiday could not be saved. Please, try again.'));
        }
        $this->set(compact('holiday'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Holiday id.
     * @return Response|null|void Redirects to index.
     * @throws RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $holiday = $this->Holidays->get($id);
        if ($this->Holidays->delete($holiday)) {
            $this->Flash->success(__('The holiday has been deleted.'));
        } else {
            $this->Flash->error(__('The holiday could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
