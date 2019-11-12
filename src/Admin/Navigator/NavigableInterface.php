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
     * @return Item|null
     */
    public function registerNavigatorItem(Navigator $navigator): ?Item;
}
