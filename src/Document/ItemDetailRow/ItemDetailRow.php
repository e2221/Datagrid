<?php
declare(strict_types=1);

namespace e2221\Datagrid\Document\ItemDetailRow;

use e2221\HtmElement\BaseElement;
use Nette\Utils\Html;

class ItemDetailRow extends BaseElement
{
    protected ItemDetailColumn $itemDetailColumn;

    protected ?string $elName = 'tr';

    public string $class = 'collapse border bg-light';

    /** @var callable|null */
    protected $setAttributesCallback;

    public function __construct(?string $elName = null, ?array $attributes = null, ?string $textContent = null)
    {
        $this->itemDetailColumn = new ItemDetailColumn();
        parent::__construct($elName, $attributes, $textContent);
    }

    public function setAttributesCallback(callable $callback): void
    {
        $this->setAttributesCallback = $callback;
    }

    public function render(?string $itemDetailId=null, $primary=null, $row=null): ?Html
    {
        if(is_callable($this->setAttributesCallback))
            $this->attributes = call_user_func($this->setAttributesCallback, $primary, $row);
        $this->attributes['id'] = $itemDetailId;
        return parent::render();
    }

    public function renderStartTag(?string $itemDetailId=null, $primary=null, $row=null): ?string
    {
        $render = $this->render($itemDetailId, $primary, $row);
        if($render instanceof Html)
        {
            return $render->startTag();
        }
        return null;
    }

    /**
     * Instance of item detail column
     * @return ItemDetailColumn
     */
    public function getItemDetailColumn(): ItemDetailColumn
    {
        return $this->itemDetailColumn;
    }



}