<?php

namespace Arbory\Base\Http\Controllers\Admin;

use Arbory\Base\Admin\Form;
use Arbory\Base\Admin\Grid;
use Arbory\Base\Admin\Page;
use Arbory\Base\Nodes\Admin\Form\Layout\FormLayout;
use Arbory\Base\Nodes\Node;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Arbory\Base\Admin\Layout;
use Illuminate\Routing\Controller;
use Illuminate\Container\Container;
use Arbory\Base\Admin\Form\FieldSet;
use Arbory\Base\Admin\Traits\Crudify;
use Illuminate\Http\RedirectResponse;
use Arbory\Base\Html\Elements\Content;
use Arbory\Base\Admin\Tools\ToolboxMenu;
use Arbory\Base\Nodes\Admin\Grid\Filter;
use Arbory\Base\Nodes\Admin\Grid\Renderer;
use Arbory\Base\Nodes\ContentTypeRegister;
use Arbory\Base\Admin\Layout\LayoutManager;
use Arbory\Base\Nodes\ContentTypeDefinition;
use Arbory\Base\Support\Nodes\NameGenerator;
use Arbory\Base\Admin\Layout\LayoutInterface;
use Arbory\Base\Repositories\NodesRepository;
use Arbory\Base\Admin\Form\Fields\Deactivator;
use Arbory\Base\Admin\Constructor\BlockRegistry;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class NodesController extends Controller
{
    use Crudify;

    protected $resource = Node::class;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var ContentTypeRegister
     */
    protected $contentTypeRegister;

    /**
     * @param Container           $container
     * @param ContentTypeRegister $contentTypeRegister
     */
    public function __construct(
        Container $container,
        ContentTypeRegister $contentTypeRegister
    ) {
        $this->container = $container;
        $this->contentTypeRegister = $contentTypeRegister;
    }

    /**
     * @param Model $model
     * @param Layout\FormLayoutInterface|null $layout
     *
     * @return Form
     */
    protected function buildForm(Model $model, ?Layout\FormLayoutInterface $layout = null)
    {
        $form = new Form($model);
        $form->setModule($this->module());
        $form->setRenderer(new Form\Builder($form));


        if ($layout) {
            $layout->setForm($form);

            $layout->setOverview($this->makeOverview($form, $layout));
        }

        return $this->form($form, $layout) ?: $form;
    }


    /**
     * @param Form            $form
     * @param LayoutInterface $layout
     *
     * @return Form
     */
    protected function form(Form $form, ?LayoutInterface $layout)
    {
        $definition = $this->resolveContentDefinition($form);

        $definition->getLayoutHandler()->call($this, $form, $layout);

        $form->setFields(function (FieldSet $fields) use ($layout, $definition) {
            $fields->hidden('parent_id');
            $fields->hidden('content_type');
            $fields->text('name')->rules('required');
            $fields->slug('slug', 'name', $this->getSlugGeneratorUrl())->rules('required');
            $fields->text('meta_title');
            $fields->text('meta_author');
            $fields->text('meta_keywords');
            $fields->text('meta_description');

            $fields->hasOne('content', $this->contentResolver($definition, $layout));
        });

        /**
         * @var Node
         */
        $node = $form->fields()->getModel();
        $contentType = $node->getContentType();

        if ($contentType) {
            $form->title(sprintf('%s (%s)', $form->getTitle(), $this->makeNameFromType($contentType)));
        }

        $form->addEventListeners(['create.after'], function () use ($form) {
            $this->afterSave($form);
        });

        return $form;
    }

    /**
     * @param Grid $grid
     *
     * @return Grid
     */
    public function grid(Grid $grid)
    {
        $grid->setColumns(function (Grid $grid) {
            $grid->column('name');
        });
        $grid->setFilter(new Filter($this->resource()));
        $grid->setRenderer(new Renderer($grid));

        return $grid;
    }

    /**
     * @param \Arbory\Base\Admin\Tools\ToolboxMenu $tools
     */
    protected function toolbox(ToolboxMenu $tools)
    {
        $node = $tools->model();

        $tools->add(
            'add_child',
            $this->url('dialog', ['dialog' => 'content_types', 'parent_id' => $node->getKey()])
        )->dialog();
        $tools->add(
            'delete',
            $this->url('dialog', ['dialog' => 'confirm_delete', 'id' => $node->getKey()])
        )->danger()->dialog();
    }

    /**
     * @param Request       $request
     * @param LayoutManager $manager
     *
     * @return RedirectResponse|Layout
     */
    public function create(Request $request, LayoutManager $manager)
    {
        $contentType = $request->get('content_type');

        if (! $this->contentTypeRegister->isValidContentType($contentType)) {
            return redirect($this->url('index'))->withErrors('Undefined content type "'.$contentType.'"');
        }

        $node = $this->resource();
        $node->setAttribute('content_type', $contentType);
        $node->setAttribute('content_id', 0);

        if ($request->has('parent_id')) {
            $node->setAttribute($node->getParentColumnName(), $request->get('parent_id'));
        }

        $layout = $this->layout('form');
        $layout->setForm($this->buildForm($node, $layout));

        $page = $manager->page(Page::class);
        $page->use($layout);
        $page->bodyClass('controller-'.Str::slug($this->module()->name()).' view-edit');

        return $page;
    }

    /**
     * @param Form $form
     */
    protected function afterSave(Form $form)
    {
        /**
         * @var $node Node
         */
        $node = $form->getModel();

        $parentId = $node->getAttribute($node->getParentColumnName());

        if ($parentId) {
            $parent = $node->find($parentId);
            $node->makeChildOf($parent);

            return;
        }

        $node->makeRoot();
    }

    /**
     * @return Node
     */
    public function resource()
    {
        $class = $this->resource;

        return new $class;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\View\View
     */
    public function contentTypesDialog(Request $request)
    {
        $contentTypes = $this->contentTypeRegister->getAllowedChildTypes(
            $this->resource()->findOrNew($request->get('parent_id'))
        );

        $types = $contentTypes->sort()->map(function (ContentTypeDefinition $definition, string $type) use ($request) {
            return [
                'title' => $definition->getName(),
                'url'   => $this->url('create', [
                    'content_type' => $type,
                    'parent_id'    => $request->get('parent_id'),
                ]),
            ];
        });

        return view('arbory::dialogs.content_types', ['types' => $types]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    protected function nodeRepositionApi(Request $request)
    {
        /**
         * @var NodesRepository
         * @var Node            $node
         */
        $nodes = new NodesRepository;
        $node = $nodes->findOneBy('id', $request->input('id'));
        $toLeftId = $request->input('toLeftId');
        $toRightId = $request->input('toRightId');

        if ($toLeftId) {
            $node->moveToRightOf($nodes->findOneBy('id', $toLeftId));
        } elseif ($toRightId) {
            $node->moveToLeftOf($nodes->findOneBy('id', $toRightId));
        }

        return response()->make();
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    protected function slugGeneratorApi(Request $request)
    {
        $reservedSlugs = [];

        if ($request->has('parent_id')) {
            $reservedSlugs = $this->resource()
                                  ->where([
                                      ['parent_id', $request->get('parent_id')],
                                      ['id', '<>', $request->get('object_id')],
                                  ])
                                  ->pluck('slug')
                                  ->toArray();
        }

        $from = $request->get('from');
        $slug = Str::slug($from);

        if (in_array($slug, $reservedSlugs, true) && $request->has('id')) {
            $slug = Str::slug($request->get('id').'-'.$from);
        }

        if (in_array($slug, $reservedSlugs, true)) {
            $slug = Str::slug($from.'-'.random_int(0, 9999));
        }

        return $slug;
    }

    /**
     * @return string
     */
    protected function getSlugGeneratorUrl()
    {
        return $this->url('api', 'slug_generator');
    }

    protected function constructorTypesDialog(Request $request)
    {
        return view('arbory::dialogs.constructor_types', [
            'types' => app(BlockRegistry::class)->all(),
            'field' => $request->get('field'),
        ]);
    }

    /**
     * Creates a closure for content field.
     *
     * @param ContentTypeDefinition $definition
     * @param LayoutInterface|null  $layout
     *
     * @return \Closure
     */
    protected function contentResolver(ContentTypeDefinition $definition, ?LayoutInterface $layout)
    {
        return function (FieldSet $fieldSet) use ($layout, $definition) {
            $content = $fieldSet->getModel();

            $definition->getFieldSetHandler()->call($content, $fieldSet, $layout);
        };
    }

    /**
     * Resolves content type based on the current model & form data.
     *
     * @param Form $form
     *
     * @return ContentTypeDefinition
     */
    protected function resolveContentDefinition(Form $form): ContentTypeDefinition
    {
        $model = $form->getModel();
        $morphType = $model->content()->getMorphType();
        // Find content type from model
        $attribute = $model->getAttribute($morphType);

        // Find it from request otherwise
        if ($attribute === null) {
            $attribute = \request()->input("{$form->getNamespace()}.{$morphType}");
        }

        $class = ( new \ReflectionClass($attribute) )->getName();
        $definition = $this->contentTypeRegister->findByModelClass($class);

        return $definition;
    }

    /**
     * @param string $type
     * @return string
     */
    protected function makeNameFromType($type): string
    {
        return $this->container->get(NameGenerator::class)->generate($type);
    }

    /**
     * @param Form $form
     * @return Form\Overview
     */
    private function makeOverview(Form $form, ?Form\OverviewLayout $layout = null)
    {
        $overview = $layout->getOverview();

        $definition = $this->resolveContentDefinition($form);

        if ($definition->hasOverviewHandler()) {
            $definition->getOverviewHandler()->call($this, $overview, $layout);
        }

        return $overview;
    }

    /**
     * Defined layouts.
     *
     * @return array
     */
    public function layouts()
    {
        return [
            'grid' => Grid\Layout::class,
            'form' => FormLayout::class,
        ];
    }
}
