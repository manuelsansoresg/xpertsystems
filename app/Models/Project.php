<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'category', 'desktop_image', 'mobile_image', 'url',
        'description', 'technologies', 'accent', 'sort_order', 'active',
    ];

    protected function casts(): array
    {
        return ['technologies' => 'array', 'active' => 'boolean'];
    }
}
