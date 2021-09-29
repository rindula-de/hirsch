<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\I18n\Date;
use Cake\ORM\Entity;

/**
 * Paypalme Entity
 *
 * @property int $id
 * @property string $link
 * @property string $name
 * @property string|null $email
 * @property Date|null $bar
 * @property boolean $onlybar
 * @property Paypalme $activePayer
 *
 * @property Payhistory $Payhistory
 */
class Paypalme extends Entity
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
        'link' => true,
        'name' => true,
        'email' => true,
        'bar' => true,
    ];

    protected $_virtual = [
        'onlybar',
    ];

    public function _getOnlybar() {
        return (isset($this->bar))?new Date($this->bar) >= new Date():false;
    }
}
