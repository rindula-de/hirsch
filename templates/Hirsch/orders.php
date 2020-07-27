<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Order[] $orders
 * @var \App\Model\Entity\Order[] $ordersGrouped
 */
echo "<textarea readonly="readonly" class=\"materialize-textarea\" onclick=\"this.select()\">";
$first = true;
foreach ($ordersGrouped as $order) {
    if (!$first) echo PHP_EOL . PHP_EOL;
    echo $order->cnt . "x " . $order->name;
    if (!empty($order->note)) {
        echo PHP_EOL . "Sonderwunsch: " . $order->note;
    }
    $first = false;
}
echo "</textarea>";

foreach ($orders as $order) {
    echo $this->Materialize->basicCard($order->name, $order->note);
}
