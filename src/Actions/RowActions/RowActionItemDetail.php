<?php
declare(strict_types=1);

namespace e2221\Datagrid\Actions\RowActions;

use e2221\HtmElement\BaseElement;
use Nette\Utils\Html;

class RowActionItemDetail extends RowAction
{

    /** @var string Button class */
    protected string $class = 'btn btn-xs btn-secondary';

    /** @var string|null Span class */
    protected ?string $spanClass = 'fa fa-eye';

    /** @var null|callable */
    protected $contentCallback=null;

    public function __construct(string $name='__rowItemDetail', string $title='Show detail')
    {
        parent::__construct($name, $title);
    }

    /**
     * Set content callback
     * @param callable|null $contentCallback
     * @return RowActionItemDetail
     */
    public function setContentCallback(?callable $contentCallback): RowActionItemDetail
    {
        $this->contentCallback = $contentCallback;
        return $this;
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

    /**
     * Content renderer
     * @return Html|null|BaseElement|string
     */
    public function renderContent()
    {
        if(is_callable($this->contentCallback))
        {
            return call_user_func($this->contentCallback, $this->row, $this->primary);
        }
        return null;
    }
}