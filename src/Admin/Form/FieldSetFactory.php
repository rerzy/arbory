<?php

namespace Arbory\Base\Admin\Form;

use Illuminate\Database\Eloquent\Model;
use Arbory\Base\Admin\Form\Fields\Styles\StyleManager;

class FieldSetFactory
{
    /**
     * @var StyleManager
     */
    protected $styleManager;

    /**
     * FieldSetFactory constructor.
     *
     * @param StyleManager $styleManager
     */
    public function __construct(StyleManager $styleManager)
    {
        $this->styleManager = $styleManager;
    }

    /**
     * @param Model $model
     * @param string|null $namespace
     * @param string|null $defaultStyle
     *
     * @return FieldSet
     */
    public function make(Model $model, $namespace = null, $defaultStyle = null)
    {
        $fieldSet = $this->newFieldSet($model, $namespace);

        $fieldSet->setStyleManager($this->styleManager);
        $fieldSet->setDefaultStyle($defaultStyle ?: $this->styleManager->getDefaultStyle());

        return $fieldSet;
    }

    /**
     * @param  Model  $model
     * @param  string  $namespace
     *
     * @return FieldSet
     */
    protected function newFieldSet(Model $model, string $namespace): FieldSet
    {
        return new FieldSet($model, $namespace);
    }
}
