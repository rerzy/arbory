<?php

namespace Arbory\Base\Admin\Grid;

use Arbory\Base\Admin\Grid;
use Arbory\Base\Admin\Layout\Body;
use Arbory\Base\Admin\Widgets\Button;
use Arbory\Base\Html\Elements\Content;
use Arbory\Base\Admin\Widgets\Breadcrumbs;
use Arbory\Base\Admin\Widgets\SearchField;
use Arbory\Base\Admin\Layout\AbstractLayout;
use Arbory\Base\Admin\Layout\LayoutInterface;

class Layout extends AbstractLayout implements LayoutInterface
{
    /**
     * @var Grid
     */
    protected $grid;

    public function __construct()
    {
        $this->addEventListener('apply', function ($body) {
            $this->addSlots($body);
        });
    }

    public function breadcrumbs(): ?Breadcrumbs
    {
        return $this->grid->getModule()->breadcrumbs();
    }

    /**
     * @return Grid
     */
    public function getGrid(): Grid
    {
        return $this->grid;
    }

    /**
     * @param Grid $grid
     *
     * @return Layout
     */
    public function setGrid(Grid $grid): self
    {
        $this->grid = $grid;

        return $this;
    }

    /**
     * @return Button|null
     */
    protected function filterButton()
    {
        if (! $this->grid->hasTool('filter')) {
            return;
        }

        return Button::create()
            ->type('button', 'filter js-filter-trigger')
            ->withIcon('filter')
            ->title(trans('arbory::filter.filter'));
    }

    /**
     * @return \Arbory\Base\Html\Elements\Element
     */
    protected function searchField()
    {
        if (! $this->grid->hasTool('search')) {
            return;
        }

        return (new SearchField($this->grid->getModule()->url('index')))->render();
    }

    public function build()
    {
        $this->setContent($this->getGrid()->render());
    }

    /**
     * @param mixed $content
     *
     * @return Content|mixed
     */
    public function contents($content)
    {
        return new Content([
            $content,
        ]);
    }

    /**
     * @param $body
     */
    protected function addSlots(Body $body)
    {
        $body->getTarget()->slot('header_right_filter', $this->filterButton());
        $body->getTarget()->slot('header_right', $this->searchField());
    }
}
