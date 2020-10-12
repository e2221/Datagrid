<?php
declare(strict_types=1);

namespace e2221\Datagrid\Actions\RowActions;

use e2221\Datagrid\Datagrid;
use Nette\Utils\Html;

class RowActionEdit extends RowAction
{
    protected ?string $spanClass = 'fa fa-pencil fa fa-pencil-alt';
    protected string $class = 'btn btn-xs btn-secondary';
    protected string $defaultClass = 'datagrid-edit-button';

    public function __construct(string $name, string $title, Datagrid $datagrid)
    {
        parent::__construct($name, $title, $datagrid);
    }

    public function render($row, $primary, ?string $itemDetailId=null): ?Html
    {
        //add datagrid name data-attribute
        $this->setDataAttributes([
            'datagrid-name'         => $this->datagrid->getName(),
            'datagrid-edit'         => sprintf('datagrid-edit-%s', $this->datagrid->getName())
        ]);

        //disabled non edit rows
        if(!is_null($this->datagrid->getEditRowKey()) && $this->datagrid->getEditRowKey() != $primary)
            $this->defaultClass = sprintf('%s disabled', $this->defaultClass);

        $button = parent::render($row, $primary);
        if(is_null($button))
            return null;
        $button->setName('a');
        $button->href($this->datagrid->link('edit!', $primary));
        return $button;
    }
}