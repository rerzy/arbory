<?php

namespace Arbory\Base\Admin\Navigator;

interface NavigableInterface extends NavigableItemInterface
{
    /**
     * @return bool
     */
    public function isNavigable(): bool;

    /**
     * @param  Navigator  $navigator
     *
     * @return mixed
     */
    public function navigator(Navigator $navigator): void;
}
