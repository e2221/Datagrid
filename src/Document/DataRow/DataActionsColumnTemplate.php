<?php
declare(strict_types=1);

namespace e2221\Datagrid\Document\DataRow;

use e2221\Datagrid\Actions\RowActions\RowActionCancel;
use e2221\Datagrid\Actions\RowActions\RowActionEdit;
use e2221\Datagrid\Actions\RowActions\RowActionItemDetail;
use e2221\Datagrid\Actions\RowActions\RowActionSave;
use e2221\Datagrid\Actions\RowActions\RowActionSortable;
use e2221\Datagrid\Datagrid;
use e2221\HtmElement\BaseElement;

class DataActionsColumnTemplate extends BaseElement
{
    protected ?string $elName = 'td';
    public array $attributes = ['class' => 'grid-col-actions'];

    private ?Datagrid $datagrid;
    protected RowActionEdit $rowActionEdit;
    protected RowActionCancel $rowActionCancel;
    protected ?RowActionItemDetail $rowActionItemDetail=null;
    protected RowActionSave $rowActionSave;
    protected ?RowActionSortable $rowActionSort=null;

    protected bool $sortable=false;

    public function __construct(Datagrid $datagrid, ?string $elName = null, ?array $attributes = null, ?string $textContent = null)
    {
        $this->datagrid = $datagrid;
        $this->rowActionEdit = new RowActionEdit('__edit', 'Edit', $this->datagrid);
        $this->rowActionCancel = new RowActionCancel('__cancel', 'Cancel', $this->datagrid);
        $this->rowActionSave = new RowActionSave('__save', 'Save');
        parent::__construct($elName, $attributes, $textContent);
    }

    /**
     * Set grid sortable - print sort button
     * @param bool $sortable
     * @return $this
     */
    public function setSortable(bool $sortable=true): self
    {
        $this->sortable = $sortable;
        $this->rowActionSort = new RowActionSortable('__sortable', 'Sort');
        return $this;
    }

    /**
     * Set item detail
     * @param string $name
     * @param string $title
     * @return RowActionItemDetail
     */
    public function setRowActionItemDetail(string $name='__rowItemDetail', $title='Show detail'): RowActionItemDetail
    {
        return $this->rowActionItemDetail = new RowActionItemDetail($name, $title);
    }

    /**
     * @return RowActionEdit
     */
    public function getRowActionEdit(): RowActionEdit
    {
        return $this->rowActionEdit;
    }

    /**
     * @return RowActionCancel
     */
    public function getRowActionCancel(): RowActionCancel
    {
        return $this->rowActionCancel;
    }

    /**
     * @return RowActionItemDetail|null
     */
    public function getRowActionItemDetail(): ?RowActionItemDetail
    {
        return $this->rowActionItemDetail;
    }

    /**
     * @return RowActionSave
     */
    public function getRowActionSave(): RowActionSave
    {
        return $this->rowActionSave;
    }

    /**
     * Get sortable row button
     * @return RowActionSortable|null
     */
    public function getRowActionSort(): ?RowActionSortable
    {
        return $this->rowActionSort;
    }

    /**
     * is row sortable
     * @return bool
     */
    public function isRowSortable(): bool
    {
        return $this->sortable;
    }

}