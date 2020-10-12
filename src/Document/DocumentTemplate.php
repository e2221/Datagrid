<?php
declare(strict_types=1);

namespace e2221\Datagrid\Document;

use e2221\Datagrid\Datagrid;
use e2221\Datagrid\Document\DataRow\DataRowTemplate;
use e2221\Datagrid\Document\HeadFilterRow\HeadFilterRowTemplate;
use e2221\Datagrid\Document\HeadRow\HeadRowTemplate;
use e2221\Datagrid\Document\ItemDetailRow\ItemDetailRow;
use e2221\Datagrid\Document\TitleRow\TitleRowTemplate;

class DocumentTemplate
{
    /** @var string Table class */
    public string $tableClass = 'table table-sm';

    /** @var string|null Adds class to defined in $tableClass */
    public ?string $addClass = null;

    /** @var array|null Html attributes of table (id=>'id') */
    public ?array $attributes = [];

    /** @var bool Adds bootstrap responsive div */
    public bool $responsive = false;

    /** @var bool Sets table stripped */
    public bool $stripped = true;

    /** @var bool Sets table hover */
    public bool $hover = true;

    /** @var bool Sets table border */
    public bool $border = true;

    /** @var bool Sets table borderless */
    public bool $borderless = false;

    /** @var bool Hides table header */
    public bool $hideTableHeader = false;

    /** @var TheadTemplate template to style <thead> tag */
    protected TheadTemplate $theadTemplate;

    /** @var TbodyTemplate template to style <tbody> tag */
    protected TbodyTemplate $tbodyTemplate;

    /** @var TfootTemplate template to style <tfoot> tag */
    protected TfootTemplate $tfootTemplate;

    /** @var TitleRowTemplate template to style all components from title row */
    protected TitleRowTemplate $titleRowTemplate;

    /** @var HeadRowTemplate template to style all components from head row */
    protected HeadRowTemplate $headRowTemplate;

    /** @var HeadFilterRowTemplate template to style all components from head-filter row */
    protected HeadFilterRowTemplate $headFilterRowTemplate;

    /** @var DataRowTemplate template to style all components from data row */
    protected DataRowTemplate $dataRowTemplate;

    /** @var ItemDetailRow template to style all components from item detail row */
    protected ItemDetailRow $itemDetailRow;

    protected Datagrid $datagrid;

    /** @var EmptyDataTemplate template to style empty data content */
    protected EmptyDataTemplate $emptyDataTemplate;

    /** @var bool Rows sortable */
    protected bool $sortableRows=false;

    /** @var string|null Link to sortable signal */
    protected ?string $sortableHandler=null;

    public function __construct(Datagrid $datagrid)
    {
        $this->datagrid = $datagrid;
        $this->theadTemplate = new TheadTemplate();
        $this->tbodyTemplate = new TbodyTemplate();
        $this->tfootTemplate = new TfootTemplate();
        $this->titleRowTemplate = new TitleRowTemplate();
        $this->headRowTemplate = new HeadRowTemplate();
        $this->headFilterRowTemplate = new HeadFilterRowTemplate();
        $this->dataRowTemplate = new DataRowTemplate(null, null, null, $datagrid);
        $this->itemDetailRow = new ItemDetailRow();
        $this->emptyDataTemplate = new EmptyDataTemplate();
    }

    /**************************************************************************
     *
     * Sortable
     *
     ***************************************************************************/

    /**
     * Set sortable rows
     * @param bool $sortable
     * @return $this
     */
    public function setSortableRows(bool $sortable=true): self
    {
        $this->sortableRows = $sortable;
        $this->tbodyTemplate->setSortable($sortable);
        $this->dataRowTemplate->getDataActionsColumnTemplate()->setSortable($sortable);
        return $this;
    }



    /**************************************************************************
     *
     * Template getters
     *
     ***************************************************************************/

    /**
     * Get thead template <thead>
     * @return TheadTemplate
     */
    public function getTheadTemplate(): TheadTemplate
    {
        return $this->theadTemplate;
    }

    /**
     * Get tbody template <tbody>
     * @return TbodyTemplate
     */
    public function getTbodyTemplate(): TbodyTemplate
    {
        return $this->tbodyTemplate;
    }

    /**
     * Get tfoot template <tfoot>
     * @return TfootTemplate
     */
    public function getTfootTemplate(): TfootTemplate
    {
        return $this->tfootTemplate;
    }

    /**
     * Get empty data template
     * @return EmptyDataTemplate
     */
    public function getEmptyDataTemplate(): EmptyDataTemplate
    {
        return $this->emptyDataTemplate;
    }

    /**
     * Get Title Row template <tr>
     * @return TitleRowTemplate
     */
    public function getTitleRowTemplate(): TitleRowTemplate
    {
        return $this->titleRowTemplate;
    }

    /**
     * Get Head row template <tr>
     * @return HeadRowTemplate
     */
    public function getHeadRowTemplate(): HeadRowTemplate
    {
        return $this->headRowTemplate;
    }

    /**
     * Get Head Filter Row template <tr>
     * @return HeadFilterRowTemplate
     */
    public function getHeadFilterRowTemplate(): HeadFilterRowTemplate
    {
        return $this->headFilterRowTemplate;
    }

    /**
     * Get Data-row template <tr>
     * @return DataRowTemplate
     */
    public function getDataRowTemplate(): DataRowTemplate
    {
        return $this->dataRowTemplate;
    }

    /**
     * Get ItemDetail-row template <tr>
     * @return ItemDetailRow
     */
    public function getItemDetailRow(): ItemDetailRow
    {
        return $this->itemDetailRow;
    }

    /**************************************************************************
     *
     * Style <table> tag
     *
     ***************************************************************************/

    /**
     * Set class that will be added
     * @param string|null $addClass
     * @return DocumentTemplate
     */
    public function setAddClass(?string $addClass): DocumentTemplate
    {
        $this->addClass = $addClass;
        return $this;
    }

    /**
     * Set html attributes
     * @param array|null $attributes
     * @return DocumentTemplate
     */
    public function setAttributes(?array $attributes): DocumentTemplate
    {
        $this->attributes = $attributes;
        return $this;
    }


    /**
     * Removes all styles
     * @return DocumentTemplate
     */
    public function removeStyles(): DocumentTemplate
    {
        $this->stripped = false;
        $this->hover = false;
        $this->border = false;
        $this->borderless = true;
        return $this;
    }

    /**
     * @param bool $hideTableHeader
     * @return DocumentTemplate
     */
    public function hideTableHeader(bool $hideTableHeader=true): DocumentTemplate
    {
        $this->hideTableHeader = $hideTableHeader;
        return $this;
    }

    /**
     * Set table class
     * @param string $tableClass
     * @return DocumentTemplate
     */
    public function setTableClass(string $tableClass): DocumentTemplate
    {
        $this->tableClass = $tableClass;
        return $this;
    }

    /**
     * Set Table responsive
     * @param bool $responsive
     * @return DocumentTemplate
     */
    public function setResponsive(bool $responsive=true): DocumentTemplate
    {
        $this->responsive = $responsive;
        return $this;
    }

    /**
     * Set Table stripped
     * @param bool $stripped
     * @return DocumentTemplate
     */
    public function setStripped(bool $stripped=true): DocumentTemplate
    {
        $this->stripped = $stripped;
        return $this;
    }

    /**
     * Set table hover
     * @param bool $hover
     * @return DocumentTemplate
     */
    public function setHover(bool $hover=true): DocumentTemplate
    {
        $this->hover = $hover;
        return $this;
    }

    /**
     * Set table border
     * @param bool $border
     * @return DocumentTemplate
     */
    public function setBorder(bool $border=true): DocumentTemplate
    {
        $this->border = $border;
        return $this;
    }

    /**
     * Set table borderless
     * @param bool $borderless
     * @return DocumentTemplate
     */
    public function setBorderless(bool $borderless=true): DocumentTemplate
    {
        $this->borderless = $borderless;
        return $this;
    }

}