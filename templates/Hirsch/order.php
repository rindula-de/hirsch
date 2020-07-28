<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Order $order
 * @var string $meal
 * @var array $paypalmes
 */
?>

<?= $this->Form->create($order) ?>

<h2><?= $meal ?></h2>
<p>Bestellung für: <?= $order->for ?></p>
<?= $this->Form->control('name', ['value' => $meal, 'type' => 'hidden']) ?>
<?= $this->Form->control('note') ?>
<?= $this->Form->control('paypalme', ['options' => $paypalmes]) ?>

<?= $this->Form->submit('Verbindlich Bestellen', ['class' => 'btn waves-purple waves-effect']) ?>

<?= $this->Form->end() ?>
