<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PackageFeature extends Model
{
    protected $fillable = [
        'package_id',
        'title',
        'description',
        'visible_summary',
        'sort_order',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'visible_summary' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
