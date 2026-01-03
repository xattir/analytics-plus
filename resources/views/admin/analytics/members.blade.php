@extends('layouts.admin', ['page_title' => 'Manage Team - ' . $site->domain])

@section('content')
<div class="col-12 p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Manage Team: {{ $site->domain }}</h4>
        <a href="{{ request()->routeIs('admin.*') ? route('admin.analytics.show', $site->id) : route('user.analytics.show', $site->id) }}" class="btn btn-secondary">Back to Dashboard</a>
    </div>
    
    <!-- Current Members -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Current Members</h5>
        </div>
        <div class="card-body">
            <div class="mb-2">
                <strong>Owner:</strong> {{ $site->owner->name }} ({{ $site->owner->email }})
            </div>
            
            @if($members->count() > 0)
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $member)
                    <tr>
                        <td>{{ $member->name }}</td>
                        <td>{{ $member->email }}</td>
                        <td>{{ $member->pivot->created_at->format('Y-m-d') }}</td>
                        <td>
                            <form method="POST" action="{{ request()->routeIs('admin.*') ? route('admin.analytics.remove-member', $site->id) : route('user.analytics.remove-member', $site->id) }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $member->id }}">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-muted">No additional members yet.</p>
            @endif
        </div>
    </div>
    
    <!-- Send Invitation -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Invite New Member</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ request()->routeIs('admin.*') ? route('admin.analytics.invite', $site->id) : route('user.analytics.invite', $site->id) }}">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <input type="email" name="email" class="form-control" placeholder="Enter email address" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Send Invitation</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Pending Invitations -->
    @if($invitations->count() > 0)
    <div class="card">
        <div class="card-header">
            <h5>Pending Invitations</h5>
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Sent</th>
                        <th>Expires</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invitations as $invitation)
                    <tr>
                        <td>{{ $invitation->email }}</td>
                        <td>{{ $invitation->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $invitation->expires_at ? $invitation->expires_at->format('Y-m-d H:i') : 'Never' }}</td>
                        <td>
                                <form method="POST" action="{{ request()->routeIs('admin.*') ? route('admin.analytics.cancel-invitation', $invitation->id) : route('user.analytics.cancel-invitation', $invitation->id) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Cancel</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection

