<?php
declare(strict_types=1);


namespace e2221\Datagrid\Actions\RowActions;


use Nette\Utils\Html;

class RowActionSortable extends RowAction
{
    protected string $class = 'btn btn-xs btn-secondary';

    protected string $defaultClass = 'handle-sort';

    public function render($row, $primary, ?string $itemDetailId=null): ?Html
    {
        $this->setIconClass('fa fa-arrows-v fa fa-arrows-alt-v');
        return parent::render($row, $primary, $itemDetailId);
    }
}