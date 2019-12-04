<?php

namespace Arbory\Base\Nodes;

use Alsofronie\Uuid\UuidModelTrait;
use Arbory\Base\Pages\PageInterface;
use Baum\NestedSet\Node as BaumNode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Arbory\Base\Repositories\NodesRepository;
use Arbory\Base\Support\Activation\HasActivationDates;

/**
 * Class Node.
 */
class Node extends Model
{
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_UNPUBLISHED = 'unpublished';
    public const STATUS_PUBLISHED_AT_DATETIME = 'published_at_datetime';


    use UuidModelTrait;
    use HasActivationDates;
    use BaumNode;

    /**
     * @var string
     */
    protected $leftColumnName = 'lft';

    /**
     * @var string
     */
    protected $rightColumnName = 'rgt';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'content_type',
        'content_id',
        'item_position',
        'locale',
        'meta_title',
        'meta_author',
        'meta_keywords',
        'meta_description',
        'activate_at',
        'expire_at',
    ];

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $options = [])
    {
        (new NodesRepository)->setLastUpdateTimestamp(time());

        return parent::save($options);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo|PageInterface
     */
    public function content()
    {
        return $this->morphTo();
    }

    /**
     * @return NodeCollection|\Illuminate\Support\Collection|static[]
     */
    public function parents()
    {
        if (! $this->relationLoaded('parents')) {
            $this->setRelation('parents', $this->parentsQuery()->get());
        }

        return $this->getRelation('parents');
    }

    /**
     * @return Builder
     */
    public function parentsQuery()
    {
        return $this->newQuery()
            ->where($this->getLeftColumnName(), '<', (int) $this->getLeft())
            ->where($this->getRightColumnName(), '>', (int) $this->getRight())
            ->orderBy($this->getDepthColumnName(), 'asc');
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->content_type;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        $uri = [];

        foreach ($this->parents() as $parent) {
            $uri[] = $parent->getSlug();
        }

        $uri[] = $this->getSlug();

        return implode('/', $uri);
    }

    /**
     * @param       $name
     * @param array $parameters
     * @param bool $absolute
     * @return string|null
     */
    public function getUrl($name, array $parameters = [], $absolute = true)
    {
        $routes = app('routes');
        $routeName = 'node.'.$this->getKey().'.'.$name;
        $route = $routes->getByName($routeName);

        return $route ? route($routeName, $parameters, $absolute) : null;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @return bool
     */
    public function getActiveAttribute()
    {
        if (! $this->hasActivated() || $this->hasExpired()) {
            return false;
        }

        if ($this->parents()->isNotEmpty()) {
            return $this->parent()->first()->isActive();
        }

        return true;
    }

    public function getPublishedStatusAttribute()
    {
        if($this->isActive()) {
            return static::STATUS_PUBLISHED;
        }

        if($this->activate_at && ! $this->hasExpired()) {
            return static::STATUS_PUBLISHED_AT_DATETIME;
        }

        return static::STATUS_UNPUBLISHED;
    }

    /**
     * Return parent id (legacy support)
     *
     * @return mixed
     */
    public function getParentId()
    {
        return $this->getAttribute($this->getParentColumnName());
    }
}
