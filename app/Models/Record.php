<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Record extends Model
{
    //

    use HasFactory;
    protected $table = 'record';

    protected $fillable = [
        'no',
        'referenceNumber',
        'year',
        'branchId',
        'committeeId',
        'docId',
        'createdBy',
        'desc',
        'conveningDate'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branchId', 'id');
    }

    public function committee()
    {
        return $this->belongsTo(Committee::class, 'committeeId', 'id');
    }

    public function document()
    {
        return $this->belongsTo(File::class, 'docId', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'createdBy', 'id');
    }
}
