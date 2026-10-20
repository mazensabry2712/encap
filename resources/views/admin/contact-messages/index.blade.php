@extends('admin.layout')
@section('page-title', 'Contact messages')
@section('content')
<div class="admin-page-intro"><div><p>General messages submitted through the public contact form. Update each status as the team follows up.</p></div></div>
<div class="admin-panel"><div class="admin-panel-head"><div><h2>Message inbox</h2><p>{{ $messages->total() }} messages in this view</p></div></div><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Received</th><th>Sender</th><th>Subject & message</th><th>Status</th></tr></thead><tbody>
@forelse($messages as $item)<tr><td>{{ $item->created_at->format('Y-m-d H:i') }}</td><td><strong>{{ $item->name }}</strong><br><a href="mailto:{{ $item->email }}">{{ $item->email }}</a><br><span class="admin-muted">{{ $item->company_name }}</span><br><span class="admin-muted">{{ $item->phone }}</span></td><td><strong>{{ $item->subject }}</strong><p>{{ $item->message }}</p></td><td><form method="post" action="{{ route('admin.contact-messages.update', $item) }}">@csrf @method('PATCH')<select class="admin-control" name="status" aria-label="Message status">@foreach(['new' => 'New', 'reviewing' => 'Reviewing', 'resolved' => 'Resolved'] as $value => $label)<option value="{{ $value }}" @selected($item->status === $value)>{{ $label }}</option>@endforeach</select><p><button type="submit">Update status</button></p></form><span class="admin-badge {{ $item->status }}">{{ ucfirst($item->status) }}</span></td></tr>
@empty<tr><td colspan="4"><strong>No contact messages yet.</strong><p class="admin-muted">New enquiries appear here after visitors submit the contact form.</p></td></tr>@endforelse
</tbody></table></div><div class="pagination">{{ $messages->links() }}</div></div>
@endsection
