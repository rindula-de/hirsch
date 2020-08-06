<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Order[] $orders
 * @var \App\Model\Entity\Order[] $preorders
 * @var \App\Model\Entity\Order[] $ordersGrouped
 * @var int $rowCount
 */

use Cake\I18n\Date;

$first = true;
$out = '';
foreach ($ordersGrouped as $order) {
    if (!$first) $out .= PHP_EOL . PHP_EOL;
    $out .= $order->cnt . "x " . $order->hirsch->name;
    if (!empty($order->note)) {
        $out .= PHP_EOL . "Sonderwunsch: " . $order->note;
    }
    $first = false;
}
?>
<h2>Heutige Bestellungen</h2>
<label>
<textarea rows='<?= count(explode("\n", $out)) ?>' readonly onclick='this.focus();this.select()'>
<?php

if ($first) echo "--- Keine Bestellungen ---";
else echo $out;
?>
</textarea>
</label>

<button class="accordion">Personen die heute bestellt haben</button>
<div class="panel">
    <?php foreach ($orders as $order): ?>
        <div class="displayName"><span><?= $order->orderedby ?></span> <?= (isset($_COOKIE['lastOrderedName']) && $order->orderedby == $_COOKIE['lastOrderedName']) ? $this->Form->postLink('<i class="material-icons">delete_forever</i>', ['controller' => 'orders', 'action' => 'delete', base64_encode($order->id)], ['confirm' => 'Bist du sicher, dass du diese Bestellung löschen willst?', 'class' => 'btn', 'escape' => false]) : "" ?></div>
    <?php endforeach; ?>
</div>

<?php if (count($preorders->toArray()) > 0): ?>
<button class="accordion">Vorbestellungen</button>
<div class="panel">
    <?php
    $first = true;
    $oldFor = new Date();
    foreach ($preorders as $order): ?>
        <?= ($order->for > $oldFor && !$first) ? "</div>" : "" ?>
        <?= ($order->for > $oldFor) ? "<div><h2>$order->for</h2>" : "" ?>
        <p><?= $order->cnt . 'x ' . $order->hirsch->name ?></p>
        <?php
        $oldFor = $order->for;
        $first = false;
    endforeach; ?>
</div>
<?php endif; ?>
