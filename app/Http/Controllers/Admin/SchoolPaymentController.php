<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolPaymentController extends Controller
{
    //
    public function create()
    {
        $students = User::where('role', 'student')->get();
        $fees = Fee::with('class')->get();

        return view('dashboards.admin.payment.makepayment', compact('students', 'fees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:users,id',
            'fee_id' => 'required|exists:fees,id',
            'amount_paid' => 'required|numeric|min:1',
        ]);

        $fee = Fee::findOrFail($data['fee_id']);

        $totalPaid = Payment::where('student_id', $data['student_id'])
            ->where('fee_id', $data['fee_id'])
            ->sum('amount_paid');

        $newTotal = $totalPaid + $data['amount_paid'];
        $remainingBalance = $fee->amount - $totalPaid;

        if ($newTotal > $fee->amount) {
            return back()->withErrors([
                'amount_paid' => "Payment exceeds remaining balance. Remaining balance is {$remainingBalance}.",
            ])->withInput();
        }
        Payment::create([
            'student_id' => $data['student_id'],
            'fee_id' => $data['fee_id'],
            'amount_paid' => $data['amount_paid'],
            'recorded_by' => Auth::id(),
        ]);

        return back()->with('success', 'Payment recorded successfully.');
    }

    public function allPayments()
    {
        $schools = School::all();
        $payments = Payment::with('student', 'fee.class')->get();

        return view('dashboards.admin.payment.myrecordpaymentlist', compact('payments', 'schools'));
    }

    public function completePayments()
    {
        $schools = School::all();

        // Group payments by student + fee
        $completePayments = Payment::with('student', 'fee.class', 'recorder')
            ->get()
            ->groupBy(function ($payment) {
                return $payment->student_id.'-'.$payment->fee_id;
            })
            ->map(function ($group) {
                $totalPaid = $group->sum('amount_paid');
                $feeAmount = $group->first()->fee->amount;

                if ($totalPaid >= $feeAmount) {
                    return [
                        'student' => $group->first()->student,
                        'fee' => $group->first()->fee,
                        'total_paid' => $totalPaid,
                        'fee_amount' => $feeAmount,
                        'payments' => $group,
                    ];
                }

                return null;
            })
            ->filter(); // remove nulls

        return view('dashboards.admin.payment.complete', compact('completePayments', 'schools'));
    }

    public function defaulters()
    {
        $payments = Payment::with('student', 'fee.class')->get();
        $defaulters = $payments->filter(function ($payment) {
            return $payment->amount_paid < $payment->fee->amount;
        });

        return view('dashboards.admin.payment.defaulters', compact('defaulters'));
    }

    public function isPaymentComplete() {}
}
