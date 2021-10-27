<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var AppView $this
 */

use App\View\AppView;
use Cake\Core\Configure;
use Cake\I18n\Time;

$cakeDescription = 'Hirsch Bestellungen';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= $cakeDescription ?>:
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <meta http-equiv="cache-control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="expires" content="0" />

    <link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">
    <link as="image" href="/img/essen.jpg" rel="preload">
    <link rel="manifest" href="/manifest.json">

    <?= $this->Html->css(['style.css']) ?>
    <?= $this->Html->css('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css') ?>

    <?= (Configure::read('debug', true)?$this->Html->script(['main.js']):$this->Html->script(['main.min.js'])) ?>
    <?= $this->Html->script('https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js') ?>
    <?= $this->Html->script('https://cdn.jsdelivr.net/npm/flatpickr') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
<!--
    Letztes Update: <?= (new Time(trim(Cake\Core\Configure::read("App.last_update"))))->nice().PHP_EOL ?>
-->
<?= $this->Nav->main() ?>
<main class="main">
    <div class="content">
        <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
    </div>
    <span id="order-until"></span>
</main>
<div id="preorderModal" class="modal">
    <div class="modal-content">
        <span class="close material-icons">close</span>
        <h2>Vorbestellen</h2>
        <input readonly type="text" class="datepicker flatpickr flatpickr-input" placeholder="Datum auswählen">
        <br>
        <br>
        <a href="#" id="preorderLink" class="btn">Datum wählen</a>
        <span style="display: none" id="preorderSlug"></span>
    </div>
</div>
<div id="informationModal" class="modal">
    <div class="modal-content">
        <span class="close material-icons">close</span>
        <p id="informationModalText">Lorem Schwippsum</p>
    </div>
</div>
<div id="orderedModal" class="modal">
    <div class="modal-content">
        <span class="close material-icons">close</span>
        <h2 id="orderedModalTitle">Bestellung</h2>
        <p id="orderedModalText">Bestellung</p>
    </div>
</div>
<?= (Configure::read('debug', true)?$this->Html->script('pageEnd.js'):$this->Html->script('pageEnd.min.js')) ?>
</body>
</html>
