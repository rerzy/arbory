<?php

namespace Arbory\Base\Admin\Constructor;

use Arbory\Base\Admin\Form;
use Arbory\Base\Admin\Layout\LazyRenderer;
use Arbory\Base\Admin\Layout\AbstractLayout;
use Arbory\Base\Admin\Layout\FormLayoutInterface;
use Arbory\Base\Admin\Layout\Transformers\AppendTransformer;

class ConstructorLayout extends AbstractLayout implements FormLayoutInterface
{
    /**
     * @var string
     */
    protected $modalUrl;

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var Form\Fields\Constructor
     */
    protected $field;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var callable
     */
    protected $fieldConfigurator;

    /**
     * ConstructorLayout constructor.
     *
     * @param  string  $name
     */
    public function __construct($name = 'blocks')
    {
        $this->name = $name;
        $this->fieldConfigurator = function () {
            $this->field->asPanels();
        };

    }

    /**
     * @return Form
     */
    public function getForm(): Form
    {
        return $this->form;
    }

    /**
     * @param  Form  $form
     *
     * @return FormLayoutInterface
     */
    public function setForm(Form $form): FormLayoutInterface
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Executes every time before render.
     *
     * @return mixed
     */
    public function build()
    {
        $this->use(new AppendTransformer(new LazyRenderer([$this, 'renderField'])));
    }

    /**
     * @param  mixed  $content
     *
     * @return mixed
     */
    public function contents($content)
    {
        return $content;
    }

    /**
     * @return Form\Fields\Constructor
     */
    public function getField(): Form\Fields\Constructor
    {
        if ($this->field === null) {
            $this->field = $this->form->fields()->findFieldByInputName($this->name);

            if ($this->field === null) {
                throw new \RuntimeException('Constructor field must be present in constructor layout');
            }
        }

        call_user_func($this->fieldConfigurator);

        return $this->field;
    }

    /**
     * @return mixed
     */
    public function renderField()
    {
        $constructor = $this->getField();

        if (! $constructor->getFieldSet()) {
            return null;
        }

        $styleManager = $constructor->getFieldSet()->getStyleManager();
        $opts = $styleManager->newOptions();

        return $styleManager->render('nested', $constructor, $opts);
    }

    /**
     * @return string
     */
    public function getModalUrl()
    {
        if ($this->modalUrl) {
            return $this->modalUrl;
        }

        return $this->getForm()->getModule()->url(
            'dialog',
            [
                'name' => 'constructor_types',
                'field' => $this->field->getNameSpacedName(),
            ]
        );
    }

    /**
     * @param $url
     *
     * @return ConstructorLayout
     */
    public function setModalUrl($url): self
    {
        $this->modalUrl = $url;

        return $this;
    }

    /**
     * @param  string  $name
     *
     * @return ConstructorLayout
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
