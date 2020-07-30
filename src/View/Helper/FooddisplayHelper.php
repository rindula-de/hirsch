<?php
declare(strict_types=1);

namespace App\View\Helper;

use App\Model\Entity\Hirsch;
use Cake\I18n\Date;
use Cake\View\Helper;

/**
 * Fooddisplay helper
 * @property Helper\HtmlHelper Html
 * @property Helper\UrlHelper Url
 * @property Helper\TextHelper Text
 */
class FooddisplayHelper extends Helper
{
    public $name = 'Fooddisplay';

    public $helpers = ['Html', 'Url', 'Text'];
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * @param Date $date
     * @param string $gericht
     * @return string
     */
    public function displayDaily($date, $gericht)
    {
        $isRuhetag = strpos(strtolower($gericht), 'ruhetag') !== false;
        $html = "";
        $dateNice = $date->nice();
        $html .= "<div class='foodcard'>";
        $html .= "<h2>Tagesessen am $dateNice</h2>";
        $html .= "<span>$gericht</span>";
        if (!$isRuhetag) {
            $html .= "<div class='actionbar'>";
            if ($date->isFuture()) {
                $html .= $this->Html->link("Vorbestellen", ['controller' => 'hirsch', 'action' => 'order', $date->diffInDays(new Date()), 'tagesessen'], ['class' => 'btn']);
            } else {
                $html .= $this->Html->link("Bestellen", ['controller' => 'hirsch', 'action' => 'order', $date->diffInDays(new Date()), 'tagesessen'], ['class' => 'btn']);
            }
            $html .= "</div>";
        }
        $html .= "</div>";
        return $html;
    }

    /**
     * @param Hirsch $gericht
     * @return string
     */
    public function displayHtg($gericht)
    {
        $isRuhetag = strpos(strtolower($gericht->name), 'ruhetag') !== false;
        $date = new Date();
        $html = "";
        $dateNice = $date->nice();
        $html .= "<div class='foodcard'>";
        $html .= "<h2>$gericht->name</h2>";
        if (!$isRuhetag) {
            $html .= "<div class='actionbar'>";
            $html .= $this->Html->link("Bestellen", ['controller' => 'hirsch', 'action' => 'order', $date->diffInDays(new Date()), $gericht->slug], ['class' => 'btn']);
            $html .= "</div>";
        }
        $html .= "</div>";
        return $html;
    }

}
