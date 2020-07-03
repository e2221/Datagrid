<?php
declare(strict_types=1);

namespace e2221\Datagrid\Actions\RowActions;


use Nette\Utils\Html;

class RowCustomAction extends RowAction
{
    public function render($row, int $primary, ?string $itemDetailId=null): ?Html
    {
        $el = parent::render($row, $primary);
        if(is_null($el))
            return null;
        $el->setName('a');
        $el->href($this->getLink());
        return $el;
    }

}