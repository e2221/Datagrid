<?php
declare(strict_types=1);

namespace e2221\Datagrid\Document;

use e2221\Datagrid\Datagrid;
use e2221\HtmElement\BaseElement;

class TbodyTemplate extends BaseElement
{
    protected bool $sortable=false;
    protected bool $connectable=false;
    protected bool $droppable=false;
    protected bool $draggable=false;
    protected ?string $elName = 'tbody';
    private Datagrid $datagrid;

    public function __construct(Datagrid $datagrid, ?string $elName = null, ?array $attributes = null, ?string $textContent = null)
    {
        parent::__construct($elName, $attributes, $textContent);
        $this->datagrid = $datagrid;
    }

    /**
     * Is sortable?
     * @return bool
     */
    public function isSortable(): bool
    {
        return $this->sortable;
    }

    /**
     * Set rows droppable
     * @param bool $droppable
     * @param string $effectClass
     * @param string $scope
     * @return $this
     */
    public function setDroppable(bool $droppable, string $effectClass=''): self
    {
        $this->droppable = $droppable;
        if($this->droppable)
        {
            $this->datagrid->onAnchor[] = function () use($effectClass){
                if(!empty($effectClass))
                    $this->setDataAttribute('effect', $effectClass);
                $this->setDataAttribute('drop-url', $this->datagrid->link('drop!'));
                $this->setDataAttribute('item-id-param', sprintf('%s-itemId', $this->datagrid->getUniqueId()));
                $this->setDataAttribute('item-moved-param', sprintf('%s-movedToId', $this->datagrid->getUniqueId()));
            };
        }
        return $this;
    }

    /**
     * Set rows draggable
     * @param bool $draggable
     * @param string $scope
     * @return $this
     */
    public function setDraggable(bool $draggable, string $scope='datagrid-draggable-items'): self
    {
        $this->draggable = $draggable;
        if($this->draggable)
        {
            $this->datagrid->onAnchor[] = function () use($scope){
                $this->addClass('ui-draggable');
                $this->setDataAttribute('scope', $scope);
            };
        }

        return $this;
    }

    /**
     * @param bool $connectable
     * @return $this
     */
    public function setConnectable(bool $connectable=true): self
    {
        $this->connectable = $connectable;
        if($connectable)
        {
            $this->datagrid->onAnchor[] = function (){
                $this->setDataAttribute('connect', '');
                $this->setDataAttribute('table-id', (string)$this->datagrid->getKeyId());
                $this->setDataAttribute('connect-url', $this->datagrid->link('rowsConnect!'));
                $this->setDataAttribute('item-id-param', $this->datagrid->getUniqueId() . '-itemId');
                $this->setDataAttribute('connect-table-id-param', $this->datagrid->getUniqueId() . '-tableId');
                $this->setDataAttribute('item-id-param', $this->datagrid->getUniqueId() . '-itemId');
                $this->setDataAttribute('prev-id-param', $this->datagrid->getUniqueId() . '-prevId');
                $this->setDataAttribute('next-id-param', $this->datagrid->getUniqueId() . '-nextId');
                $this->addClass('datagrid-connected-grids');
            };
        }
        return $this;
    }

    /**
     * Set sortable
     * @param bool $sortable
     * @return TbodyTemplate
     */
    public function setSortable(bool $sortable=true): self
    {
        $this->sortable = $sortable;
        if($sortable)
        {
            $this->setDataAttribute('sortable', '');
            $this->datagrid->onAnchor[] = function(){
                $this->setDataAttribute('sortable-url', $this->datagrid->link('rowsSort!'));
                $this->setDataAttribute('item-id-param', $this->datagrid->getUniqueId() . '-itemId');
                $this->setDataAttribute('prev-id-param', $this->datagrid->getUniqueId() . '-prevId');
                $this->setDataAttribute('next-id-param', $this->datagrid->getUniqueId() . '-nextId');
            };
        }
        return $this;
    }
}