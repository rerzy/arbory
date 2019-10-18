<?php

namespace Arbory\Base\Nodes\Admin\Form\Layout;

use Arbory\Base\Admin\Constructor\ConstructorLayout;
use Arbory\Base\Admin\Form\Layout;
use Arbory\Base\Admin\Form\Layout as BaseFormLayout;
use Arbory\Base\Admin\Form\Overview;
use Arbory\Base\Admin\Form\Widgets\Controls;
use Arbory\Base\Admin\Layout\Footer\Tools;
use Arbory\Base\Admin\Layout\PanelLayout;
use Arbory\Base\Admin\Layout\Transformers\AppendTransformer;
use Arbory\Base\Admin\Layout\Transformers\WrapTransformer;
use Arbory\Base\Admin\Layout\WrappableInterface;
use Arbory\Base\Admin\Panels\FormPanel;
use Arbory\Base\Admin\Widgets\Breadcrumbs;
use Arbory\Base\Nodes\Node;
use Illuminate\Support\Collection;

class FormLayout extends ConstructorLayout
{
    /**
     * @var Overview
     */
    protected $overview;

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

    public function contents($content)
    {
        /**
         * @var WrappableInterface
         */
        $renderer = $this->form->getRenderer();

        $renderer->setContent($content);

        return $renderer;
    }

    /**
     * @param  Overview  $overview
     *
     * @return FormLayout
     */
    public function setOverview(Overview $overview): FormLayout
    {
        $this->overview = $overview;

        return $this;
    }

    public function overview()
    {
        return $this->overview->render();
    }

    public function build()
    {
        parent::build();

        $this->use(
            new AppendTransformer(
                new Controls(new Tools(), $this->form->getModule()->url('index'))
            )
        );


        $this->use(
            new WrapTransformer(
                (new FormPanel())->setForm($this->form)
            )
        );
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