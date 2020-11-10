<?php
declare(strict_types=1);

namespace e2221\Datagrid\Actions\RowActions;

use e2221\Datagrid\Actions\UniversalAction;
use e2221\Datagrid\Confirmation\Confirmation;
use Nette\Utils\Html;

class RowAction extends UniversalAction
{
    /** @var */
    protected $row;

    /** @var int|null */
    protected ?int $primary = null;

    /** @var null|callable  */
    protected $titleCallback = null;

    /** @var null|callable  */
    protected $linkCallback = null;

    /** @var null|callable  */
    protected $dataAttributesCallback = null;

    /** @var null|callable  */
    protected $openInNewTabCallback = null;

    /** @var null|callable */
    protected $classCallback = null;

    /** @var null|callable  */
    protected $addClassCallback = null;

    /** @var null|callable  */
    protected $spanClassCallback = null;

    /** @var null|callable  */
    protected $iconClassCallback = null;

    /** @var null|callable  */
    protected $confirmationMessageCallback = null;

    /** @var null|callable Condition if Row action have to be rendered */
    protected $showIfCallback = null;

    /**
     * @param callable|null $titleCallback
     * @return RowAction
     */
    public function setTitleCallback(?callable $titleCallback): RowAction
    {
        $this->titleCallback = $titleCallback;
        return $this;
    }

    /**
     * @param callable|null $linkCallback
     * @return RowAction
     */
    public function setLinkCallback(?callable $linkCallback): RowAction
    {
        $this->linkCallback = $linkCallback;
        return $this;
    }

    /**
     * @param callable|null $dataAttributesCallback
     * @return RowAction
     */
    public function setDataAttributesCallback(?callable $dataAttributesCallback): RowAction
    {
        $this->dataAttributesCallback = $dataAttributesCallback;
        return $this;
    }

    /**
     * @param callable|null $openInNewTabCallback
     * @return RowAction
     */
    public function setOpenInNewTabCallback(?callable $openInNewTabCallback): RowAction
    {
        $this->openInNewTabCallback = $openInNewTabCallback;
        return $this;
    }

    /**
     * @param callable|null $addClassCallback
     * @return RowAction
     */
    public function setAddClassCallback(?callable $addClassCallback): RowAction
    {
        $this->addClassCallback = $addClassCallback;
        return $this;
    }

    /**
     * @param callable|null $spanClassCallback
     * @return RowAction
     */
    public function setSpanClassCallback(?callable $spanClassCallback): RowAction
    {
        $this->spanClassCallback = $spanClassCallback;
        return $this;
    }

    /**
     * @param callable|null $iconClassCallback
     * @return RowAction
     */
    public function setIconClassCallback(?callable $iconClassCallback): RowAction
    {
        $this->iconClassCallback = $iconClassCallback;
        return $this;
    }

    /**
     * @param callable|null $confirmationMessageCallback
     * @return RowAction
     */
    public function setConfirmationMessageCallback(?callable $confirmationMessageCallback): RowAction
    {
        $this->confirmationMessageCallback = $confirmationMessageCallback;
        return $this;
    }


    /**
     * @param callable|null $classCallback
     * @return RowAction
     */
    public function setClassCallback(?callable $classCallback): RowAction
    {
        $this->classCallback = $classCallback;
        return $this;
    }

    /**
     * Sets show if callback
     * @param callable|null $showIfCallback
     * @return RowAction
     */
    public function setShowIfCallback(?callable $showIfCallback): RowAction
    {
        $this->showIfCallback = $showIfCallback;
        return $this;
    }


    /*
     * **********************************************************************
     */

    /**
     * @param $row
     * @param int|string $primary
     * @param string|null $itemDetailId
     * @return Html|null
     */
    public function render($row, $primary, ?string $itemDetailId=null): ?Html
    {
        $this->row = $row;
        $this->primary = $primary;

        if(is_callable($this->showIfCallback))
        {
            if(!call_user_func($this->showIfCallback, $this->row, $this->primary))
                return null;
        }

        $a = Html::el();

        if ($this->getDataAttributes() !== []) {
            foreach ($this->getDataAttributes() as $key => $attrValue) {
                $a->data((string) $key, $attrValue);
            }
        }

        if ($this->getOpenInNewTab()) {
            $a->addAttributes(['target' => '_blank']);
        }

        if ($this->getTitle() !== null) {
            $a->setAttribute('title', $this->getTitle());
        }

        if ($this->getClass() !== null) {
            $a->setAttribute('class', $this->getClass());
        }

        if($this->getSpanClass() !== null) {
            $span = Html::el('span');
            $span->setAttribute('class', $this->getSpanClass());
            $a->addHtml($span);
        }else if ($this->getIconClass() !== null) {
            $icon = Html::el('i');
            $icon->setAttribute('class', $this->getIconClass());
            $a->addHtml($icon);
        }else{
            $a->addHtml($this->getTitle());
        }

        if($this->getConfirmationMessage()) {
            $a->setAttribute('onclick', new Confirmation($this->getConfirmationMessage()));
        }

        return $a;
    }

    /**
     * Get title
     * @return string
     */
    protected function getTitle(): string
    {
        if(is_callable($this->titleCallback))
        {
            return (string)call_user_func($this->titleCallback, $this->row, $this->primary);
        }else
        {
            return $this->title;
        }
    }

    /**
     * Get Link
     * @return string
     */
    protected function getLink(): string
    {
        if(is_callable($this->linkCallback))
        {
            return (string)call_user_func($this->linkCallback, $this->row, $this->primary);
        }else
        {
            return $this->link;
        }
    }

    /**
     * Get data attributes
     * @return array
     */
    protected function getDataAttributes(): array
    {
        if(is_callable($this->dataAttributesCallback))
        {
            return (array)call_user_func($this->dataAttributesCallback, $this->row, $this->primary);
        }else
        {
            return $this->dataAttributes;
        }
    }

    /**
     * Get open in new tab
     * @return bool
     */
    protected function getOpenInNewTab(): bool
    {
        if(is_callable($this->openInNewTabCallback))
        {
            return (bool)call_user_func($this->openInNewTabCallback, $this->row, $this->primary);
        }else
        {
            return $this->openInNewTab;
        }
    }

    /**
     * Get class
     * @return string
     */
    protected function getClass(): string
    {
        $class = is_callable($this->classCallback) ? (string)call_user_func($this->classCallback, $this->row, $this->primary) : $this->class;
        $addClass = is_callable($this->addClassCallback) ? (array)call_user_func($this->addClassCallback, $this->row, $this->primary) : $this->addClass;
        return
            sprintf('%s%s', $this->defaultClass, empty($this->defaultClass) ? '' : ' ') .
            sprintf('%s%s', $class, empty($class) ? '' : ' ') .
            sprintf('%s%s', implode(' ', $addClass), count($addClass) ? '' : ' ');
    }

    /**
     * Get Span Class
     * @return string|null
     */
    protected function getSpanClass(): ?string
    {
        if(is_callable($this->spanClassCallback))
        {
            return call_user_func($this->spanClassCallback, $this->row, $this->primary);
        }else
        {
            return $this->spanClass;
        }
    }

    /**
     * Get Icon Class
     * @return string|null
     */
    protected function getIconClass(): ?string
    {
        if(is_callable($this->iconClassCallback))
        {
            return call_user_func($this->iconClassCallback, $this->row, $this->primary);
        }else
        {
            return $this->iconClass;
        }
    }

    /**
     * Get Confirmation Message
     * @return string|null
     */
    protected function getConfirmationMessage(): ?string
    {
        if(is_callable($this->confirmationMessageCallback))
        {
            return call_user_func($this->confirmationMessageCallback, $this->row, $this->primary);
        }else
        {
            return $this->confirmationMessage;
        }
    }

    /**
     * Get link callback
     * @return callable|null
     */
    public function getLinkCallback(): ?callable
    {
        return $this->linkCallback;
    }


}