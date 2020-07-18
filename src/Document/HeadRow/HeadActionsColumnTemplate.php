<?php
declare(strict_types=1);

namespace e2221\Datagrid\Document;

use e2221\HtmElement\BaseElement;
use Nette\Utils\Html;

class HeadActionsColumnTemplate extends BaseElement
{
    protected ?string $elName = 'th';

    public ?string $class = '';

    public array $attributes = ['class' => 'grid-col-actions'];

    /** @var bool set column as sticky top */
    public bool $stickyTop = false;


    public function render(): ?Html
    {
        if($this->stickyTop)
            $this->class .= ' column-sticky';
        return parent::render();
    }

    /**
     * Sets column sticky top
     * @param bool $sticky
     * @return HeadActionsColumnTemplate
     */
    public function setStickyTop(bool $sticky=true): HeadActionsColumnTemplate
    {
        $this->stickyTop = $sticky;
        return $this;
    }
}