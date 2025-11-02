<?php

namespace App\Http\Controllers\cartographicTerms;

use App\Http\Controllers\Controller;
use App\Models\ClassificationsCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ClassificationsCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {


        // استعلام لجلب جميع فئات التصنيفات
        $categoriesQuery = ClassificationsCategory::query();

        // تطبيق التصفية إذا كانت موجودة في الطلب
        if ($request->filled('searchKey')) {
            $searchKey = $request->get('searchKey');
            $categoriesQuery->where(function ($query) use ($searchKey) {
                $query->where('arName', 'like', "%$searchKey%")
                    ->orWhere('enName', 'like', "%$searchKey%");
            });
        }

        // تطبيق الترتيب إذا كان موجودًا في الطلب
        if ($request->has('sortBy') && $request->has('sortDir')) {
            $allowedSorts = ['arName', 'enName', 'created_at'];
            if ($request->filled('sortBy') && in_array($request->sortBy, $allowedSorts)) {
                $categoriesQuery->orderBy($request->sortBy, $request->boolean('sortDir') ? 'desc' : 'asc');
            }
        }

        // تحميل التصنيفات المرتبطة بكل فئة تصنيف
        if ($request->has('withClassifications')) {
            $categoriesQuery->with('classifications');
        }

        // تطبيق الترقيم الصفحي إذا كان موجودًا في الطلب

        $categories = $categoriesQuery->paginate(
            $request->get('perPage', config('request.pagination.per_page'))
        );


        // إرجاع النتيجة كاستجابة JSON
        return response()->json($categories, 200);
    }


    public function listOfAllCategories()
    {

        // استعلام لجلب جميع فئات التصنيفات
        $categoriesQuery = ClassificationsCategory::query();


        Cache::store('cartographic_terms')->forget('classificationsCategoriesList');

        // تخزين نتيجة الاستعلام في الكاش
        $categories = Cache::store('cartographic_terms')->rememberForever('classificationsCategoriesList', function () use ($categoriesQuery) {
            return $categoriesQuery->with('classifications')->get();
        });


        //    $categories =  $categoriesQuery->with('classifications')->get();

        // إرجاع النتيجة كاستجابة JSON
        return response()->json($categories, 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassificationsCategory $classificationsCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClassificationsCategory $classificationsCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassificationsCategory $classificationsCategory)
    {
        //
    }
}
