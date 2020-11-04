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

    <link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">

    <?= $this->Html->css(['style.css?' . round(time() / 1000)]) ?>
    <?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>
    <?= $this->Html->css('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css') ?>

    <?= $this->Html->script('https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js') ?>
    <?= $this->Html->script('https://cdn.jsdelivr.net/npm/flatpickr') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <link href="/vue-apps/bezahlen/dist/css/app.css" rel="stylesheet">
    <?= $this->fetch('script') ?>
    <script>
        var csrfToken = <?= json_encode($this->request->getAttribute('csrfToken')) ?>;
    </script>
</head>
<body>
<?= $this->Nav->main() ?>
<main class="main">
    <?= $this->Flash->render() ?>
    <div id="app">
    </div>
    <?= $this->fetch('content') ?>
</main>
<div id="preorderModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
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
        <span class="close">&times;</span>
        <p id="informationModalText">Lorem Schwippsum</p>
    </div>
</div>
<footer>
</footer>
<?= $this->Html->script('pageEnd.js?' . round(time() / 1000)) ?>
<script type="module" src="/vue-apps/<?= $layoutName ?>/dist/js/chunk-vendors.js"></script>
<script type="module" src="/vue-apps/<?= $layoutName ?>/dist/js/app.js"></script>
<script>!function () {
        var e = document, t = e.createElement("script");
        if (!("noModule" in t) && "onbeforeload" in t) {
            var n = !1;
            e.addEventListener("beforeload", function (e) {
                if (e.target === t) n = !0; else if (!e.target.hasAttribute("nomodule") || !n) return;
                e.preventDefault()
            }, !0), t.type = "module", t.src = ".", e.head.appendChild(t), t.remove()
        }
    }();</script>
<script src="/vue-apps/<?= $layoutName ?>/dist/js/chunk-vendors-legacy.js" nomodule></script>
<script src="/vue-apps/<?= $layoutName ?>/dist/js/app-legacy.js" nomodule></script>
</body>
</html>
