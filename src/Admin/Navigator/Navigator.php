<?php

namespace Arbory\Base\Admin\Navigator;

use Arbory\Base\Html\Html;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Renderable;

class Navigator implements Renderable
{
    /**
     * @var Item[]|Collection
     */
    protected $items;

    public function __construct()
    {
        // TODO: Maybe start from item root?
        $this->items = new Collection();
    }

    /**
     * @param  Item  $item
     *
     * @return Navigator
     */
    public function add(Item $item): self
    {
        $this->items->push($item);

        return $this;
    }

    /**
     * @param  NavigableInterface  $navigable
     * @param                    $title
     * @param  null  $anchor
     *
     * @return Item
     */
    public function addItem(NavigableInterface $navigable, $title, $anchor = null): Item
    {
        $item = new Item($navigable, $title, $anchor);

        $this->add($item);

        return $item;
    }

    /**
     * @param  NavigableInterface  $navigable
     *
     * @return Collection
     */
    public function findByNavigable(NavigableInterface $navigable): Collection
    {
        return $this->items->filter(static function(Item $item) use($navigable) {
            return $item->getNavigable() === $navigable;
        });
    }

    public function findByNavigableItemDeep(NavigableItemInterface $navigableItem, ?Collection $collection = null):
    ?Item
    {
        $collection = $collection ?: $this->items;

        $return = null;

        $collection->each(function(Item $item) use($navigableItem, &$return) {
            if($item->getNavigable() === $navigableItem) {
                $return = $item;

                return false;
            }

            if($item->getChildren()->count()) {
                $return = $this->findByNavigableItemDeep($navigableItem, $item->getChildren());
            }
        });

        return $return;
    }

    /**
     * @return Item[]|Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Get the evaluated contents of the object.
     *
     * @return mixed
     */
    public function render()
    {
        $list = Html::ul()->addClass('navigator');

        foreach ($this->getItems() as $item) {
            $list->append($item->render());
        }

        return $list;
    }

    public function attachReference(NavigableItemInterface $navigable, $contents)
    {
        return (new NavigatorReferencer($this))->reference($navigable, $contents);
    }
}
