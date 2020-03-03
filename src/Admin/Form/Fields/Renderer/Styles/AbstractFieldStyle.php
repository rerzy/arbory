<?php

namespace Arbory\Base\Admin\Form\Fields\Renderer\Styles;

use Arbory\Base\Admin\Form\Fields\FieldInterface;
use Arbory\Base\Admin\Form\Fields\RenderOptionsInterface;
use Arbory\Base\Html\Html;
use Illuminate\Contracts\Support\Renderable;

abstract class AbstractFieldStyle
{
    /**
     * @param  FieldInterface  $field
     *
     * @return mixed
     */
    protected function renderField(FieldInterface $field)
    {
        $content = $field->render();

        if ($field instanceof RenderOptionsInterface) {
            if ($wrapper = $field->getWrapper()) {
                return $wrapper($content);
            }
        }

        return $content;
    }

    /**
     * @param  string|null  $content
     *
     * @return Renderable|string
     */
    protected function renderTooltip(?string $content)
    {
        return Html::div(
            Html::i()->addClass('fa fa-question-circle-o')
        )->addAttributes([
            'data-tippy-content' => $content,
        ])->addClass('tooltip');
    }
}
