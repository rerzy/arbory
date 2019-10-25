<?php

namespace Arbory\Base\Admin\Form\Fields\Styles;

use Arbory\Base\Admin\Form;
use Arbory\Base\Admin\Layout\LayoutManager;
use Arbory\Base\Admin\Navigator\NavigableInterface;
use Arbory\Base\Admin\Navigator\Navigator;
use Arbory\Base\Admin\Page;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application;
use Arbory\Base\Admin\Form\Fields\FieldInterface;
use Arbory\Base\Admin\Form\Fields\Renderer\GenericRenderer;
use Arbory\Base\Admin\Form\Fields\Renderer\Styles\FieldStyleInterface;
use Arbory\Base\Admin\Form\Fields\Renderer\Styles\Options\StyleOptions;
use Arbory\Base\Admin\Form\Fields\Renderer\Styles\Options\StyleOptionsInterface;

class StyleManager
{

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $styles;

    /**
     * @var string
     */
    protected $defaultStyle;
    
    /**
     * @var Container
     */
    protected $container;

    /**
     * StyleManager constructor.
     *
     * @param  Container  $container
     * @param  array  $styles
     * @param             $defaultStyle
     */
    public function __construct(Container $container, array $styles, ?string $defaultStyle)
    {
        $this->styles = collect($styles);
        $this->defaultStyle = $defaultStyle;
        $this->container = $container;
    }

    /**
     * @param string $name
     * @param        $class
     *
     * @return $this
     */
    public function addStyle(string $name, $class)
    {
        $this->styles->put($name, $class);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function removeStyle(string $name)
    {
        $this->styles->forget($name);

        return $this;
    }

    /**
     * @param string $name
     * @param FieldInterface $field
     * @param StyleOptionsInterface|null $options
     *
     * @return mixed|null
     */
    public function render(string $name, FieldInterface $field, ?StyleOptionsInterface $options = null)
    {

        if ($this->styles->has($name)) {
            /** @var FieldStyleInterface $style */
            $style = $this->container->make(
                $this->styles->get($name)
            );

            $options = $options ?: $this->newOptions();

            if ($renderer = $field->getRenderer()) {
                $options = $field->getRenderer()->configure($options);
            } else {
                $renderer = new GenericRenderer();

                $renderer->setField($field);
            }

            $field->beforeRender($renderer);
            $contents = $style->render($renderer, $options);
            $field->afterRender($renderer, $contents);


            return $contents;
        } else {
            throw new \InvalidArgumentException("Unknown field style '{$name}'");
        }
    }

    /**
     * @return string
     */
    public function getDefaultStyle(): string
    {
        return $this->defaultStyle;
    }

    /**
     * @param string $defaultStyle
     */
    public function setDefaultStyle(string $defaultStyle): void
    {
        $this->defaultStyle = $defaultStyle;
    }

    public function newOptions()
    {
        return new StyleOptions();
    }
}
