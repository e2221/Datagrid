<?php
declare(strict_types=1);

namespace e2221\Datagrid\Document\TitleRow;


use e2221\Datagrid\Datagrid;
use e2221\HtmElement\BaseElement;

class TitleTemplate extends BaseElement
{
    protected ?string $elName = 'div';
    public string $defaultClass = 'col';

    public function __construct(Datagrid $datagrid)
    {
        parent::__construct();
        if($datagrid->isMultipleFilterable())
            $this->class = 'col-sm-6';
    }

}