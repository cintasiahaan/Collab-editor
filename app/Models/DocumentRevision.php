<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class DocumentRevision extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'document_revisions';

    protected $fillable = [
        'document_id',
        'user_id',
        'content',
    ];

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT RELATION
    |--------------------------------------------------------------------------
    */

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /*
    |--------------------------------------------------------------------------
    | USER RELATION
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}