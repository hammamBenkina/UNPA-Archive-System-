<?php


namespace App\Services;

use App\Models\File;
// use App\Services\FirebaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileService
{


    public function upload($file,  $destination = '')
    {
        try {
            $fileData = array(
                "name" => time() . '-' . $file->getClientOriginalName(),
                "size" => $file->getSize(),
                "format" => $file->getMimeType(),
                // "format" => $file->getClientOriginalExtension()
            );

            $STORAGE_TYPE =  config('filesystems.default');

            switch ($STORAGE_TYPE) {
                case 'local':

                    if (!$this->uploadOnServer($file, $fileData['name'],  $destination))
                        return false;

                    break;
                case 'firebase':
                    break;
                case 'subabase':
                    // $subabase = new SubabaseService();
                    // $subabase->uploadFile($file, $fileData['name']);
                    break;
            }

            return $this->create($fileData);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function uploadOnServer($file, string $fileName,   string $dest): bool
    {

        if (!$file->isValid())
            return false;

        try {

            // حفظ الملف في المجلد المحدد
            // $file->move($dest, $fileName);
            Storage::disk('public')->putFileAs($dest, $file, $fileName);

            // إعادة استجابة نجاح مع مسار الملف
            return true;
        } catch (\Throwable $th) {
            Log::error("File upload failed", ['error' => $th->getMessage()]);
            return false;
        }
    }

    public function create(array $fileData)
    {

        try {
            $file = File::create([
                'name' => $fileData['name'],
                'size' => $fileData['size'],
                'format' => $fileData['format'],
                'location' => config('filesystems.default')
            ]);
            return $file;
        } catch (\Throwable $e) {
            Log::error("خطأ أثناء إضافة الملف: {$e->getMessage()}");
            return false;
        }
    }

    public function getAllFiles(array $params)
    {
        $query = File::where('id', '>', -1);
        return $query->paginate($params['perPage'] ?? 20, ['*'], 'page', $params["currPage"] ?? 1);
    }

    public function removeFile(string $relativePath): bool
    {
        try {
            // نتحقق من وجود الملف في الـ public disk
            if (!Storage::disk('public')->exists($relativePath)) {
                return false;
            }

            // نحذف الملف
            return Storage::disk('public')->delete($relativePath);
        } catch (\Throwable $e) {
            // نسجل الخطأ للرجوع إليه لاحقًا
            Log::error('فشل في حذف الملف', [
                'path' => $relativePath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
