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
            <?= $this->Html->link(__('List Holidays'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="holidays form content">
            <?= $this->Form->create($holiday) ?>
            <fieldset>
                <legend><?= __('Add Holiday') ?></legend>
                <?php
                echo $this->Form->control('start');
                echo $this->Form->control('end');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
