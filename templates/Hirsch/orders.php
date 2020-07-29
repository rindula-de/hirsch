<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Order[] $orders
 * @var \App\Model\Entity\Order[] $ordersGrouped
 */
echo "<h2>Heutige Bestellungen</h2>";
echo "<textarea readonly onclick='this.focus();this.select()'>";
$first = true;
foreach ($ordersGrouped as $order) {
    if (!$first) echo PHP_EOL . PHP_EOL;
    echo $order->cnt . "x " . $order->name;
    if (!empty($order->note)) {
        echo PHP_EOL . "Sonderwunsch: " . $order->note;
    }
    $first = false;
}
if ($first) echo "--- Keine Bestellungen ---";
echo "</textarea>";
