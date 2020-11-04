<?php
/**
 * @var AppView $this
 * @var Paypalme[]|CollectionInterface $paypalmes
 * @var Paypalme|null $active
 */

use App\Model\Entity\Paypalme;
use App\View\AppView;
use Cake\Collection\CollectionInterface;

?>
<script>
    var paypalmes = <?= json_encode($paypalmes) ?>;
    var payAddLink = <?= json_encode($this->Html->link('Hier', ['_name' => 'selberZahlen'])) ?>;
    var activeId = <?= json_encode($active->id) ?>;
</script>
<!--
<div class="paypalmes index content">
    <div class="info">
        Du nutzt Paypal? Jetzt einfach bezahlen!
        Wähle einfach denjenigen aus, der heute für das Essen zuständig ist!
    </div>
    <h3>Paypalierer</h3>
    <?= $this->Form->create(null, ['url' => ['action' => 'pay']]) ?>
    <?php if ($this->request->is('mobile')): ?>
        <?= $this->Form->control('tip', ['type' => 'number', 'min' => 0, 'max' => 5, 'value' => 0.5, 'step' => 0.01]) ?>
    <?php else: ?>
        <?= $this->Form->control('tip', ['type' => 'range', 'min' => 0, 'max' => 5, 'value' => 0.5, 'step' => 0.5]) ?>
    <?php endif; ?>
    <?php foreach ($paypalmes as $paypalme): ?>
        <div class="paypalmeslistitem<?= (!empty($active->id) && $active->id == $paypalme->id) ? " active" : "" ?>"
             data-database-id="<?= $paypalme->id ?>">
            <?= $this->Form->button($paypalme->name, ['name' => 'id', 'value' => base64_encode($paypalme->id)]) ?>
        </div>
    <?php endforeach; ?>
    <?= $this->Form->end() ?>
    <hr>
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
    <div>
        <p>Du willst auch in der Liste stehen? <?= $this->Html->link('Hier', ['_name' => 'selberZahlen']) ?> kannst du
            dich eintragen</p>
    </div>
</div>
-->
