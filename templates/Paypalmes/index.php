<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Paypalme[]|\Cake\Collection\CollectionInterface $paypalmes
 */
?>
<div class="paypalmes index content">
    <div class="info">
        Du nutzt Paypal? Jetzt einfach bezahlen!
        Wähle einfach denjenigen aus, der heute für das Essen zuständig ist!
    </div>
    <h3>Paypalierer</h3>
        <?php foreach ($paypalmes as $paypalme): ?>
            <div class="paypalmeslistitem" data-database-id="<?= $paypalme->id ?>">
                <?= $this->Html->link($paypalme->name, $paypalme->link . '3.5') ?>
            </div>
        <?php endforeach; ?>
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
        <p>Du willst auch in der Liste stehen? <?= $this->Html->link('Hier', ['_name' => 'selberZahlen']) ?> kannst du dich eintragen</p>
    </div>
</div>
