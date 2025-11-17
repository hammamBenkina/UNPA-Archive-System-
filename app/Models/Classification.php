<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Classification extends Model
{

    use HasFactory;
    // تعريف الاتصال بقاعدة البيانات المخصصة
    protected $connection = 'cartographic_terms';

    // الخصائص التي يمكن تعيينها بشكل جماعي
    protected $fillable = [
        'arName',
        'enName',
        'arSymbol',
        'enSymbol',
        'icon',
        'color',
        'categoryId',
        'desc',
    ];


    public function category(){
        return $this->belongsTo(ClassificationsCategory::class , 'categoryId' , 'id');
    }
}
