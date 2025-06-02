<?php

namespace App\Plugins\IBKnowledgeBase\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Orchid\Attachment\Attachable;
use Orchid\Attachment\Models\Attachment;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Ilike;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Platform\Concerns\Sortable;
use Orchid\Screen\AsSource;
use Orchid\Support\Facades\Dashboard;

class Article extends Model
{
    use AsSource, Filterable, Attachable, Sortable, Filterable;

    protected $table = 'ibkb_articles';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'parent_id',
        'author_id',
        'status',
        'system_id',
        'tags',
    ];

    public static function boot(): void
    {
        parent::boot();

        self::creating(function (self $article): void {
            $article->slug = md5(time() . $article->name);
        });
    }

    public function pictures(): MorphToMany
    {
        $query = $this->morphToMany(
            Dashboard::model(Attachment::class),
            'attachmentable',
            'attachmentable',
            'attachmentable_id',
            'attachment_id'
        );

        return $query->where('mime', 'ILIKE', '%image%')->orderBy('sort');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Article::class, 'parent_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function system(): BelongsTo
    {
        return $this->belongsTo(InformationSystem::class);
    }

    public function getMatrixTags(): array
    {
        return $this->exists
            ? collect(explode(';', $this->tags))
            ->map(fn(string $tag): array => ['tag' => $tag])
            ->toArray()
            : [];
    }

    protected $allowedFilters = [
        'id' => Where::class,
        'title' => Ilike::class,
        'slug' => Ilike::class,
        'content' => Ilike::class,
        'parent_id' => Where::class,
        'author_id' => Where::class,
        'status' => Where::class,
        'system_id' => Where::class,
        'tags' => Ilike::class,
        'updated_at' => WhereDateStartEnd::class,
        'created_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'id',
        'name',
        'updated_at',
        'created_at',
    ];
}
