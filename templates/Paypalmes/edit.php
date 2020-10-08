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
            <?php
            echo $this->Form->control('link');
            echo $this->Form->control('name');
            echo $this->Form->control('email');
            ?>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
