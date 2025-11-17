<?php

namespace App\Http\Controllers\cartographicTerms;

use App\Http\Controllers\Controller;
use App\Models\Classification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ClassificationController extends Controller
{
<<<<<<< HEAD

    
    // ================================================================================
    // Planning symbol
    // ================================================================================


=======
>>>>>>> 0e90a6f (Complete cartographic terms system)
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // استعلام لجلب جميع التصنيفات
        $classificationsQuery = Classification::query();


        // البحث بالكلمات
        if ($request->filled('searchKey')) {
            $searchKey = $request->get('searchKey');
            $classificationsQuery->where(function ($query) use ($searchKey) {
                $query->where('arName', 'like', "%$searchKey%")
                    ->orWhere('enName', 'like', "%$searchKey%");
            });
        }


        // التصفية بالفئة
        if ($request->filled('categoryId')) {
            $classificationsQuery->where('categoryId', $request->get('categoryId'));
        }

        
        // الترتيب
        if ($request->has('sortBy') && $request->has('sortDir')) {
            $allowedSorts = ['arName', 'enName', 'created_at'];
            if (in_array($request->sortBy, $allowedSorts)) {
                $classificationsQuery->orderBy($request->sortBy, $request->boolean('sortDir') ? 'desc' : 'asc');
            }
        }


        // العلاقات والصفحات
        $classification = $classificationsQuery
            ->with(['category:id,arName,enName'])
            ->paginate(
                $request->get('perPage', config('request.pagination.per_page')),
                ['*'],
                'page',
                $request->get('page', 1)
            );

        return response()->json($classification, 200);
    }



    public function listOfAllClassifications()
    {

        // استعلام لجلب جميع فئات التصنيفات
        $categoriesQuery = Classification::query();


        // تخزين نتيجة الاستعلام في الكاش
        $categories = Cache::store('cartographic_terms')->rememberForever('classificationsList', function () use ($categoriesQuery) {
            return $categoriesQuery->with('category:id,arName,enName')->get();
        });


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
    public function show(Classification $classification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classification $classification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classification $classification)
    {
        //
    }
}
