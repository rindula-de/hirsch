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
 * @var \App\View\AppView $this
 */

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
    <?= $this->Html->css('https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css') ?>

    <?= $this->Html->script('https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js') ?>
    <?= $this->Html->script('https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
    <?= $this->Nav->main() ?>
    <main class="main">
        <div class="content">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>
    <div id="preorderModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Vorbestellen</h2>
            <input readonly type="text" id="datepickerPreorder" placeholder="Datum auswählen">
            <br>
            <br>
            <a href="#" id="preorderLink" class="btn">Bestellen</a>
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
</body>
</html>
