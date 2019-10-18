<?php

namespace Arbory\Base\Admin\Form\Overview\Blocks;


use Arbory\Base\Admin\Form\Fields\FieldInterface;
use Arbory\Base\Html\Html;
use Illuminate\Contracts\Support\Renderable;
use Arbory\Base\Admin\Form\Overview\Navigation\Navigable;

/**
 * Class Navigation
 * @package Arbory\Base\Admin\Form\Overview\Blockss
 */
class Navigation extends Block implements Renderable
{
    /**
     * @var array
     */
    protected $fieldNames;

    public function __construct(string $name = 'navigation')
    {
        parent::__construct($name);
    }

    /**
     * @param array $names
     * @return $this
     */
    public function setFieldNames(array $names)
    {
        $this->fieldNames = $names;

        return $this;
    }

    /**
     * @return \Arbory\Base\Html\Elements\Element|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function render()
    {
        $links = Html::div();

        $fields = $this->getFields();
        array_walk($fields, function (Navigable $field) use ($links) {
            $links->append($field->navigation()->render());
        });

        return view('arbory::blocks.navigation', [
            'links' => $links
        ]);
    }


    /**
     * @return array
     */
    protected function getFields()
    {
        return array_filter(array_map(function ($name) {
            return $this->overview->getForm()->fields()->findFieldByInputName($name);
        }, $this->fieldNames));
    }

    /**
     * @param FieldInterface $field
     * @return bool
     */
    protected function fieldHasRelationships(FieldInterface $field)
    {
        return method_exists($field, 'getRelatedModel');
    }

    public function beforeModelSave()
    {

    }
}

