<?php

namespace Arbory\Base\Admin\Form;


use Arbory\Base\Admin\Form\Overview\Blocks\Block;
use Arbory\Base\Admin\Form;
use Arbory\Base\Html\Elements\Element;
use Arbory\Base\Html\Html;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

/**
 * Class Overview
 * @package App\Admin\Form
 */
class Overview implements Renderable
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * @var Collection|Block[]
     */
    protected $blocks;

    /**
     * Overview constructor.
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->blocks = collect();
        $this->form = $form;

        $this->form->addEventListeners(['create.before', 'update.before'], function (Request $request) {

            foreach ($this->blocks as $block) {
                $block->beforeModelSave($request);
            }
        });
    }

    /**
     * @return Element|string
     */
    public function render()
    {
        $blocks = Html::div();
        foreach ($this->blocks as $block) {
            $blocks->append($block->render());
        }
        return view('arbory::blocks.overview', [
            'blocks' => $blocks
        ]);
    }

    /**
     * @param  Block  $block
     *
     * @return Overview
     */
    public function add(Block $block): self
    {
        $block->setOverview($this);
        $this->blocks->push($block);

        return $this;
    }

    /**
     * @return Form
     */
    public function getForm(): Form
    {
        return $this->form;
    }

    /**
     * @return Collection|Block[]
     */
    public function getBlocks()
    {
        return $this->blocks;
    }
}

