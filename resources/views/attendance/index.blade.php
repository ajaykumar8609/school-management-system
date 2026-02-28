@extends('layouts.app')

@section('title', 'Attendance')
@section('header-title', 'Attendance Management')

@section('content')
<div class="card">
    <form method="get" action="{{ route('attendance.index') }}" style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:20px;">
        <div class="form-group" style="margin:0;">
            <label class="form-label">Class</label>
            <select name="class_id" id="att_class" class="form-control" required onchange="this.form.submit()">
                <option value="">Select</option>
                @foreach($classes as $c)
                <option value="{{ $c->id }}" {{ ($classId ?? '') == $c->id ? 'selected' : '' }}>{{ $c->class_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label">Section</label>
            <select name="section_id" class="form-control" required onchange="this.form.submit()">
                <option value="">Select</option>
                @foreach($sections as $s)
                <option value="{{ $s->id }}" {{ ($sectionId ?? '') == $s->id ? 'selected' : '' }}>{{ $s->schoolClass?->class_name }}-{{ $s->section_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label">Date</label>
            <input type="date" name="date" class="form-control" value="{{ $date ?? date('Y-m-d') }}" onchange="this.form.submit()">
        </div>
    </form>

    @if($students->isNotEmpty())
    <form action="{{ route('attendance.submit') }}" method="post">
        @csrf
        <input type="hidden" name="class_id" value="{{ $classId }}">
        <input type="hidden" name="section_id" value="{{ $sectionId }}">
        <input type="hidden" name="date" value="{{ $date }}">
        <div style="margin-bottom:16px;">
            <button type="button" onclick="markAll('Present')" class="btn btn-sm btn-success">Mark All Present</button>
            <button type="button" onclick="markAll('Absent')" class="btn btn-sm btn-secondary">Mark All Absent</button>
        </div>
        <table>
            <thead>
                <tr><th>Photo</th><th>Student Name</th><th>Roll No</th><th>Status</th><th>Remarks</th></tr>
            </thead>
            <tbody>
                @foreach($students as $s)
                <tr>
                    <td>
                        @if($s->photo)
                        <img src="{{ asset('storage/' . $s->photo) }}" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                        @else
                        <div style="width:36px;height:36px;border-radius:50%;background:var(--gray-200);display:flex;align-items:center;justify-content:center;font-weight:600;">{{ substr($s->first_name,0,1) }}</div>
                        @endif
                    </td>
                    <td>{{ $s->full_name }}</td>
                    <td>{{ $s->roll_no ?? '-' }}</td>
                    <td>
                        <select name="attendance[{{ $loop->index }}][status]" class="form-control status-select" style="min-width:120px;">
                            <option value="Present" {{ ($s->attendance_status ?? '') == 'Present' ? 'selected' : '' }}>Present</option>
                            <option value="Absent" {{ ($s->attendance_status ?? '') == 'Absent' ? 'selected' : '' }}>Absent</option>
                            <option value="Late" {{ ($s->attendance_status ?? '') == 'Late' ? 'selected' : '' }}>Late</option>
                        </select>
                        <input type="hidden" name="attendance[{{ $loop->index }}][student_id]" value="{{ $s->id }}">
                    </td>
                    <td><input type="text" name="attendance[{{ $loop->index }}][remarks]" class="form-control" value="{{ $s->attendance_remarks ?? '' }}" placeholder="Remarks"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary" style="margin-top:16px;">Submit Attendance</button>
    </form>
    @else
    <p>Select Class, Section and Date to load students.</p>
    @endif
</div>

@push('scripts')
<script>
function markAll(status) {
    document.querySelectorAll('.status-select').forEach(el => el.value = status);
}
</script>
@endpush
@endsection
