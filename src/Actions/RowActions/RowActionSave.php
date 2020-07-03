<?php
declare(strict_types=1);

namespace e2221\Datagrid\Actions\RowActions;

use e2221\Datagrid\Actions\UniversalAction;
use Nette\Utils\Html;

class RowActionSave extends RowAction
{

    protected string $class = 'btn btn-xs btn-primary';

    public function __construct(string $name, string $title)
    {
        parent::__construct($name, $title);
    }

    public function render($row, $primary, ?string $itemDetailId=null): ?Html
    {
        $button = parent::render($row, $primary);
        if(is_null($button))
            return null;
        $button->setName('input');
        $button->type('submit');
        $button->setAttribute('name', 'edit[save]');
        $button->value('Save');
        return $button;
    }

    /**
     * It is not possible to call this (it is input)
     * @param callable|null $confirmationMessageCallback
     * @return $this|RowAction
     */
    public function setConfirmationMessageCallback(?callable $confirmationMessageCallback): RowAction
    {
        return $this;
    }

    /**
     * It is not possible to call this (it is input)
     * @param string|null $confirmationMessage
     * @return $this|UniversalAction
     */
    public function setConfirmationMessage(?string $confirmationMessage): \e2221\Datagrid\Actions\UniversalAction
    {
        return $this;
    }
}