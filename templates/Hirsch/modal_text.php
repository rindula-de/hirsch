<?php
/**
 * @var \App\View\AppView $this
 * @var Cake\I18n\Time $lastShowed
 * @var \Cake\ORM\Query|\App\Model\Entity\Holiday|null $holiday
 */

use Cake\I18n\Date;
use Cake\I18n\Time;

if ($lastShowed->diffInMinutes(new Time(), true) >= 1):
    $this->request->getSession()->write('lastShowed', new Time());
    if ($holiday && (new Time())->between($holiday->start, $holiday->end, true)): ?>
        Aktuell sind Betriebsferien beim Hirsch! Nach dem <?= $holiday->end ?> können wieder Bestellungen aufgenommen werden!
    <?php elseif ((new Date())->dayOfWeek == 1): ?>
        Montags ist Ruhetag. Es gibt heute keine Bestellungen, du kannst jedoch schon für ein anderes Mal vorbestellen!
    <?php endif;
endif;
