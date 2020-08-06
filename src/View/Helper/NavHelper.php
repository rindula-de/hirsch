<?php


namespace App\View\Helper;


use Cake\View\Helper;

class NavHelper extends Helper
{
    public $name = 'Nav';

    public $helpers = ['Html', 'Url'];

    private $navItems = array(
        [
            'title' => 'Übersicht',
            'url' => ['_name' => 'karte']
        ],
        [
            'title' => 'Bestellungen',
            'url' => ['_name' => 'bestellungen']
        ],
        [
            'title' => 'Bezahlen',
            'url' => ['_name' => 'bezahlen']
        ],
    );

    public function main()
    {
        return '<nav id="navbar" class="navbar">' . $this->nav($this->navItems) . '<span>Bestellungen am selben Tag bis 10:55 Uhr möglich</span><a href="javascript:void(0);" class="icon" onclick="openSideMenu()"> <i class="material-icons">menu</i> </a></nav>';
    }

    private function nav(array $items)
    {
        $content = '';

        foreach ($items as $item) {
            $class = array();

            if ($this->isActive($item)) {
                $class[] = 'active';
            }

            $url = $this->getUrl($item);

            $content .= $this->Html->link($item['title'], $url, [
                    'escape' => false,
                    'class' => join(' ', $class)
            ]);
        }

        return $content;
    }

    private function isActive($item)
    {
        $url = str_replace([" "], ["%20"], $this->Url->build($this->getUrl($item)));
        if ($this->getView()->getRequest()->getRequestTarget() == $url) {
            return true;
        }
        return false;
    }

    private function getUrl($item)
    {
        $url = false;
        if (!empty($item['url'])) {
            $url = $item['url'];
        }

        return $url;
    }
}
