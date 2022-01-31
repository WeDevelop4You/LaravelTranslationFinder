<?php

namespace WeDevelop4You\TranslationFinder\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * WeDevelop4You\TranslationFinder\Models\Translation.
 *
 * @property int                             $id
 * @property int                             $translation_id
 * @property string                          $locale
 * @property string                          $translation
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \WeDevelop4You\TranslationFinder\Models\TranslationKey $key
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Translation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Translation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Translation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Translation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translation whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translation whereTranslation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translation whereTranslationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Translation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Translation extends Model
{
    use HasFactory;

    protected $table = 'translations';

    protected $fillable = [
        'translation_id',
        'locale',
        'translation',
    ];

    /**
     * @return BelongsTo
     */
    public function key(): BelongsTo
    {
        return $this->belongsTo(TranslationKey::class);
    }
}
