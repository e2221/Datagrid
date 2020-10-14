<?php
declare(strict_types=1);

namespace e2221\Datagrid\Actions\RowActions;

use Nette\Utils\Html;

class RowActionDraggable extends RowAction
{
    protected string $class = 'btn btn-xs btn-outline-secondary';
    protected ?string $iconClass = 'fas fa-expand-arrows-alt';
    protected string $defaultClass = 'handle-drag';

    public function render($row, $primary, ?string $itemDetailId=null): ?Html
    {
        $button = parent::render($row, $primary);
        if(is_null($button))
            return null;
        $button->setName('a');
        return $button;
    }

}