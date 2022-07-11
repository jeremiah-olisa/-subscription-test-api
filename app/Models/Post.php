<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'content',
        'website_id',
    ];

    public const AllQueryFields = ['id', 'title', 'website_id'];

    public const DefaultSorts = ['id', 'title', 'website_id'];

    public const AllowedSorts = ['id', 'title', 'website_id'];

    public const AllowedIncludes = ['website'];
    public const AllowedFilters = ['id', 'title', 'website_id', 'description', 'content'];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }
}
