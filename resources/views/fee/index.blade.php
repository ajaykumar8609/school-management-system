@extends('layouts.app')

@section('title', 'Fee Management')
@section('header-title', 'Fee Management')

@section('content')
<div class="card">
    <form method="get" style="margin-bottom:20px;display:flex;gap:12px;">
        <input type="text" name="search" class="form-control" placeholder="Search student..." value="{{ request('search') }}" style="max-width:300px;">
        <button type="submit" class="btn btn-secondary">Search</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Class</th>
                <th>Total Fee</th>
                <th>Paid</th>
                <th>Due</th>
                <th>Last Payment</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $s)
            <tr>
                <td>{{ $s->full_name }}</td>
                <td>{{ $s->schoolClass?->class_name }}{{ $s->section?->section_name ? '-' . $s->section->section_name : '' }}</td>
                <td>₹{{ number_format($s->total_fee) }}</td>
                <td>₹{{ number_format($s->paid_amount) }}</td>
                <td>₹{{ number_format($s->due_amount) }}</td>
                <td>{{ $s->last_payment?->payment_date->format('d M Y') ?? '-' }}</td>
                <td><span class="badge {{ $s->fee_status === 'Paid' ? 'badge-success' : ($s->fee_status === 'Partial' ? 'badge-warning' : 'badge-danger') }}">{{ $s->fee_status }}</span></td>
                <td>
                    <a href="{{ route('students.fee-report', $s) }}" class="btn btn-sm btn-primary">View</a>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="openPaymentModal({{ $s->id }}, '{{ addslashes($s->full_name) }}')">Add Payment</button>
                </td>
            </tr>
            @empty
            <tr><td colspan="8">No students.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $students->withQueryString()->links() }}
</div>

<div id="paymentModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;">
    <div class="card" style="max-width:500px;width:90%;">
        <h3 class="card-title">Add Payment</h3>
        <p id="modalStudentName"></p>
        <form action="{{ route('fee.add-payment') }}" method="post">
            @csrf
            <input type="hidden" name="student_id" id="modalStudentId">
            <div class="form-group">
                <label class="form-label">Fee Type</label>
                <input type="text" name="fee_type" class="form-control" value="Tuition" required>
            </div>
            <div class="form-group">
                <label class="form-label">Amount</label>
                <input type="number" name="amount" class="form-control" step="0.01" required>
            </div>
            <div class="form-group">
                <label class="form-label">Payment Mode</label>
                <select name="payment_mode" class="form-control">
                    <option value="Cash">Cash</option>
                    <option value="Online">Online</option>
                    <option value="Card">Card</option>
                    <option value="UPI">UPI</option>
                    <option value="Cheque">Cheque</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Transaction ID</label>
                <input type="text" name="transaction_id" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Payment Date</label>
                <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Remarks</label>
                <input type="text" name="remarks" class="form-control">
            </div>
            <div style="display:flex;gap:12px;">
                <button type="submit" class="btn btn-primary">Save Payment</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openPaymentModal(id, name) {
    document.getElementById('modalStudentId').value = id;
    document.getElementById('modalStudentName').textContent = 'Student: ' + name;
    document.getElementById('paymentModal').style.display = 'flex';
}
function closeModal() {
    document.getElementById('paymentModal').style.display = 'none';
}
document.getElementById('paymentModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endpush
@endsection
