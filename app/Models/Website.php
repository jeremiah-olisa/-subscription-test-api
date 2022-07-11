<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'owner_id',
    ];


    public const AllQueryFields = ['id', 'name', 'url', 'owner_id'];

    public const DefaultSorts = ['id', 'name', 'url', 'owner_id'];

    public const AllowedSorts = ['id', 'name', 'url', 'owner_id'];

    public const AllowedIncludes = ['owner', 'posts', 'subscribers'];
    public const AllowedFilters = ['id', 'name', 'url', 'owner_id'];

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
