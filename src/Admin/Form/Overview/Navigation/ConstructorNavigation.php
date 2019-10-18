<?php

namespace Arbory\Base\Admin\Form\Overview\Navigation;


use Arbory\Base\Html\Elements\Element;
use Arbory\Base\Html\Html;
use Arbory\Base\Admin\Form\Fields\Constructor;

class ConstructorNavigation extends AbstractNavigation
{

    /**
     * @var Constructor
     */
    protected $field;

    /**
     * @return Element
     */
    public function render(): Element
    {
        $content = Html::div();

        foreach ($this->field->getValue() as $block) {
            $model = $block->block()->getRelated();
            $fieldSet = $this->field->getRelationFieldSet($block, $block->sequence_no);

            $content->append(Html::link($this->formatBlockTypeName(get_class($model)))->addAttributes([
                'href' => '#' . $fieldSet->getNamespace()
            ]));
        }

        return $content;
    }

    /**
     * @param string $type
     * @return string
     */
    private function formatBlockTypeName(string $type)
    {
        $class = array_last(explode('\\', $type));

        $split = preg_split('/(?<=[a-z])(?=[A-Z])/x', $class);
        return join($split, ' ');
    }
}

