<?php
/**
 * @var \App\View\AppView $this
 * @var array $displayData Tagesessensdaten
 * @var array $htg Hirsch to Go Gerichte
 */
?>
<button class="accordion">Hirsch to Go</button>
<div class="panel">
    <?php
    foreach ($htg as $gericht) {
        echo $this->Fooddisplay->displayHtg($gericht);
    }
    ?>
</div>
<button class="accordion">Tagesessen</button>
<div class="panel">
    <?php
    foreach ($displayData as $data) {
        echo $this->Fooddisplay->displayDaily($data['date'], $data['gericht']);
    }
    ?>
</div>
