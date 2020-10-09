<?php
declare(strict_types=1);


namespace e2221\Datagrid\Column;


use Nette\Forms\Container;
use Nextras\Datagrid\Datagrid;

class ColumnSelect extends ColumnExtended
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
        $this->setHtmlType('select');
    }

    /**
     * @param array $selection
     * @return $this
     */
    public function setSelection(array $selection): self
    {
        $this->setEditSelection($selection);
        return $this;
    }

    /**
     * Add control select
     * @param Container $container
     * @return Container
     *
     * @internal
     */
    public function addControl(Container $container): Container
    {
        $control = $container->addSelect($this->name, $this->label, $this->getEditSelection());
        foreach($this->defaultInputAttributes as $attribute => $value)
            $control->setHtmlAttribute($attribute, $value);
        if($this->required)
            $control->setRequired();
        return $container;
    }

}