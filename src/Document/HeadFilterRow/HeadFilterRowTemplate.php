<?php
declare(strict_types=1);

namespace e2221\Datagrid\Document;

use e2221\HtmElement\BaseElement;

class HeadFilterRowTemplate extends BaseElement
{
    protected HeadFilterColumnTemplate $headFilterColumnTemplate;
    protected HeadFilterActionsColumnTemplate $headFilterActionsColumnTemplate;

    protected ?string $elName = 'tr';

    public array $attributes = [
        'class'     => 'grid-filters'
    ];

    public function __construct(?string $elName = null, ?array $attributes = null, ?string $textContent = null)
    {
        $this->headFilterColumnTemplate = new HeadFilterColumnTemplate();
        $this->headFilterActionsColumnTemplate = new HeadFilterActionsColumnTemplate();
        parent::__construct($elName, $attributes, $textContent);
    }

    /**
     * Get instance of column head filter
     * @return HeadFilterColumnTemplate
     */
    public function getHeadFilterColumnTemplate(): HeadFilterColumnTemplate
    {
        return $this->headFilterColumnTemplate;
    }

    /**
     * Get instance of actions column head filter
     * @return HeadFilterActionsColumnTemplate
     */
    public function getHeadFilterActionsColumnTemplate(): HeadFilterActionsColumnTemplate
    {
        return $this->headFilterActionsColumnTemplate;
    }


}