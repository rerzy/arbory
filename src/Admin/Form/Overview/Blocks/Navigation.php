<?php

namespace Arbory\Base\Admin\Form\Overview\Blocks;


use Arbory\Base\Admin\Form\Fields\FieldInterface;
use Arbory\Base\Html\Html;
use Illuminate\Contracts\Support\Renderable;
use Arbory\Base\Admin\Form\Overview\Navigation\Navigable;
use Illuminate\Support\Collection;

/**
 * Class Navigation
 * @package Arbory\Base\Admin\Form\Overview\Blockss
 */
class Navigation extends Block implements Renderable
{
    /**
     * Navigation constructor.
     *
     * @param  string  $name
     */
    public function __construct(string $name = 'navigation')
    {
        parent::__construct($name);
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $links = Html::div();

        return view('arbory::blocks.navigation', [
            'links' => $this->overview->getForm()->getNavigator()->render()
        ]);
    }

    public function beforeModelSave()
    {

    }
}

