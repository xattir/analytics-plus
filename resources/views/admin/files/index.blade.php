@extends('layouts.admin')
@section('content')
<style>
    .upload-area {
        border: 2px dashed #7b60fb;
        border-radius: 12px;
        padding: 40px;
        text-align: center;
        background: #f8f9fa;
        transition: all 0.3s ease;
        cursor: pointer;
        margin-bottom: 20px;
    }
    .upload-area:hover {
        background: #e9ecef;
        border-color: #667eea;
    }
    .upload-area.dragover {
        background: #e3f2fd;
        border-color: #1976d2;
        transform: scale(1.02);
    }
    .upload-icon {
        font-size: 48px;
        color: #7b60fb;
        margin-bottom: 16px;
    }
    .upload-text {
        font-size: 16px;
        color: #495057;
        margin-bottom: 8px;
    }
    .upload-hint {
        font-size: 12px;
        color: #6c757d;
    }
    .file-upload-progress {
        display: none;
        margin-top: 16px;
    }
    .file-item {
        position: relative;
    }
    .copy-link-btn {
        cursor: pointer;
    }
    .copy-link-btn:hover {
        opacity: 0.8;
    }
    .file-preview {
        max-width: 100px;
        max-height: 100px;
        object-fit: cover;
        border-radius: 4px;
    }
    .video-preview {
        width: 100px;
        height: 100px;
        background: #000;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        color: #fff;
    }
</style>

<div class="col-12 p-3">
	<div class="col-12 col-lg-12 p-0 main-box">
	 
		<div class="col-12 px-0">
			<div class="col-12 p-0 row">
				<div class="col-12 col-lg-4 py-3 px-3">
					<span class="fas fa-files"></span> مدير الملفات
				</div>
				<div class="col-12 col-lg-4 p-0">
				</div>
			</div>
			<div class="col-12 divider" style="min-height: 2px;"></div>
		</div>

		@can('hub-files-create')
		<!-- Upload Area with Drag & Drop -->
		<div class="col-12 p-3">
			<div class="upload-area" id="uploadArea">
				<div class="upload-icon">
					<i class="fas fa-cloud-upload-alt"></i>
				</div>
				<div class="upload-text">
					اسحب الملفات هنا أو اضغط للاختيار
				</div>
				<div class="upload-hint">
					صور أو فيديو - الحد الأقصى 100MB
				</div>
				<input type="file" id="fileInput" accept="image/*,video/*" style="display: none;" multiple>
				<div class="file-upload-progress" id="uploadProgress">
					<div class="progress" style="height: 20px;">
						<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
					</div>
					<div class="mt-2" id="uploadStatus"></div>
				</div>
			</div>
		</div>
		@endcan

		<div class="col-12 py-2 px-2 row">
			<div class="col-12 col-lg-4 p-2">
				<form method="GET">
					<input type="text" name="q" class="form-control" placeholder="بحث ... " value="{{request()->get('q')}}">
				</form>
			</div>
		</div>
		<div class="col-12 p-3" style="overflow:auto">
			<div class="col-12 p-0" style="min-width:1100px;min-height:50dvh">
				
			
			<table class="table table-bordered  table-hover">
				<thead>
					<tr>
						<th>#</th>
						<th>الملف</th>
						<th>مستخدم في</th> 
						<th>تاريخ الرفع</th> 
						<th>تحكم</th>
					</tr>
				</thead>  
				<tbody>
					@forelse($files as $file)
					<tr class="file-item" data-file-id="{{$file->id}}">
						<td>{{$file->id}}</td>
						 
						<td class="text-truncate d-flex align-items-center">
							@php
								// Get extension from file_name or mime_type
								$extension = '';
								if($file->file_name) {
									$extension = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
								}
								$mimeType = $file->mime_type ?? '';
								$isImage = strpos($mimeType, 'image/') === 0 || in_array($extension, ['jpg','jpeg','gif','png','webp']);
								$isVideo = strpos($mimeType, 'video/') === 0 || in_array($extension, ['mp4','avi','mov','wmv','flv','webm']);
							@endphp
							@if($isImage)
							<div class="col-auto p-1">
								<img src="{{$file->getUrl()}}" class="file-preview mx-2" alt="Preview">
							</div>
							@elseif($isVideo)
							<div class="col-auto p-1">
								<div class="video-preview mx-2">
									<i class="fas fa-video fa-2x"></i>
								</div>
							</div>
							@else
							<div class="col-auto p-1">
								<div class="video-preview mx-2" style="background: #6c757d;">
									<i class="fas fa-file fa-2x"></i>
								</div>
							</div>
							@endif
							<div class="col-auto p-1">
								<strong>{{$file->name}}</strong>
								<br>
								<small class="text-muted">{{$file->file_name}}</small>
								<br>
								<i class="fas fa-box-open mx-1"></i> {{ number_format($file->size / (1024), 2)}} KB
								@if($file->mime_type)
								<br><small class="text-muted">{{$file->mime_type}}</small>
								@endif
							</div>
						</td>
					 
						<td>
							@if($file->model && $file->model->type)
								{{$file->model->type}}
							@else
								<span class="text-muted">-</span>
							@endif
						</td>
					    <td>
							{{$file->created_at->format('Y-m-d H:i')}}
							<br><small class="text-muted">{{$file->created_at->diffForHumans()}}</small>
						</td>
						<td style="width: 250px;">
							@can('hub-files-read')
							<button class="btn btn-outline-primary btn-sm font-1 mx-1 py-1 px-2 copy-link-btn" 
									data-file-url="{{$file->getUrl()}}" 
									onclick="copyFileLink(this, '{{$file->getUrl()}}')"
									title="نسخ الرابط">
								<span class="fas fa-copy"></span> نسخ الرابط
							</button>
							<a href="{{$file->getUrl()}}" target="_blank" class="btn btn-outline-success btn-sm font-1 mx-1 py-1 px-2">
								<span class="fas fa-eye"></span> عرض
							</a>
							@endcan
							@can('hub-files-delete')
							<form method="POST" action="{{route('admin.files.destroy',$file)}}" class="d-inline-block">@csrf @method("DELETE")
								<button class="btn btn-outline-danger btn-sm font-1 mx-1 py-1 px-2" onclick="var result = confirm('هل أنت متأكد من عملية الحذف ؟');if(result){}else{event.preventDefault()}">
									<span class="fas fa-trash"></span> حذف
								</button>
							</form>
							@endcan
						</td>
					</tr>
					@empty
					<tr>
						<td colspan="5" class="text-center py-5">
							<i class="fas fa-inbox fa-3x text-muted mb-3"></i>
							<p class="text-muted">لا توجد ملفات</p>
						</td>
					</tr>
					@endforelse
				</tbody>
			</table>
			</div>
		</div>
		<div class="col-12 p-3">
			{{$files->appends(request()->query())->render()}}
		</div>
	</div>
</div>
@endsection

@section('scripts')
@can('hub-files-create')
<script>
(function() {
    'use strict';
    
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const uploadProgress = document.getElementById('uploadProgress');
    const uploadStatus = document.getElementById('uploadStatus');
    const progressBar = uploadProgress.querySelector('.progress-bar');
    
    // Click to select files
    uploadArea.addEventListener('click', function(e) {
        if (e.target !== fileInput) {
            fileInput.click();
        }
    });
    
    // File input change
    fileInput.addEventListener('change', function(e) {
        handleFiles(e.target.files);
    });
    
    // Drag and drop
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFiles(files);
        }
    });
    
    function handleFiles(files) {
        Array.from(files).forEach(function(file) {
            uploadFile(file);
        });
    }
    
    function uploadFile(file) {
        // Validate file size (100MB = 104857600 bytes)
        const maxSize = 100 * 1024 * 1024;
        if (file.size > maxSize) {
            alert('حجم الملف ' + file.name + ' أكبر من 100MB');
            return;
        }
        
        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 
                           'video/mp4', 'video/avi', 'video/quicktime', 'video/x-msvideo', 
                           'video/x-ms-wmv', 'video/x-flv', 'video/webm'];
        if (!validTypes.includes(file.type)) {
            alert('نوع الملف غير مدعوم: ' + file.name);
            return;
        }
        
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', '{{csrf_token()}}');
        
        // Show progress
        uploadProgress.style.display = 'block';
        progressBar.style.width = '0%';
        uploadStatus.textContent = 'جاري رفع ' + file.name + '...';
        
        const xhr = new XMLHttpRequest();
        
        // Upload progress
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressBar.style.width = percentComplete + '%';
            }
        });
        
        // Upload complete
        xhr.addEventListener('load', function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    uploadStatus.innerHTML = '<span style="color: green;">✓ تم رفع ' + file.name + ' بنجاح</span>';
                    progressBar.classList.remove('progress-bar-animated');
                    progressBar.classList.add('bg-success');
                    
                    // Reload page after 1 second to show new file
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    uploadStatus.innerHTML = '<span style="color: red;">✗ خطأ: ' + (response.message || 'فشل الرفع') + '</span>';
                    progressBar.classList.add('bg-danger');
                }
            } else {
                uploadStatus.innerHTML = '<span style="color: red;">✗ خطأ في رفع الملف</span>';
                progressBar.classList.add('bg-danger');
            }
        });
        
        // Upload error
        xhr.addEventListener('error', function() {
            uploadStatus.innerHTML = '<span style="color: red;">✗ حدث خطأ أثناء الرفع</span>';
            progressBar.classList.add('bg-danger');
        });
        
        xhr.open('POST', '{{route("admin.files.upload")}}');
        xhr.send(formData);
    }
})();

// Copy file link to clipboard
function copyFileLink(button, url) {
    // Create temporary input element
    const input = document.createElement('input');
    input.value = url;
    document.body.appendChild(input);
    input.select();
    input.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        // Show feedback
        const originalText = button.innerHTML;
        button.innerHTML = '<span class="fas fa-check"></span> تم النسخ!';
        button.classList.remove('btn-outline-primary');
        button.classList.add('btn-success');
        
        setTimeout(function() {
            button.innerHTML = originalText;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-primary');
        }, 2000);
    } catch (err) {
        alert('فشل نسخ الرابط. الرابط: ' + url);
    }
    
    document.body.removeChild(input);
}
</script>
@endcan
@endsection
