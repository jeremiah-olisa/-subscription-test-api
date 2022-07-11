<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    public const AllQueryFields = ['id', 'name', 'url'];

    public const DefaultSorts = ['id', 'name', 'url'];

    public const AllowedSorts = ['id', 'name', 'url'];

    public const AllowedIncludes = ['owner', 'posts', 'subscribers'];
    public const AllowedFilters = ['id', 'name', 'url'];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }
}
