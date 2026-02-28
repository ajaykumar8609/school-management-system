@extends('layouts.app')

@section('title', 'Academic')
@section('header-title', 'Academic Management')

@section('content')
<div class="card">
    <form method="get" style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:20px;">
        <div class="form-group" style="margin:0;">
            <label class="form-label">Class</label>
            <select name="class_id" class="form-control" onchange="this.form.submit()">
                <option value="">Select</option>
                @foreach($classes as $c)
                <option value="{{ $c->id }}" {{ ($classId ?? '') == $c->id ? 'selected' : '' }}>{{ $c->class_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label">Exam</label>
            <select name="exam_id" class="form-control" onchange="this.form.submit()">
                <option value="">Select</option>
                @foreach($exams as $e)
                <option value="{{ $e->id }}" {{ ($examId ?? '') == $e->id ? 'selected' : '' }}>{{ $e->exam_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label">Subject</label>
            <select name="subject_id" class="form-control" onchange="this.form.submit()">
                <option value="">Select</option>
                @foreach($subjects as $sub)
                <option value="{{ $sub->id }}" {{ ($subjectId ?? '') == $sub->id ? 'selected' : '' }}>{{ $sub->subject_name }}</option>
                @endforeach
            </select>
        </div>
    </form>

    @if($students->isNotEmpty())
    <form action="{{ route('academic.save-marks') }}" method="post">
        @csrf
        <input type="hidden" name="exam_id" value="{{ $examId }}">
        <input type="hidden" name="subject_id" value="{{ $subjectId }}">
        <table>
            <thead>
                <tr><th>Student Name</th><th>Roll No</th><th>Marks Obtained</th><th>Total Marks</th><th>Grade</th></tr>
            </thead>
            <tbody>
                @foreach($students as $s)
                <tr>
                    <td>{{ $s->full_name }}</td>
                    <td>{{ $s->roll_no ?? '-' }}</td>
                    <td>
                        <input type="number" step="0.01" name="marks[{{ $loop->index }}][marks_obtained]" class="form-control" value="{{ $s->marks_obtained ?? '' }}" min="0" style="width:100px;">
                        <input type="hidden" name="marks[{{ $loop->index }}][student_id]" value="{{ $s->id }}">
                    </td>
                    <td><input type="number" step="0.01" name="marks[{{ $loop->index }}][total_marks]" class="form-control" value="{{ $s->total_marks ?? 100 }}" min="0" style="width:100px;"></td>
                    <td>{{ $s->grade ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary" style="margin-top:16px;">Save Marks</button>
    </form>
    @else
    <p>Select Class, Exam and Subject to enter marks.</p>
    @endif
</div>
@endsection
