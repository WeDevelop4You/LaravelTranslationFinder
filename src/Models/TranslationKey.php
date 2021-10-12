<?php

namespace WeDevelop4You\TranslationFinder\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * WeDevelop4You\TranslationFinder\Models\TranslationKey
 *
 * @property int $id
 * @property string $environment
 * @property string $group
 * @property string $key
 * @property mixed|null $tags
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|\WeDevelop4You\TranslationFinder\Models\TranslationSource[] $sources
 * @property-read Collection|\WeDevelop4You\TranslationFinder\Models\Translation[] $translations
 * @method static Builder|TranslationKey newModelQuery()
 * @method static Builder|TranslationKey newQuery()
 * @method static Builder|TranslationKey query()
 * @method static Builder|TranslationKey whereCreatedAt($value)
 * @method static Builder|TranslationKey whereEnvironment($value)
 * @method static Builder|TranslationKey whereGroup($value)
 * @method static Builder|TranslationKey whereId($value)
 * @method static Builder|TranslationKey whereKey($value)
 * @method static Builder|TranslationKey whereTags($value)
 * @method static Builder|TranslationKey whereUpdatedAt($value)
 * @mixin Eloquent
 */
class TranslationKey extends Model
{
    use HasFactory;

    protected $table = 'translation_keys';

    protected $fillable = [
        'environment',
        'group',
        'key',
        'tags',
    ];

    protected $casts = [
        'tags' => AsCollection::class,
    ];

    /**
     * @return HasMany
     */
    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translation_id');
    }

    /**
     * @return HasMany
     */
    public function sources(): HasMany
    {
        return $this->hasMany(TranslationSource::class, 'translation_id');
    }

    /**
     * @param string $locale
     * @return HasMany|Model|object
     */
    public function getTranslation(string $locale)
    {
        return $this->translations()->where('locale', $locale)->first();
    }

    /**
     * @param string $locale
     * @return Model|object
     */
    public function getOrCreateTranslation(string $locale)
    {
        return $this->translations()->where('locale', $locale)->firstOrNew();
    }
}
