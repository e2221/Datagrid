<?php
declare(strict_types=1);

namespace e2221\Datagrid;

use e2221\Datagrid\Actions\FilterActions\CancelFilterAction;
use e2221\Datagrid\Actions\FilterActions\FilterAction;
use e2221\Datagrid\Actions\HeaderActions\CustomAction;
use e2221\Datagrid\Actions\RowActions\RowActionCancel;
use e2221\Datagrid\Actions\RowActions\RowActionEdit;
use e2221\Datagrid\Actions\RowActions\RowActionItemDetail;
use e2221\Datagrid\Actions\RowActions\RowActionSave;
use e2221\Datagrid\Actions\RowActions\RowCustomAction;
use e2221\Datagrid\Column\ColumnExtended;
use e2221\Datagrid\Document\DocumentTemplate;
use Exception;
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

    /** @var FilterAction|null filter button style instance */
    protected ?FilterAction $filterButton=null;

    /** @var CancelFilterAction|null cancel filter button style instance */
    protected ?CancelFilterAction $cancelFilterButton=null;

    /** @var array Row custom actions */
    protected array $rowCustomActions=[];

    /** @var RowActionEdit instance of row action - Edit button */
    protected RowActionEdit $rowActionEdit;

    /** @var RowActionSave instance of row action - Save button */
    protected RowActionSave $rowActionSave;

    /** @var RowActionCancel instance of row action - Cancel button */
    protected RowActionCancel $rowActionCancel;

    /** @var RowActionItemDetail|null instance of row action - Item detail */
    protected ?RowActionItemDetail $rowActionItemDetail=null;

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

    public function __construct()
    {
        $this->uniqueHash = Random::generate(5, 'a-z');
        $this->filterButton = new FilterAction();
        $this->cancelFilterButton = new CancelFilterAction();
        $this->documentTemplate = new DocumentTemplate();
        $this->rowActionEdit = new RowActionEdit('__edit', 'Edit', $this);
        $this->rowActionSave = new RowActionSave('__save', 'Save');
        $this->rowActionCancel = new RowActionCancel('__cancel', 'Cancel');
    }

    /**
     * Set multiple filter
     * @param bool $multiple
     * @return $this
     */
    public function setMultipleFilter(bool $multiple=true): Datagrid
    {
        $this->multipleFilter = $multiple;
        return $this;
    }

    /**
     * Row Action - Item detail setter
     * @param string $name
     * @param string $title
     * @return RowActionItemDetail
     */
    public function setRowActionItemDetail(string $name='__rowItemDetail', string $title='Show detail'): RowActionItemDetail
    {
        return $this->rowActionItemDetail = new RowActionItemDetail($name, $title);
    }

    /**
     * Row Action - Save button instance
     * @return RowActionSave
     */
    public function getRowActionSave(): RowActionSave
    {
        return $this->rowActionSave;
    }

    /**
     * Row Action - Cancel button instance
     * @return RowActionCancel
     */
    public function getRowActionCancel(): RowActionCancel
    {
        return $this->rowActionCancel;
    }


    /**
     * Row Action - EDIT button instance
     * @return RowActionEdit|null
     */
    public function getRowActionEdit(): ?RowActionEdit
    {
        return $this->rowActionEdit;
    }


    /**
     * Head Action - CANCEL FIlTER button instance
     * @return CancelFilterAction|null
     */
    public function getCancelFilterButton(): ?CancelFilterAction
    {
        return $this->cancelFilterButton;
    }

    /**
     * Head Action - FILTER button instance
     * @return FilterAction|null
     */
    public function getFilterButton(): ?FilterAction
    {
        return $this->filterButton;
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
    public function getCellsTemplates()
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
            if($column->isMultipleFilterable())
                $this->isMultipleFilterable = TRUE;
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
                    $form = $this->formContainerGenerator($form, $name, $column->label, $column->getHtmlType(), false, $column->getEditSelection());
                }
                $form->addSubmit('filter', 'Filter')->getControlPrototype()->class = 'btn btn-sm btn-outline-primary';
                $form->addSubmit('cancel', 'Cancel')->getControlPrototype()->class = 'btn btn-sm btn-outline-secondary';
                return $form;
            });
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
                    $form = $this->formContainerGenerator($form, $name, $column->label, $column->getHtmlType(), $column->isRequired(), $column->getEditSelection());
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
     */
    public function setPagination($itemsPerPage, callable $itemsCountCallback = null, ?array $itemsCountList = null, ?string $allOptionTitle = null)
    {
        parent::setPagination($this->itemsPerPage ?? $itemsPerPage, $itemsCountCallback);
        $this->itemsCountList = $itemsCountList;
        $this->allOptionTitle = $allOptionTitle;
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

        if($this->paginator instanceof Paginator && !is_null($this->itemsPerPage))
            $this->paginator->itemsPerPage = $this->itemsPerPage;

        $this->template->filterButton = $this->filterButton;
        $this->template->cancelFilterButton = $this->cancelFilterButton;
        $this->template->isFilterable = $this->isFilterable;
        $this->template->isEditable = $this->isEditable;
        $this->template->hasCustomActions = count($this->customActions) > 0;
        $this->template->hasRowCustomActions = count($this->rowCustomActions) > 0;
        $this->template->rowCustomActions = $this->rowCustomActions;
        $this->template->documentTemplate = $this->getDocumentTemplate();
        $this->template->rowActionEdit = $this->rowActionEdit;
        $this->template->rowActionSave = $this->rowActionSave;
        $this->template->rowActionCancel = $this->rowActionCancel;
        $this->template->rowActionItemDetail = $this->rowActionItemDetail;
        $this->template->uniqueHash = $this->uniqueHash;
        $this->template->itemsCountList = $this->itemsCountList;
        $this->template->allOptionTitle = $this->allOptionTitle;
        $this->template->itemsPerPage = $this->itemsPerPage;
        $this->template->hasMultipleFilter = $this->isMultipleFilterable;

        $this->template->form = $this['form'];
        $this->template->data = $this->getData();
        $this->template->columns = $this->columns;
        $this->template->customActions = $this->customActions;
        $this->template->editRowKey = $this->editRowKey;
        $this->template->rowPrimaryKey = $this->rowPrimaryKey;
        $this->template->paginator = $this->paginator;
        $this->template->sendOnlyRowParentSnippet = $this->sendOnlyRowParentSnippet;
        $this->template->cellsTemplates = $this->getCellsTemplates();
        $this->template->showFilterCancel = $this->filterDataSource != $this->filterDefaults; // @ intentionaly
        $this->template->setFile(__DIR__ . '/templates/Datagrid.latte');

        $this->onRender($this);
        $this->template->render();
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
    public ?FilterAction $filterButton;
    public ?CancelFilterAction $cancelFilterButton;
    public bool $isEditable;
    public bool $isFilterable;
    public bool $hasCustomActions;
    public bool $hasRowCustomActions;
    public array $rowCustomActions;
    public DocumentTemplate $documentTemplate;
    public RowActionEdit $rowActionEdit;
    public RowActionSave $rowActionSave;
    public RowActionCancel $rowActionCancel;
    public ?RowActionItemDetail $rowActionItemDetail;
    public string $uniqueHash;
    public ?array $itemsCountList;
    public ?string $allOptionTitle;
    public ?int $itemsPerPage;
    public bool $hasMultipleFilter;
}