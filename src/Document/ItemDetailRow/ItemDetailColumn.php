<?php
declare(strict_types=1);

namespace e2221\Datagrid\Document;

use Nette\Utils\Html;

class ItemDetailColumn extends \e2221\HtmElement\BaseElement
{

    /** @var callable|null */
    protected $setAttributesCallback;

    public function setAttributesCallback(callable $callback): void
    {
        $this->setAttributesCallback = $callback;
    }

    public function render(?int $colspan=null, $primary=null, $row=null): ?Html
    {
        $this->attributes['colspan'] = $colspan;
        if(is_callable($this->setAttributesCallback))
            $this->attributes = call_user_func($this->setAttributesCallback, $primary, $row);
        return parent::render();
    }

    public function renderStartTag(?int $colspan=null, $primary=null, $row=null): ?string
    {
        $render = $this->render($colspan, $primary, $row);
        if($render instanceof Html)
        {
            return $render->startTag();
        }
        return null;
    }

}