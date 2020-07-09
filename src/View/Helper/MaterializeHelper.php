<?php


namespace App\View\Helper;


use Cake\View\Helper;

class MaterializeHelper extends Helper
{
    public $name = 'Materialize';

    public $helpers = ['Html', 'Url', 'Text'];

    /**
     * @param string $title
     * @param string $body
     * @param array $links ['text' => '', 'url' => null, 'options' => []]
     * @return string
     */
    public function basicCard($title = "", $body = "", $links = [])
    {
        $html = "
    <div class=\"col s6\">
      <div class=\"card blue accent-2\">
        <div class=\"card-content white-text\">
          <span class=\"card-title\">$title</span>
          " . $this->Text->autoParagraph($body) . "
        </div>";
        if (!empty($links)):
            $html .= "<div class=\"card-action\">";
            foreach ($links as $link) {
                if (empty($link['text']) || empty($link['url'])) continue;
                if (empty($link['options'])) $link['options'] = [];
                $html .= $this->Html->link(
                    $link['text'],
                    $link['url'],
                    $link['options']);
            }
            $html .= "</div>";
        endif;
        $html .= "</div>
    </div>";
        return $html;
    }

}
