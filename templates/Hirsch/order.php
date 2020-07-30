<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Order $order
 * @var \App\Model\Entity\Hirsch $meal
 * @var array $paypalmes
 */
?>

<?= $this->Form->create($order) ?>

<h2><?= $meal->name ?></h2>
<p>Bestellung für: <?= $order->for ?></p>
<?= $this->Form->control('name', ['value' => $meal->slug, 'type' => 'hidden']) ?>
<?= $this->Form->control('note', ['autocomplete' => 'off', 'placeholder' => 'Keine', 'label' => 'Sonderwünsche']) ?>

<?= $this->Form->submit('Verbindlich Bestellen', ['class' => 'btn waves-purple waves-effect']) ?>

<?= $this->Form->end() ?>
