<?php

namespace Arbory\Base\Admin\Form\Overview\Navigation;


use Arbory\Base\Html\Elements\Element;
use Arbory\Base\Html\Html;
use Arbory\Base\Admin\Form\Fields\Section;

class SectionNavigation extends AbstractNavigation
{

    /**
     * @var Section
     */
    protected $field;

    /**
     * @return Element
     */
    public function render(): Element
    {
        return Html::link(
            $this->formatBlockTypeName(get_class($this->field->getRelatedModel()))
        )->addAttributes([
            'href' => '#' . $this->field->getNameSpacedName()
        ]);
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

