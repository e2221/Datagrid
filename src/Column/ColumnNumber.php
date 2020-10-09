<?php
declare(strict_types=1);


namespace e2221\Datagrid\Column;


use Nette\Forms\Container;
use Nextras\Datagrid\Datagrid;

class ColumnNumber extends ColumnExtended
{
    /**
     * ColumnTextarea constructor.
     * @param string $name
     * @param string|null $label
     * @param Datagrid $grid
     */
    public function __construct(string $name, string $label, Datagrid $grid)
    {
        parent::__construct($name, $label, $grid);
        $this->setHtmlType('number');
    }

    /**
     * @internal
     * @ignore
     * @param array $editSelection
     * @return ColumnExtended
     */
    public function setEditSelection(array $editSelection): ColumnExtended
    {
        return parent::setEditSelection($editSelection);
    }

    /**
     * Add control number
     * @param Container $container
     * @return Container
     *
     * @internal
     */
    public function addControl(Container $container): Container
    {
        $control = $container->addText($this->name, $this->label);
        $control->setHtmlType('number');
        foreach($this->defaultInputAttributes as $attribute => $value)
            $control->setHtmlAttribute($attribute, $value);
        if($this->required)
            $control->setRequired();
        return $container;
    }
}