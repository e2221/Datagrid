<?php
declare(strict_types=1);

namespace e2221\Datagrid\Actions\RowActions;

use e2221\Datagrid\Datagrid;
use Nette\Utils\Html;

class RowActionCancel extends RowAction
{
    protected string $class = 'btn btn-xs btn-warning';
    protected string $defaultClass = 'datagrid-cancel-button';

    public function __construct(string $name, string $title, Datagrid $datagrid)
    {
        parent::__construct($name, $title, $datagrid);
    }

    public function render($row, $primary, ?string $itemDetailId=null): ?Html
    {
        //add datagrid name data-attribute
        $this->setDataAttributes([
            'datagrid-name'         => $this->datagrid->getName()
        ]);

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