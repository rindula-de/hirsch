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
            $html .= $this->Html->link("Bestellen", ['_name' => 'bestellen', $date->diffInDays(new Date()), $gericht->slug], ['class' => 'btn']);
            $html .= "&nbsp;" . $this->Html->link("Vorbestellen", ['_name' => 'bestellen', $date->diffInDays(new Date('+1 days')), $gericht->slug], ['class' => 'btn preorderBtn', 'data-slug' => $gericht->slug]);
            $html .= "</div>";
        }
        $html .= "</div>";
        return $html;
    }

}
