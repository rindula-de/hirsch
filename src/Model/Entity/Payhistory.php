<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;

/**
 * Payhistory Entity
 *
 * @property int $id
 * @property int $paypalme_id
 * @property FrozenTime $created
 *
 * @property Paypalme $paypalme
 */
class Payhistory extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'paypalme_id' => true,
        'created' => true,
        'paypalme' => true,
    ];
}
