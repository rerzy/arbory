<?php

namespace Arbory\Base\Admin\Form\Fields\Renderer;

use Arbory\Base\Admin\Form\Fields\Renderer\ControlFieldRenderer;
use Arbory\Base\Html\Elements\Element;
use Arbory\Base\Html\Html;

/**
 * Class CheckBoxFieldRenderer
 * @package Arbory\Base\Admin\Form\Fields\Renderer
 */
class RadioFieldRenderer extends ControlFieldRenderer
{
    /**
     * @var \Arbory\Base\Admin\Form\Fields\Checkbox
     */
    protected $field;

    /**
     * @return Element
     */
    public function render()
    {
        $control = $this->getControl();
        $control = $this->configureControl($control);
        $control->addAttributes([
            'id' => $this->field->getCheckedValue()
        ]);

        $element = $control->element();

        $element->setValue($this->field->getCheckedValue());

        $control->setChecked(
            $this->field->getValue() == $this->field->getCheckedValue()
        );

        return Html::div([
            $control->render($element),
            Html::label($this->field->getLabel())->addAttributes(['for' => $this->field->getCheckedValue()])
        ])->addClass('value');
    }
}
