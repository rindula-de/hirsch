<?php
/**
 * @var AppView $this
 * @var array $displayData Tagesessensdaten
 * @var array $htg Hirsch to Go Gerichte
 */

use App\View\AppView;
use Cake\I18n\Date;

?>

<template id="foodcard_template">
<!--    $isRuhetag = strpos(strtolower($gericht), 'ruhetag') !== false;-->
    <div class='foodcard'>
        <h2 data-role="title"></h2>
        <span data-role="gericht"></span>
        <div data-role="actionbar" class='actionbar'>
            <a data-role="order" class="btn"></a>
<!--            $html .= $this->Html->link(($date->isFuture()) ? "Vorbestellen" : 'Bestellen', ['_name' => 'bestellen', $date->diffInDays(new Date()), 'tagesessen'], ['class' => 'btn']);-->
        </div>
    </div>
</template>

<button class="accordion">Hirsch to Go</button>
<div class="panel">
    <?php
    foreach ($htg as $gericht) {
        echo $this->Fooddisplay->displayHtg($gericht);
    }
    ?>
</div>
<button class="accordion">Tagesessen</button>
<div class="panel" id="tagesessen_panel">
    <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
</div>


<script>
    let holidays = JSON.parse('<?= json_encode($holidays) ?>');
    let template = document.getElementById("foodcard_template");
    let tagesessen_panel = document.getElementById('tagesessen_panel');

    $.ajax({
        url: "/hirsch/get_tagesessen",
        context: document.body,
        dataType: 'json',
        success: function (result) {
            tagesessen_panel.innerHTML = "";
            if (result) {
                for (let i = 0; i < result.length; i++) {
                    let clone = template.content.cloneNode(true);
                    clone.querySelector("[data-role=title]").innerHTML = "Tagesessen für den " + (new Date(result[i]['date'])).toLocaleDateString();
                    clone.querySelector("[data-role=gericht]").innerHTML = result[i]['gericht'];
                    let btn = clone.querySelector("[data-role=order]");
                    btn.innerHTML = (i === 0) ? "Bestellen":"Vorbestellen";
                    btn.setAttribute("href", "<?= $this->Url->build(['_name' => 'bestellen', '_i_', 'tagesessen']) ?>");
                    btn.setAttribute("href", btn.getAttribute("href").replace("_i_", i));

                    tagesessen_panel.append(clone);
                }
            } else {
                let clone = template.content.cloneNode(true);
                clone.querySelector("[data-role=title]").innerHTML = "Tagesessen konnten nicht geladen werden";
                clone.querySelector("[data-role=gericht]").innerHTML = "Die Liste der Tagesessen konnten nicht geladen werden! Wenn du trotzdem bestellen möchtest, findest du die Karte unter <a href='https://www.hirsch-restaurant.de/speisekarte/' target='speisekarte'>https://www.hirsch-restaurant.de/speisekarte/</a>";
                clone.querySelector("[data-role=order]").innerHTML = "Bestellen";
                clone.querySelector("[data-role=order]").setAttribute("href", "<?= $this->Url->build(['_name' => 'bestellen', 0, 'tagesessen']) ?>");

                tagesessen_panel.append(clone);
            }
            tagesessen_panel.style.maxHeight = tagesessen_panel.scrollHeight + "px";
        }
    });
</script>
