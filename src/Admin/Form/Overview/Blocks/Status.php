<?php

namespace Arbory\Base\Admin\Form\Overview\Blocks;


use Arbory\Base\Admin\Form\Fields\DateTime;
use Arbory\Base\Admin\Form\Fields\FieldInterface;
use Arbory\Base\Admin\Form\Fields\Styles\StyleManager;
use Arbory\Base\Admin\Form\FieldSet;
use Arbory\Base\Html\Elements\Content;
use Arbory\Base\Nodes\Node;
use Arbory\Base\Services\FieldTypeRegistry;
use Arbory\Base\Support\Activation\HasActivationDates;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Arbory\Base\Admin\Form\Fields\Radio;
use Arbory\Base\Admin\Form\Validator;
use Arbory\Base\Admin\Form\Overview;
use Illuminate\Support\Arr;

/**
 * Class Status
 * @package Arbory\Base\Admin\Form\Overview\Blocks
 */
class Status extends Block implements Renderable
{
    public const FIELD_PUBLISHED_STATUS = 'published_status';

    /**
     * @var string
     */
    protected $modalId;

    /**
     * @var StyleManager
     */
    protected $styleManager;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var Model|HasActivationDates
     */
    protected $model;

    /**
     * @var FieldTypeRegistry
     */
    protected $fieldTypeRegistry;

    /**
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i';

    /**
     * Status constructor.
     *
     * @param  string  $name
     */
    public function __construct($name = 'status')
    {
        parent::__construct($name);
        $this->setModalId($this->getName());

        $this->styleManager = app(StyleManager::class);
        $this->validator = app(Validator::class);
        $this->fieldTypeRegistry = app(FieldTypeRegistry::class);
    }

    /**
     * @param  Overview  $overview
     *
     * @return Status
     */
    public function setOverview(Overview $overview)
    {
        $this->overview = $overview;
        $this->model = $overview->getForm()->getModel();
        $this->createValidation();

        return $this;
    }

    public function createValidation()
    {
        $this->overview->getForm()->addEventListener('validate.before', function ($request) {
            $this->validator->setRules([
                $this->overview->getForm()->getNamespace().'.'.$this->model->getActivateAtColumnName() => 'nullable|date_format:' . $this->dateFormat,
                $this->overview->getForm()->getNamespace().'.'.$this->model->getExpireAtColumnName() => 'nullable|date_format:' . $this->dateFormat . '|after_or_equal:resource.'.
                    $this->model->getActivateAtColumnName(),
            ]);
            $this->validator->validate($this->validator->rules());
        });
    }

    /**
     * @return array
     */
    protected function getPublishedStatusNames()
    {
        return [
            Node::STATUS_PUBLISHED,
            Node::STATUS_UNPUBLISHED,
            Node::STATUS_PUBLISHED_AT_DATETIME,
        ];
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function dialog()
    {
        $fieldSet = new FieldSet($this->overview->getForm()->getModel(), 'status');
        $published = $this->getPublishedField()->setFieldSet($fieldSet)->setLabel(trans('arbory::dialog.status.publish_on_save'));
        $publishedAtDatetime = $this->getPublishedAtDatetimeField()->setFieldSet($fieldSet)->setLabel(trans('arbory::dialog.status.publish_at_datetime'));
        $unpublished = $this->getUnpublishedField()->setFieldSet($fieldSet)->setLabel(trans('arbory::dialog.status.unpublish_on_save'));
        $activateAt = $this->createDateTimeField($this->model->getActivateAtColumnName())->setFieldSet($fieldSet)->setValue
        ($this->getActivateAtField()
              ->getValue());
        $expireAt = $this->createDateTimeField($this->model->getExpireAtColumnName())->setFieldSet($fieldSet)->setValue
        ($this->getExpireAtField()
              ->getValue());


        return view('arbory::dialogs.status', [
            'published' => (string) $this->styleManager->render('basic', $published),
            'unpublished' => (string) $this->styleManager->render('basic', $unpublished),
            'publishedAtDatetime' => (string) $this->styleManager->render('basic', $publishedAtDatetime),
            'activateAt' => (string) $this->styleManager->render('normal', $activateAt),
            'expireAt' => (string) $this->styleManager->render('normal', $expireAt),
        ]);
    }

    /**
     * @param  string  $id
     */
    public function setModalId($id)
    {
        $this->modalId = $id;
    }

    /**
     * @return string^
     */
    protected function getModalId()
    {
        return $this->modalId;
    }

    /**
     * @return Content|string
     */
    public function render()
    {
        return view('arbory::blocks.status', [
            'modalId' => $this->getModalId(),
            'dialog' => $this->dialog(),
            'formId' => 'edit-resource',
            'published' => $this->getPublishedField()->render(),
            'publishedAtDatetime' => $this->getPublishedAtDatetimeField()->render(),
            'unpublished' => $this->getUnpublishedField()->render(),
            'activateAt' => $this->getActivateAtField()->render(),
            'expireAt' => $this->getExpireAtField()->render(),
            'currentStatus' => $this->overview->getForm()->getModel()->getAttribute($this::FIELD_PUBLISHED_STATUS) ??
                Node::STATUS_UNPUBLISHED,
            'options' => $this->getPublishedStatusNames(),
        ]);
    }

    /**
     * @return Radio
     */
    protected function getPublishedField()
    {
        return $this->createRadioField($this::FIELD_PUBLISHED_STATUS, Node::STATUS_PUBLISHED);
    }

    /**
     * @return Radio
     */
    protected function getPublishedAtDatetimeField()
    {
        return $this->createRadioField($this::FIELD_PUBLISHED_STATUS, Node::STATUS_PUBLISHED_AT_DATETIME);
    }

    /**
     * @return Radio
     */
    protected function getUnpublishedField()
    {
        return $this->createRadioField($this::FIELD_PUBLISHED_STATUS, Node::STATUS_UNPUBLISHED);
    }

    /**
     * @return DateTime
     */
    protected function getActivateAtField()
    {
        return $this->createDateTimeField($this->model->getActivateAtColumnName());
    }

    /**
     * @return DateTime
     */
    protected function getExpireAtField()
    {
        return $this->createDateTimeField($this->model->getExpireAtColumnName());
    }


    /**
     * @param  string  $name
     * @param  string  $value
     *
     * @return Radio
     */
    protected function createRadioField($name, $value)
    {
        return $this->fieldTypeRegistry->resolve('radio', [$name])->values($value)->setFieldSet($this->fieldSet());
    }

    /**
     * @param  string  $name
     *
     * @return DateTime
     */
    protected function createDateTimeField($name)
    {
        return $this->fieldTypeRegistry->resolve('dateTime', [$name])
                                       ->setFieldSet($this->fieldSet())
                                       ->setFormat($this->dateFormat);
    }

    /**
     * @param  Request  $request
     */
    public function beforeModelSave(Request $request)
    {
        $requestResource = $request->get('resource');
        $publishedStatus = Arr::get($requestResource, $this::FIELD_PUBLISHED_STATUS);
        $model = $this->overview->getForm()->getModel();

        switch ($publishedStatus) {
            case Node::STATUS_PUBLISHED:
                $model->setAttribute($this->model->getActivateAtColumnName(), now());
                $model->setAttribute($this->model->getExpireAtColumnName(), null);
                break;

            case Node::STATUS_UNPUBLISHED:
                $model->setAttribute($this->model->getActivateAtColumnName(), null);
                $model->setAttribute($this->model->getExpireAtColumnName(), null);
                break;

            case Node::STATUS_PUBLISHED_AT_DATETIME:
                $model->setAttribute($this->model->getActivateAtColumnName(),
                    Arr::get($requestResource, $this->model->getActivateAtColumnName()));
                $model->setAttribute($this->model->getExpireAtColumnName(),
                    Arr::get($requestResource, $this->model->getExpireAtColumnName()));
                break;
        }
    }

    /**
     * @param  string  $dateFormat
     *
     * @return Status
     */
    public function setDateFormat(string $dateFormat): Status
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    /**
     * @return string
     */
    public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    /**
     * @return FieldSet
     */
    protected function fieldSet(): FieldSet
    {
        return $this->overview->getForm()->fields();
    }
}

