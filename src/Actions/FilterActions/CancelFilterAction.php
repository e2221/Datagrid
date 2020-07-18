<?php

namespace e2221\Datagrid\Actions\FilterActions;


use e2221\Datagrid\Actions\Action;
use Nette\Utils\Html;

class CancelFilterAction extends Action
{
    protected string $class = 'btn btn-xs btn-warning';
    protected ?string $title = 'Cancel';

    public function __construct(string $name='__cancelFilter', string $title='Cancel')
    {
        parent::__construct($name, $title);
    }

    public function render(): Html
    {
        $input = parent::render();
        $input->setName('button');
        $input->setAttribute('name', 'filter[cancel]');
        $input->setAttribute('value', $this->title);
        $input->type = 'submit';
        return $input;
    }
}