@extends('layouts.app')

@section('title', 'Academic Record - ' . $student->full_name)
@section('header-title', 'Academic Record')

@section('content')
<div class="card" style="margin-bottom:20px;">
    <p><strong>{{ $student->full_name }}</strong> | Class {{ $student->schoolClass?->class_name }}{{ $student->section?->section_name ?? '' }} | Roll: {{ $student->roll_no ?? '-' }}</p>
</div>

<div class="card">
    <form method="get" style="margin-bottom:16px;">
        <label>Filter by Exam:</label>
        <select name="exam_id" onchange="this.form.submit()">
            <option value="">All</option>
            @foreach($exams as $e)
            <option value="{{ $e->id }}" {{ request('exam_id') == $e->id ? 'selected' : '' }}>{{ $e->exam_name }}</option>
            @endforeach
        </select>
    </form>

    @php
        $marks = $student->marks;
        if (request('exam_id')) $marks = $marks->where('exam_id', request('exam_id'));
        $totalObtained = $marks->sum('marks_obtained');
        $totalMax = $marks->sum('total_marks');
        $overallPct = $totalMax > 0 ? round(($totalObtained / $totalMax) * 100, 2) : 0;
        $overallGrade = $overallPct >= 90 ? 'A+' : ($overallPct >= 80 ? 'A' : ($overallPct >= 70 ? 'B' : ($overallPct >= 60 ? 'C' : ($overallPct >= 50 ? 'D' : 'F'))));
    @endphp

    <table>
        <thead><tr><th>Exam</th><th>Subject</th><th>Marks Obtained</th><th>Total Marks</th><th>%</th><th>Grade</th></tr></thead>
        <tbody>
            @forelse($marks as $m)
            <tr>
                <td>{{ $m->exam?->exam_name }}</td>
                <td>{{ $m->subject?->subject_name }}</td>
                <td>{{ $m->marks_obtained }}</td>
                <td>{{ $m->total_marks }}</td>
                <td>{{ $m->percentage }}%</td>
                <td>{{ $m->grade ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="6">No records.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($marks->isNotEmpty())
    <p style="margin-top:16px;"><strong>Total:</strong> {{ $totalObtained }} / {{ $totalMax }} | <strong>Overall %:</strong> {{ $overallPct }}% | <strong>Grade:</strong> {{ $overallGrade }}</p>
    @endif
</div>
<a href="{{ route('students.show', $student) }}" class="btn btn-secondary">← Back to Profile</a>
@endsection
