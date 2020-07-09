<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Order[] $orders
 */

foreach ($orders as $order) {
    echo $this->Materialize->basicCard($order->name, $order->note);
}
