<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Fee;
use App\Models\FeePayment;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['schoolClass', 'section', 'fees', 'feePayments']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('first_name', 'like', "%{$s}%")
                ->orWhere('last_name', 'like', "%{$s}%")
                ->orWhere('roll_no', 'like', "%{$s}%"));
        }

        $students = $query->orderBy('first_name')->paginate($request->get('per_page', 15));

        $students->getCollection()->transform(function ($s) {
            $s->total_fee = $s->fees->sum('final_amount') ?: 0;
            $s->paid_amount = $s->feePayments->sum('amount');
            $s->due_amount = max(0, $s->total_fee - $s->paid_amount);
            $s->last_payment = $s->feePayments->sortByDesc('payment_date')->first();
            $s->fee_status = $s->due_amount <= 0 ? 'Paid' : ($s->paid_amount > 0 ? 'Partial' : 'Due');
            return $s;
        });

        return view('fee.index', compact('students'));
    }

    public function addPayment(Request $request)
    {
        $valid = $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_type' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'payment_mode' => 'required|in:Cash,Online,Card,UPI,Cheque',
            'transaction_id' => 'nullable|string|max:100',
            'payment_date' => 'required|date',
            'remarks' => 'nullable|string|max:255',
        ]);

        $valid['receipt_no'] = 'RCP' . str_pad(FeePayment::max('id') + 1, 6, '0', STR_PAD_LEFT);
        $valid['status'] = 'Paid';

        FeePayment::create($valid);

        return redirect()->back()->with('success', 'Payment added successfully.');
    }

    public function deletePayment(FeePayment $payment)
    {
        $payment->delete();
        return redirect()->back()->with('success', 'Payment deleted.');
    }
}
