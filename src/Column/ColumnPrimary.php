<?php
declare(strict_types=1);


namespace e2221\Datagrid\Column;


use Nextras\Datagrid\Datagrid;

class ColumnPrimary extends ColumnExtended
{
    /**
     * ColumnId constructor.
     * @param string $name
     * @param string|null $label
     * @param Datagrid $grid
     */
    public function __construct(string $name, string $label, Datagrid $grid)
    {
        parent::__construct($name, $label, $grid);
        $this->setHidden();
        $grid->setRowPrimaryKey($name);
    }
}