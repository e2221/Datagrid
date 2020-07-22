<?php
declare(strict_types=1);

namespace e2221\Datagrid\Document\DataRow;

use e2221\Datagrid\Actions\RowActions\RowActionCancel;
use e2221\Datagrid\Actions\RowActions\RowActionEdit;
use e2221\Datagrid\Actions\RowActions\RowActionItemDetail;
use e2221\Datagrid\Actions\RowActions\RowActionSave;
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

    public function __construct(?string $elName = null, ?array $attributes = null, ?string $textContent = null, ?Datagrid $datagrid=null)
    {
        $this->datagrid = $datagrid;
        $this->rowActionEdit = new RowActionEdit('__edit', 'Edit', $this->datagrid);
        $this->rowActionCancel = new RowActionCancel('__cancel', 'Cancel');
        $this->rowActionSave = new RowActionSave('__save', 'Save');

        parent::__construct($elName, $attributes, $textContent);
    }

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



}