<?php
/**
 * @var \App\View\AppView $this
 * @var array $displayData Tagesessensdaten
 * @var array $htg Hirsch to Go Gerichte
 */

use Cake\I18n\Time;

$i = 0;
?>
<div class="row">
    <?php
    foreach ($displayData

    as $data):
    $isRuhetag = strpos(strtolower($data['gericht']), 'ruhetag') !== false;
    ?>
    <?php if ($i == 1): ?>
</div>
<div class="row"><h2>Kommende Tagesessen</h2>
    <?php endif; ?>
    <div class='col <?= ($i < 1 || $this->request->is('mobile')) ? 's12' : 's4' ?>'>
        <div class="card <?= ($i < 1) ? "light-green" : "light-blue" ?> lighten-4">

            <div class="card-content">
                    <span
                        class="card-title <?= ($i < 1) ? "activator" : "" ?> direction-left">Tagesessen <?= $data['date']->format('d.m.Y') ?><?= ($i < 1 && !$isRuhetag) ? '<i class="material-icons right">more_vert</i>' : "" ?></span>
                <p><?= $data['gericht'] ?></p><br>
                <?php if (!$isRuhetag): ?>
                    <p class="chip"><span class="teal-text"><?= $this->Number->currency(3.5); ?></span></p>
                <?php endif; ?>

            </div>
            <?php if (!$isRuhetag): ?>
                <?php if ($i < 1): ?>
                    <div class="card-reveal">
                        <span class="card-title direction-left">Hirsch to Go<i
                                class="material-icons right">more_vert</i></span>
                        <ul class="collection">
                            <?php foreach ($htg as $h): ?>
                                <li class="collection-item"><?= ((new Time())->hour < 10 || ((new Time())->hour == 10 && (new Time())->minute <= 45)) ? $this->Form->postLink($h, ['controller' => 'hirsch', 'action' => 'order', $h], ['class' => 'waves-purple waves-effect', 'title' => $h . ' bestellen']) : h($h) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <div class="card-action">
                    <?php if ($i < 1): ?>
                        <?= ((new Time())->hour < 10 || ((new Time())->hour == 10 && (new Time())->minute <= 45)) ? $this->Form->postButton("Tagesessen bestellen", ['controller' => 'hirsch', 'action' => 'order', $i, $data['gericht']], ['class' => 'btn center-align waves-purple waves-effect']) : "Die heutigen Bestellungen sind geschlossen" ?>
                    <?php else: ?>
                        <?= $this->Form->postButton("Vorbestellen", ['controller' => 'hirsch', 'action' => 'order', $i, $data['gericht']], ['class' => 'btn center-align waves-purple waves-effect']) ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    $i++;
    endforeach;
    ?>
</div>
