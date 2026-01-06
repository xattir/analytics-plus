@extends('layouts.admin', ['page_title' => 'إدارة الفريق'])

@section('content')
<style>
    .members-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
    }
    
    .members-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(123, 96, 251, 0.1);
        backdrop-filter: blur(10px);
        margin-bottom: 24px;
    }
    
    .members-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
        padding-bottom: 24px;
        border-bottom: 2px solid rgba(123, 96, 251, 0.1);
    }
    
    .members-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
        background: linear-gradient(135deg, #7b60fb 0%, #667eea 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .members-header-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #7b60fb 0%, #667eea 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(123, 96, 251, 0.3);
    }
    
    .members-header-icon svg {
        width: 24px;
        height: 24px;
        color: white;
    }
    
    .btn-modern {
        padding: 12px 24px;
        font-size: 15px;
        font-weight: 600;
        border-radius: 12px;
        border: none;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    
    .btn-modern-secondary {
        background: #f3f4f6;
        color: #374151;
    }
    
    .btn-modern-secondary:hover {
        background: #e5e7eb;
        color: #1f2937;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .section-title {
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 24px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .section-title svg {
        width: 24px;
        height: 24px;
        color: #7b60fb;
    }
    
    .owner-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        background: linear-gradient(135deg, rgba(123, 96, 251, 0.1) 0%, rgba(123, 96, 251, 0.05) 100%);
        border-radius: 12px;
        border: 1px solid rgba(123, 96, 251, 0.2);
        margin-bottom: 24px;
    }
    
    .owner-badge svg {
        width: 20px;
        height: 20px;
        color: #7b60fb;
    }
    
    .owner-badge strong {
        color: #1f2937;
        font-weight: 600;
    }
    
    .owner-badge span {
        color: #6b7280;
        font-size: 14px;
    }
    
    .table-modern {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }
    
    .table-modern thead {
        background: linear-gradient(135deg, rgba(123, 96, 251, 0.1) 0%, rgba(123, 96, 251, 0.05) 100%);
    }
    
    .table-modern thead th {
        padding: 16px 20px;
        font-weight: 600;
        color: #1f2937;
        font-size: 14px;
        text-align: right;
        border: none;
    }
    
    .table-modern tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .table-modern tbody tr:hover {
        background: rgba(123, 96, 251, 0.03);
    }
    
    .table-modern tbody tr:last-child {
        border-bottom: none;
    }
    
    .table-modern tbody td {
        padding: 16px 20px;
        color: #374151;
        font-size: 14px;
        vertical-align: middle;
    }
    
    .btn-sm-modern {
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .btn-danger-modern {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
    }
    
    .btn-danger-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        color: white;
    }
    
    .form-modern {
        display: flex;
        gap: 12px;
        margin-top: 24px;
    }
    
    .form-input-modern {
        flex: 1;
        padding: 14px 18px;
        font-size: 15px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        color: #1f2937;
    }
    
    .form-input-modern:focus {
        outline: none;
        border-color: #7b60fb;
        box-shadow: 0 0 0 4px rgba(123, 96, 251, 0.1);
    }
    
    .btn-primary-modern {
        background: linear-gradient(135deg, #7b60fb 0%, #667eea 100%);
        color: white;
        padding: 14px 32px;
        font-size: 15px;
        font-weight: 600;
        border-radius: 12px;
        border: none;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(123, 96, 251, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-primary-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(123, 96, 251, 0.4);
        color: white;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }
    
    .empty-state svg {
        width: 64px;
        height: 64px;
        margin: 0 auto 16px;
        color: #d1d5db;
    }
    
    .empty-state p {
        font-size: 16px;
        margin: 0;
    }
    
    @media (max-width: 768px) {
        .members-container {
            padding: 20px 16px;
        }
        
        .members-card {
            padding: 24px 20px;
        }
        
        .members-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }
        
        .form-modern {
            flex-direction: column;
        }
        
        .table-modern {
            font-size: 12px;
        }
        
        .table-modern thead th,
        .table-modern tbody td {
            padding: 12px 12px;
        }
    }
</style>

<div class="members-container">
    <div class="members-card">
        <div class="members-header">
            <h1>
                <div class="members-header-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                إدارة الفريق: {{ $site->title ?? $site->domain }}
            </h1>
            <a href="{{ request()->routeIs('admin.*') ? route('admin.analytics.show', ['site' => $site->site_key]) : route('user.analytics.show', ['site' => $site->site_key]) }}" class="btn-modern btn-modern-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>العودة إلى لوحة التحكم</span>
            </a>
        </div>
        
        <!-- Current Members -->
        <div>
            <h2 class="section-title">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                الأعضاء الحاليون
            </h2>
            
            <div class="owner-badge">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <strong>المالك:</strong>
                <span>{{ $site->owner->name }} ({{ $site->owner->email }})</span>
            </div>
            
            @if($members->count() > 0)
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>تاريخ الإضافة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $member)
                    <tr>
                        <td>{{ $member->name }}</td>
                        <td>{{ $member->email }}</td>
                        <td>{{ $member->pivot->created_at->format('Y-m-d') }}</td>
                        <td>
                            <form method="POST" action="{{ request()->routeIs('admin.*') ? route('admin.analytics.remove-member', ['site' => $site->site_key]) : route('user.analytics.remove-member', ['site' => $site->site_key]) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من إزالة هذا العضو؟')">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $member->id }}">
                                <button type="submit" class="btn-sm-modern btn-danger-modern">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                    <span>إزالة</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <p>لا يوجد أعضاء إضافيون بعد.</p>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Send Invitation -->
    <div class="members-card">
        <h2 class="section-title">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
            </svg>
            دعوة عضو جديد
        </h2>
        <form method="POST" action="{{ request()->routeIs('admin.*') ? route('admin.analytics.invite', ['site' => $site->site_key]) : route('user.analytics.invite', ['site' => $site->site_key]) }}" class="form-modern">
            @csrf
            <input type="email" name="email" class="form-input-modern" placeholder="أدخل عنوان البريد الإلكتروني" required>
            <button type="submit" class="btn-primary-modern">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                </svg>
                <span>إرسال الدعوة</span>
            </button>
        </form>
    </div>
    
    <!-- Pending Invitations -->
    @if($invitations->count() > 0)
    <div class="members-card">
        <h2 class="section-title">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            الدعوات المعلقة
        </h2>
        <table class="table-modern">
            <thead>
                <tr>
                    <th>البريد الإلكتروني</th>
                    <th>تاريخ الإرسال</th>
                    <th>تاريخ الانتهاء</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invitations as $invitation)
                <tr>
                    <td>{{ $invitation->email }}</td>
                    <td>{{ $invitation->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $invitation->expires_at ? $invitation->expires_at->format('Y-m-d H:i') : 'أبداً' }}</td>
                    <td>
                        <form method="POST" action="{{ request()->routeIs('admin.*') ? route('admin.analytics.cancel-invitation', $invitation->id) : route('user.analytics.cancel-invitation', $invitation->id) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من إلغاء هذه الدعوة؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-sm-modern btn-danger-modern">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                                <span>إلغاء</span>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
