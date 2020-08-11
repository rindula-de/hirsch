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
            <?= $this->Form->control('link', ['placeholder' => 'https://paypal.me/<name>/', 'label' => 'PaypalMe Name']); ?>
            <?= $this->Form->control('name', ['placeholder' => 'Anzeigename', 'label' => 'Anzeigename']); ?>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
