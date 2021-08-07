<?php

namespace WeDevelop4You\TranslationFinder\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * WeDevelop4You\TranslationFinder\Models\TranslationKey
 *
 * @property int $id
 * @property string $environment
 * @property string $group
 * @property string $key
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\WeDevelop4You\TranslationFinder\Models\TranslationSource[] $sources
 * @property-read \Illuminate\Database\Eloquent\Collection|\WeDevelop4You\TranslationFinder\Models\Translation[] $translations
 * @method static \Illuminate\Database\Eloquent\Builder|TranslationKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TranslationKey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TranslationKey query()
 * @method static \Illuminate\Database\Eloquent\Builder|TranslationKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TranslationKey whereEnvironment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TranslationKey whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TranslationKey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TranslationKey whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TranslationKey whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TranslationKey extends Model
{
    use HasFactory;

    protected $table = 'translation_keys';

    protected $fillable = [
        'environment',
        'group',
        'key',
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
}
