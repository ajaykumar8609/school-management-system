@extends('layouts.app')

@section('title', 'Fee Report - ' . $student->full_name)
@section('header-title', 'Fee Report')

@section('content')
<div class="card" style="margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
    <p style="margin:0;"><strong>{{ $student->full_name }}</strong> | Class {{ $student->schoolClass?->class_name }}{{ $student->section?->section_name ?? '' }} | Roll: {{ $student->roll_no ?? '-' }}</p>
    <a href="{{ route('fee.index') }}?search={{ urlencode($student->first_name . ' ' . $student->last_name) }}" class="btn btn-primary">Set Fee / Add Payment</a>
</div>

<div class="grid-4" style="margin-bottom:24px;">
    <div class="card" style="background:#dbeafe;">
        <strong>Total Fee</strong><br><span style="font-size:20px;">₹{{ number_format($totalFee) }}</span>
    </div>
    <div class="card" style="background:#d1fae5;">
        <strong>Paid Amount</strong><br><span style="font-size:20px;">₹{{ number_format($paid) }}</span>
    </div>
    <div class="card" style="background:#fef3c7;">
        <strong>Remaining</strong><br><span style="font-size:20px;">₹{{ number_format(max(0, $totalFee - $paid)) }}</span>
    </div>
</div>

@if($student->fees->isNotEmpty())
<div class="card" style="margin-bottom:20px;">
    <h3 class="card-title">Fee Structure (by Academic Year)</h3>
    <table>
        <thead><tr><th>Academic Year</th><th>Total Fee</th><th>Discount</th><th>Final Amount</th></tr></thead>
        <tbody>
            @foreach($student->fees as $f)
            <tr>
                <td>{{ $f->academic_year }}</td>
                <td>₹{{ number_format($f->total_fee) }}</td>
                <td>₹{{ number_format($f->discount) }}</td>
                <td>₹{{ number_format($f->final_amount) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<div class="card">
    <h3 class="card-title">Payment History</h3>
    <table>
        <thead>
            <tr><th>Date</th><th>Fee Type</th><th>Amount</th><th>Payment Mode</th><th>Transaction ID</th><th>Receipt No</th><th>Status</th></tr>
        </thead>
        <tbody>
            @forelse($student->feePayments as $p)
            <tr>
                <td>{{ $p->payment_date->format('d M Y') }}</td>
                <td>{{ $p->fee_type }}</td>
                <td>₹{{ number_format($p->amount) }}</td>
                <td>{{ $p->payment_mode }}</td>
                <td>{{ $p->transaction_id ?? '-' }}</td>
                <td>{{ $p->receipt_no ?? '-' }}</td>
                <td><span class="badge badge-success">{{ $p->status }}</span></td>
            </tr>
            @empty
            <tr><td colspan="7">No payments yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<a href="{{ route('students.show', $student) }}" class="btn btn-secondary">← Back to Profile</a>
@endsection
