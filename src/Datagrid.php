<?php
declare(strict_types=1);

namespace e2221\Datagrid;

use e2221\Datagrid\Actions\Export\ExportAction;
use e2221\Datagrid\Actions\HeaderActions\CustomAction;
use e2221\Datagrid\Actions\RowActions\RowActionItemDetail;
use e2221\Datagrid\Actions\RowActions\RowCustomAction;
use e2221\Datagrid\Column\ColumnDate;
use e2221\Datagrid\Column\ColumnEmail;
use e2221\Datagrid\Column\ColumnExtended;
use e2221\Datagrid\Column\ColumnNumber;
use e2221\Datagrid\Column\ColumnPrimary;
use e2221\Datagrid\Column\ColumnSelect;
use e2221\Datagrid\Column\ColumnText;
use e2221\Datagrid\Column\ColumnTextarea;
use e2221\Datagrid\Document\DataRow\DataActionsColumnTemplate;
use e2221\Datagrid\Document\DataRow\DataColumnTemplate;
use e2221\Datagrid\Document\DataRow\DataRowTemplate;
use e2221\Datagrid\Document\DocumentTemplate;
use e2221\Datagrid\Document\EmptyDataTemplate;
use e2221\Datagrid\Document\HeadFilterRow\HeadFilterActionsColumnTemplate;
use e2221\Datagrid\Document\HeadFilterRow\HeadFilterColumnTemplate;
use e2221\Datagrid\Document\HeadFilterRow\HeadFilterRowTemplate;
use e2221\Datagrid\Document\HeadRow\HeadActionsColumnTemplate;
use e2221\Datagrid\Document\HeadRow\HeadColumnTemplate;
use e2221\Datagrid\Document\HeadRow\HeadRowTemplate;
use e2221\Datagrid\Document\ItemDetailRow\ItemDetailColumn;
use e2221\Datagrid\Document\ItemDetailRow\ItemDetailRow;
use e2221\Datagrid\Document\TbodyTemplate;
use e2221\Datagrid\Document\TfootTemplate;
use e2221\Datagrid\Document\TheadTemplate;
use e2221\Datagrid\Document\TitleRow\MultipleFilterTemplate;
use e2221\Datagrid\Document\TitleRow\TitleColumnTemplate;
use e2221\Datagrid\Document\TitleRow\TitleRowTemplate;
use e2221\Datagrid\Document\TitleRow\TitleTemplate;
use e2221\Datagrid\Export\CsvExport;
use e2221\HtmElement\BaseElement;
use Exception;
use InvalidArgumentException;
use Nette\Application\AbortException;
use Nette\Application\UI;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\ComponentModel\IComponent;
use Nette\Forms\Container;
use Nette\Utils\Html;
use Nette\Utils\Paginator;
use Nette\Utils\Random;

class Datagrid extends \Nextras\Datagrid\Datagrid
{
    /** @var bool is there at least one editable column? */
    private bool $isEditable = FALSE;

    /** @var bool is there at least one filterable column? */
    private bool $isFilterable = FALSE;

    /** @var bool false=filter foreach column, true=one multiple filter */
    private bool $isMultipleFilterable = FALSE;

    /** @var array Head actions */
    protected array $customActions=[];

    /** @var array Row custom actions */
    protected array $rowCustomActions=[];

    /** @var DocumentTemplate Document layout instance */
    protected DocumentTemplate $documentTemplate;

    /** @var string Unique hash to recognize one datagrid if there are more grids on one page */
    private string $uniqueHash;

    /** @var array|null Paginator - items count list */
    private ?array $itemsCountList=null;

    /** @var string|null  Paginator - all option title - if is string show option to select all*/
    private ?string $allOptionTitle=null;

    /** @var int|null @persistent Paginator - items per page */
    public ?int $itemsPerPage=null;

    /** @var array|null @persistent Multiple filter */
    public array $multipleFilter = [];

    /** @var callable */
    protected $multipleFilterFormFactory;

    /** @var string Export file name */
    public string $exportFileName='export.csv';

    /** @var bool Rows sortable */
    protected bool $sortableRows=false;
    
    /** @var null|callable  */
    protected $sortCallback=null;

    public function __construct()
    {
        $this->uniqueHash = Random::generate(5, 'a-z');
        $this->documentTemplate = new DocumentTemplate($this);
    }

    /**
     * Set csv export file name
     * @param string $exportFileName
     * @return Datagrid
     */
    public function setExportFileName(string $exportFileName): Datagrid
    {
        $this->exportFileName = $exportFileName;
        return $this;
    }

    /**
     * Get edit row key
     * @return mixed
     */
    public function getEditRowKey()
    {
        return $this->editRowKey;
    }

    /**
     * Set rows sortable
     * @param bool $sortable
     * @return $this
     * @throws UI\InvalidLinkException
     */
    public function setSortableRows(bool $sortable=true): self
    {
        $this->sortableRows = $sortable;
        return $this;
    }

    /**
     * Set sortable rows callback
     * @param null|callable $callback
     * @return $this
     */
    public function setSortableRowsCallback(?callable $callback): self
    {
        $this->sortCallback = $callback;
        return $this;
    }


    /**************************************************************************
     *
     * Get document templates which includes all document templates
     *
     ***************************************************************************/

    /**
     * Get document template to style
     * @return DocumentTemplate
     */
    public function getDocumentTemplate(): DocumentTemplate
    {
        return $this->documentTemplate;
    }

    /**
     * Set Datagrid Title
     * @param string|Html|BaseElement $title
     * @return TitleTemplate
     */
    public function setTitle($title)
    {
        $t = $this->documentTemplate->getTitleRowTemplate()->setTitleTemplate();
        if($title instanceof Html)
        {
            $t->addHtml($title);
        }else if($title instanceof BaseElement){
            $t->addElement($title);
        }else{
            $t->setTextContent($title);
        }
        return $t;
    }

    /**************************************************************************
     *
     * Add-get column
     *
     ***************************************************************************/

    /**
     * Add column
     * @param $name
     * @param string|null $label
     * @return ColumnExtended
     * @deprecated use addColumnX instead
     * @ignore
     */
    public function addColumn($name, $label = null): ColumnExtended
    {
        if (!$this->rowPrimaryKey) {
            $this->rowPrimaryKey = $name;
        }
        $label = $label ? $this->translate($label) : ucfirst($name);
        return $this->columns[$name] = new ColumnExtended($name, $label, $this);
    }

    /**
     * Add primary column
     * @param string $name
     * @param string $label
     * @return ColumnPrimary
     */
    public function addColumnPrimary(string $name='id', string $label='ID'): ColumnPrimary
    {
        if (!$this->rowPrimaryKey)
            $this->rowPrimaryKey = $name;
        $label = $label ? $this->translate($label) : ucfirst($name);
        return $this->columns[$name] = new ColumnPrimary($name, $label, $this);
    }

    /**
     * Add Column Datetime
     * @param string $name
     * @param string|null $label
     * @return ColumnDate
     */
    public function addColumnDatetime(string $name, ?string $label=null): ColumnDate
    {
        if (!$this->rowPrimaryKey)
            $this->rowPrimaryKey = $name;
        $label = $label ? $this->translate($label) : ucfirst($name);
        return $this->columns[$name] = new ColumnDate($name, $label, $this);
    }

    /**
     * Add column email
     * @param string $name
     * @param string|null $label
     * @return ColumnEmail
     */
    public function addColumnEmail(string $name, ?string $label=null): ColumnEmail
    {
        if (!$this->rowPrimaryKey)
            $this->rowPrimaryKey = $name;
        $label = $label ? $this->translate($label) : ucfirst($name);
        return $this->columns[$name] = new ColumnEmail($name, $label, $this);
    }

    /**
     * Add column number
     * @param string $name
     * @param string|null $label
     * @return ColumnNumber
     */
    public function addColumnNumber(string $name, ?string $label=null): ColumnNumber
    {
        if (!$this->rowPrimaryKey)
            $this->rowPrimaryKey = $name;
        $label = $label ? $this->translate($label) : ucfirst($name);
        return $this->columns[$name] = new ColumnNumber($name, $label, $this);
    }

    /**
     * Add column select
     * @param string $name
     * @param string|null $label
     * @return ColumnSelect
     */
    public function addColumnSelect(string $name, ?string $label=null): ColumnSelect
    {
        if (!$this->rowPrimaryKey)
            $this->rowPrimaryKey = $name;
        $label = $label ? $this->translate($label) : ucfirst($name);
        return $this->columns[$name] = new ColumnSelect($name, $label, $this);
    }

    /**
     * Add column text
     * @param string $name
     * @param string|null $label
     * @return ColumnText
     */
    public function addColumnText(string $name, ?string $label=null): ColumnText
    {
        if (!$this->rowPrimaryKey)
            $this->rowPrimaryKey = $name;
        $label = $label ? $this->translate($label) : ucfirst($name);
        return $this->columns[$name] = new ColumnText($name, $label, $this);
    }

    /**
     * Add column textarea
     * @param string $name
     * @param string|null $label
     * @return ColumnTextarea
     */
    public function addColumnTextarea(string $name, ?string $label=null): ColumnTextarea
    {
        if (!$this->rowPrimaryKey)
            $this->rowPrimaryKey = $name;
        $label = $label ? $this->translate($label) : ucfirst($name);
        return $this->columns[$name] = new ColumnTextarea($name, $label, $this);
    }

    /**
     * Get column
     * @param  string $name
     * @return ColumnExtended
     */
    public function getColumn($name)
    {
        if (!isset($this->columns[$name])) {
            throw new InvalidArgumentException("Unknown column $name.");
        }
        return $this->columns[$name];
    }

    /**************************************************************************
     *
     * Add Head and Data-Row Actions
     *
     ***************************************************************************/

    /**
     * Adds head custom action
     * @param $name
     * @param null $title
     * @return CustomAction
     */
    public function addCustomAction($name, $title = null): CustomAction
    {
        $title = $title ? $this->translate($title) : ucfirst($name);
        return $this->customActions[$name] = new CustomAction($name, $title);
    }

    /**
     * Add head-export action
     * @param string $title
     * @param string $exportFileName
     * @return ExportAction
     */
    public function addExportAction(string $title='Export', string $exportFileName='export.csv'): ExportAction
    {
        $this->exportFileName = $exportFileName;
        return $this->documentTemplate->getHeadRowTemplate()
            ->getColumnActionsHeadTemplate()
            ->setExportAction($this, '_export', $title);
    }

    /**
     * Add data-row custom action
     * @param $name
     * @param null $title
     * @return RowCustomAction
     */
    public function addRowCustomAction($name, $title = null): RowCustomAction
    {
        $title = $title ? $this->translate($title) : ucfirst($name);
        return $this->rowCustomActions[$name] = new RowCustomAction($name, $title);
    }

    /**
     * Row Action - Item detail setter
     * @param string $name
     * @param string $title
     * @return RowActionItemDetail
     */
    public function setRowActionItemDetail(string $name='__rowItemDetail', string $title='Show detail'): RowActionItemDetail
    {
        return $this->documentTemplate
            ->getDataRowTemplate()
            ->getDataActionsColumnTemplate()
            ->setRowActionItemDetail($name, $title);
    }

    /**
     * Get cells templates
     * load parent getCellsTemplates() and add defaultTemplate.blocks.latte
     * @return array
     */
    public function getCellsTemplates(): array
    {
        $this->addCellsTemplate(__DIR__ . '/templates/defaultTemplate.blocks.latte');
        return parent::getCellsTemplates();
    }

    /**************************************************************************
     *
     * Pagination settings
     *
     ***************************************************************************/

    /**
     * Set pagination
     * @param $itemsPerPage
     * @param callable|null $itemsCountCallback
     * @param array|null $itemsCountList list of possible items per page (ex. [10, 20, 50]
     * @param string|null $allOptionTitle title of option to select all without pagination
     * @return Datagrid
     */
    public function setPagination($itemsPerPage, callable $itemsCountCallback = null, ?array $itemsCountList = null, ?string $allOptionTitle = null): Datagrid
    {
        parent::setPagination($this->itemsPerPage ?? $itemsPerPage, $itemsCountCallback);
        $this->itemsCountList = $itemsCountList;
        $this->allOptionTitle = $allOptionTitle;
        return $this;
    }

    /**************************************************************************
     *
     * Signals
     *
     ***************************************************************************/

    /**
     * Signal to reload all grid
     */
    public function handleReload(): void
    {
        $this->redrawControl('rows');
    }

    /**
     * Signal to create export of data
     * @throws AbortException
     * @throws Exception
     */
    public function handleExport(): void
    {
        $this->exportData();
    }

    /**
     * Rows sort
     * @param int|null $itemId
     * @param int|null $prevId
     * @param int|null $nextId
     */
    public function handleRowsSort(?int $itemId=null, ?int $prevId=null, ?int $nextId=null): void
    {
        if(is_callable($this->sortCallback))
            call_user_func($this->sortCallback, $this, $itemId, $prevId, $nextId);
    }

    /**************************************************************************
     *
     * Load state
     * Generate Filter, Edit, MultipleFilter form factories
     *
     **************************************************************************
     * @param array $params
     */
    public function loadState(array $params): void
    {
        parent::loadState($params);
        //generate filter form factory
        $this->generateFilterFormFactory();
        //generate edit form factory
        $this->generateEditFormFactory();
        //generate multiple filter form factory
        $this->generateMultipleFilterFormFactory();
    }

    /**************************************************************************
     *
     * Renderer
     *
     ***************************************************************************/
    public function render(): void
    {
        if ($this->filterFormFactory) {
            $this['form']['filter']->setDefaults($this->filter);
        }
        if($this->multipleFilterFormFactory && count($this->multipleFilter)) {
            $this['form']['filterMultiple']->setDefaults($this->multipleFilter);
        }

        if($this->paginator instanceof Paginator && !is_null($this->itemsPerPage))
            $this->paginator->itemsPerPage = $this->itemsPerPage;

        if($this->sortableRows === true)
            $this->getDocumentTemplate()
                ->setSortableRows(true)
                ->getTbodyTemplate()
                ->setDataAttribute('sortable-url', $this->link('rowsSort!'));

        $this->template->customActions = $this->customActions;
        $this->template->rowCustomActions = $this->rowCustomActions;
        $this->template->hasCustomActions = count($this->customActions) > 0;
        $this->template->hasRowCustomActions = count($this->rowCustomActions) > 0;
        $this->template->isFilterable = $this->isFilterable;
        $this->template->isEditable = $this->isEditable;
        $this->template->hasMultipleFilter = $this->isMultipleFilterable;
        $this->template->itemsPerPage = $this->itemsPerPage;
        $this->template->uniqueHash = $this->uniqueHash;
        $this->template->itemsCountList = $this->itemsCountList;
        $this->template->allOptionTitle = $this->allOptionTitle;
        $this->template->documentTemplate = $this->getDocumentTemplate();
        $this->template->theadTemplate = $this->documentTemplate->getTheadTemplate();
        $this->template->tbodyTemplate = $this->documentTemplate->getTbodyTemplate();
        $this->template->tfootTemplate = $this->documentTemplate->getTfootTemplate();
        $this->template->emptyDataTemplate = $this->documentTemplate->getEmptyDataTemplate();
        //title Row Parts
        $titleRowTemplate = $this->documentTemplate->getTitleRowTemplate();
        $this->template->titleRowTemplate = $titleRowTemplate;
        $this->template->titleColumnTemplate = $titleRowTemplate->getColumnTitleTemplate();
        $this->template->titleTemplate = $titleRowTemplate->getTitleTemplate();
        $this->template->multipleFilterTemplate = $titleRowTemplate->getMultipleFilterTemplate();
        //header Row parts
        $headRowTemplate = $this->documentTemplate->getHeadRowTemplate();
        $this->template->headRowTemplate = $headRowTemplate;
        $this->template->headColumnTemplate = $headRowTemplate->getColumnHeadTemplate();
        $this->template->headActionsColumnTemplate = $headRowTemplate->getColumnActionsHeadTemplate();
        //filter row parts
        $filterRowTemplate = $this->documentTemplate->getHeadFilterRowTemplate();
        $this->template->headFilterRowTemplate = $filterRowTemplate;
        $this->template->headFilterColumnTemplate = $filterRowTemplate->getHeadFilterColumnTemplate();
        $this->template->headFilterActionsColumnTemplate = $filterRowTemplate->getHeadFilterActionsColumnTemplate();
        //data row parts
        $dataRowTemplate = $this->documentTemplate->getDataRowTemplate();
        $this->template->dataRowTemplate = $dataRowTemplate;
        $this->template->dataColumnTemplate = $dataRowTemplate->getDataColumnTemplate();
        $this->template->dataActionsColumnTemplate = $dataRowTemplate->getDataActionsColumnTemplate();
        //item detail row parts
        $itemDetailRow = $this->documentTemplate->getItemDetailRow();
        $this->template->itemDetailRowTemplate = $itemDetailRow;
        $this->template->itemDetailColumnTemplate = $itemDetailRow->getItemDetailColumn();

        //from Nextras/datagrid
        $this->template->form = $this['form'];
        $this->template->data = $this->getData();
        $this->template->columns = $this->columns;
        $this->template->editRowKey = $this->editRowKey;
        $this->template->rowPrimaryKey = $this->rowPrimaryKey;
        $this->template->paginator = $this->paginator;
        $this->template->sendOnlyRowParentSnippet = $this->sendOnlyRowParentSnippet;
        $this->template->cellsTemplates = $this->getCellsTemplates();
        $this->template->showFilterCancel = $this->showCancelFilterButton(); // @ intentionaly
        $this->template->showMultipleCancel = $this->showMultipleCancelFilterButton();
        $this->template->setFile(__DIR__ . '/templates/Datagrid.latte');

        if(is_callable($this->onRender))
            $this->onRender($this);
        $this->template->render();
    }

    /**
     * Export data to CSV
     * @throws AbortException
     * @throws Exception
     */
    private function exportData(): void
    {
        $data = [];
        foreach($this->columns as $columnName => $column)
            $data[0][] = $column->label;
        foreach($this->getData() as $rowID => $row)
        {
            $row = $row->toArray();
            foreach($row as $columnName => $column)
            {
                if(!array_key_exists($columnName, $this->columns))
                    unset($row[$columnName]);
            }
            $data[] = $row;
        }

        $export = new CsvExport($data, $this->exportFileName, 'utf-8', ';', true);
        $this->getPresenter()->sendResponse($export);
    }

    /**
     * Show cancel filter button?
     * @return bool
     */
    private function showCancelFilterButton(): bool
    {
        return $this->filterDataSource != $this->filterDefaults;
    }

    /**
     * Show cancel filter button?
     * @return bool
     */
    private function showMultipleCancelFilterButton(): bool
    {
        return (bool) count($this->multipleFilter);
    }


    /**
     * Rewrites createComponentForm => filterMultiple added
     * @return UI\Form
     */
    public function createComponentForm()
    {
        $form = parent::createComponentForm();
        if($this->multipleFilterFormFactory)
        {
            $form['filterMultiple'] = call_user_func($this->multipleFilterFormFactory);

        }
        return $form;
    }

    /**
     * Gets multiple filter columns (columns without excluded columns)
     * @param string $columnN
     * @return array
     */
    private function getMultipleFilterColumns(string $columnN): array
    {
        $column = $this->columns[$columnN];
        $columns = [];
        foreach($this->columns as $columnName => $col)
        {
            if($columnN !== $columnName && !in_array($columnName, $column->getListExcludedFromMultipleFilter()))
                $columns[] = $columnName;
        }
        return $columns;
    }

    /**
     * @param $key
     * @return $this
     */
    public function setEditRowKey($key): Datagrid
    {
        $this->editRowKey = $key;
        return $this;
    }

    /**
     * Rewrite
     * @param null $key
     * @return mixed
     * @throws Exception
     */
    protected function getData($key = null)
    {
        if (!$this->data) {
            $onlyRow = $key !== null && $this->presenter->isAjax();

            if ($this->orderColumn !== NULL && !isset($this->columns[$this->orderColumn])) {
                $this->orderColumn = NULL;
            }

            if (!$onlyRow && $this->paginator) {
                $itemsCount = call_user_func(
                    $this->paginatorItemsCountCallback,
                    $this->filterDataSource,
                    $this->orderColumn ? [$this->orderColumn, strtoupper($this->orderType)] : null,
                    $this->multipleFilter
                );

                $this->paginator->setItemCount($itemsCount);
                if ($this->paginator->page !== $this->page) {
                    $this->paginator->page = $this->page = 1;
                }
            }

            $this->data = call_user_func(
                $this->dataSourceCallback,
                $this->filterDataSource,
                $this->orderColumn ? [$this->orderColumn, strtoupper($this->orderType)] : null,
                $onlyRow ? null : $this->paginator,
                $this->multipleFilter
            );
        }

        if ($key === null) {
            return $this->data;
        }

        foreach ($this->data as $row) {
            if ($this->getter($row, $this->rowPrimaryKey) == $key) {
                return $row;
            }
        }

        throw new Exception('Row not found ' . $key);
    }


    /**
     * Rewrite processForm to implement filter multiple
     * @param UI\Form $form
     * @throws AbortException
     */
    public function processForm(UI\Form $form)
    {
        if (isset($form['filterMultiple'])) {
            if ($form['filterMultiple']['filterMultiple']->isSubmittedBy()) {
                $values = $form['filterMultiple']->getValues(true);
                unset($values['filterMultiple']);
                $values = $this->filterFormFilter($values);

                foreach($values as $valueK => $val)
                {
                    if(empty($val))
                        continue;
                    foreach($this->getMultipleFilterColumns($valueK) as $k)
                    {
                        $values[$k] = $val;
                    }
                }

                if ($this->paginator) {
                    $this->page = $this->paginator->page = 1;
                }

                if(count($values) > 0)
                    $this->multipleFilter = $values;
                $this->redrawControl('rows');
                return;
            } elseif ($form['filterMultiple']['cancelMultiple']->isSubmittedBy()) {
                if ($this->paginator) {
                    $this->page = $this->paginator->page = 1;
                }
                $this->multipleFilter = [];
                $form['filterMultiple']->setValues([], true);
                $this->redrawControl('rows');
                return;
            }
        }
        $allowRedirect = true;
        if (isset($form['edit'])) {
            if ($form['edit']['save']->isSubmittedBy()) {
                if ($form['edit']->isValid()) {
                    call_user_func($this->editFormCallback, $form['edit']);
                } else {
                    $this->editRowKey = $form['edit'][$this->rowPrimaryKey]->getValue();
                    $allowRedirect = false;
                }
            }
            if ($form['edit']['cancel']->isSubmittedBy() || ($form['edit']['save']->isSubmittedBy() && $form['edit']->isValid())) {
                $editRowKey = $form['edit'][$this->rowPrimaryKey]->getValue();
                $this->redrawRow($editRowKey);
                $this->getData($editRowKey);
            }
            if ($this->editRowKey !== null) {
                $this->redrawRow($this->editRowKey);
            }
        }

        if (isset($form['filter'])) {
            if ($form['filter']['filter']->isSubmittedBy()) {
                $values = $form['filter']->getValues(true);
                unset($values['filter']);
                $values = $this->filterFormFilter($values);
                if ($this->paginator) {
                    $this->page = $this->paginator->page = 1;
                }
                $this->filter = $this->filterDataSource = $values;
                $this->redrawControl('rows');
            } elseif ($form['filter']['cancel']->isSubmittedBy()) {
                if ($this->paginator) {
                    $this->page = $this->paginator->page = 1;
                }

                $filterDefault = $this->filterDefaults;
                $this->filter = $this->filterDataSource = $filterDefault;
                $form['filter']->setValues($this->filter, true);
                $this->redrawControl('rows');
            }
        }

        if (isset($form['actions'])) {
            if ($form['actions']['process']->isSubmittedBy()) {
                $action = $form['actions']['action']->getValue();
                if ($action) {
                    $rows = [];
                    foreach($this->getData() as $row) {
                        $rows[] = $this->getter($row, $this->rowPrimaryKey);
                    }
                    $ids = array_intersect($rows, $form->getHttpData($form::DATA_TEXT, 'actions[items][]'));
                    $callback = $this->globalActions[$action][1];
                    $callback($ids, $this);
                    $this->data = null;
                    $form['actions']->setValues(['action' => null, 'items' => []]);
                }
            }
        }

        if (!$this->presenter->isAjax() && $allowRedirect) {
            $this->redirect('this');
        }
    }

    /**
     * Copy from Nextras - private
     * @param array $values
     * @return array
     */
    private function filterFormFilter(array $values)
    {
        $filtered = [];
        foreach ($values as $key => $value) {
            $isDefaultDifferent = isset($this->filterDefaults[$key]) && $this->filterDefaults[$key] !== $value;
            if ($isDefaultDifferent || !self::isEmptyValue($value)) {
                $filtered[$key] = $value;
            }
        }
        return $filtered;
    }

    /**
     * Copy from Nextras - private
     * @param $value
     * @return bool
     */
    private static function isEmptyValue($value)
    {
        return $value === NULL || $value === '' || $value === [] || $value === false;
    }

    /**************************************************************************
     *
     * Container generator
     * Getters for Filterable, MultipleFilterable, Editable Columns
     *
     ***************************************************************************/

    /**
     * Get filterable columns
     * @return ColumnExtended[]
     */
    private function getFilterableColumns(): array
    {
        $filterableColumns = [];
        foreach ($this->columns as $columnName => $column)
        {
            if($column->isFilterable()  && $column->isHidden() === false)
            {
                $filterableColumns[$columnName] = $column;
                $this->isFilterable = TRUE;
            }
        }
        return $filterableColumns;
    }

    /**
     * Filter-form factory generator
     */
    private function generateFilterFormFactory(): void
    {
        $filterableColumns = $this->getFilterableColumns();
        if(count($filterableColumns) > 0)
        {
            $this->setFilterFormFactory(function () use ($filterableColumns) {
                $form = new Container();
                foreach($filterableColumns as $name => $column)
                {
                    $form = $column->getFilterControl($form);
                    //$form = $this->formContainerGenerator($form, $name, $column->label, $column->getHtmlType(), false, $column->getEditSelection(), $column->getFilterInputHtmlDecorations());
                }
                $form->addSubmit('filter', 'Filter');
                $form->addSubmit('cancel', 'Cancel');
                return $form;
            });
        }
    }

    /**
     * Get multiple filterable columns
     * @return ColumnExtended[]
     */
    private function getMultipleFilterableColumns(): array
    {
        $multipleFColumns = [];
        foreach ($this->columns as $columnName => $column)
        {
            if($column->isMultipleFilterable()  && $column->isHidden() === false)
            {
                $multipleFColumns[$columnName] = $column;
                $this->isMultipleFilterable = TRUE;
            }
        }
        return $multipleFColumns;
    }

    /**
     * Generate multiple filter form factory
     */
    private function generateMultipleFilterFormFactory(): void
    {
        $multipleColumns = $this->getMultipleFilterableColumns();
        if(count($multipleColumns) > 0)
        {
            $this->multipleFilterFormFactory = function () use ($multipleColumns) {
                $form = new Container();
                foreach($multipleColumns as $name => $column)
                {
                    $form = $column->getMultipleFilterControl($form);
                    //$form = $this->formContainerGenerator($form, $name, $column->label, $column->getHtmlType(), false, $column->getEditSelection(), $column->getFilterMultipleHtmlDecorations());
                }
                $form->addSubmit('filterMultiple', 'Filter');
                $form->addSubmit('cancelMultiple', 'Cancel');
                return $form;
            };
        }
    }

    /**
     * Get editable columns
     * @return ColumnExtended[]
     */
    private function getEditableColumns(): array
    {
        $editableColumns = [];
        foreach ($this->columns as $columnName => $column)
        {
            if($column->isEditable() && $column->isHidden() === false)
            {
                $editableColumns[$columnName] = $column;
                $this->isEditable = TRUE;
            }
        }
        return $editableColumns;
    }

    /**
     * Generate edit-form factory
     */
    private function generateEditFormFactory(): void
    {
        $default = [];
        $editableColumns = $this->getEditableColumns();
        if(count($editableColumns) > 0)
        {
            $this->setEditFormFactory(function ($row) use ($editableColumns, $default){
                $form = new Container();
                $passwordsColumns = [];
                foreach($editableColumns as $name => $column)
                {
                    //support for column password
                    if($column->getHtmlType() == 'Password')
                        $passwordsColumns[] = $name;
                    $form = $column->getEditControl($form);
                    //$form = $this->formContainerGenerator($form, $name, $column->label, $column->getHtmlType(), $column->isRequired(), $column->getEditSelection(), $column->getEditInputHtmlDecorations());
                    if($row)
                        $default[$name] = $column->getEditValue($row);
                }
                $form->addSubmit('save', 'Save');
                $form->addSubmit('cancel', 'Cancel');
                if ($row) {
                    $form->setDefaults($default);
                    foreach($passwordsColumns as $column)
                        $form[$column]->getControlPrototype()->value = $default[$column];
                }
                return $form;
            });
        }
    }
}

/**************************************************************************
 *
 * Template
 *
 ***************************************************************************/
class DatagridTemplate extends Template
{
    public IComponent $form;
    public $data;
    public array $columns;
    public ?int $editRowKey=null;
    public string $rowPrimaryKey;
    public Paginator $paginator;
    public bool $sendOnlyRowParentSnippet;
    public array $cellsTemplates;
    public bool $showFilterCancel;
    public array $customActions;
    public bool $isEditable;
    public bool $isFilterable;
    public bool $hasCustomActions;
    public bool $hasRowCustomActions;
    public array $rowCustomActions;
    public DocumentTemplate $documentTemplate;
    public string $uniqueHash;
    public ?array $itemsCountList;
    public ?string $allOptionTitle;
    public ?int $itemsPerPage;
    public bool $hasMultipleFilter;
    public bool $showMultipleCancel;

    public TheadTemplate $theadTemplate;
    public TbodyTemplate $tbodyTemplate;
    public TfootTemplate $tfootTemplate;
    public TitleRowTemplate $titleRowTemplate;
    public TitleColumnTemplate $titleColumnTemplate;
    public ?TitleTemplate $titleTemplate;
    public MultipleFilterTemplate $multipleFilterTemplate;
    public HeadRowTemplate $headRowTemplate;
    public HeadColumnTemplate $headColumnTemplate;
    public HeadActionsColumnTemplate $headActionsColumnTemplate;
    public HeadFilterRowTemplate $headFilterRowTemplate;
    public HeadFilterColumnTemplate $headFilterColumnTemplate;
    public HeadFilterActionsColumnTemplate $headFilterActionsColumnTemplate;
    public DataRowTemplate $dataRowTemplate;
    public DataColumnTemplate $dataColumnTemplate;
    public DataActionsColumnTemplate $dataActionsColumnTemplate;
    public ItemDetailRow $itemDetailRowTemplate;
    public ItemDetailColumn $itemDetailColumnTemplate;
    public EmptyDataTemplate $emptyDataTemplate;
}