<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->hasOne(User::class);
    }
    
    public function website()
    {
        return $this->hasOne(Website::class);
    }
}
