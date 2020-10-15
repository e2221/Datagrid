<?php
declare(strict_types=1);

namespace e2221\Datagrid\Document\HeadRow;

use e2221\HtmElement\BaseElement;
use Nette\Utils\Html;

class HeadColumnTemplate extends BaseElement
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
     * @return $this
     */
    public function setAttributesCallback(callable $callback): HeadColumnTemplate
    {
        $this->attributesCallback = $callback;
        return $this;
    }

    public function render(string $columnName=null): ?Html
    {
        if(!is_null($columnName))
            $this->class = ' grid-col-' . $columnName;
        if(is_callable($this->attributesCallback))
        {
            $attrs = call_user_func($this->attributesCallback, $columnName);
            if(is_array($attrs)){
                foreach ($attrs as $attr => $value) {
                    $this->attributes[$attr] = $value;
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
     * @param int|null $topPosition
     * @return $this
     */
    public function setStickyTop(bool $sticky=true, ?int $topPosition=null): self
    {
        $this->stickyTop = $sticky;
        if($this->stickyTop === true && is_numeric($topPosition))
            $this->setAttribute('style', sprintf('top:%spx', $topPosition));
        return $this;
    }
}