<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emotion extends Model
{
    use HasFactory;
    protected $fillable = ['nom', 'niveau', 'image_icone', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(Emotion::class, 'parent_id');
    }

    public function enfants()
    {
        return $this->hasMany(Emotion::class, 'parent_id');
    }
}
