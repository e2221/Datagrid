<?php
declare(strict_types=1);

namespace e2221\Datagrid\Document;

use e2221\HtmElement\BaseElement;

class HeadRowTemplate extends BaseElement
{
    protected HeadColumnTemplate $columnHeadTemplate;
    protected HeadActionsColumnTemplate $columnActionsHeadTemplate;

    protected ?string $elName = 'tr';

    public array $attributes = [
        'class'     => 'grid-columns'
    ];

    public function __construct(?string $elName = null, ?array $attributes = null, ?string $textContent = null)
    {
        $this->columnHeadTemplate = new HeadColumnTemplate();
        $this->columnActionsHeadTemplate = new HeadActionsColumnTemplate();
        parent::__construct($elName, $attributes, $textContent);
    }

    /**
     * Instance of head columns
     * @return HeadColumnTemplate
     */
    public function getColumnHeadTemplate(): HeadColumnTemplate
    {
        return $this->columnHeadTemplate;
    }

    /**
     * Instance of head actions column
     * @return HeadActionsColumnTemplate
     */
    public function getColumnActionsHeadTemplate(): HeadActionsColumnTemplate
    {
        return $this->columnActionsHeadTemplate;
    }

}