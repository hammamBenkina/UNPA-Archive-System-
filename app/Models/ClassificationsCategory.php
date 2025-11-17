<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class ClassificationsCategory extends Model
{
    use HasFactory;

    // تعريف الاتصال بقاعدة البيانات المخصصة
    protected $connection = 'cartographic_terms';

    // تعريف اسم الجدول إذا كان مختلفًا عن الاسم الافتراضي
    protected $table = 'classifications_category';


    // الخصائص التي يمكن تعيينها بشكل جماعي
    protected $fillable = [
        'arName',
        'enName',
        'arSymbol',
        'enSymbol',
        'color',
        'desc',
    ];

    
    // العلاقات مع النماذج الأخرى (إذا وجدت)

    public function classifications()
    {
        return $this->hasMany(Classification::class, 'categoryId');
    }
}
