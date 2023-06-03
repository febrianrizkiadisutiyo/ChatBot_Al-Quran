<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verse extends Model
{
    use HasFactory;

    public function verse_translations()
    {
        return $this->hasMany(verses_translations::class);
    }

}
