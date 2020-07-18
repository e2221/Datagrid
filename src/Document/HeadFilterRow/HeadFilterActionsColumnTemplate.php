<?php
declare(strict_types=1);

namespace e2221\Datagrid\Document;

use e2221\Datagrid\Actions\FilterActions\CancelFilterAction;
use e2221\Datagrid\Actions\FilterActions\FilterAction;
use e2221\HtmElement\BaseElement;
use Nette\Utils\Html;

class HeadFilterActionsColumnTemplate extends BaseElement
{
    protected ?string $elName = 'th';

    public ?string $class = '';

    public array $attributes = ['class' => 'grid-col-actions'];

    /** @var bool set column as sticky top */
    public bool $stickyTop = false;

    protected FilterAction $filterActionButton;
    protected CancelFilterAction $cancelFilterActionButton;

    public function __construct(?string $elName = null, ?array $attributes = null, ?string $textContent = null)
    {
        $this->filterActionButton = new FilterAction();
        $this->cancelFilterActionButton = new CancelFilterAction();
        parent::__construct($elName, $attributes, $textContent);
    }

    public function render(): ?Html
    {
        if($this->stickyTop)
            $this->class .= ' column-sticky';
        return parent::render();
    }

    /**
     * Sets column sticky top
     * @param bool $sticky
     * @return HeadFilterActionsColumnTemplate
     */
    public function setStickyTop(bool $sticky=true): HeadFilterActionsColumnTemplate
    {
        $this->stickyTop = $sticky;
        return $this;
    }

    /**
     * Get instance of filter button
     * @return FilterAction
     */
    public function getFilterActionButton(): FilterAction
    {
        return $this->filterActionButton;
    }

    /**
     * Get instance of cancel button
     * @return CancelFilterAction
     */
    public function getCancelFilterActionButton(): CancelFilterAction
    {
        return $this->cancelFilterActionButton;
    }
}