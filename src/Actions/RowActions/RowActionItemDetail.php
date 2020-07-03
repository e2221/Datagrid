<?php
declare(strict_types=1);

namespace e2221\Datagrid\Actions\RowActions;

use Nette\Utils\Html;

class RowActionItemDetail extends RowAction
{

    protected string $class = 'btn btn-xs btn-secondary';

    protected ?string $spanClass = 'fa fa-eye';

    public function __construct(string $name='__rowItemDetail', string $title='Show detail')
    {
        parent::__construct($name, $title);
    }

    public function render($row, $primary, ?string $itemDetailId=null): ?Html
    {
        $button = parent::render($row, $primary);
        if(is_null($button))
            return null;
        $button->setName('a');
        $button->href('#' . $itemDetailId);
        $button->setAttribute('role', 'button');
        $button->setAttribute('data-toggle', 'collapse');
        $button->setAttribute('aria-expanded', 'collapse');
        $button->setAttribute('aria-controls', $itemDetailId);
        return $button;
    }
}