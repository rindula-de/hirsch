<?php
/**
 * @var AppView $this
 * @var array $displayData Tagesessensdaten
 * @var array $htg Hirsch to Go Gerichte
 */

use App\View\AppView;
use Cake\I18n\Date;

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
    if ($displayData) {
        foreach ($displayData as $data) {
            echo $this->Fooddisplay->displayDaily($data['date'], $data['gericht']);
        }
    } else {
        echo $this->Fooddisplay->displayDaily(new Date(), "Die Tagesessen konnten nicht geladen werden!", false);
    }
    ?>
</div>


<script>
    let holidays = JSON.parse('<?= json_encode($holidays) ?>')
</script>
