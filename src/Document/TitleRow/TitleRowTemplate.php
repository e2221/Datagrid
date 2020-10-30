<?php
declare(strict_types=1);

namespace e2221\Datagrid\Document\TitleRow;


use e2221\Datagrid\Datagrid;
use e2221\HtmElement\BaseElement;

class TitleRowTemplate extends BaseElement
{
    protected MultipleFilterTemplate $multipleFilterTemplate;
    protected ?TitleTemplate $titleTemplate=null;
    protected TitleColumnTemplate $columnTitleTemplate;

    protected ?string $elName = 'tr';
    private Datagrid $datagrid;

    public function __construct(Datagrid $datagrid)
    {
        parent::__construct();
        $this->datagrid = $datagrid;
        $this->multipleFilterTemplate = new MultipleFilterTemplate();
        $this->columnTitleTemplate = new TitleColumnTemplate();
    }

    /**
     * Set title template
     * @return TitleTemplate
     */
    public function setTitleTemplate(): TitleTemplate
    {
        $this->titleTemplate = new TitleTemplate($this->datagrid);
        return $this->titleTemplate;
    }

    /**
     * Get instance of Title template (<div>)
     * @return TitleTemplate|null
     */
    public function getTitleTemplate(): ?TitleTemplate
    {
        return $this->titleTemplate;
    }

    /**
     * Instance of multiple filter template (<div>)
     * @return MultipleFilterTemplate
     */
    public function getMultipleFilterTemplate(): MultipleFilterTemplate
    {
        return $this->multipleFilterTemplate;
    }

    /**
     * Instance of title column template <td>
     * @return TitleColumnTemplate
     */
    public function getColumnTitleTemplate(): TitleColumnTemplate
    {
        return $this->columnTitleTemplate;
    }
}