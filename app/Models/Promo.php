<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'image_path',
        'original_price',
        'promo_price',
        'discount_percentage',
        'start_date',
        'end_date',
        'is_featured',
        'is_active',
        'promo_code',
        'remaining_slots',
    ];

    protected $casts = [
        'original_price' => 'decimal:2',
        'promo_price' => 'decimal:2',
        'discount_percentage' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'remaining_slots' => 'integer',
    ];

    /**
     * Scope a query to only include active promos.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    /**
     * Scope a query to only include featured promos.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get the remaining time for the promo.
     */
    public function getRemainingTimeAttribute()
    {
        return now()->diffForHumans($this->end_date, ['parts' => 2]);
    }

    /**
     * Get the discount percentage.
     */
    public function getDiscountPercentageAttribute($value)
    {
        if ($value) {
            return $value;
        }

        if ($this->original_price && $this->promo_price) {
            return round((($this->original_price - $this->promo_price) / $this->original_price) * 100);
        }

        return 0;
    }

    /**
     * Check if the promo is ending soon (less than 3 days).
     */
    public function getIsEndingSoonAttribute()
    {
        return $this->end_date->diffInDays(now()) < 3;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($promo) {
            if (empty($promo->slug)) {
                $promo->slug = \Illuminate\Support\Str::slug($promo->title);
            }
        });

        static::updating(function ($promo) {
            if ($promo->isDirty('title') && empty($promo->slug)) {
                $promo->slug = \Illuminate\Support\Str::slug($promo->title);
            }
        });
    }

    /**
     * Check if the promo has limited slots.
     */
    public function getHasLimitedSlotsAttribute()
    {
        return $this->remaining_slots !== null && $this->remaining_slots > 0;
    }
}
