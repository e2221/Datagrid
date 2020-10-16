<?php
declare(strict_types=1);


namespace e2221\Datagrid\Document;

use e2221\Datagrid\Datagrid;
use e2221\HtmElement\BaseElement;
use Nette\Utils\Html;

class EmptyDataTemplate extends BaseElement
{
    public ?string $textContent = 'Empty result';
    protected bool $droppable=false;
    private Datagrid $datagrid;
    protected ?BaseElement $parentElement=null;
    private string $scope;

    /**
     * @param Datagrid $datagrid
     * @param bool $droppable
     * @param string $effectClass
     * @param string $scope
     */
    public function setDroppable(Datagrid $datagrid, bool $droppable=true, string $effectClass='', string $scope=''): void
    {
        $this->datagrid = $datagrid;
        $this->droppable = $droppable;
        $this->scope = $scope;
        if($this->droppable)
        {
            $this->parentElement = BaseElement::getStatic('div');
            $this->datagrid->onAnchor[] = function () use($effectClass){
                if(!empty($effectClass))
                    $this->parentElement->setDataAttribute('effect', $effectClass);
                $this->parentElement->setDataAttribute('drop-url', $this->datagrid->link('drop!'));
                $this->parentElement->setDataAttribute('item-id-param', sprintf('%s-itemId', $this->datagrid->getUniqueId()));
                $this->parentElement->setDataAttribute('item-moved-param', sprintf('%s-movedToId', $this->datagrid->getUniqueId()));
            };
        }
    }

    public function render(): ?Html
    {
        if($this->droppable === false)
            return parent::render();

        return $this->parentElement->render()->addHtml(
            Html::el('div')
                ->addAttributes([
                    'data-droppable'    => '',
                    'data-scope'        => $this->scope
                ])
                ->setText($this->textContent)
        );
    }

}