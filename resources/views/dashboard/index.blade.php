@extends('layouts.app')

@section('title', 'Dashboard')
@section('header-title', 'Student Admissions')

@section('content')
<div class="grid-4" style="margin-bottom:24px;">
    <div class="card" style="background:linear-gradient(135deg,#2563eb,#3b82f6);color:white;border:none;">
        <div style="font-size:14px;opacity:0.9;">Total Students</div>
        <div style="font-size:28px;font-weight:700;">{{ $totalStudents }}</div>
    </div>
    <div class="card" style="background:linear-gradient(135deg,#10b981,#34d399);color:white;border:none;">
        <div style="font-size:14px;opacity:0.9;">Active Students</div>
        <div style="font-size:28px;font-weight:700;">{{ $activeStudents }}</div>
    </div>
    <div class="card" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa);color:white;border:none;">
        <div style="font-size:14px;opacity:0.9;">New Admissions This Month</div>
        <div style="font-size:28px;font-weight:700;">{{ $newAdmissions }}</div>
    </div>
    <div class="card" style="background:linear-gradient(135deg,#ef4444,#f87171);color:white;border:none;">
        <div style="font-size:14px;opacity:0.9;">Students With Due Fees</div>
        <div style="font-size:28px;font-weight:700;">{{ $studentsWithDueFees }}</div>
    </div>
</div>
<div class="grid-4" style="margin-bottom:24px;">
    <div class="card" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);color:white;border:none;">
        <div style="font-size:14px;opacity:0.9;">Total Fee Collection (This Month)</div>
        <div style="font-size:24px;font-weight:700;">₹{{ number_format($feeCollected) }}</div>
    </div>
</div>

<div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
    <a href="{{ route('students.create') }}" class="btn btn-primary">➕ Add Student</a>
    <a href="{{ route('students.index') }}" class="btn btn-secondary">📋 Student List</a>
    <a href="{{ route('attendance.index') }}" class="btn btn-secondary">📅 Attendance</a>
    <a href="{{ route('academic.index') }}" class="btn btn-secondary">📚 Academic</a>
    <a href="{{ route('fee.index') }}" class="btn btn-secondary">💰 Fee Management</a>
</div>

<div class="grid-2">
    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 class="card-title">Recent Admissions</h3>
            <a href="{{ route('students.index') }}" class="btn btn-sm btn-secondary">View All</a>
        </div>
        <table>
            <thead><tr><th>Name</th><th>Class</th><th>Admission Date</th></tr></thead>
            <tbody>
                @forelse($recentAdmissions as $s)
                <tr>
                    <td>{{ $s->full_name }}</td>
                    <td>{{ $s->schoolClass?->class_name }}{{ $s->section?->section_name ? ' (' . $s->section->section_name . ')' : '' }}</td>
                    <td>{{ $s->admission_date?->format('d M Y') ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="3">No admissions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 class="card-title">Notices</h3>
            <a href="#" class="btn btn-sm btn-secondary">View All</a>
        </div>
        <ul style="list-style:none;">
            @forelse($recentNotices as $n)
            <li style="padding:10px 0;border-bottom:1px solid var(--gray-200);">{{ $n->title }} ({{ $n->notice_date?->format('d M') }})</li>
            @empty
            <li>No notices.</li>
            @endforelse
        </ul>
    </div>
</div>

<div class="card" style="margin-top:20px;">
    <h3 class="card-title">Attendance Overview</h3>
    <div style="height:280px;">
        <canvas id="attendanceChart"></canvas>
    </div>
</div>

<div class="grid-2" style="margin-top:20px;">
    <div class="card">
        <h3 class="card-title">Class-wise Student Distribution</h3>
        <div style="height:250px;">
            <canvas id="classChart"></canvas>
        </div>
    </div>
    <div class="card">
        <h3 class="card-title">Present vs Absent Today</h3>
        <p style="margin-bottom:12px;">Present: <strong>{{ $presentToday }}</strong> | Absent: <strong>{{ $absentToday }}</strong></p>
        <div style="height:250px;">
            <canvas id="presentChart"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script>
const attendanceData = @json($attendanceByDay);
new Chart(document.getElementById('attendanceChart'), {
    type: 'line',
    data: {
        labels: Object.keys(attendanceData),
        datasets: [
            { label: 'Present', data: Object.values(attendanceData).map(d=>d.present), borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.1)', fill: true },
            { label: 'Absent', data: Object.values(attendanceData).map(d=>d.absent), borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,0.1)', fill: true }
        ]
    },
    options: { responsive: true, maintainAspectRatio: false }
});
const classData = @json($classDistribution);
new Chart(document.getElementById('classChart'), {
    type: 'pie',
    data: {
        labels: classData.map(d=>d.label),
        datasets: [{ data: classData.map(d=>d.count), backgroundColor: ['#2563eb','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899'] }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});
new Chart(document.getElementById('presentChart'), {
    type: 'doughnut',
    data: {
        labels: ['Present', 'Absent'],
        datasets: [{ data: [{{ $presentToday }}, {{ $absentToday }}], backgroundColor: ['#10b981', '#ef4444'] }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});
</script>
@endpush
@endsection
