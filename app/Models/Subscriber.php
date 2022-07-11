<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'website_id',
    ];


    public const AllQueryFields = ['id', 'user_id', 'website_id'];

    public const DefaultSorts = ['id', 'user_id', 'website_id'];

    public const AllowedSorts = ['id', 'user_id', 'website_id'];

    public const AllowedIncludes = ['user', 'website'];
    public const AllowedFilters = ['id', 'user_id', 'website_id'];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function website()
    {
        return $this->hasOne(Website::class);
    }
}
