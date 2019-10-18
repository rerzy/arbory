<?php

namespace Arbory\Base\Admin\Form\Overview\Blocks;

use Arbory\Base\Admin\Form\Fields\Constructor as ConstructorField;
use Arbory\Base\Admin\Form\Fields\HasOne;
use Arbory\Base\Admin\Form\Fields\Renderer\Nested\PaneledItemRenderer;
use Arbory\Base\Services\FieldTypeRegistry;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;

/**
 * Class Constructor
 * @package Arbory\Base\Admin\Form\Overview\Blocks
 */
class Constructor extends Block implements Renderable
{
    /**
     * @var string
     */
    protected $modalId;

    /**
     * @var string
     */
    private $constructorFieldName;

    /**
     * @var \Closure
     */
    private $fieldConfigurator;

    /**
     * @var ConstructorField
     */
    private $field;

    /**
     * Constructor constructor.
     * @param string $name
     * @param string $constructorFieldName
     */
    public function __construct(string $name, string $constructorFieldName)
    {
        parent::__construct($name);
        $this->setModalId($this->getName());
        $this->constructorFieldName = $constructorFieldName;


        $this->fieldConfigurator = function () {
            $this->field->asPanels();
        };
    }

    /**
     * @param string $id
     */
    public function setModalId(string $id)
    {
        $this->modalId = $id;
    }

    /**
     * @return string
     */
    protected function getModalId()
    {
        return $this->modalId;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function dialog()
    {
        $constructor = $this->constructor();

        return view('arbory::dialogs.constructor_types', [
            'types' => $constructor->getTypes()->all(),
            'field' => implode('.', [
                $this->overview->getForm()->getNamespace(),
                $this->constructorFieldName
            ])
        ]);
    }

    /**
     * @return ConstructorField
     */
    public function constructor(): ConstructorField
    {
        $form = $this->overview->getForm();

        if ($this->field === null) {
            $field = $form->fields()->findFieldByInputName($this->constructorFieldName);

            if ($field === null) {
                throw new \RuntimeException('Constructor field must be present in constructor layout');
            }

            if ($field) {
                $this->field = $field;
            } else {
                /**
                 * @var FieldTypeRegistry
                 */
                $registry = app(FieldTypeRegistry::class);

                $this->field = $registry->resolve('constructor', [$this->constructorFieldName]);
            }
        }

        call_user_func($this->fieldConfigurator);

        return $this->field;
    }


    /**
     * @return \Arbory\Base\Html\Elements\Element|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function render()
    {
        if (!$this->constructor()) {
            return null;
        }

        return view('arbory::blocks.constructor', [
            'modalId' => $this->getModalId(),
            // TODO: Find out why dialog returns the modal for status block when returning view object
            'dialog' => $this->dialog()->render()
        ]);
    }

    public function beforeModelSave()
    {
    }
}

