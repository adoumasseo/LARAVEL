<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Board extends Model
{
    use HasFactory;
     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'board_name',
        'status',
        'user_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
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
