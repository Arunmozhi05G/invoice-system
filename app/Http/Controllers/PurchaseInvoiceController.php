<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests\StorePurchaseInvoiceRequest;
use App\Http\Requests\UpdatePurchaseInvoiceRequest;

class PurchaseInvoiceController extends Controller
{
    public function index()
    {
        $invoices = PurchaseInvoice::with('supplier')
            ->when(request('search'), function ($query, $search) {
                $query->where('invoice_no', 'like', "%{$search}%")
                    ->orWhere('bill_no', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('purchase-invoices.index', compact('invoices'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('purchase-invoices.create', compact(
            'suppliers',
            'products'
        ));
    }

    public function store(StorePurchaseInvoiceRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {

            $invoice = PurchaseInvoice::create([
                'invoice_no' => 'PUR-' . strtoupper(Str::random(8)),
                'supplier_id' => $data['supplier_id'],
                'bill_no' => $data['bill_no'] ?? null,
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
            ->route('purchase-invoices.index')
            ->with('success', 'Purchase invoice created successfully.');
    }

    public function show(PurchaseInvoice $purchaseInvoice)
    {
        $purchaseInvoice->load(['supplier', 'items.product', 'payments']);

        return view('purchase-invoices.show', [
            'invoice' => $purchaseInvoice
        ]);
    }

    public function edit(PurchaseInvoice $purchaseInvoice)
    {
        $purchaseInvoice->load('items');

        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('purchase-invoices.edit', compact(
            'purchaseInvoice',
            'suppliers',
            'products'
        ));
    }

    public function update(
        UpdatePurchaseInvoiceRequest $request,
        PurchaseInvoice $purchaseInvoice
    ) {
        $data = $request->validated();

        DB::transaction(function () use ($data, $purchaseInvoice) {

            $purchaseInvoice->update([
                'supplier_id' => $data['supplier_id'],
                'bill_no' => $data['bill_no'] ?? null,
                'invoice_date' => $data['invoice_date'],
                'due_date' => $data['due_date'],
                'notes' => $data['notes'] ?? null,
            ]);

            // Replace old invoice items.
            $purchaseInvoice->items()->delete();

            $this->saveItems($purchaseInvoice, $data['items']);
        });

        return redirect()
            ->route('purchase-invoices.index')
            ->with('success', 'Purchase invoice updated successfully.');
    }

    public function destroy(PurchaseInvoice $purchaseInvoice)
    {
        if ($purchaseInvoice->paid_amount > 0) {
            return back()->with(
                'error',
                'Cannot delete an invoice that has payments.'
            );
        }

        DB::transaction(function () use ($purchaseInvoice) {
            $purchaseInvoice->items()->delete();
            $purchaseInvoice->delete();
        });

        return redirect()
            ->route('purchase-invoices.index')
            ->with('success', 'Purchase invoice deleted successfully.');
    }

    private function saveItems($invoice, array $items): void
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
                throw new \InvalidArgumentException(
                    'Item discount cannot exceed item amount.'
                );
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
            'grand_total' => round(
                $subtotal - $discountTotal + $taxTotal,
                2
            ),
        ]);
    }
}