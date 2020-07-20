<?php
declare(strict_types=1);

namespace e2221\Datagrid;

use e2221\Datagrid\Actions\HeaderActions\CustomAction;
use e2221\Datagrid\Actions\RowActions\RowActionItemDetail;
use e2221\Datagrid\Actions\RowActions\RowCustomAction;
use e2221\Datagrid\Column\ColumnExtended;
use e2221\Datagrid\Document\DataActionsColumnTemplate;
use e2221\Datagrid\Document\DataColumnTemplate;
use e2221\Datagrid\Document\DataRowTemplate;
use e2221\Datagrid\Document\DocumentTemplate;
use e2221\Datagrid\Document\HeadActionsColumnTemplate;
use e2221\Datagrid\Document\HeadColumnTemplate;
use e2221\Datagrid\Document\HeadFilterActionsColumnTemplate;
use e2221\Datagrid\Document\HeadFilterColumnTemplate;
use e2221\Datagrid\Document\HeadFilterRowTemplate;
use e2221\Datagrid\Document\HeadRowTemplate;
use e2221\Datagrid\Document\ItemDetailColumn;
use e2221\Datagrid\Document\ItemDetailRow;
use e2221\Datagrid\Document\MultipleFilterTemplate;
use e2221\Datagrid\Document\TbodyTemplate;
use e2221\Datagrid\Document\TfootTemplate;
use e2221\Datagrid\Document\TheadTemplate;
use e2221\Datagrid\Document\TitleColumnTemplate;
use e2221\Datagrid\Document\TitleRowTemplate;
use e2221\Datagrid\Document\TitleTemplate;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\UI;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\ComponentModel\IComponent;
use Nette\Forms\Container;
use Nette\Utils\Paginator;
use Nette\Utils\Random;

class Datagrid extends \Nextras\Datagrid\Datagrid
{
    /** @var array|string[] Default input attributes */
    public array $defaultInputAttributes = [
        'class' => 'form-control form-control-sm'
    ];

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

    /** @var callable */
    protected $multipleFilterFormFactory;


    public function __construct()
    {
        $this->uniqueHash = Random::generate(5, 'a-z');
        $this->documentTemplate = new DocumentTemplate($this);
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
     * Get document template to style
     * @return DocumentTemplate
     */
    public function getDocumentTemplate(): DocumentTemplate
    {
        return $this->documentTemplate;
    }


    /**
     * Generates universal Form Container
     * @param Container $container
     * @param string $name
     * @param string $caption
     * @param string $html
     * @param bool $required
     * @param array $selection
     * @param array $htmlDecorations
     * @return Container
     */
    private function formContainerGenerator(Container $container, string $name, string $caption='', string $html='text', bool $required=false, ?array $selection=null, array $htmlDecorations=[]): Container
    {
        switch ($html){
            case 'select':
                $container->addSelect($name, $caption, $selection);
                break;
            case 'textarea':
                $container->addTextArea($name, $caption);
                break;
            default:
                $addMethod = 'add' . $html;
                if(!method_exists($this, $addMethod))
                    $addMethod = 'addText';
                $container->$addMethod($name, $caption);
                break;
        }
        foreach ($this->defaultInputAttributes as $attribute => $value) {
            $container[$name]->setHtmlAttribute($attribute, $value);
        }
        foreach ($htmlDecorations as $tag => $value)
            $container[$name]->setHtmlAttribute($tag, $value);
        if($required)
            $container[$name]->setRequired();
        return $container;
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


    /**
     * Get filterable columns
     * @return array
     * @internal
     */
    private function getFilterableColumns(): array
    {
        $filterableColumns = [];
        foreach ($this->columns as $columnName => $column)
        {
            if($column->isFilterable())
            {
                $filterableColumns[$columnName] = $column;
                $this->isFilterable = TRUE;
            }
        }
        return $filterableColumns;
    }

    /**
     * Filter-form factory generator
     * @internal
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
                    $form = $this->formContainerGenerator($form, $name, $column->label, $column->getHtmlType(), false, $column->getEditSelection(), $column->getFilterInputHtmlDecorations());
                }
                $form->addSubmit('filter', 'Filter')->getControlPrototype()->class = 'btn btn-sm btn-outline-primary';
                $form->addSubmit('cancel', 'Cancel')->getControlPrototype()->class = 'btn btn-sm btn-outline-secondary';
                return $form;
            });
        }
    }

    /**
     * Get multiple filterable columns
     * @return array
     */
    private function getMultipleFilterableColumns(): array
    {
        $multipleFColumns = [];
        foreach ($this->columns as $columnName => $column)
        {
            if($column->isMultipleFilterable())
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
                    $form = $this->formContainerGenerator($form, $name, $column->label, $column->getHtmlType(), false, $column->getEditSelection(), $column->getFilterMultipleHtmlDecorations());
                }
                $form->addSubmit('filterMultiple', 'Filter')->getControlPrototype()->class = 'btn btn-sm btn-outline-primary';
                $form->addSubmit('cancelMultiple', 'Cancel')->getControlPrototype()->class = 'btn btn-sm btn-outline-secondary';
                return $form;
            };
        }
    }

    /**
     * Get editable columns
     * @return array
     * @internal
     */
    private function getEditableColumns(): array
    {
        $editableColumns = [];
        foreach ($this->columns as $columnName => $column)
        {
            if($column->isEditable())
            {
                $editableColumns[$columnName] = $column;
                $this->isEditable = TRUE;
            }
        }
        return $editableColumns;
    }

    /**
     * Generate edit-form factory
     * @internal
     */
    private function generateEditFormFactory(): void
    {
        $editableColumns = $this->getEditableColumns();
        if(count($editableColumns) > 0)
        {
            $this->setEditFormFactory(function ($row) use ($editableColumns){
                $form = new Container();
                foreach($editableColumns as $name => $column)
                {
                    $form = $this->formContainerGenerator($form, $name, $column->label, $column->getHtmlType(), $column->isRequired(), $column->getEditSelection(), $column->getEditInputHtmlDecorations());
                }
                $form->addSubmit('save', 'Save');
                $form->addSubmit('cancel', 'Cancel');
                if ($row) {
                    $form->setDefaults($row);
                }
                return $form;
            });
        }
    }


    /**
     * Adds column
     * @param $name
     * @param null $label
     * @return ColumnExtended
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
     * Adds head action
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
     * Adds row custom action
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
     * Set pagination
     * @param $itemsPerPage
     * @param callable|null $itemsCountCallback
     * @param array|null $itemsCountList
     * @param string|null $allOptionTitle
     * @return Datagrid
     */
    public function setPagination($itemsPerPage, callable $itemsCountCallback = null, ?array $itemsCountList = null, ?string $allOptionTitle = null): Datagrid
    {
        parent::setPagination($this->itemsPerPage ?? $itemsPerPage, $itemsCountCallback);
        $this->itemsCountList = $itemsCountList;
        $this->allOptionTitle = $allOptionTitle;
        return $this;
    }


    /**
     * Signal to reload all grid
     */
    public function handleReload(): void
    {
        $this->redrawControl('rows');
    }

    /**
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


    /**
     * Render
     * @throws Exception
     */
    public function render(): void
    {
        if ($this->filterFormFactory) {
            $this['form']['filter']->setDefaults($this->filter);
        }
        if($this->multipleFilterFormFactory && array_key_exists('_multiple', $this->filter)) {
            $this['form']['filterMultiple']->setDefaults($this->filter['_multiple']);
        }

        if($this->paginator instanceof Paginator && !is_null($this->itemsPerPage))
            $this->paginator->itemsPerPage = $this->itemsPerPage;

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
        $this->template->setFile(__DIR__ . '/templates/Datagrid.latte');

        $this->onRender($this);
        $this->template->render();
    }

    /**
     * Show cacnecl filter button?
     * @return bool
     */
    private function showCancelFilterButton(): bool
    {
        $filter = $this->filterDataSource;
        if(array_key_exists('_multiple', $filter))
            unset($filter['_multiple']);
        return $filter != $this->filterDefaults;
    }

    /**
     * Rewrited createComponentForm => filterMultiple added
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
     * Rewrite processForm to implemetn filter multiple
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
                    foreach($this->getMultipleFilterColumns($valueK) as $k)
                    {
                        $values[$k] = $val;
                    }
                }

                if ($this->paginator) {
                    $this->page = $this->paginator->page = 1;
                }
                $this->filter['_multiple'] = $this->filterDataSource['_multiple'] = $values;
                $this->redrawControl('rows');
                return;
            } elseif ($form['filterMultiple']['cancelMultiple']->isSubmittedBy()) {
                if ($this->paginator) {
                    $this->page = $this->paginator->page = 1;
                }
                unset($this->filterDataSource['_multiple']);
                $this->filter = $this->filterDataSource;
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
                if(isset($this->filterDataSource['_multiple']))
                    $values['_multiple'] = $this->filterDataSource['_multiple'];
                $this->filter = $this->filterDataSource = $values;
                $this->redrawControl('rows');
            } elseif ($form['filter']['cancel']->isSubmittedBy()) {
                if ($this->paginator) {
                    $this->page = $this->paginator->page = 1;
                }

                $filterDefault = $this->filterDefaults;
                if(isset($this->filterDataSource['_multiple']))
                    $filterDefault['_multiple'] = $this->filterDataSource['_multiple'];
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


}


/**
 * Class DatagridTemplate
 */
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
}