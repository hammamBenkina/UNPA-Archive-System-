<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use PhpParser\Builder\Class_;

class Committee extends Model
{
    //

    use HasFactory;

    protected $table  = 'committee';

    protected $fillable = [
        'no',
        'yearOfEstablishment',
        'isCurrent',
        'createdBy',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'createdBy', 'id');
    }

    public function members()
    {
        return $this->hasMany(CommitteeMember::class, 'committeeId', 'id');
    }
}
