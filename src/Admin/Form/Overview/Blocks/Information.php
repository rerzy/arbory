<?php

namespace Arbory\Base\Admin\Form\Overview\Blocks;


use Arbory\Base\Admin\Form\Fields\FieldInterface;
use Arbory\Base\Html\Html;
use Arbory\Base\Nodes\Node;
use Arbory\Base\Support\Nodes\NameGenerator;
use Illuminate\Contracts\Support\Renderable;
use Arbory\Base\Admin\Form\Overview\Navigation\Navigable;
use Illuminate\Support\Collection;

/**
 * Class Navigation
 * @package Arbory\Base\Admin\Form\Overview\Blockss
 */
class Information extends Block implements Renderable
{
    /**
     * Navigation constructor.
     *
     * @param  string  $name
     */
    public function __construct(string $name = 'general_information')
    {
        parent::__construct($name);
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $form = $this->overview->getForm();
        $model = $form->getModel();

        $contentType = null;
        $createdAt = $model->getAttribute($model->getCreatedAtColumn());
        $updatedAt = $model->getAttribute($model->getUpdatedAtColumn());
        $isNode = false;

        if($model instanceof Node) {
            $isNode = true;
            $contentType = $this->formatContentType($model->getContentType());
        }

        return view('arbory::blocks.general_information', [
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
            'contentType' => $contentType,
            'isNode' => $isNode,
        ]);
    }

    public function beforeModelSave()
    {

    }

    /**
     * @param  string  $contentType
     *
     * @return string
     */
    protected function formatContentType(string $contentType): string
    {
        return app(NameGenerator::class)->generate($contentType);
    }
}

