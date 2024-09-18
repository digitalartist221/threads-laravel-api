<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Thread extends Model
{
    use HasFactory;

    protected $fillable =[
        'user_id',
        'body',
        'image',
    ];


    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function likes() : HasMany {
        
        return $this->hasMany(Like::class);
    }

    /**
     * Get all of the comments for the Thread
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
