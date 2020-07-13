<?php
declare(strict_types=1);

namespace e2221\Datagrid\Column;

use Nette\Utils\Html;
use Nextras\Datagrid\Column;

class ColumnExtended extends Column
{
    const SUPPORTED_HTML_TYPES = ['text', 'number', 'password', 'select', 'textarea'];

    /** @var bool Is column filterable */
    protected bool $filterable = FALSE;

    /** @var bool Is column filterable multiple (for selected fields) */
    protected bool $multipleFilterable = FALSE;

    /** @var array List of excluded columns in multiple filtering */
    protected array $listExcludedFromMultipleFilter = [];

    /** @var array Set HTML decoration of filter input */
    protected array $inputHtmlDecorations = [];

    /** @var bool Is column editable? */
    protected bool $editable = FALSE;

    /** @var string  Sets column html type (only if editable) */
    protected string $htmlType = 'text';

    /** @var array|null Sets selection (only if editable && column is Select) */
    protected ?array $editSelection = null;

    /** @var bool Sets required (only if editable)  */
    protected bool $required = FALSE;

    /** @var bool Sets hidden column */
    protected bool $hidden = FALSE;


    /** @var null|callable Set custom Nette\Utils\Html of column callback */
    protected $htmlCallback = null;

    /** @var callable|null Set column link callback */
    protected $linkCallback = null;

    /** @var callable|null Set custom column value callback */
    protected $cellValueCallback = null;



    /*
     * Render settings
     * *************************************************************************
     */

    /**
     * @param callable|null $htmlCallback
     * @return ColumnExtended
     */
    public function setHtmlCallback(?callable $htmlCallback): ColumnExtended
    {
        $this->htmlCallback = $htmlCallback;
        return $this;
    }

    /**
     * @param callable|null $linkCallback
     * @return ColumnExtended
     */
    public function setLinkCallback(callable $linkCallback): ColumnExtended
    {
        $this->linkCallback = $linkCallback;
        return $this;
    }

    /**
     * @param callable|null $cellValueCallback
     * @return ColumnExtended
     */
    public function setCellValueCallback(?callable $cellValueCallback): ColumnExtended
    {
        $this->cellValueCallback = $cellValueCallback;
        return $this;
    }


    /**
     * Render Column
     * @param $row
     * @param int $primary
     * @param $cell
     * @return Html
     */
    public function render($row, int $primary, $cell): Html
    {
        $html = Html::el(null);

        if(is_callable($this->linkCallback))
        {
            $html->setName('a');
            $html->href((string)call_user_func($this->linkCallback, $row, $primary, $cell));
        }

        $addHtml = is_callable($this->htmlCallback) ? call_user_func($this->htmlCallback, $row, $primary, $cell) : Html::el(null);
        $addHtml->setText(is_callable($this->cellValueCallback) ? call_user_func($this->cellValueCallback, $row, $primary, $cell) : $cell);

        $html->addHtml($addHtml);
        return $html;
    }



    /*
     * Col global settings
     * *********************************************************************
     */

    /**
     * Sets column filterable
     * @param bool $filterable
     * @return ColumnExtended
     */
    public function setFilterable(bool $filterable=true): ColumnExtended
    {
        $this->filterable = $filterable;
        return $this;
    }

    /**
     * Sets column multiple filterable
     * @param bool $filterable
     * @param array $listExcludedColumns exclude from multiple filter
     * @return $this
     */
    public function setMultipleFilterable(bool $filterable=true, array $listExcludedColumns=[]): ColumnExtended
    {
        $this->multipleFilterable = $filterable;
        $this->listExcludedFromMultipleFilter = $listExcludedColumns;
        return $this;
    }

    /**
     * Set html decoration to the filter input (placeholder=>'my placeholder text', ...)
     * @param array $inputDecorations
     * @return $this
     */
    public function setInputHtmlDecoration(array $inputDecorations): ColumnExtended
    {
        $this->inputHtmlDecorations = $inputDecorations;
        return $this;
    }

    /**
     * Sets column editable
     * @param bool $editable
     * @return ColumnExtended
     */
    public function setEditable(bool $editable=true): ColumnExtended
    {
        $this->editable = $editable;
        return $this;
    }

    /**
     * Sets html type of column
     * @param string $htmlType
     * @return ColumnExtended
     */
    public function setHtmlType(string $htmlType='text'): ColumnExtended
    {
        $this->htmlType = $htmlType;
        return $this;
    }

    /**
     * Sets selection (only if html type = select)
     * @param array $editSelection
     * @return ColumnExtended
     */
    public function setEditSelection(array $editSelection): ColumnExtended
    {
        $this->editSelection = $editSelection;
        return $this;
    }

    /**
     * Sets Col required (only if editable)
     * @param bool $required
     * @return ColumnExtended
     */
    public function setRequired(bool $required=true): ColumnExtended
    {
        $this->required = $required;
        return $this;
    }

    /**
     * @return string
     */
    public function getHtmlType(): string
    {
        return $this->htmlType;
    }

    /**
     * @return array|null
     */
    public function getEditSelection(): ?array
    {
        return $this->editSelection;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * @param bool $hidden
     */
    public function setHidden(bool $hidden=true): void
    {
        $this->hidden = $hidden;
    }


    /**
     * @return bool
     */
    public function isFilterable(): bool
    {
        return $this->filterable;
    }

    /**
     * @return bool
     */
    public function isMultipleFilterable(): bool
    {
        return $this->multipleFilterable;
    }

    /**
     * @return bool
     */
    public function isEditable(): bool
    {
        return $this->editable;
    }




}