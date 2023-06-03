<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class translations extends Model
{
    use HasFactory;

    public function verse_translations()
    {
        return $this->hasMany(verses_translations::class);
    }
}
