<?php
declare(strict_types=1);

namespace e2221\Datagrid\Actions;

use e2221\Datagrid\Datagrid;
use Nette\Utils\Html;

class UniversalAction
{
    /** @var string name is unique */
    protected string $name;

    /** @var string|null html title */
    protected ?string $title=null;

    /** @var string Url to signal or away */
    protected string $link='';

    /** @var array attributes */
    protected array $dataAttributes=[];

    /** @var bool Open link in new tab */
    protected bool $openInNewTab=false;

    /** @var string Style class */
    protected string $class='btn btn-xs btn-secondary';

    /** @var array classes that will be connected to class */
    protected array $addClass=[];

    /** @var string|null span is showed only if this variable is not null */
    protected ?string $spanClass=null;

    /** @var string|null i is showed only if this variable is not null */
    protected ?string $iconClass=null;

    /** @var string|null render confirmation only if confirmation isset */
    protected ?string $confirmationMessage=null;

    /**
     * @var Datagrid|null
     */
    protected ?Datagrid $datagrid;

    public function __construct(string $name, ?string $title=null, ?Datagrid $datagrid=null)
    {
        $this->name = $name;
        $this->title = $title;
        $this->datagrid = $datagrid;
    }


    /**
     * @param string|null $confirmationMessage
     * @return UniversalAction
     */
    public function setConfirmationMessage(?string $confirmationMessage): UniversalAction
    {
        $this->confirmationMessage = $confirmationMessage;
        return $this;
    }


    /**
     * @param string $url
     * @return UniversalAction
     */
    public function setLink(string $url): UniversalAction
    {
        $this->link = $url;
        return $this;
    }


    /**
     * @param bool $openInNewTab
     * @return UniversalAction
     */
    public function openInNewTab(bool $openInNewTab=true): UniversalAction
    {
        $this->openInNewTab = $openInNewTab;
        return $this;
    }


    /**
     * @param string $class
     * @return UniversalAction
     */
    public function setClass(string $class): UniversalAction
    {
        $this->class = $class;
        return $this;
    }


    /**
     * @param array $addClass
     * @return UniversalAction
     */
    public function setAddClass(array $addClass): UniversalAction
    {
        $this->addClass = $addClass;
        return $this;
    }


    /**
     * @param string|null $spanClass
     * @return UniversalAction
     */
    public function setSpanClass(?string $spanClass): UniversalAction
    {
        $this->spanClass = $spanClass;
        return $this;
    }

    /**
     * @param string|null $iconClass
     * @return UniversalAction
     */
    public function setIconClass(?string $iconClass): UniversalAction
    {
        $this->iconClass = $iconClass;
        return $this;
    }



    /**
     * @param array $dataAttributes
     * @return UniversalAction
     */
    public function setDataAttributes(array $dataAttributes): UniversalAction
    {
        $this->dataAttributes = $dataAttributes;
        return $this;
    }

    public function renderUniversal(): Html
    {
        $a = Html::el();

        if ($this->dataAttributes !== []) {
            foreach ($this->dataAttributes as $key => $attrValue) {
                $a->data((string) $key, $attrValue);
            }
        }

        if ($this->openInNewTab) {
            $a->addAttributes(['target' => '_blank']);
        }

        if ($this->title !== null) {
            $a->setAttribute('title', $this->title);
        }

        if ($this->class !== null) {
            $a->setAttribute('class', $this->class);
        }

        if($this->addClass !== []) {
            $a->setAttribute('class', ($this->class ? $this->class . ' ' : '') . implode(' ', $this->addClass));
        }

        if($this->spanClass !== null) {
            $span = Html::el('span');
            $span->setAttribute('class', $this->spanClass);
            $a->addHtml($span);
        }else if ($this->iconClass !== null) {
            $icon = Html::el('i');
            $icon->setAttribute('class', $this->iconClass);
            $a->addHtml($icon);
        }else{
            $a->addHtml($this->title);
        }

        if($this->confirmationMessage) {
            $a->setAttribute('onclick', new Confirmation($this->confirmationMessage));
        }

        return $a;
    }


}