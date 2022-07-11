<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    use HasFactory;

    public const AllQueryFields = ['id', 'name', 'url'];

    public const DefaultSorts = ['id', 'name', 'url'];

    public const AllowedSorts = ['id', 'name', 'url'];

    public const AllowedIncludes = ['owner', 'posts', 'subscribers'];
    public const AllowedFilters = ['id', 'name', 'url'];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }
    
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    
    public function subscribers()
    {
        return $this->hasMany(Subscriber::class);
    }

}
