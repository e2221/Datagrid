<?php
declare(strict_types=1);

namespace e2221\Datagrid\Document\DataRow;

use e2221\Datagrid\Datagrid;
use e2221\HtmElement\BaseElement;
use Nette\Utils\Html;

class DataRowTemplate extends BaseElement
{
    private Datagrid $datagrid;
    protected DataColumnTemplate $dataColumnTemplate;
    protected DataActionsColumnTemplate $dataActionsColumnTemplate;
    protected bool $draggable=false;
    protected bool $droppable=false;

    protected ?string $elName = 'tr';

    /** @var callable|null */
    protected $setAttributesCallback;

    public function __construct(Datagrid $datagrid, ?string $elName = null, ?array $attributes = null, ?string $textContent = null)
    {
        $this->datagrid = $datagrid;
        $this->dataColumnTemplate = new DataColumnTemplate();
        $this->dataActionsColumnTemplate = new DataActionsColumnTemplate($datagrid);
        parent::__construct($elName, $attributes, $textContent);
    }

    /**
     * Set droppable row
     * @param bool $droppable
     * @param string $scope
     * @return $this
     */
    public function setDroppable(bool $droppable, string $scope='datagrid-draggable-items'): self
    {
        $this->droppable = $droppable;
        if($this->droppable)
        {
            $this->setDataAttribute('droppable', '');
            $this->setDataAttribute('scope', $scope);
        }
        return $this;
    }

    /**
     * Set draggable row
     * @param bool $draggable
     * @return $this
     */
    public function setDraggable(bool $draggable): self
    {
        $this->draggable = $draggable;
        if($this->draggable)
        {
            $this->setDataAttribute('draggable', '');
            $this->dataActionsColumnTemplate->setDraggable($this->draggable);
        }
        return $this;
    }

    /**
     * Set attributes callback
     * @param callable $callback
     */
    public function setAttributesCallback(callable $callback): void
    {
        $this->setAttributesCallback = $callback;
    }

    public function render($rowID=null, $primary=null, $row=null): ?Html
    {
        $this->element->attrs = [];
        $this->attributes = [];

        if(is_callable($this->setAttributesCallback))
        {
            $attrs = call_user_func($this->setAttributesCallback, $primary, $row);
            if(is_array($attrs))
            {
                foreach ($attrs as $attribute => $value) {
                    $this->attributes[$attribute] = $value;
                }
            }
        }
        $this->setAttribute('id', $rowID);
        $this->setDataAttribute('id', (string)$primary);
        if($this->draggable && is_callable($this->datagrid->getDragHelperCallback()))
            $this->setDataAttribute('helper', call_user_func($this->datagrid->getDragHelperCallback(), $row, $primary));
        return parent::render();
    }

    /**
     * Render start tag
     * @param null $rowID
     * @param null $primary
     * @param null $row
     * @return string|null
     */
    public function renderStartTag($rowID=null, $primary=null, $row=null): ?string
    {
        $render = $this->render($rowID, $primary, $row);
        if($render instanceof Html)
        {
            return $render->startTag();
        }
        return null;
    }

    /**
     * Get instance of data column template
     * @return DataColumnTemplate
     */
    public function getDataColumnTemplate(): DataColumnTemplate
    {
        return $this->dataColumnTemplate;
    }

    /**
     * Get instance of data actions column template
     * @return DataActionsColumnTemplate
     */
    public function getDataActionsColumnTemplate(): DataActionsColumnTemplate
    {
        return $this->dataActionsColumnTemplate;
    }

}