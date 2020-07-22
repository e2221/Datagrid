<?php
declare(strict_types=1);

namespace e2221\Datagrid\Document\HeadFilterRow;

use e2221\HtmElement\BaseElement;
use Nette\Utils\Html;

class HeadFilterColumnTemplate extends BaseElement
{
    protected ?string $elName = 'th';

    public string $class = '';

    /** @var bool set column as sticky top */
    public bool $stickyTop = false;

    /** @var null|callable  */
    public $attributesCallback=null;

    /**
     * Set attributes callback
     * @param callable $callback
     * @return HeadFilterColumnTemplate
     */
    public function setAttributesCallback(callable $callback): HeadFilterColumnTemplate
    {
        $this->attributesCallback = $callback;
        return $this;
    }

    public function render(string $columnName=null): ?Html
    {
        if(!is_null($columnName))
            $this->class .= ' grid-col-' . $columnName;
        if(is_callable($this->attributesCallback))
        {
            $attrs = call_user_func($this->attributesCallback, $columnName);
            if(is_array($attrs)){
                foreach ($attrs as $attr => $value) {
                    $this->attributes[$attrs] = $value;
                }
            }
        }
        if($this->stickyTop)
            $this->class .= ' column-sticky';
        return parent::render();
    }

    public function renderStartTag(string $columnName=null): ?string
    {
        $render = $this->render($columnName);
        if($render instanceof Html)
        {
            return $render->startTag();
        }
        return null;
    }

    /**
     * Sets column sticky top
     * @param bool $sticky
     * @return HeadFilterColumnTemplate
     */
    public function setStickyTop(bool $sticky=true): HeadFilterColumnTemplate
    {
        $this->stickyTop = $sticky;
        return $this;
    }
}