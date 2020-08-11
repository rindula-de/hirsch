<?php
/**
 * @var AppView $this
 * @var Holiday[]|CollectionInterface $holidays
 */

use App\Model\Entity\Holiday;
use App\View\AppView;
use Cake\Collection\CollectionInterface;

?>
<div class="holidays index content">
    <?= $this->Html->link(__('New Holiday'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Holidays') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
            <tr>
                <th><?= $this->Paginator->sort('id') ?></th>
                <th><?= $this->Paginator->sort('start') ?></th>
                <th><?= $this->Paginator->sort('end') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($holidays as $holiday): ?>
                <tr>
                    <td><?= $this->Number->format($holiday->id) ?></td>
                    <td><?= h($holiday->start) ?></td>
                    <td><?= h($holiday->end) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $holiday->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $holiday->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $holiday->id], ['confirm' => __('Are you sure you want to delete # {0}?', $holiday->id)]) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>
