<?php

namespace e2221\Datagrid\Actions\FilterActions;


use e2221\Datagrid\Actions\Action;
use Nette\Utils\Html;

class FilterAction extends Action
{
    protected string $class = 'btn btn-xs btn-secondary';
    protected ?string $title = 'Filter';

    public function __construct(string $name='__filter', string $title='Filter')
    {
        parent::__construct($name, $title);
    }

    public function render(): Html
    {
        $input = parent::render();
        $input->setName('input');
        $input->setAttribute('name', 'filter[filter]');
        $input->setAttribute('value', 'Filter');
        $input->type = 'submit';
        return $input;
    }
}