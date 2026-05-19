<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Document extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'documents';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function revisions()
    {
        return $this->hasMany(DocumentRevision::class) ->orderBy('created_at', 'desc');
    }
}