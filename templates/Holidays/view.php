<?php
/**
 * @var AppView $this
 * @var Holiday $holiday
 */

use App\Model\Entity\Holiday;
use App\View\AppView;

?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Holiday'), ['action' => 'edit', $holiday->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Holiday'), ['action' => 'delete', $holiday->id], ['confirm' => __('Are you sure you want to delete # {0}?', $holiday->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Holidays'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Holiday'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="holidays view content">
            <h3><?= h($holiday->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($holiday->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Start') ?></th>
                    <td><?= h($holiday->start) ?></td>
                </tr>
                <tr>
                    <th><?= __('End') ?></th>
                    <td><?= h($holiday->end) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
