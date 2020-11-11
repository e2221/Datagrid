<?php
declare(strict_types=1);


namespace e2221\Datagrid\Column;


use e2221\Datagrid\Datagrid;
use Nette\Forms\Container;


class ColumnPassword extends ColumnExtended
{
    /**
     * ColumnPassword constructor.
     * @param string $name
     * @param string|null $label
     * @param Datagrid $grid
     */
    public function __construct(string $name, string $label, Datagrid $grid)
    {
        parent::__construct($name, $label, $grid);
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
        $control = $container->addPassword($this->name, $this->label);
        foreach($this->defaultInputAttributes as $attribute => $value)
            $control->setHtmlAttribute($attribute, $value);
        if($this->required)
            $control->setRequired();
        return $container;
    }
}