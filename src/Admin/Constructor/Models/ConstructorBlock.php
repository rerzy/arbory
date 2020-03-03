<?php

namespace Arbory\Base\Admin\Constructor\Models;

use Arbory\Base\Admin\Navigator\NavigableItemInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ConstructorBlock extends Model implements NavigableItemInterface
{
    /**
     * @var string
     */
    protected $table = 'constructor_blocks';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'content_type',
        'content_id',
        'position',
    ];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function block(): MorphTo
    {
        return $this->morphTo();
    }

    public function content(): MorphTo
    {
        return $this->morphTo();
    }
}
