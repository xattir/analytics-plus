<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\TempFile;
use Illuminate\Http\Request;

class BackendFileController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:hub-files-create', ['only' => ['create','store', 'upload']]);
        $this->middleware('can:hub-files-read',   ['only' => ['show', 'index']]);
        $this->middleware('can:hub-files-update',   ['only' => ['edit','update']]);
        $this->middleware('can:hub-files-delete',   ['only' => ['delete', 'destroy']]);
    }


    public function index(Request $request)
    {
        $userId = auth()->id();
        
        // Get files from TempFile model (user's uploaded files)
        // Media files are linked to TempFile via morph relationship
        $files = Media::where('model_type', TempFile::class)
            ->whereHas('model', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where(function($q) use ($request) {
                if($request->id != null) {
                    $q->where('id', $request->id);
                }
            })
            ->with('model') // Eager load the TempFile model
            ->orderBy('id', 'DESC')
            ->paginate(20);
            
        return view('admin.files.index', compact('files'));
    }
    
    /**
     * Upload file (image or video) with drag & drop support
     * Maximum size: 100MB
     */
    public function upload(Request $request)
    {
        if(!auth()->user()->can('hub-files-create')) {
            abort(403);
        }
        
        $request->validate([
            'file' => 'required|file|mimes:jpeg,jpg,png,gif,webp,mp4,avi,mov,wmv,flv,webm|max:102400', // 100MB = 102400 KB (Laravel max is in KB)
        ], [
            'file.required' => 'الرجاء اختيار ملف للرفع',
            'file.mimes' => 'نوع الملف غير مدعوم. المسموح: صور (jpeg, jpg, png, gif, webp) أو فيديو (mp4, avi, mov, wmv, flv, webm)',
            'file.max' => 'حجم الملف يجب أن يكون أقل من 100MB',
        ]);
        
        try {
            $userId = auth()->id();
            $file = $request->file('file');
            
            // Create TempFile record
            $tempFile = TempFile::create([
                'name' => uniqid('file_'),
                'user_id' => $userId
            ]);
            
            // Upload file using Spatie Media Library
            $media = $tempFile->addMediaFromRequest('file')
                ->toMediaCollection('user-files');
            
            return response()->json([
                'success' => true,
                'message' => 'تم رفع الملف بنجاح',
                'file' => [
                    'id' => $media->id,
                    'name' => $media->name,
                    'file_name' => $media->file_name,
                    'url' => $media->getUrl(),
                    'size' => $media->size,
                    'mime_type' => $media->mime_type,
                    'created_at' => $media->created_at->format('Y-m-d H:i:s'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء رفع الملف: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('hub-files-create'))abort(403);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!auth()->user()->can('hub-files-create'))abort(403);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function show(Media $media)
    {
        if(!auth()->user()->can('hub-files-read'))abort(403);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function edit(Media $media)
    {
        if(!auth()->user()->can('hub-files-update'))abort(403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Media $media)
    {
        if(!auth()->user()->can('hub-files-update'))abort(403);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function destroy(Media $file)
    {
        if(!auth()->user()->can('hub-files-delete'))abort(403);
        
        // Check if user owns this file
        $userId = auth()->id();
        if($file->model_type === TempFile::class && $file->model && $file->model->user_id != $userId) {
            abort(403, 'ليس لديك صلاحية لحذف هذا الملف');
        }
        
        $file->forceDelete();
        //you have to remove it if you want
        flash()->success(__('utils/toastr.process_success_message'));
        return redirect()->back();
    }
    
    /**
     * Get file URL for copying
     */
    public function getUrl(Media $file)
    {
        if(!auth()->user()->can('hub-files-read'))abort(403);
        
        // Check if user owns this file
        $userId = auth()->id();
        if($file->model_type === TempFile::class && $file->model && $file->model->user_id != $userId) {
            abort(403, 'ليس لديك صلاحية للوصول إلى هذا الملف');
        }
        
        return response()->json([
            'success' => true,
            'url' => $file->getUrl()
        ]);
    }
}
