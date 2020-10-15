<?php
declare(strict_types=1);


namespace e2221\Datagrid\Document;


use e2221\Datagrid\Datagrid;
use e2221\HtmElement\BaseElement;

class GridTemplate extends BaseElement
{
    protected ?string $elName = 'div';
    public string $class = 'grid';
    private Datagrid $datagrid;
    protected bool $stickyTop = false;

    public function __construct(Datagrid $datagrid, ?string $elName = null, ?array $attributes = null)
    {
        parent::__construct($elName, $attributes);
        $this->datagrid = $datagrid;
        $this->setDataAttribute('grid-name', $this->datagrid->getUniqueId());
    }

    /**
     * Set grid sticky
     * @param bool $sticky
     * @param int|null $topPosition
     * @return $this
     */
    public function setGridSticky(bool $sticky=true, ?int $topPosition=null): self
    {
        $this->stickyTop = $sticky;
        if($this->stickyTop === true)
        {
            $this->addClass('sticky-top');
            if(is_numeric($topPosition))
                $this->setAttribute('style', sprintf('top:%spx', $topPosition));
        }
        return $this;
    }

}