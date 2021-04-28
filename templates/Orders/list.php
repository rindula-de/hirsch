<?php
/**
 * @var AppView $this
 * @var Query|Order[] $orders
 * @var Query|Order[] $preorders
 * @var Query|Order[] $ordersGrouped
 * @var int $rowCount
 */

use App\Model\Entity\Order;
use App\View\AppView;
use Cake\I18n\Date;
use Cake\I18n\Time;
use Cake\ORM\Query;

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

<button class="accordion">Personen die heute bestellt haben (<?= count($orders->toArray()) ?>)</button>
<div class="panel">
    <?php foreach ($orders as $order): ?>
        <div class="displayName">
            <span><span style="cursor: pointer;" data-name="<?= h($order->orderedby) ?>" data-ordered="<?= h($order->hirsch->name) ?>" data-special="<?= h($order->note) ?>" onclick="$('#orderedModalTitle').html('Bestellung von '+$(this).attr('data-name'));$('#orderedModalText').html($(this).attr('data-ordered')+(($(this).attr('data-special')!='')?'<br><br>'+$(this).attr('data-special'):''));$('#orderedModal').addClass('active')">🍚</span> <?= $order->orderedby ?></span> <?= (isset($_COOKIE['lastOrderedName']) && $order->orderedby == \Cake\Utility\Security::decrypt($_COOKIE['lastOrderedName'], 'ordererNameDecryptionKeyLongVersion')) ? $this->Form->postLink('<i class="material-icons">delete_forever</i>', ['controller' => 'orders', 'action' => 'delete', base64_encode($order->id)], ['confirm' => 'Bist du sicher, dass du diese Bestellung löschen willst?', 'class' => 'btn', 'escape' => false, 'disabled' => (new Time())->hour >= 11]) : "" ?>
        </div>
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
