<?php

namespace Arbory\Base\Support\Activation;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait HasActivationDates
{
    /**
     * @return string
     */
    public function getActivateAtColumnName(): string
    {
        return 'activate_at';
    }

    /**
     * @return string
     */
    public function getExpireAtColumnName(): string
    {
        return 'expire_at';
    }

    /**
     * @return string
     */
    public function getQualifiedActivateAtColumnName(): string
    {
        return $this->getTable() . '.' . $this->getActivateAtColumnName();
    }

    /**
     * @return string
     */
    public function getQualifiedExpireAtColumnName(): string
    {
        return $this->getTable() . '.' . $this->getExpireAtColumnName();
    }

    /**
     * @param $value
     * @return Carbon|null
     */
    public function getActivateAtAttribute($value)
    {
        return is_null($value) ? null : Carbon::createFromFormat('Y-m-d H:i:s', $value);
    }

    /**
     * @param $value
     * @return Carbon|null
     */
    public function getExpireAtAttribute($value)
    {
        return is_null($value) ? null : Carbon::createFromFormat('Y-m-d H:i:s', $value);
    }

    /**
     * @return bool
     */
    public function getActiveAttribute()
    {
        return $this->hasActivated() && ! $this->hasExpired();
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query)
    {
        $now = now();

        return $query->where($this->getActivateAtColumnName(), '<=', $now)
            ->where(function (Builder $query) use ($now) {
                return $query->where($this->getExpireAtColumnName(), '>=', $now)
                    ->orWhereNull($this->getExpireAtColumnName());
            });
    }

    /**
     * @return bool
     */
    public function hasExpired(): bool
    {
        return $this->expire_at !== null && $this->expire_at->isPast();
    }

    /**
     * @return bool
     */
    public function hasActivated(): bool
    {
        return $this->activate_at !== null && $this->activate_at->isPast();
    }
}
