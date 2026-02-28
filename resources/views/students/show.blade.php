@extends('layouts.app')

@section('title', $student->full_name)
@section('header-title', 'Student Profile')

@section('content')
<div class="card" style="display:flex;gap:24px;align-items:center;flex-wrap:wrap;">
    <div>
        @if($student->photo)
        <img src="{{ asset('storage/' . $student->photo) }}" alt="" style="width:100px;height:100px;border-radius:12px;object-fit:cover;">
        @else
        <div style="width:100px;height:100px;border-radius:12px;background:var(--gray-200);display:flex;align-items:center;justify-content:center;font-size:36px;font-weight:600;">{{ substr($student->first_name,0,1) }}</div>
        @endif
    </div>
    <div style="flex:1;">
        <h2 style="font-size:24px;margin-bottom:4px;">{{ $student->full_name }}</h2>
        <p style="color:var(--gray-500);">Class {{ $student->schoolClass?->class_name }}{{ $student->section?->section_name ? ' - ' . $student->section->section_name : '' }} | Roll No: {{ $student->roll_no ?? '-' }}</p>
        <span class="badge {{ $student->status ? 'badge-success' : 'badge-danger' }}">{{ $student->status ? 'Active' : 'Inactive' }}</span>
    </div>
    <div>
        <a href="{{ route('students.edit', $student) }}" class="btn btn-primary">✏ Edit</a>
        <a href="{{ route('students.academic', $student) }}" class="btn btn-secondary">📊 Academic</a>
        <a href="{{ route('students.fee-report', $student) }}" class="btn btn-secondary">💰 Fee Report</a>
    </div>
</div>

<div style="margin-top:20px;">
    <div style="display:flex;gap:8px;margin-bottom:16px;border-bottom:1px solid var(--gray-200);">
        <button type="button" onclick="showTab('personal')" class="tab-btn btn btn-secondary btn-sm active" data-tab="personal" style="background:var(--primary);color:white;border-color:var(--primary);">Personal Info</button>
        <button type="button" onclick="showTab('academic')" class="tab-btn btn btn-secondary btn-sm" data-tab="academic">Academic Records</button>
        <button type="button" onclick="showTab('attendance')" class="tab-btn btn btn-secondary btn-sm" data-tab="attendance">Attendance</button>
        <button type="button" onclick="showTab('fee')" class="tab-btn btn btn-secondary btn-sm" data-tab="fee">Fee Details</button>
        <button type="button" onclick="showTab('documents')" class="tab-btn btn btn-secondary btn-sm" data-tab="documents">Documents</button>
    </div>

    <div id="tab-personal" class="tab-content">
        <div class="card">
            <h3 class="card-title">Personal Information</h3>
            <div class="grid-2">
                <p><strong>DOB:</strong> {{ $student->dob?->format('d M Y') ?? '-' }}</p>
                <p><strong>Gender:</strong> {{ $student->gender }}</p>
                <p><strong>Blood Group:</strong> {{ $student->blood_group ?? '-' }}</p>
                <p><strong>Category:</strong> {{ $student->category ?? '-' }}</p>
                <p><strong>Admission Date:</strong> {{ $student->admission_date?->format('d M Y') ?? '-' }}</p>
                <p><strong>Admission No:</strong> {{ $student->admission_no }}</p>
            </div>
            <h4 style="margin-top:16px;">Parent Details</h4>
            <p><strong>Father:</strong> {{ $student->father_name ?? '-' }} | <strong>Mother:</strong> {{ $student->mother_name ?? '-' }}</p>
            <p><strong>Contact:</strong> {{ $student->parent_contact ?? '-' }} | <strong>Alt:</strong> {{ $student->alt_contact ?? '-' }}</p>
            <p><strong>Email:</strong> {{ $student->email ?? '-' }}</p>
            <h4 style="margin-top:16px;">Address</h4>
            <p><strong>Current:</strong> {{ $student->current_address ?? '-' }}</p>
            <p><strong>Permanent:</strong> {{ $student->permanent_address ?? '-' }}</p>
        </div>
    </div>

    <div id="tab-academic" class="tab-content" style="display:none;">
        <div class="card">
            <h3 class="card-title">Subject-wise Academic Records</h3>
            <table>
                <thead><tr><th>Exam</th><th>Subject</th><th>Marks</th><th>Total</th><th>Grade</th></tr></thead>
                <tbody>
                    @forelse($student->marks as $m)
                    <tr>
                        <td>{{ $m->exam?->exam_name }}</td>
                        <td>{{ $m->subject?->subject_name }}</td>
                        <td>{{ $m->marks_obtained }}</td>
                        <td>{{ $m->total_marks }}</td>
                        <td>{{ $m->grade ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5">No records.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <p style="margin-top:12px;"><a href="{{ route('students.academic', $student) }}">View Full Academic Record →</a></p>
        </div>
    </div>

    <div id="tab-attendance" class="tab-content" style="display:none;">
        <div class="card">
            @php
                $present = $student->attendance->where('status', 'Present')->count();
                $absent = $student->attendance->whereIn('status', ['Absent','Late'])->count();
                $total = $present + $absent;
                $pct = $total > 0 ? round(($present / $total) * 100, 1) : 0;
            @endphp
            <p><strong>Monthly Attendance %:</strong> {{ $pct }}% | Present: {{ $present }} | Absent: {{ $absent }}</p>
            <table>
                <thead><tr><th>Date</th><th>Status</th><th>Remarks</th></tr></thead>
                <tbody>
                    @forelse($student->attendance->sortByDesc('date')->take(10) as $a)
                    <tr><td>{{ $a->date->format('d M Y') }}</td><td>{{ $a->status }}</td><td>{{ $a->remarks ?? '-' }}</td></tr>
                    @empty
                    <tr><td colspan="3">No attendance records.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="tab-fee" class="tab-content" style="display:none;">
        <div class="card">
            @php
                $totalFee = $student->fees->sum('final_amount') ?: 0;
                $paid = $student->feePayments->sum('amount');
                $due = max(0, $totalFee - $paid);
            @endphp
            <div class="grid-4" style="margin-bottom:20px;">
                <div style="padding:16px;background:var(--gray-50);border-radius:8px;"><strong>Total Fee</strong><br>₹{{ number_format($totalFee) }}</div>
                <div style="padding:16px;background:#d1fae5;border-radius:8px;"><strong>Paid</strong><br>₹{{ number_format($paid) }}</div>
                <div style="padding:16px;background:#fef3c7;border-radius:8px;"><strong>Due</strong><br>₹{{ number_format($due) }}</div>
            </div>
            <h4>Payment History</h4>
            <table>
                <thead><tr><th>Date</th><th>Fee Type</th><th>Amount</th><th>Mode</th><th>Receipt No</th></tr></thead>
                <tbody>
                    @forelse($student->feePayments as $p)
                    <tr><td>{{ $p->payment_date->format('d M Y') }}</td><td>{{ $p->fee_type }}</td><td>₹{{ number_format($p->amount) }}</td><td>{{ $p->payment_mode }}</td><td>{{ $p->receipt_no ?? '-' }}</td></tr>
                    @empty
                    <tr><td colspan="5">No payments.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <p style="margin-top:12px;"><a href="{{ route('students.fee-report', $student) }}">View Full Fee Report →</a></p>
        </div>
    </div>

    <div id="tab-documents" class="tab-content" style="display:none;">
        <div class="card">
            <p>Documents section - upload/view documents.</p>
            @forelse($student->documents as $d)
            <p><a href="{{ asset('storage/' . $d->file_path) }}" target="_blank">{{ $d->document_name }}</a></p>
            @empty
            <p>No documents uploaded.</p>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
function showTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(el => { el.classList.remove('active'); el.style.background = ''; el.style.color = ''; el.style.borderColor = ''; });
    var btn = document.querySelector('.tab-btn[data-tab="' + tab + '"]');
    if (btn) { btn.classList.add('active'); btn.style.background = 'var(--primary)'; btn.style.color = 'white'; btn.style.borderColor = 'var(--primary)'; }
    document.getElementById('tab-' + tab).style.display = 'block';
}
</script>
@endpush
@endsection
