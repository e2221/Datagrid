<?php
declare(strict_types=1);

namespace e2221\Datagrid\Column;

use Nette\Forms\Container;
use Nette\Utils\Html;
use Nextras\Datagrid\Column;

class ColumnExtended extends Column
{

    /** @var bool Is column filterable */
    protected bool $filterable = FALSE;

    /** @var bool Is column filterable multiple (for selected fields) */
    protected bool $multipleFilterable = FALSE;

    /** @var array List of excluded columns in multiple filtering */
    protected array $listExcludedFromMultipleFilter = [];

    /** @var array Set HTML decoration of edit input */
    protected array $editInputHtmlDecorations = [];

    /** @var array Set HTML decoration of filter input */
    protected array $filterInputHtmlDecorations = [];

    /** @var array Set HTML decoration of filter multiple input */
    protected array $filterMultipleHtmlDecorations = [];

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

    /** @var callable|null Set value in edit form (makes sense if column is set as editable) */
    protected $editValueCallback = null;

    /** @var array|string[] Default input attributes */
    public array $defaultInputAttributes = [
        'class' => 'form-control form-control-sm'
    ];


    /**************************************************************************
     *
     * Render settings
     *
     ***************************************************************************/

    /**
     * Set html content callback
     * @param callable|null $htmlCallback
     * @return ColumnExtended
     */
    public function setHtmlCallback(?callable $htmlCallback): ColumnExtended
    {
        $this->htmlCallback = $htmlCallback;
        return $this;
    }

    /**
     * Set link callback
     * @param callable|null $linkCallback
     * @return ColumnExtended
     */
    public function setLinkCallback(callable $linkCallback): ColumnExtended
    {
        $this->linkCallback = $linkCallback;
        return $this;
    }

    /**
     * Modify cell value in grid
     * @param callable|null $cellValueCallback
     * @return ColumnExtended
     */
    public function setCellValueCallback(?callable $cellValueCallback): ColumnExtended
    {
        $this->cellValueCallback = $cellValueCallback;
        return $this;
    }

    /**
     * Set value to input during edit
     * @param callable|null $editValueCallback
     * @return $this
     */
    public function setEditValueCallback(?callable $editValueCallback): ColumnExtended
    {
        $this->editValueCallback = $editValueCallback;
        return $this;
    }

    /**************************************************************************
     *
     * Render
     *
     ***************************************************************************/

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

    /**************************************************************************
     *
     * Col global settings
     *
     ***************************************************************************/

    /**
     * Sets column filterable
     * @param bool $filterable
     * @param array $filterHtmlDecorations html tags
     * @return ColumnExtended
     */
    public function setFilterable(bool $filterable=true, array $filterHtmlDecorations=[]): ColumnExtended
    {
        $this->filterable = $filterable;
        $this->filterInputHtmlDecorations = $filterHtmlDecorations;
        return $this;
    }

    /**
     * Sets column multiple filterable
     * @param bool $filterable
     * @param array $listExcludedColumns exclude from multiple filter
     * @param array $filterHtmlDecorations html tags
     * @return $this
     */
    public function setMultipleFilterable(bool $filterable=true, array $listExcludedColumns=[], array $filterHtmlDecorations=[]): ColumnExtended
    {
        $this->multipleFilterable = $filterable;
        $this->listExcludedFromMultipleFilter = $listExcludedColumns;
        $this->filterMultipleHtmlDecorations = $filterHtmlDecorations;
        return $this;
    }

    /**
     * Set html tags for Edit input
     * @param array $editInputHtmlDecorations
     * @return ColumnExtended
     */
    public function setEditInputHtmlDecorations(array $editInputHtmlDecorations): ColumnExtended
    {
        $this->editInputHtmlDecorations = $editInputHtmlDecorations;
        return $this;
    }

    /**
     * Set html tags for Filter input
     * @param array $filterInputHtmlDecorations
     * @return ColumnExtended
     */
    public function setFilterInputHtmlDecorations(array $filterInputHtmlDecorations): ColumnExtended
    {
        $this->filterInputHtmlDecorations = $filterInputHtmlDecorations;
        return $this;
    }

    /**
     * Set html tags for Filter-multiple input
     * @param array $filterMultipleHtmlDecorations
     * @return ColumnExtended
     */
    public function setFilterMultipleHtmlDecorations(array $filterMultipleHtmlDecorations): ColumnExtended
    {
        $this->filterMultipleHtmlDecorations = $filterMultipleHtmlDecorations;
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
    public function setHtmlType(string $htmlType='Text'): ColumnExtended
    {
        $this->htmlType = ucfirst($htmlType);
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

    /**
     * @return array
     */
    public function getListExcludedFromMultipleFilter(): array
    {
        return $this->listExcludedFromMultipleFilter;
    }

    /**
     * @return array
     */
    public function getEditInputHtmlDecorations(): array
    {
        return $this->editInputHtmlDecorations;
    }

    /**
     * @return array
     */
    public function getFilterInputHtmlDecorations(): array
    {
        return $this->filterInputHtmlDecorations;
    }

    /**
     * @return array
     */
    public function getFilterMultipleHtmlDecorations(): array
    {
        return $this->filterMultipleHtmlDecorations;
    }

    /**
     * @param $row
     * @return mixed
     * @internal
     */
    public function getEditValue($row)
    {
        if(is_callable($this->editValueCallback))
        {
            return call_user_func($this->editValueCallback, $row);
        }else{
            if(is_object($row) && method_exists($row, 'toArray'))
                $row = $row->toArray();
            return $row[$this->name];
        }
    }

    /**
     * Universal add control
     * @param Container $container
     * @return Container
     *
     * @internal
     */
    public function addControl(Container $container): Container
    {
        switch ($this->htmlType){
            case 'Select':
                $container->addSelect($this->name, $this->label, $this->getEditSelection());
                break;
            default:
                $addMethod = 'add' . $this->htmlType;
                if(!method_exists($container, $addMethod))
                    $addMethod = 'addText';
                $container->$addMethod($this->name, $this->label);
                break;
        }
        foreach($this->defaultInputAttributes as $attribute => $value)
            $container[$this->name]->setHtmlAttribute($attribute, $value);
        if($this->required)
            $container[$this->name]->setRequired();
        return $container;
    }

    /**
     * Get universal edit control
     * @param Container $container
     * @return Container
     *
     * @internal
     */
    public function getEditControl(Container $container): Container
    {
        $container = $this->addControl($container);
        foreach($this->getEditInputHtmlDecorations() as $attribute => $value)
            $container[$this->name]->setHtmlAttribute($attribute, $value);
        return $container;
    }

    /**
     * Get universal filter control
     * @param Container $container
     * @return Container
     */
    public function getFilterControl(Container $container): Container
    {
        $container = $this->addControl($container);
        foreach($this->getFilterInputHtmlDecorations() as $attribute => $value)
            $container[$this->name]->setHtmlAttribute($attribute, $value);
        return $container;
    }

    /**
     * Get universal multiple filter control
     * @param Container $container
     * @return Container
     */
    public function getMultipleFilterControl(Container $container): Container
    {
        $container = $this->addControl($container);
        foreach($this->getFilterMultipleHtmlDecorations() as $attribute => $value)
            $container[$this->name]->setHtmlAttribute($attribute, $value);
        return $container;
    }
}