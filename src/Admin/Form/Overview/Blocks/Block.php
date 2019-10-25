<?php

namespace Arbory\Base\Admin\Form\Overview\Blocks;


use Arbory\Base\Html\Elements\Element;
use Arbory\Base\Admin\Form\Overview;

abstract class Block
{
    /**
     * @var Overview
     */
    protected $overview;

    /**
     * @var string
     */
    protected $name;

    /**
     * Status constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->setName($name);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param  Overview  $overview
     *
     * @return Block
     */
    public function setOverview(Overview $overview)
    {
        $this->overview = $overview;

        return $this;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|Element|string
     */
    abstract public function render();
}

