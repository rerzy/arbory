<?php

namespace Arbory\Base\Admin\Form\Fields\Concerns;

use Arbory\Base\Admin\Form\Fields\FieldInterface;
use Arbory\Base\Admin\Form\FieldSet;
use Arbory\Base\Services\FieldTypeRegistry;
use Arbory\Base\Admin\Form\Fields\Translatable;

/**
 * Trait IsTranslatable
 * @package Arbory\Base\Admin\Form\Fields\Concerns
 *
 * @mixin FieldInterface
 */
trait IsTranslatable
{
    /**
     * Set the field as translatable.
     *
     * @return Translatable|\Arbory\Base\Admin\Form\Fields\FieldInterface
     */
    public function translatable()
    {
        /**
         * @var FieldTypeRegistry $registry
         */
        $registry = app(FieldTypeRegistry::class);
        $translatable = $registry->resolve('translatable', [clone $this]);

        /**
         * @var $fieldSet FieldSet
         */
        $fieldSet = $this->getFieldSet();

        $translatable->setFieldSet($fieldSet);
        $fieldSet->overwrite($this, $translatable);

        return $translatable;
    }
}
