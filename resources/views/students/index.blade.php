@extends('layouts.app')

@section('title', 'Student List')
@section('header-title', 'Student Management')

@section('content')
<div class="card">
    <form method="get" action="{{ route('students.index') }}" style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;align-items:flex-end;">
        <div class="form-group" style="margin:0;flex:1;min-width:200px;">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Search by Name / Roll No / Contact" value="{{ request('search') }}">
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label">Class</label>
            <select name="class_id" class="form-control" style="min-width:120px;">
                <option value="">All</option>
                @foreach($classes as $c)
                <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->class_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label">Section</label>
            <select name="section_id" class="form-control" style="min-width:100px;">
                <option value="">All</option>
                @foreach($sections as $s)
                <option value="{{ $s->id }}" {{ request('section_id') == $s->id ? 'selected' : '' }}>{{ $s->schoolClass?->class_name }}-{{ $s->section_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label">Status</label>
            <select name="status" class="form-control" style="min-width:100px;">
                <option value="">All</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <button type="submit" class="btn btn-secondary">Search</button>
        <a href="{{ route('students.index') }}" class="btn btn-secondary">Clear</a>
        <a href="{{ route('students.create') }}" class="btn btn-primary">➕ Add Student</a>
    </form>

    <table>
        <thead>
            <tr>
                <th>Photo</th>
                <th>Name</th>
                <th>Roll No</th>
                <th>Class</th>
                <th>Section</th>
                <th>Contact</th>
                <th>Fee Status</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $s)
            @php
                $total = $s->fees->sum('final_amount') ?: 0;
                $paid = $s->feePayments->sum('amount');
                $feeStatus = $paid >= $total ? 'Paid' : ($paid > 0 ? 'Partial' : 'Due');
            @endphp
            <tr>
                <td>
                    @if($s->photo)
                    <span style="display:inline-flex;width:40px;height:40px;border-radius:50%;overflow:hidden;background:var(--gray-200);align-items:center;justify-content:center;font-weight:600;">
                        <img src="{{ asset('storage/' . $s->photo) }}" alt="" style="width:100%;height:100%;object-fit:cover;" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <span style="display:none;">{{ substr($s->first_name,0,1) }}</span>
                    </span>
                    @else
                    <div style="width:40px;height:40px;border-radius:50%;background:var(--gray-200);display:flex;align-items:center;justify-content:center;font-weight:600;">{{ substr($s->first_name,0,1) }}</div>
                    @endif
                </td>
                <td>{{ $s->full_name }}</td>
                <td>{{ $s->roll_no ?? '-' }}</td>
                <td>{{ $s->schoolClass?->class_name ?? '-' }}</td>
                <td>{{ $s->section?->section_name ?? '-' }}</td>
                <td>{{ $s->parent_contact ?? '-' }}</td>
                <td>
                    <span class="badge {{ $feeStatus === 'Paid' ? 'badge-success' : ($feeStatus === 'Partial' ? 'badge-warning' : 'badge-danger') }}">{{ $feeStatus }}</span>
                </td>
                <td>
                    <span class="badge {{ $s->status ? 'badge-success' : 'badge-danger' }}">{{ $s->status ? 'Active' : 'Inactive' }}</span>
                </td>
                <td style="white-space:nowrap;">
                    <a href="{{ route('students.show', $s) }}" class="btn btn-sm btn-primary" title="View">👁</a>
                    <a href="{{ route('students.edit', $s) }}" class="btn btn-sm btn-secondary" title="Edit">✏</a>
                    <a href="{{ route('students.academic', $s) }}" class="btn btn-sm btn-secondary" title="Academic">📊</a>
                    <a href="{{ route('students.fee-report', $s) }}" class="btn btn-sm btn-secondary" title="Fee Report">💰</a>
                    <form action="{{ route('students.destroy', $s) }}" method="post" style="display:inline;" onsubmit="return confirm('Delete this student?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">🗑</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="9">No students found.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $students->withQueryString()->links() }}
</div>
@endsection
