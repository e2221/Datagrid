<?php
declare(strict_types=1);

namespace e2221\Datagrid\Actions\Export;

use e2221\Datagrid\Actions\Action;
use e2221\Datagrid\Datagrid;
use Nette\Application\UI\InvalidLinkException;
use Nette\Utils\Html;

class ExportAction extends Action
{
    protected string $class = 'btn btn-xs btn-secondary';
    protected ?string $title = 'Export';
    protected array $dataAttributes = ['ajax'=>'false'];

    public function __construct(string $name='__export', string $title='Export', ?Datagrid $datagrid=null)
    {
        parent::__construct($name, $title, $datagrid);
    }

    /**
     * @return Html
     * @throws InvalidLinkException
     */
    public function render(): Html
    {
        $a = parent::render();
        $a->setName('a');
        $a->href($this->datagrid->link('export!'));
        return $a;
    }
}