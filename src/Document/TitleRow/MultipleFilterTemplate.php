<?php
declare(strict_types=1);

namespace e2221\Datagrid\Document;


use e2221\HtmElement\BaseElement;
use Nette\Utils\Html;

class MultipleFilterTemplate extends BaseElement
{
    /** @var null|Html|string */
    protected $render=null;

    protected ?string $elName = 'div';

    public ?string $class = 'col col-6';


    public function render(): ?Html
    {

        $a = parent::render();
        $this->render = $a;
        return $a;
    }

    public function renderStartTag(): ?string
    {
        $render = ($this->render ?? $this->render());
        if($render instanceof Html)
        {
            return $render->startTag();
        }
        return null;
    }

    public function renderEndTag(): ?string
    {
        $render = ($this->render ?? $this->render());
        if($render instanceof Html)
        {
            return $render->endTag();
        }
        return null;
    }

}