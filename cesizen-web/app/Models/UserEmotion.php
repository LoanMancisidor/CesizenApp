<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserEmotion extends Model
{
    protected $fillable = ['user_id', 'emotion_id'];

    public function emotion(): BelongsTo
    {
        return $this->belongsTo(Emotion::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}