<?php

namespace Arbory\Base\Admin\Form\Overview\Blocks;


use Arbory\Base\Admin\Form\Fields\DateTime;
use Arbory\Base\Admin\Form\Fields\FieldInterface;
use Arbory\Base\Admin\Form\Fields\Styles\StyleManager;
use Arbory\Base\Admin\Form\FieldSet;
use Arbory\Base\Html\Elements\Content;
use Illuminate\Contracts\Support\Renderable;
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
    const STATUS_PUBLISHED = 'published';
    const STATUS_UNPUBLISHED = 'unpublished';
    const STATUS_PUBLISHED_AT_DATETIME = 'published_at_datetime';

    const FIELD_ACTIVATE_AT = 'activate_at';
    const FIELD_EXPIRE_AT = 'expire_at';
    const FIELD_PUBLISHED_STATUS = 'published_status';

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
     * Status constructor.
     * @param string $name
     */
    public function __construct($name = 'status')
    {
        parent::__construct($name);
        $this->setModalId($this->getName());
        $this->styleManager = app(StyleManager::class);
        $this->validator = app(Validator::class);
    }

    /**
     * @param Overview $overview
     */
    public function setOverview(Overview $overview)
    {
        $this->overview = $overview;
        $this->createValidation();
    }

    public function createValidation()
    {
        $this->overview->getForm()->addEventListener('validate.before', function ($request) {
            $this->validator->setRules([
                'resource.' . $this::FIELD_ACTIVATE_AT => 'nullable|date_format:Y-m-d H:i',
                'resource.' . $this::FIELD_EXPIRE_AT => 'nullable|date_format:Y-m-d H:i|after_or_equal:resource.' . $this::FIELD_ACTIVATE_AT
            ]);
            $this->validator->validate($this->validator->rules());
        });
    }


    protected function getFieldNames()
    {
        return [
            $this::FIELD_PUBLISHED_STATUS,
            $this::FIELD_ACTIVATE_AT,
            $this::FIELD_EXPIRE_AT
        ];
    }

    /**
     * @return array
     */
    protected function getPublishedStatusNames()
    {
        return [
            $this::STATUS_PUBLISHED,
            $this::STATUS_UNPUBLISHED,
            $this::STATUS_PUBLISHED_AT_DATETIME
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
        $activateAt = $this->createDateTimeField($this::FIELD_ACTIVATE_AT)->setFieldSet($fieldSet)->setValue($this->getActivateAtField()->getValue());
        $expireAt = $this->createDateTimeField($this::FIELD_EXPIRE_AT)->setFieldSet($fieldSet)->setValue($this->getExpireAtField()->getValue());


        return view('arbory::dialogs.status', [
            'published' => (string) $this->styleManager->render('basic', $published),
            'unpublished' => (string) $this->styleManager->render('basic', $unpublished),
            'publishedAtDatetime' => (string) $this->styleManager->render('basic', $publishedAtDatetime),
            'activateAt' => (string) $this->styleManager->render('normal', $activateAt),
            'expireAt' => (string) $this->styleManager->render('normal', $expireAt)
        ]);
    }

    /**
     * @param string $id
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
            'currentStatus' => $this->overview->getForm()->getModel()->getAttribute($this::FIELD_PUBLISHED_STATUS) ?? $this::STATUS_UNPUBLISHED,
            'options' => [$this::STATUS_PUBLISHED, $this::STATUS_UNPUBLISHED, $this::STATUS_PUBLISHED_AT_DATETIME]
        ]);
    }

    /**
     * @return Radio
     */
    protected function getPublishedField()
    {
        return $this->createRadioField($this::FIELD_PUBLISHED_STATUS, $this::STATUS_PUBLISHED);
    }

    /**
     * @return Radio
     */
    protected function getPublishedAtDatetimeField()
    {
        return $this->createRadioField($this::FIELD_PUBLISHED_STATUS, $this::STATUS_PUBLISHED_AT_DATETIME);
    }

    /**
     * @return Radio
     */
    protected function getUnpublishedField()
    {
        return $this->createRadioField($this::FIELD_PUBLISHED_STATUS, $this::STATUS_UNPUBLISHED);
    }

    /**
     * @return DateTime
     */
    protected function getActivateAtField()
    {
        return $this->createDateTimeField($this::FIELD_ACTIVATE_AT);
    }

    /**
     * @return DateTime
     */
    protected function getExpireAtField()
    {
        return $this->createDateTimeField($this::FIELD_EXPIRE_AT);
    }


    /**
     * @param string $name
     * @param string $value
     * @return Radio
     */
    protected function createRadioField($name, $value)
    {
        return (new Radio($name))->values($value)->setFieldSet($this->fieldSet());
    }

    /**
     * @param string $name
     * @return DateTime
     */
    protected function createDateTimeField($name)
    {
        return (new DateTime($name))->setFieldSet($this->fieldSet());
    }

    /**
     * @param Request $request
     */
    public function beforeModelSave(Request $request)
    {
        $requestResource = $request->get('resource');
        $publishedStatus = Arr::get($requestResource, $this::FIELD_PUBLISHED_STATUS);
        $model = $this->overview->getForm()->getModel();

        switch ($publishedStatus) {
            case $this::STATUS_PUBLISHED:
                $model->setAttribute($this::FIELD_ACTIVATE_AT, date('Y-m-d H:i:s'));
                $model->setAttribute($this::FIELD_EXPIRE_AT, null);
                break;

            case $this::STATUS_UNPUBLISHED:
                $model->setAttribute($this::FIELD_ACTIVATE_AT, null);
                $model->setAttribute($this::FIELD_EXPIRE_AT, null);
                break;

            case $this::STATUS_PUBLISHED_AT_DATETIME:
                $model->setAttribute($this::FIELD_ACTIVATE_AT, Arr::get($requestResource, $this::FIELD_ACTIVATE_AT));
                $model->setAttribute($this::FIELD_EXPIRE_AT, Arr::get($requestResource, $this::FIELD_EXPIRE_AT));
                break;
        }

        $this->overview->getForm()->getModel()->setAttribute($this::FIELD_PUBLISHED_STATUS, $publishedStatus);
    }

    /**
     * @return FieldSet
     */
    protected function fieldSet(): FieldSet
    {
        return $this->overview->getForm()->fields();
    }
}

