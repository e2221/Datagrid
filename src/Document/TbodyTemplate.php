<?php
declare(strict_types=1);

namespace e2221\Datagrid\Document;

use e2221\Datagrid\Datagrid;
use e2221\HtmElement\BaseElement;

class TbodyTemplate extends BaseElement
{
    protected bool $sortable=false;
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
                $this->setDataAttribute('itemIdParam', $this->datagrid->getUniqueId() . '-itemId');
                $this->setDataAttribute('prevIdParam', $this->datagrid->getUniqueId() . '-prevId');
                $this->setDataAttribute('nextIdParam', $this->datagrid->getUniqueId() . '-nextId');
            };
        }
        return $this;
    }
}