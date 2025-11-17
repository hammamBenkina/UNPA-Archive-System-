<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Applicants extends Model
{
    //

    public static $TYPES = ['مواطن', 'شركة خاصة', 'مكتب هندسي', 'جهة حكومية', 'مطور عقاري', 'نشاط تجاري', 'أخرى'];

    protected $fillable = [
        'name',
        'type',
        'phone',
        'email',
        'nationId',
    ];
}
