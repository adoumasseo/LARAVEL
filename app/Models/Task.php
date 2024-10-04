<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content',
        'status',
        'board_id',
    ];
    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }
    protected $dates = [
        'created_at',
        'updated_at',
        // Ajoute ici d'autres colonnes de dates si nécessaire
    ];

    // Mutateur pour le champ created_at
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->translatedFormat('l, d F Y');
    }

    // Mutateur pour le champ updated_at
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->translatedFormat('l, d F Y');
    }
}
