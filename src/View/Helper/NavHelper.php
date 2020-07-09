<?php


namespace App\View\Helper;


use Cake\View\Helper;

class NavHelper extends Helper
{
    public $name = 'Nav';

    public $helpers = ['Html', 'Url'];

    private $navItems = array(
        [
            'title' => 'Bestellen',
            'url' => ['controller' => 'hirsch', 'action' => 'index']
        ],
        [
            'title' => 'Bestellungen',
            'url' => ['controller' => 'hirsch', 'action' => 'orders']
        ],
    );

    public function main()
    {
        return '<nav id="navbar"><div class="nav-wrapper"><ul id="nav-mobile" class="left hide-on-med-and-down">' . $this->nav($this->navItems) . '</ul></div></nav>';
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

            $content .= '<li class="' . implode(' ', $class) . '">' . $this->Html->link($item['title'], $url, array(
                    'escape' => false
                )) . '</li>';
        }

        return $content;
    }

    private function isActive($item)
    {
        $url = $this->Url->build($this->getUrl($item));
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
