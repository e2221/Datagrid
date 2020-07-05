<?php
declare(strict_types=1);

namespace e2221\Datagrid\Document;

use Nette\Utils\Html;

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

    /** @var string|Html|null Table title */
    public $tableTitle = null;


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
     * Sets table title
     * @param string|null|Html $tableTitle
     * @return DocumentTemplate
     */
    public function setTableTitle($tableTitle): DocumentTemplate
    {
        $this->tableTitle = $tableTitle;
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