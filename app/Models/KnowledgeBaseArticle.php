<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Str;

class KnowledgeBaseArticle extends Model
{
  use HasFactory, Searchable, LogsActivity;

  protected $guarded = [];

  protected $casts = [
    'categories' => 'array',
    'tags' => 'array',
    'rating' => 'decimal:2',
    'attachments' => 'array',
    'related_articles' => 'array',
    'published_at' => 'datetime',
    'is_featured' => 'boolean',
  ];

  protected $appends = ['estimated_read_time', 'is_published'];

  // Relationships
  public function author()
  {
    return $this->belongsTo(User::class, 'author_id');
  }

  // Accessors
  public function getEstimatedReadTimeAttribute()
  {
    $wordCount = str_word_count(strip_tags($this->content));
    $minutes = ceil($wordCount / 200); // Average reading speed
    return $minutes;
  }

  public function getIsPublishedAttribute()
  {
    return $this->status === 'published' && $this->published_at && $this->published_at <= now();
  }

  // Scopes
  public function scopePublished($query)
  {
    return $query->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now());
  }

  public function scopeDraft($query)
  {
    return $query->where('status', 'draft');
  }

  public function scopeArchived($query)
  {
    return $query->where('status', 'archived');
  }

  public function scopeFeatured($query)
  {
    return $query->where('is_featured', true);
  }

  public function scopeByCategory($query, $category)
  {
    return $query->whereJsonContains('categories', $category);
  }

  public function scopeWithTag($query, $tag)
  {
    return $query->whereJsonContains('tags', $tag);
  }

  public function scopePopular($query, $minViews = 100)
  {
    return $query->where('view_count', '>=', $minViews);
  }

  public function scopeHighlyRated($query, $minRating = 4.0)
  {
    return $query->where('rating', '>=', $minRating)->where('rating_count', '>', 0);
  }

  public function scopeByAuthor($query, $authorId)
  {
    return $query->where('author_id', $authorId);
  }

  public function scopeRecentlyUpdated($query, $days = 30)
  {
    return $query->where('updated_at', '>=', now()->subDays($days));
  }

  // Business Logic Methods
  public function publish()
  {
    $this->update([
      'status' => 'published',
      'published_at' => $this->published_at ?? now(),
    ]);

    activity()->performedOn($this)->log('Article published');

    return $this;
  }

  public function unpublish()
  {
    $this->update(['status' => 'draft']);

    activity()->performedOn($this)->log('Article unpublished');

    return $this;
  }

  public function archive()
  {
    $this->update(['status' => 'archived']);

    activity()->performedOn($this)->log('Article archived');

    return $this;
  }

  public function incrementViews()
  {
    $this->increment('view_count');
    return $this;
  }

  public function addRating($rating, $userId = null)
  {
    // In a full implementation, you'd store individual ratings in a separate table
    // For now, we'll update the aggregate rating
    $totalRating = $this->rating * $this->rating_count + $rating;
    $newCount = $this->rating_count + 1;
    $newRating = $totalRating / $newCount;

    $this->update([
      'rating' => round($newRating, 2),
      'rating_count' => $newCount,
    ]);

    return $this;
  }

  public function addCategory($category)
  {
    $categories = $this->categories ?? [];
    if (!in_array($category, $categories)) {
      $categories[] = $category;
      $this->update(['categories' => $categories]);
    }
    return $this;
  }

  public function removeCategory($category)
  {
    $categories = $this->categories ?? [];
    $categories = array_values(array_filter($categories, fn($c) => $c !== $category));
    $this->update(['categories' => $categories]);
    return $this;
  }

  public function addTag($tag)
  {
    $tags = $this->tags ?? [];
    if (!in_array($tag, $tags)) {
      $tags[] = $tag;
      $this->update(['tags' => $tags]);
    }
    return $this;
  }

  public function removeTag($tag)
  {
    $tags = $this->tags ?? [];
    $tags = array_values(array_filter($tags, fn($t) => $t !== $tag));
    $this->update(['tags' => $tags]);
    return $this;
  }

  public function addRelatedArticle($articleId)
  {
    $related = $this->related_articles ?? [];
    if (!in_array($articleId, $related)) {
      $related[] = $articleId;
      $this->update(['related_articles' => $related]);
    }
    return $this;
  }

  public function removeRelatedArticle($articleId)
  {
    $related = $this->related_articles ?? [];
    $related = array_values(array_filter($related, fn($id) => $id !== $articleId));
    $this->update(['related_articles' => $related]);
    return $this;
  }

  public function getRelatedArticles()
  {
    if (empty($this->related_articles)) {
      return collect();
    }

    return static::whereIn('id', $this->related_articles)->published()->get();
  }

  public function getStatusColorAttribute()
  {
    return match ($this->status) {
      'published' => '#10B981',
      'draft' => '#F59E0B',
      'archived' => '#6B7280',
      default => '#6B7280',
    };
  }

  public function getRatingColorAttribute()
  {
    if ($this->rating >= 4.5) {
      return '#10B981';
    }
    if ($this->rating >= 3.5) {
      return '#F59E0B';
    }
    if ($this->rating >= 2.0) {
      return '#EF4444';
    }
    return '#6B7280';
  }

  // Scout Configuration
  public function toSearchableArray()
  {
    return [
      'title' => $this->title,
      'content' => strip_tags($this->content),
      'excerpt' => $this->excerpt,
      'categories' => $this->categories,
      'tags' => $this->tags,
    ];
  }

  public function shouldBeSearchable()
  {
    return $this->status === 'published';
  }

  // Activity Log Configuration
  public function getActivitylogOptions(): LogOptions
  {
    return LogOptions::defaults()
      ->logOnly(['title', 'status', 'categories', 'tags', 'is_featured', 'published_at', 'sort_order'])
      ->logOnlyDirty()
      ->dontSubmitEmptyLogs()
      ->setDescriptionForEvent(fn(string $eventName) => "Knowledge base article has been {$eventName}");
  }

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($article) {
      if (!$article->slug) {
        $article->slug = Str::slug($article->title);

        // Ensure unique slug
        $count = 1;
        $originalSlug = $article->slug;
        while (static::where('slug', $article->slug)->exists()) {
          $article->slug = $originalSlug . '-' . $count++;
        }
      }

      $article->title = self::sanitize($article->title);
      $article->excerpt = self::sanitize($article->excerpt);
    });

    static::updating(function ($article) {
      $article->title = self::sanitize($article->title);
      $article->excerpt = self::sanitize($article->excerpt);
    });
  }

  protected static function sanitize($input)
  {
    return $input ? htmlspecialchars($input, ENT_QUOTES, 'UTF-8') : $input;
  }
}
