<?php
declare(strict_types=1);

namespace e2221\Datagrid\Document\DataRow;

use e2221\Datagrid\Datagrid;
use e2221\HtmElement\BaseElement;
use Nette\Utils\Html;

class DataRowTemplate extends BaseElement
{
    protected DataColumnTemplate $dataColumnTemplate;
    protected DataActionsColumnTemplate $dataActionsColumnTemplate;

    protected ?string $elName = 'tr';

    /** @var callable|null */
    protected $setAttributesCallback;

    public function __construct(?string $elName = null, ?array $attributes = null, ?string $textContent = null, ?Datagrid $datagrid=null)
    {
        $this->dataColumnTemplate = new DataColumnTemplate();
        $this->dataActionsColumnTemplate = new DataActionsColumnTemplate(null, null, null, $datagrid);
        parent::__construct($elName, $attributes, $textContent);
    }

    /**
     * Set attributes callback
     * @param callable $callback
     */
    public function setAttributesCallback(callable $callback): void
    {
        $this->setAttributesCallback = $callback;
    }

    public function render($rowID=null, $primary=null, $row=null): ?Html
    {
        $this->element->attrs = [];
        $this->attributes = [];

        if(is_callable($this->setAttributesCallback))
        {
            $attrs = call_user_func($this->setAttributesCallback, $primary, $row);
            if(is_array($attrs))
            {
                foreach ($attrs as $attribute => $value) {
                    $this->attributes[$attribute] = $value;
                }
            }
        }
        $this->setAttribute('id', $rowID);
        $this->setDataAttribute('id', $rowID);
        return parent::render();
    }

    /**
     * Render start tag
     * @param null $rowID
     * @param null $primary
     * @param null $row
     * @return string|null
     */
    public function renderStartTag($rowID=null, $primary=null, $row=null): ?string
    {
        $render = $this->render($rowID, $primary, $row);
        if($render instanceof Html)
        {
            return $render->startTag();
        }
        return null;
    }

    /**
     * Get instance of data column template
     * @return DataColumnTemplate
     */
    public function getDataColumnTemplate(): DataColumnTemplate
    {
        return $this->dataColumnTemplate;
    }

    /**
     * Get instance of data actions column template
     * @return DataActionsColumnTemplate
     */
    public function getDataActionsColumnTemplate(): DataActionsColumnTemplate
    {
        return $this->dataActionsColumnTemplate;
    }

}