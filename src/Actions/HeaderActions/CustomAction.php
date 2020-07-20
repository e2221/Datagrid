<?php

namespace e2221\Datagrid\Actions\HeaderActions;

use e2221\Datagrid\Actions\Action;
use Nette\Utils\Html;

class CustomAction extends Action
{
    /**
     * @return Html
     */
    public function render(): Html
    {
        $a = parent::render();
        $a->setName('a');
        $a->href($this->link);
        return $a;
    }
}