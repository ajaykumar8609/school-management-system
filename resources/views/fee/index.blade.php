@extends('layouts.app')

@section('title', 'Fee Management')
@section('header-title', 'Fee Management')

@section('content')
<div class="card" style="margin-bottom:20px;background:#eff6ff;">
    <h3 class="card-title" style="margin-bottom:12px;">How Fee Works</h3>
    <p style="color:var(--gray-700);font-size:14px;line-height:1.5;">
        <strong>Total Fee</strong> = Fee assigned to student (Set Fee) • <strong>Paid</strong> = Sum of all payments • <strong>Due</strong> = Total − Paid
    </p>
</div>

<div class="card">
    <form method="get" style="margin-bottom:20px;display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
        <div class="form-group" style="margin:0;">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Name / Roll No" value="{{ request('search') }}" style="min-width:180px;">
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label">Class</label>
            <select name="class_id" class="form-control" style="min-width:120px;">
                <option value="">All</option>
                @foreach($classes ?? [] as $c)
                <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->class_name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-secondary">Filter</button>
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
                <td style="white-space:nowrap;">
                    @php $ay = $s->fees->first()?->academic_year ?? (date('n') >= 4 ? date('Y').'-'.substr((int)date('Y')+1, 2) : ((int)date('Y')-1).'-'.substr(date('Y'), 2)); @endphp
                    <button type="button" class="btn btn-sm btn-primary" onclick="openSetFeeModal({{ $s->id }}, '{{ addslashes($s->full_name) }}', {{ $s->fees->sum('total_fee') ?: 0 }}, {{ $s->fees->sum('discount') ?: 0 }}, '{{ $ay }}')">Set Fee</button>
                    <a href="{{ route('students.fee-report', $s) }}" class="btn btn-sm btn-secondary">View</a>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="openPaymentModal({{ $s->id }}, '{{ addslashes($s->full_name) }}', {{ $s->due_amount }})">Add Payment</button>
                </td>
            </tr>
            @empty
            <tr><td colspan="8">No students.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $students->withQueryString()->links() }}
</div>

<div id="setFeeModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;">
    <div class="card" style="max-width:450px;width:90%;">
        <h3 class="card-title">Set Fee</h3>
        <p id="setFeeStudentName" style="margin-bottom:16px;color:var(--gray-600);"></p>
        <form action="{{ route('fee.set-fee') }}" method="post">
            @csrf
            <input type="hidden" name="student_id" id="setFeeStudentId">
            <div class="form-group">
                <label class="form-label">Academic Year</label>
                <input type="text" name="academic_year" id="setFeeYear" class="form-control" placeholder="e.g. 2024-25" required>
            </div>
            <div class="form-group">
                <label class="form-label">Total Fee (₹)</label>
                <input type="number" name="total_fee" id="setFeeTotal" class="form-control" min="0" step="0.01" required>
            </div>
            <div class="form-group">
                <label class="form-label">Discount (₹)</label>
                <input type="number" name="discount" id="setFeeDiscount" class="form-control" min="0" step="0.01" value="0">
            </div>
            <p id="setFeeFinal" style="font-weight:600;color:var(--primary);margin-bottom:16px;"></p>
            <div style="display:flex;gap:12px;">
                <button type="submit" class="btn btn-primary">Save Fee</button>
                <button type="button" class="btn btn-secondary" onclick="closeSetFeeModal()">Cancel</button>
            </div>
        </form>
    </div>
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
function openSetFeeModal(id, name, totalFee, discount, academicYear) {
    document.getElementById('setFeeStudentId').value = id;
    document.getElementById('setFeeStudentName').textContent = 'Student: ' + name;
    document.getElementById('setFeeYear').value = academicYear || '';
    document.getElementById('setFeeTotal').value = totalFee || '';
    document.getElementById('setFeeDiscount').value = discount || 0;
    updateSetFeeFinal();
    document.getElementById('setFeeModal').style.display = 'flex';
}
function closeSetFeeModal() {
    document.getElementById('setFeeModal').style.display = 'none';
}
function updateSetFeeFinal() {
    var total = parseFloat(document.getElementById('setFeeTotal').value) || 0;
    var disc = parseFloat(document.getElementById('setFeeDiscount').value) || 0;
    document.getElementById('setFeeFinal').textContent = 'Final Amount: ₹' + (total - disc).toLocaleString('en-IN');
}
document.getElementById('setFeeTotal').addEventListener('input', updateSetFeeFinal);
document.getElementById('setFeeDiscount').addEventListener('input', updateSetFeeFinal);
document.getElementById('setFeeModal').addEventListener('click', function(e) {
    if (e.target === this) closeSetFeeModal();
});

function openPaymentModal(id, name, dueAmount) {
    document.getElementById('modalStudentId').value = id;
    document.getElementById('modalStudentName').innerHTML = 'Student: ' + name + (dueAmount > 0 ? ' <span style="color:var(--red);">(Due: ₹' + dueAmount.toLocaleString('en-IN') + ')</span>' : '');
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
