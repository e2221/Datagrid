<?php
declare(strict_types=1);


namespace e2221\Datagrid\Column;


use Nette\Forms\Container;
use Nextras\Datagrid\Datagrid;

class ColumnTextarea extends ColumnExtended
{
    /** @var int|null Rows */
    protected ?int $rows=null;

    /** @var int|null Rows */
    protected ?int $cols=null;

    /**
     * ColumnTextarea constructor.
     * @param string $name
     * @param string|null $label
     * @param Datagrid $grid
     */
    public function __construct(string $name, string $label, Datagrid $grid)
    {
        parent::__construct($name, $label, $grid);
        $this->setHtmlType('textarea');
    }

    /**
     * Set number of cols
     * @param int $cols
     * @return $this
     */
    public function setColsNumber(int $cols): self
    {
        $this->cols = $cols;
        return $this;
    }

    /**
     * Set number of rows
     * @param int $cols
     * @return $this
     */
    public function setRowsNumber(int $cols): self
    {
        $this->rows = $cols;
        return $this;
    }

    /**
     * Add control textarea
     * @param Container $container
     * @return Container
     *
     * @internal
     */
    public function addControl(Container $container): Container
    {
        $control = $container->addTextArea($this->name, $this->label, $this->cols, $this->rows);
        foreach($this->defaultInputAttributes as $attribute => $value)
            $control->setHtmlAttribute($attribute, $value);
        if($this->required)
            $control->setRequired();
        return $container;
    }
}