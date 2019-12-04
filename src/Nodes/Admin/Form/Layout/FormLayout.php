<?php

namespace Arbory\Base\Nodes\Admin\Form\Layout;

use Arbory\Base\Admin\Form\OverviewLayout;
use Arbory\Base\Admin\Form\Overview;
use Arbory\Base\Admin\Layout\PageInterface;
use Arbory\Base\Admin\Widgets\Breadcrumbs;
use Arbory\Base\Nodes\Node;
use Illuminate\Support\Collection;

class FormLayout extends OverviewLayout
{
    /**
     * @var Overview
     */
    protected $overview;


    /**
     * Executes when the layout is applied.
     *
     * @param PageInterface $page
     */
    public function applyToPage(PageInterface $page)
    {
        $page->setBreadcrumbs($this->breadcrumbs());
    }

    /**
     * @return Breadcrumbs|null
     */
    public function breadcrumbs(): ?Breadcrumbs
    {
        $module = $this->form->getModule();
        $breadcrumbs = $module->breadcrumbs();
        /**
         * @var $node Node
         */
        $node = $this->form->getModel();

        foreach ($this->getParents($node) as $parent) {
            $breadcrumbs->addItem($parent->name, $module->url('edit', $parent->getKey()));
        }

        $breadcrumbs->addItem(
            $this->form->getTitle(),
            $node->getKey()
                ? $module->url('edit', $node->getKey())
                : $module->url('create')
        );

        return $breadcrumbs;
    }


    /**
     * @param  Node  $node
     *
     * @return Collection
     */
    protected function getParents(Node $node): Collection
    {
        if ($node->exists) {
            return $node->ancestors()->get();
        }

        if ($parentId = $node->getAttribute('parent_id')) {
            $parentNode = $node->newQuery()->findOrFail($parentId);

            return $parentNode->ancestors()->get()->merge([$parentNode]);
        }

        return collect();
    }


}