<?php
declare(strict_types=1);

namespace e2221\Datagrid\Document;

use Nette\Utils\Html;

class DataColumnTemplate extends \e2221\HtmElement\BaseElement
{
    protected ?string $elName = 'td';

    /** @var callable|null */
    protected $setAttributesCallback;

    public function setAttributesCallback(callable $callback): void
    {
        $this->setAttributesCallback = $callback;
    }

    public function render($colID=null, $primary=null, $row=null, $cell=null): ?Html
    {
        $this->element->attrs = [];
        $this->attributes = [];

        if(is_callable($this->setAttributesCallback))
        {
            $attrs = call_user_func($this->setAttributesCallback, $primary, $row, $cell);
            if(is_array($attrs))
            {
                foreach ($attrs as $attribute => $value) {
                    $this->attributes[$attribute] = $value;
                }
            }
        }
        $this->attributes['id'] = $colID;
        return parent::render();
    }

    /**
     * Render start tag
     * @param null $colID
     * @param null $primary
     * @param null $row
     * @param null $cell
     * @return string|null
     */
    public function renderStartTag($colID=null, $primary=null, $row=null, $cell=null): ?string
    {
        $render = $this->render($colID, $primary, $row, $cell);
        if($render instanceof Html)
        {
            return $render->startTag();
        }
        return null;
    }
}