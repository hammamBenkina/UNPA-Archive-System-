<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommitteeMember extends Model
{
    //

    use HasFactory;

    protected $table = 'committee_members';

    protected $fillable = [
        'name',
        'adjective',
        'about',
        'committeeId',
        'accountId',
        'createdBy',
    ];

    public function committee()
    {
        return $this->belongsTo(Committee::class, 'committeeId', 'id');
    }

    public function account()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }
}
