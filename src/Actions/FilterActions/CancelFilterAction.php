<?php

namespace e2221\Datagrid\Actions\FilterActions;


use e2221\Datagrid\Actions\Action;
use Nette\Utils\Html;

class CancelFilterAction extends Action
{
    protected string $class = 'btn btn-xs btn-warning';
    protected ?string $title = 'Cancel filter';

    public function __construct(string $name='__cancelFilter', string $title='Filter')
    {
        parent::__construct($name, $title);
    }

    public function render(): Html
    {
        $input = parent::render();
        $input->setName('input');
        $input->setAttribute('name', 'filter[cancel]');
        $input->setAttribute('value', 'Cancel');
        $input->type = 'submit';
        return $input;
    }
}