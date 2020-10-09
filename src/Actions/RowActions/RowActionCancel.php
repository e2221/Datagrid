<?php
declare(strict_types=1);

namespace e2221\Datagrid\Actions\RowActions;

use Nette\Utils\Html;

class RowActionCancel extends RowAction
{
    protected string $class = 'btn btn-xs btn-warning';
    protected string $defaultClass = 'datagrid-cancel-button';

    public function __construct(string $name, string $title)
    {
        parent::__construct($name, $title);
    }

    public function render($row, $primary, ?string $itemDetailId=null): ?Html
    {
        $button = parent::render($row, $primary);
        if(is_null($button))
            return null;
        $button->setName('input');
        $button->type('submit');
        $button->setAttribute('name', 'edit[cancel]');
        $button->value('Cancel');
        return $button;
    }
}