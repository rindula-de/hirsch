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
