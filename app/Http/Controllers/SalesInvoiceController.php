<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalesInvoiceRequest;
use App\Http\Requests\UpdateSalesInvoiceRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesInvoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SalesInvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $salesInvoices = SalesInvoice::with('customer')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_no', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('sales-invoices.index', compact('salesInvoices'));
    }

    public function create(): View
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('sales-invoices.create', compact('customers', 'products'));
    }

    public function store(StoreSalesInvoiceRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $invoice = SalesInvoice::create([
                'invoice_no' => 'SAL-' . strtoupper(Str::random(8)),
                'customer_id' => $data['customer_id'],
                'invoice_date' => $data['invoice_date'],
                'due_date' => $data['due_date'],
                'subtotal' => 0,
                'discount' => 0,
                'tax_total' => 0,
                'grand_total' => 0,
                'paid_amount' => 0,
                'notes' => $data['notes'] ?? null,
            ]);

            $this->saveItems($invoice, $data['items']);
        });

        return redirect()
            ->route('sales-invoices.index')
            ->with('success', 'Sales invoice created successfully.');
    }

    public function show(SalesInvoice $salesInvoice): View
    {
        $salesInvoice->load(['customer', 'items.product', 'payments']);

        return view('sales-invoices.show', compact('salesInvoice'));
    }

    public function edit(SalesInvoice $salesInvoice): View
    {
        if ($salesInvoice->payments()->exists()) {
            return redirect()
                ->route('sales-invoices.index')
                ->with('error', 'An invoice with payments cannot be edited.');
        }

        $salesInvoice->load('items');
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('sales-invoices.edit', compact(
            'salesInvoice',
            'customers',
            'products'
        ));
    }

    public function update(
        UpdateSalesInvoiceRequest $request,
        SalesInvoice $salesInvoice
    ): RedirectResponse {
        if ($salesInvoice->payments()->exists()) {
            return redirect()
                ->route('sales-invoices.index')
                ->with('error', 'An invoice with payments cannot be edited.');
        }

        $data = $request->validated();

        DB::transaction(function () use ($data, $salesInvoice) {
            $salesInvoice->update([
                'customer_id' => $data['customer_id'],
                'invoice_date' => $data['invoice_date'],
                'due_date' => $data['due_date'],
                'notes' => $data['notes'] ?? null,
            ]);

            $salesInvoice->items()->delete();

            $this->saveItems($salesInvoice, $data['items']);
        });

        return redirect()
            ->route('sales-invoices.index')
            ->with('success', 'Sales invoice updated successfully.');
    }

    public function destroy(SalesInvoice $salesInvoice): RedirectResponse
    {
        if ($salesInvoice->paid_amount > 0 || $salesInvoice->payments()->exists()) {
            return back()->with('error', 'An invoice with payments cannot be deleted.');
        }

        DB::transaction(function () use ($salesInvoice) {
            $salesInvoice->items()->delete();
            $salesInvoice->delete();
        });

        return redirect()
            ->route('sales-invoices.index')
            ->with('success', 'Sales invoice deleted successfully.');
    }

    private function saveItems(SalesInvoice $invoice, array $items): void
    {
        $subtotal = 0;
        $discountTotal = 0;
        $taxTotal = 0;

        foreach ($items as $item) {
            $product = Product::findOrFail($item['product_id']);

            $quantity = (float) $item['quantity'];
            $unitPrice = (float) $item['unit_price'];
            $discount = (float) ($item['discount'] ?? 0);
            $taxRate = (float) $item['tax_rate'];

            $gross = $quantity * $unitPrice;

            if ($discount > $gross) {
                throw ValidationException::withMessages([
                    'items' => 'Item discount cannot exceed gross amount.',
                ]);
            }

            $taxableAmount = $gross - $discount;
            $taxAmount = round($taxableAmount * $taxRate / 100, 2);
            $lineTotal = round($taxableAmount + $taxAmount, 2);

            $invoice->items()->create([
                'product_id' => $product->id,
                'description' => $item['description'] ?? $product->name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount' => $discount,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'line_total' => $lineTotal,
            ]);

            $subtotal += $gross;
            $discountTotal += $discount;
            $taxTotal += $taxAmount;
        }

        $invoice->update([
            'subtotal' => round($subtotal, 2),
            'discount' => round($discountTotal, 2),
            'tax_total' => round($taxTotal, 2),
            'grand_total' => round($subtotal - $discountTotal + $taxTotal, 2),
        ]);
    }
}
