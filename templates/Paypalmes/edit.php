<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Paypalme $paypalme
 */
?>
<div class="row">
    <div class="column-responsive column-80">
        <div class="paypalmes form content">
            <?= $this->Form->create($paypalme) ?>
            <?php
            echo $this->Form->control('link');
            echo $this->Form->control('name');
            ?>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
