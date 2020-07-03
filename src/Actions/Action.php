<?php
declare(strict_types=1);

namespace e2221\Datagrid\Actions;

use Nette\Utils\Html;

class Action extends UniversalAction
{

    /**
     * @return Html
     */
    public function render(): Html
    {
        return $this->renderUniversal();
    }



}