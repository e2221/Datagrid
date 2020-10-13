<?php
declare(strict_types=1);

namespace e2221\Datagrid\Actions\RowActions;


use Nette\Utils\Html;

class RowCustomAction extends RowAction
{
    /**
     * @param $row
     * @param int|string $primary
     * @param string|null $itemDetailId
     * @return Html|null
     */
    public function render($row, $primary, ?string $itemDetailId=null): ?Html
    {
        $el = parent::render($row, $primary);
        if(is_null($el))
            return null;
        $el->setName('a');
        $el->href($this->getLink());
        return $el;
    }

}