<?php

namespace Arbory\Base\Admin\Form\Overview\Navigation;


use Arbory\Base\Admin\Form\Fields\AbstractField;
use Arbory\Base\Html\Elements\Element;
use Illuminate\Contracts\Support\Renderable;

abstract class AbstractNavigation implements Renderable
{

    /**
     * @var AbstractField
     */
    protected $field;

    /**
     * AbstractNavigation constructor.
     * @param AbstractField $field
     */
    public function __construct(AbstractField $field)
    {
        $this->field = $field;
    }

    /**
     * @return Element
     */
    abstract public function render(): Element;
}

