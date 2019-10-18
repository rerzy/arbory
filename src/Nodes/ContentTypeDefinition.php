<?php

namespace Arbory\Base\Nodes;

use Closure;

class ContentTypeDefinition
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $model;

    /**
     * @var Closure
     */
    protected $fieldSetHandler;

    /**
     * @var Closure
     */
    protected $layoutHandler;

    /**
     * @var Closure
     */
    protected $overviewHandler;


    /**
     * @param string $model
     */
    public function __construct(string $model)
    {
        $this->model = $model;
        $this->name = $this->makeNameFromType($model);

        $this->layoutHandler = function () {
        };
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @return Closure
     */
    public function getFieldSetHandler(): Closure
    {
        return $this->fieldSetHandler;
    }

    /**
     * @param Closure $fieldSetHandler
     */
    public function setFieldSetHandler(Closure $fieldSetHandler)
    {
        $this->fieldSetHandler = $fieldSetHandler;
    }

    /**
     * @param Closure $layoutHandler
     */
    public function setLayoutHandler(Closure $layoutHandler): void
    {
        $this->layoutHandler = $layoutHandler;
    }

    /**
     * @return Closure
     */
    public function getLayoutHandler(): Closure
    {
        return $this->layoutHandler;
    }

    /**
     * @return Closure
     */
    public function getOverviewHandler(): Closure
    {
        return $this->overviewHandler;
    }

    /**
     * @param Closure $overviewHandler
     */
    public function setOverviewHandler(Closure $overviewHandler)
    {
        $this->overviewHandler = $overviewHandler;
    }

    /**
     * @return bool
     */
    public function hasOverviewHandler(): bool
    {
        return isset($this->overviewHandler);
    }

    /**
     * @param string $type
     * @return string
     */
    protected function makeNameFromType($type): string
    {
        $className = class_basename($type);
        $title = preg_replace('/Page$/', '', $className);

        return implode(
            ' ',
            preg_split(
                '/(?<=[a-z])(?=[A-Z])|(?=[A-Z][a-z])/',
                $title,
                -1,
                PREG_SPLIT_NO_EMPTY
            )
        );
    }
}
