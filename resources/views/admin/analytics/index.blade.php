@extends('layouts.admin', ['page_title' => 'Analytics Sites'])

@section('content')
<div class="col-12 p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Analytics Sites @if(isset($isSuperAdmin) && $isSuperAdmin) <small class="text-muted">(All Sites - Superadmin View)</small> @endif</h4>
        <a href="{{ request()->routeIs('admin.*') ? route('admin.analytics.create') : route('user.analytics.create') }}" class="btn btn-primary">Create New Site</a>
    </div>
    
    @if(isset($pendingInvitations) && $pendingInvitations->count() > 0)
    <div class="alert alert-info mb-3">
        <h5>Pending Invitations</h5>
        <ul class="mb-0">
            @foreach($pendingInvitations as $invitation)
            <li>
                You've been invited to manage <strong>{{ $invitation->site->domain }}</strong>
                <a href="{{ route('user.analytics.accept-invitation', $invitation->token) }}" class="btn btn-sm btn-success ml-2">Accept</a>
                <a href="{{ route('user.analytics.reject-invitation', $invitation->token) }}" class="btn btn-sm btn-danger ml-2">Reject</a>
            </li>
            @endforeach
        </ul>
    </div>
    @endif
    
    <div class="col-12 row p-4" style="padding: 30px 0px;position: relative;background: #fff;overflow-x: auto;">
        <table class="table table-striped table-bordered col-12">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Domain</th>
                    @if(isset($isSuperAdmin) && $isSuperAdmin)
                    <th>Owner</th>
                    @endif
                    <th>Site Key</th>
                    <th>Sessions</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sites as $site)
                <tr>
                    <td>{{ $site->id }}</td>
                    <td>{{ $site->domain }}</td>
                    @if(isset($isSuperAdmin) && $isSuperAdmin)
                    <td>
                        @if($site->owner)
                            {{ $site->owner->name }} ({{ $site->owner->email }})
                        @else
                            <span class="text-muted">No owner</span>
                        @endif
                    </td>
                    @endif
                    <td><code>{{ $site->site_key }}</code></td>
                    <td>{{ $site->sessions_count }}</td>
                    <td>{{ $site->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        @if(isset($isSuperAdmin) && $isSuperAdmin)
                            <a href="{{ route('admin.analytics.show', $site->id) }}" class="btn btn-sm btn-info">View Dashboard</a>
                            <a href="{{ route('admin.analytics.tracking-code', $site->id) }}" class="btn btn-sm btn-success">Get Code</a>
                            <a href="{{ route('admin.analytics.members', $site->id) }}" class="btn btn-sm btn-secondary">Manage Team</a>
                        @else
                            <a href="{{ route('user.analytics.show', $site->id) }}" class="btn btn-sm btn-info">View Dashboard</a>
                            <a href="{{ route('user.analytics.tracking-code', $site->id) }}" class="btn btn-sm btn-success">Get Code</a>
                            @if($site->user_id == auth()->id())
                                <a href="{{ route('user.analytics.members', $site->id) }}" class="btn btn-sm btn-secondary">Manage Team</a>
                            @endif
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ isset($isSuperAdmin) && $isSuperAdmin ? '7' : '6' }}" class="text-center">No analytics sites found. <a href="{{ request()->routeIs('admin.*') ? route('admin.analytics.create') : route('user.analytics.create') }}">Create one</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="d-flex justify-content-center">
        {{ $sites->links() }}
    </div>
</div>
@endsection

