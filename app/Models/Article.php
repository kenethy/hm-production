<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'author_name',
        'user_id',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Get the user that owns the article.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a slug from the title.
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
            
            // Set author name if not provided
            if (empty($article->author_name) && auth()->check()) {
                $article->author_name = auth()->user()->name;
                $article->user_id = auth()->id();
            }
            
            // Set meta title if not provided
            if (empty($article->meta_title)) {
                $article->meta_title = $article->title;
            }
            
            // Set excerpt if not provided
            if (empty($article->excerpt)) {
                $article->excerpt = Str::limit(strip_tags($article->content), 150);
            }
        });

        static::updating(function ($article) {
            if ($article->isDirty('title') && empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
            
            // Update excerpt if content changed and excerpt is empty
            if ($article->isDirty('content') && empty($article->excerpt)) {
                $article->excerpt = Str::limit(strip_tags($article->content), 150);
            }
        });
    }

    /**
     * Scope a query to only include published articles.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include draft articles.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}
