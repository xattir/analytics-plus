@extends('layouts.admin')
@section('content')

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


		<div class="col-12 py-2 px-2 row">
			<div class="col-12 col-lg-4 p-2">
				<form method="GET">
					<input type="text" name="q" class="form-control" placeholder="بحث ... " value="{{request()->get('q')}}">
				</form>
			</div>
		</div>
		<div class="col-12 p-3" style="overflow:auto">
			<div class="col-12 p-0" style="min-width:1100px;min-height:50dvh">
				
			
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th>#</th>
						<th>الملف</th>
						<th>تاريخ الرفع</th> 
						<th>تحكم</th>
					</tr>
				</thead>  
				<tbody id="filesTableBody">
					@forelse($files as $file)
					<tr class="file-item" data-file-id="{{$file->id}}">
						<td>{{$file->id}}</td>
						<td>
							@php
								$extension = '';
								if($file->file_name) {
									$extension = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
								}
								$mimeType = $file->mime_type ?? '';
								$isImage = strpos($mimeType, 'image/') === 0 || in_array($extension, ['jpg','jpeg','gif','png','webp']);
								$isVideo = strpos($mimeType, 'video/') === 0 || in_array($extension, ['mp4','avi','mov','wmv','flv','webm']);
							@endphp
							<div class="d-flex align-items-center">
								@if($isImage)
								<img src="{{$file->getUrl()}}" style="width:40px; height:40px; object-fit:cover; border-radius:4px; margin-left:8px;">
								@elseif($isVideo)
								<div style="width:40px; height:40px; background:#000; border-radius:4px; display:flex; align-items:center; justify-content:center; margin-left:8px;">
									<i class="fas fa-video text-white"></i>
								</div>
								@else
								<div style="width:40px; height:40px; background:#6c757d; border-radius:4px; display:flex; align-items:center; justify-content:center; margin-left:8px;">
									<i class="fas fa-file text-white"></i>
								</div>
								@endif
								<div>
									<div><strong>{{$file->name}}</strong></div>
									<small class="text-muted">{{ number_format($file->size / (1024), 2)}} KB</small>
								</div>
							</div>
						</td>
					    <td>{{$file->created_at->format('Y-m-d H:i')}}</td>
						<td style="width: 1%;text-wrap: nowrap;">
							@can('hub-files-read')
							<button class="btn btn-outline-primary btn-sm font-1 mx-1 py-1 px-2" onclick="copyFileLink(this, '{{$file->getUrl()}}')">
								<span class="fal fa-copy"></span> نسخ الرابط
							</button>
							<a href="{{$file->getUrl()}}" target="_blank" class="btn btn-outline-success btn-sm font-1 mx-1 py-1 px-2">
								<span class="fal fa-eye"></span> عرض
							</a>
							@endcan
							@can('hub-files-delete')
							<form method="POST" action="{{route('admin.files.destroy',$file)}}" class="d-inline-block">@csrf @method("DELETE")
								<button class="btn btn-outline-danger btn-sm font-1 mx-1 py-1 px-2" onclick="var result = confirm('هل أنت متأكد من عملية الحذف ؟');if(result){}else{event.preventDefault()}">
									<span class="fal fa-trash-can"></span> حذف
								</button>
							</form>
							@endcan
						</td>
					</tr>
					@empty
					<tr>
						<td colspan="4" class="text-center py-5">
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
<script>
// Copy file link to clipboard
function copyFileLink(button, url) {
    const input = document.createElement('input');
    input.value = url;
    document.body.appendChild(input);
    input.select();
    input.setSelectionRange(0, 99999);
    
    try {
        document.execCommand('copy');
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

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Add file to table after upload (called from modal)
function addFileToTable(fileData) {
    const tbody = document.getElementById('filesTableBody');
    if (!tbody) return;
    
    // Remove empty row if exists
    const emptyRow = tbody.querySelector('tr td[colspan]');
    if (emptyRow) {
        emptyRow.closest('tr').remove();
    }
    
    // Determine file type
    const mimeType = fileData.mime_type || '';
    const extension = fileData.file_name ? fileData.file_name.split('.').pop().toLowerCase() : '';
    const isImage = mimeType.startsWith('image/') || ['jpg','jpeg','gif','png','webp'].includes(extension);
    const isVideo = mimeType.startsWith('video/') || ['mp4','avi','mov','wmv','flv','webm'].includes(extension);
    
    let previewHtml = '';
    if (isImage) {
        previewHtml = '<img src="' + escapeHtml(fileData.url) + '" style="width:40px; height:40px; object-fit:cover; border-radius:4px; margin-left:8px;">';
    } else if (isVideo) {
        previewHtml = '<div style="width:40px; height:40px; background:#000; border-radius:4px; display:flex; align-items:center; justify-content:center; margin-left:8px;"><i class="fas fa-video text-white"></i></div>';
    } else {
        previewHtml = '<div style="width:40px; height:40px; background:#6c757d; border-radius:4px; display:flex; align-items:center; justify-content:center; margin-left:8px;"><i class="fas fa-file text-white"></i></div>';
    }
    
    const sizeKB = (fileData.size / 1024).toFixed(2);
    const createdAt = new Date(fileData.created_at).toLocaleDateString('ar-EG', {year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'});
    
    const row = document.createElement('tr');
    row.className = 'file-item';
    row.setAttribute('data-file-id', fileData.id);
    
    const csrfToken = '@json(csrf_token())';
    const deleteUrl = '@json(route("admin.files.index"))' + '/' + fileData.id;
    
    row.innerHTML = `
        <td>${fileData.id}</td>
        <td>
            <div class="d-flex align-items-center">
                ${previewHtml}
                <div>
                    <div><strong>${escapeHtml(fileData.name)}</strong></div>
                    <small class="text-muted">${sizeKB} KB</small>
                </div>
            </div>
        </td>
        <td>${createdAt}</td>
        <td style="width: 1%;text-wrap: nowrap;">
            <button class="btn btn-outline-primary btn-sm font-1 mx-1 py-1 px-2" onclick="copyFileLink(this, '${escapeHtml(fileData.url).replace(/'/g, "\\'")}')">
                <span class="fal fa-copy"></span> نسخ الرابط
            </button>
            <a href="${escapeHtml(fileData.url)}" target="_blank" class="btn btn-outline-success btn-sm font-1 mx-1 py-1 px-2">
                <span class="fal fa-eye"></span> عرض
            </a>
            <form method="POST" action="${deleteUrl}" class="d-inline-block">
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <input type="hidden" name="_method" value="DELETE">
                <button class="btn btn-outline-danger btn-sm font-1 mx-1 py-1 px-2" onclick="var result = confirm('هل أنت متأكد من عملية الحذف ؟');if(!result){event.preventDefault()}">
                    <span class="fal fa-trash-can"></span> حذف
                </button>
            </form>
        </td>
    `;
    
    tbody.insertBefore(row, tbody.firstChild);
}
</script>
@endsection
