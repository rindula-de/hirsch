<?php
/**
 * @var AppView $this
 * @var Paypalme $paypalme
 */

use App\Model\Entity\Paypalme;
use App\View\AppView;

?>
<div class="row">
    <div class="column-responsive column-80">
        <div class="paypalmes form content">
            <?= $this->Form->create($paypalme) ?>
            <?= $this->Form->control('link', ['placeholder' => 'https://paypal.me/<name>/', 'label' => 'PaypalMe Name']); ?>
            <?= $this->Form->control('name', ['placeholder' => 'Anzeigename', 'label' => 'Anzeigename']); ?>
            <?= $this->Form->control('email', ['placeholder' => 'E-Mail', 'label' => 'E-Mail (für die Bestellzusammenfassung)']); ?>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
