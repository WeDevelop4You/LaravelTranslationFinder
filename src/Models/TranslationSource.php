<?php

namespace WeDevelop4You\TranslationFinder\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * WeDevelop4You\TranslationFinder\Models\TranslationSource
 *
 * @property-read \WeDevelop4You\TranslationFinder\Models\TranslationKey $key
 * @method static \Illuminate\Database\Eloquent\Builder|TranslationSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TranslationSource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TranslationSource query()
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
