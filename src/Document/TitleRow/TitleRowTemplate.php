<?php
declare(strict_types=1);

namespace e2221\Datagrid\Document;


class TitleRowTemplate extends \e2221\HtmElement\BaseElement
{
    protected MultipleFilterTemplate $multipleFilterTemplate;
    protected ?TitleTemplate $titleTemplate=null;
    protected TitleColumnTemplate $columnTitleTemplate;

    protected ?string $elName = 'tr';

    public function __construct(?string $elName = null, ?array $attributes = null, ?string $textContent = null)
    {
        parent::__construct($elName, $attributes, $textContent);
        $this->multipleFilterTemplate = new MultipleFilterTemplate();
        $this->columnTitleTemplate = new TitleColumnTemplate();
    }

    /**
     * Set title template
     * @return TitleTemplate
     */
    public function setTitleTemplate(): TitleTemplate
    {
        $this->titleTemplate = new TitleTemplate();
        return $this->titleTemplate;
    }

    /**
     * Get instance of Title template (<div>)
     * @return TitleTemplate|null
     */
    public function getTitleTemplate(): ?TitleTemplate
    {
        return $this->titleTemplate;
    }

    /**
     * Instance of multiple filter template (<div>)
     * @return MultipleFilterTemplate
     */
    public function getMultipleFilterTemplate(): MultipleFilterTemplate
    {
        return $this->multipleFilterTemplate;
    }

    /**
     * Instance of title column template <td>
     * @return TitleColumnTemplate
     */
    public function getColumnTitleTemplate(): TitleColumnTemplate
    {
        return $this->columnTitleTemplate;
    }
}