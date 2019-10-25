<?php

namespace Arbory\Base\Admin\Form\Fields;


use Arbory\Base\Admin\Form\Controls\RadioControl;
use Arbory\Base\Admin\Form\Fields\Checkbox;
use Arbory\Base\Admin\Form\Fields\Renderer\RadioFieldRenderer;
use Illuminate\Http\Request;

/**
 * Class Checkbox
 * @package Arbory\Base\Admin\Form\Fields
 */
class Radio extends Checkbox
{
    /**
     * @var string
     */
    protected $rendererClass = RadioFieldRenderer::class;

    /**
     * @var string
     */
    protected $control = RadioControl::class;

    /**
     * @var boolean
     */
    protected $selected = false;

    /**
     * @param Request $request
     */
    public function beforeModelSave(Request $request)
    {
        if($this->isHidden() || $this->isDisabled()) {
            return;
        }

        $value = $request->has($this->getNameSpacedName()) ?: false;

        $this->getModel()->setAttribute($this->getName(), $value);
    }
}
