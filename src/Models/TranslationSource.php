<?php

namespace WeDevelop4You\TranslationFinder\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * WeDevelop4You\TranslationFinder\Models\TranslationSource.
 *
 * @property int                             $id
 * @property int                             $translation_id
 * @property string                          $source
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \WeDevelop4You\TranslationFinder\Models\TranslationKey $key
 *
 * @method static \Illuminate\Database\Eloquent\Builder|TranslationSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TranslationSource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TranslationSource query()
 * @method static \Illuminate\Database\Eloquent\Builder|TranslationSource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TranslationSource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TranslationSource whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TranslationSource whereTranslationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TranslationSource whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TranslationSource extends Model
{
    use HasFactory;

    protected $table = 'translation_sources';

    protected $fillable = [
        'translation_id',
        'source',
    ];

    /**
     * @return BelongsTo
     */
    public function key(): BelongsTo
    {
        return $this->belongsTo(TranslationKey::class);
    }
}
