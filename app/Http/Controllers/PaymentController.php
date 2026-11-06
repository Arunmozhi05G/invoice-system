<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Payment;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = Payment::with('payable')
            ->when($request->search, function ($query, $search) {
                $query->where('reference', 'like', "%{$search}%")
                    ->orWhere('method', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('payments.index', compact('payments'));
    }

    public function create(Request $request): View
    {
        $salesInvoices = SalesInvoice::with('customer')
            ->orderBy('id', 'desc')
            ->get();

        $purchaseInvoices = PurchaseInvoice::with('supplier')
            ->orderBy('id', 'desc')
            ->get();

        $methods = PaymentMethod::cases();

        return view('payments.create', compact(
            'salesInvoices',
            'purchaseInvoices',
            'methods'
        ));
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $invoice = $data['invoice_type'] === 'sales'
                ? SalesInvoice::findOrFail($data['invoice_id'])
                : PurchaseInvoice::findOrFail($data['invoice_id']);

            $pendingBalance = $invoice->grand_total - $invoice->paid_amount;

            if ($data['amount'] > $pendingBalance) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment amount cannot exceed pending balance of ₹' . number_format($pendingBalance, 2),
                ]);
            }

            $invoice->payments()->create([
                'amount' => $data['amount'],
                'paid_on' => $data['paid_on'],
                'method' => $data['method'],
                'reference' => $data['reference'] ?? null,
            ]);

            $invoice->update([
                'paid_amount' => $invoice->payments()->sum('amount'),
            ]);
        });

        return redirect()
            ->route('payments.index')
            ->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment): View
    {
        $payment->load('payable');

        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment): View
    {
        $payment->load('payable');
        $methods = PaymentMethod::cases();

        return view('payments.edit', compact('payment', 'methods'));
    }

    public function update(
        UpdatePaymentRequest $request,
        Payment $payment
    ): RedirectResponse {
        $data = $request->validated();

        DB::transaction(function () use ($data, $payment) {
            $invoice = $payment->payable;

            $otherPayments = $invoice->payments()
                ->where('id', '!=', $payment->id)
                ->sum('amount');

            $maxAllowed = $invoice->grand_total - $otherPayments;

            if ($data['amount'] > $maxAllowed) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment amount cannot exceed remaining balance of ₹' . number_format($maxAllowed, 2),
                ]);
            }

            $payment->update([
                'amount' => $data['amount'],
                'paid_on' => $data['paid_on'],
                'method' => $data['method'],
                'reference' => $data['reference'] ?? null,
            ]);

            $invoice->update([
                'paid_amount' => $invoice->payments()->sum('amount'),
            ]);
        });

        return redirect()
            ->route('payments.index')
            ->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        DB::transaction(function () use ($payment) {
            $invoice = $payment->payable;

            $payment->delete();

            $invoice->update([
                'paid_amount' => $invoice->payments()->sum('amount'),
            ]);
        });

        return redirect()
            ->route('payments.index')
            ->with('success', 'Payment deleted successfully.');
    }
}
