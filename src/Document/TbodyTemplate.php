<?php
declare(strict_types=1);

namespace e2221\Datagrid\Document;

use e2221\HtmElement\BaseElement;

class TbodyTemplate extends BaseElement
{
    protected bool $sortable=false;

    protected ?string $elName = 'tbody';

    /**
     * Is sortable?
     * @return bool
     */
    public function isSortable(): bool
    {
        return $this->sortable;
    }

    /**
     * Set sortable
     * @param bool $sortable
     * @return TbodyTemplate
     */
    public function setSortable(bool $sortable=true): self
    {
        if($sortable)
            $this->setDataAttribute('sortable', '');
        $this->sortable = $sortable;
        return $this;
    }
}