<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Travel extends Model
{
    use HasFactory;

    protected $table = 'travels';

    protected $guarded = ['id'];

    //observe  travel model
    protected static function booted()
    {
        self::observe(Travel::class);
    }
}
