<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property bool $is_published
 * @property \Carbon\Carbon|null $published_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Post extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'is_published',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function setTitleAndSlug(string $title): void
    {
        $this->title = $title;
        $this->slug = Str::slug($title);
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function publish(): void
    {
        if ($this->isNotPublished()) {
            $this->is_published = true;
            $this->published_at = now();
        }
    }

    private function isNotPublished(): bool
    {
        return !$this->is_published && !$this->published_at;
    }

    public function unpublish(): void
    {
        $this->is_published = false;
        $this->published_at = null;
    }

}
