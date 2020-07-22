<?php
declare(strict_types=1);

namespace e2221\Datagrid\Document\HeadRow;

use e2221\Datagrid\Actions\Export\ExportAction;
use e2221\Datagrid\Datagrid;
use e2221\HtmElement\BaseElement;
use Nette\Utils\Html;

class HeadActionsColumnTemplate extends BaseElement
{
    protected ?string $elName = 'th';

    public string $class = '';

    public array $attributes = ['class' => 'grid-col-actions'];

    /** @var bool set column as sticky top */
    public bool $stickyTop = false;

    protected ?ExportAction $exportAction = null;

    public function render(): ?Html
    {
        if($this->stickyTop)
            $this->class .= ' column-sticky';
        return parent::render();
    }

    /**
     * Sets column sticky top
     * @param bool $sticky
     * @return HeadActionsColumnTemplate
     */
    public function setStickyTop(bool $sticky=true): HeadActionsColumnTemplate
    {
        $this->stickyTop = $sticky;
        return $this;
    }

    /**
     * Get instance of export action button
     * @return ExportAction|null
     */
    public function getExportAction(): ?ExportAction
    {
        return $this->exportAction;
    }

    /**
     * Set export action
     * @param Datagrid $datagrid
     * @param string $name
     * @param string $title
     * @return ExportAction
     */
    public function setExportAction(Datagrid $datagrid, string $name='_export', string $title='Export'): ExportAction
    {
        return $this->exportAction = new ExportAction($name, $title, $datagrid);
    }
}