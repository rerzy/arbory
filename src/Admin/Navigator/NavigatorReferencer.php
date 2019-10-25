<?php


namespace Arbory\Base\Admin\Navigator;


use Arbory\Base\Html\Elements\Element;
use Illuminate\Database\Eloquent\Model;

class NavigatorReferencer
{
    /**
     * @var Navigator
     */
    protected $navigator;

    /**
     * NavigatorReferencer constructor.
     *
     * @param  Navigator  $navigator
     */
    public function __construct(Navigator $navigator)
    {
        $this->navigator = $navigator;
    }

    /**
     * @param  NavigableItemInterface  $navigable
     * @param $contents
     *
     * @return mixed
     */
    public function reference(NavigableItemInterface $navigable, $contents) {
        $item = $this->navigator->findByNavigableItemDeep($navigable);

        if(! $item) {
            return $contents;
        }

        if($contents instanceof Element) {
            $contents->addAttributes([
                'data-navigator-reference' => $item->getReference()
            ]);
        }

        return $contents;
    }
}