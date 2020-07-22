<?php
declare(strict_types=1);

namespace e2221\Datagrid\Document\TitleRow;

use e2221\HtmElement\BaseElement;
use Nette\Utils\Html;

class TitleColumnTemplate extends BaseElement
{
    protected ?string $elName = 'td';

    protected BaseElement $wrapper;

    public function __construct(?string $elName=null, ?array $attributes=null, ?string $textContent=null)
    {
        parent::__construct($elName, $attributes, $textContent);
        $this->wrapper = BaseElement::getStatic('div');
        $this->wrapper->setClass('row');
    }

    public function getWrapper(): BaseElement
    {
        return $this->wrapper;
    }

    public function render(?int $colspan=null): ?Html
    {
        $this->element->addHtml($this->wrapper->render());
        if(!is_null($colspan))
            $this->attributes['colspan'] = $colspan;
        return parent::render();
    }

    public function renderStartTag(?int $colspan=null): ?string
    {
        $render = ($this->render ?? $this->render($colspan));
        if($render instanceof Html)
        {
            return $render->startTag();
        }
        return null;
    }
}