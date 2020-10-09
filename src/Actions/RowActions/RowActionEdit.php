<?php
declare(strict_types=1);

namespace e2221\Datagrid\Actions\RowActions;

use e2221\Datagrid\Datagrid;
use Nette\Utils\Html;

class RowActionEdit extends RowAction
{
    protected ?string $spanClass = 'fa fa-pencil fa fa-pencil-alt';

    protected string $class = 'btn btn-xs btn-secondary datagrid-edit-button';

    public function __construct(string $name, string $title, Datagrid $datagrid)
    {
        parent::__construct($name, $title, $datagrid);
    }

    public function render($row, $primary, ?string $itemDetailId=null): ?Html
    {
        $button = parent::render($row, $primary);
        if(is_null($button))
            return null;
        $button->setName('a');
        $button->href($this->datagrid->link('edit!', $primary));
        return $button;
    }
}