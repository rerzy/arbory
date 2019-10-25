<?php

namespace Arbory\Base\Admin\Form;

use Arbory\Base\Admin\Form\Overview;
use Arbory\Base\Admin\Form\Widgets\Controls;
use Arbory\Base\Admin\Layout\Footer\Tools;
use Arbory\Base\Admin\Layout\Transformers\WrapTransformer;
use Arbory\Base\Admin\Layout\WrappableInterface;
use Arbory\Base\Admin\Panels\FormPanel;
use Arbory\Base\Admin\Form;
use Arbory\Base\Admin\Layout\Grid;
use Arbory\Base\Admin\Layout\GridLayout;
use Arbory\Base\Admin\Layout\LazyRenderer;
use Arbory\Base\Admin\Layout\AbstractLayout;
use Arbory\Base\Admin\Layout\FormLayoutInterface;
use Arbory\Base\Admin\Layout\Transformers\AppendTransformer;
use Arbory\Base\Html\Elements\Content;
use Arbory\Base\Nodes\Admin\Form\Layout\FormLayout;

class OverviewLayout extends AbstractLayout implements FormLayoutInterface
{
    public const SLOTS = [
        'content_bottom',
        'overview_bottom',
    ];

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var Overview|null
     */
    protected $overview;

    public function __construct(?Overview $overview = null)
    {
        $this->overview = $overview;
    }


    /**
     * @return Form
     */
    public function getForm(): Form
    {
        return $this->form;
    }

    /**
     * @param  Form  $form
     *
     * @return FormLayoutInterface
     */
    public function setForm(Form $form): FormLayoutInterface
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Executes every time before render.
     *
     * @return mixed
     */
    public function build()
    {
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

    public function contents($content)
    {
        /**
         * @var WrappableInterface
         */
        $renderer = $this->form->getRenderer();

        $gridLayout = new GridLayout(new Grid());
        $gridLayout->setWidth(9);
        $gridLayout->addColumn(3, new LazyRenderer([$this, 'renderOverview']));
        $gridLayout->build();
        

        return $renderer
            ->setContent(new Content([
                $gridLayout->contents($content),
                $this->slot('content_bottom'),
            ]))
            ->render();
    }

    /**
     * @return \Arbory\Base\Admin\Form\Overview|null
     */
    public function getOverview(): ?\Arbory\Base\Admin\Form\Overview
    {
        if (! $this->overview) {
            return $this->overview = $this->createOverview();
        }

        return $this->overview;
    }

    /**
     * @param  Overview  $overview
     *
     * @return FormLayout
     */
    public function setOverview(Overview $overview): self
    {
        $this->overview = $overview;

        return $this;
    }

    /**
     * @return \Arbory\Base\Html\Elements\Element|string
     */
    public function renderOverview()
    {
        return new Content([
            $this->overview->render(),
            $this->slot('overview_bottom')
        ]);
    }

    /**
     * @return \Arbory\Base\Admin\Form\Overview
     */
    protected function createOverview(): Overview
    {
        // TODO: Rework this
        $overview = new \Arbory\Base\Admin\Form\Overview(
            $this->getForm()
        );

        $overview->add(new Overview\Blocks\Status());
        $overview->add(new Overview\Blocks\Navigation());

        return $overview;
    }
}
