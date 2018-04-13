<?php

namespace Arbory\Base\Admin;

use Arbory\Base\Admin\Form\Builder;
use Arbory\Base\Admin\Form\FieldSet;
use Arbory\Base\Admin\Form\Fields\FieldInterface;
use Arbory\Base\Admin\Form\Validator;
use Arbory\Base\Admin\Traits\EventDispatcher;
use Arbory\Base\Content\Relation;
use Arbory\Base\Html\Elements\Element;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class Form
 * @package Arbory\Base\Admin
 */
class Form implements Renderable
{
    use ModuleComponent;
    use EventDispatcher;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var FieldSet
     */
    protected $fields;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * Form constructor.
     * @param Model $model
     * @param $callback
     */
    public function __construct( Model $model, $callback )
    {
        $this->model = $model;
        $this->fields = new FieldSet( $model, 'resource' );
        $this->builder = new Builder( $this );
        $this->validator = app( Validator::class );

        $callback( $this );

        $this->registerEventListeners();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->render();
    }

    /**
     * @param $title
     * @return Form
     */
    public function title( $title )
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    public function getTitle()
    {
        if( $this->title === null )
        {
            $this->title = ( $this->model->getKey() )
                ? (string) $this->model
                : trans( 'arbory::resources.create_new' );
        }

        return $this->title;
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return FieldSet
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * @param FieldInterface $field
     * @return FieldInterface
     */
    public function addField( FieldInterface $field )
    {
        $this->fields()->push( $field );

        return $field;
    }

    /**
     * @param Request $request
     */
    public function store( Request $request )
    {
        $this->trigger( 'create.before', $request );

        $this->model->save();

        $this->trigger( 'create.after', $request );

        $this->model->push();
    }

    /**
     * @param Request $request
     */
    public function update( Request $request )
    {
        $this->trigger( 'update.before', $request );

        $this->model->save();

        $this->trigger( 'update.after', $request );

        $this->model->push();
    }

    /**
     *
     */
    public function destroy()
    {
        $this->trigger( 'delete.before', $this );

        $this->model->delete();

        $this->model->morphMany( Relation::class, 'related' )->get()->each( function( Relation $relation )
        {
            $relation->delete();
        });

        $this->trigger( 'delete.after', $this );
    }

    /**
     * @return Validator
     */
    public function validate()
    {
        $this->trigger( 'validate.before', request() );

        $this->validator->setRules($this->fields->getRules());
        $this->validator->validate($this->validator->rules());

        return $this->validator;
    }

    /**
     * @return void
     */
    protected function registerEventListeners()
    {
        $this->addEventListeners( [ 'create.before', 'update.before' ],
            function ( $request )
            {
                foreach( $this->fields() as $field )
                {
                    $field->beforeModelSave( $request );
                }
            }
        );

        $this->addEventListeners( [ 'create.after', 'update.after' ],
            function ( $request )
            {
                foreach( $this->fields() as $field )
                {
                    $field->afterModelSave( $request );
                }
            }
        );
    }

    /**
     * @param $action
     * @return $this
     */
    public function setAction( $action )
    {
        $this->builder->setAction( $action );

        return $this;
    }

    /**
     * @return \Arbory\Base\Html\Elements\Content|Element
     */
    public function render()
    {
        return $this->builder->render();
    }
}
