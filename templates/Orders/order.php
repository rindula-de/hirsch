<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Order $order
 * @var \App\Model\Entity\Hirsch $meal
 * @var string $cookiedName
 * @var array $paypalmes
 */
?>

<?= $this->Form->create($order) ?>

<h2><?= $meal->name ?></h2>
<p>Bestellung für: <?= $order->for ?></p>
<?= $this->Form->control('name', ['value' => $meal->slug, 'type' => 'hidden']) ?>
<?= $this->Form->control('orderedby', ['autocomplete' => 'name', 'placeholder' => 'Max Mustermann', 'label' => 'Dein Name', 'value' => $cookiedName]) ?>
<?= $this->Form->control('note', ['autocomplete' => 'off', 'placeholder' => 'Keine', 'label' => 'Sonderwünsche', 'list' => 'wishlist']) ?>

<?= $this->Form->submit('Verbindlich Bestellen', ['class' => 'btn waves-purple waves-effect']) ?>

<?= $this->Form->end() ?>

<datalist id="wishlist">
    <option value="+ Pommes"></option>
    <option value="+ Senf"></option>
    <option value="+ Mayonnaise"></option>
    <option value="+ Ketchup"></option>
    <option value="Extra Soße"></option>
</datalist>
