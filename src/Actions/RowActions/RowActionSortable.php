<?php
declare(strict_types=1);

namespace e2221\Datagrid\Actions\RowActions;

use Nette\Utils\Html;

class RowActionSortable extends RowAction
{
    protected string $class = 'btn btn-xs btn-secondary';
    protected ?string $iconClass = 'fa fa-arrows-v fa fa-arrows-alt-v';
    protected string $defaultClass = 'handle-sort';

    public function render($row, $primary, ?string $itemDetailId=null): ?Html
    {
        $button = parent::render($row, $primary);
        if(is_null($button))
            return null;
        $button->setName('a');
        return $button;
    }

}