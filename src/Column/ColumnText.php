<?php
declare(strict_types=1);


namespace e2221\Datagrid\Column;


use e2221\Datagrid\Datagrid;
use Nette\Forms\Container;


class ColumnText extends ColumnExtended
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
        $this->setHtmlType('text');
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
     * Add control text
     * @param Container $container
     * @return Container
     *
     * @internal
     */
    public function addControl(Container $container): Container
    {
        $control = $container->addText($this->name, $this->label);
        foreach($this->defaultInputAttributes as $attribute => $value)
            $control->setHtmlAttribute($attribute, $value);
        if($this->required)
            $control->setRequired();
        return $container;
    }
}