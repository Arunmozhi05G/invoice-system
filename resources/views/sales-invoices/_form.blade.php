@php
    $invoice = $invoice ?? null;
    $existingItems = old('items');
    if ($existingItems === null) {
        $existingItems = $invoice
            ? $invoice->items->map(fn($item) => [
                'product_id' => $item->product_id,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount' => $item->discount,
                'tax_rate' => $item->tax_rate,
            ])->toArray()
            : [[
                'product_id' => '',
                'description' => '',
                'quantity' => 1,
                'unit_price' => 0,
                'discount' => 0,
                'tax_rate' => 18,
            ]];
    }
@endphp

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label">Customer *</label>
        <select name="customer_id" class="form-select" required>
            <option value="">Select Customer</option>
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}"
                    @selected(old('customer_id', $invoice->customer_id ?? '') == $customer->id)>
                    {{ $customer->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Invoice Date *</label>
        <input type="date" name="invoice_date" class="form-control"
            value="{{ old('invoice_date', $invoice ? $invoice->invoice_date->format('Y-m-d') : date('Y-m-d')) }}"
            required>
    </div>

    <div class="col-md-4">
        <label class="form-label">Due Date *</label>
        <input type="date" name="due_date" class="form-control"
            value="{{ old('due_date', $invoice ? $invoice->due_date->format('Y-m-d') : date('Y-m-d', strtotime('+30 days'))) }}"
            required>
    </div>
</div>

<h5 class="mb-3">Invoice Items</h5>

<div class="table-responsive mb-3">
    <table class="table table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th style="min-width: 180px;">Product</th>
                <th style="min-width: 150px;">Description</th>
                <th style="min-width: 90px;">Qty</th>
                <th style="min-width: 110px;">Price</th>
                <th style="min-width: 100px;">Discount</th>
                <th style="min-width: 90px;">Tax %</th>
                <th style="min-width: 110px;">Total</th>
                <th style="width: 50px;">Action</th>
            </tr>
        </thead>
        <tbody id="itemsBody">
            @foreach($existingItems as $index => $item)
                <tr>
                    <td>
                        <select name="items[{{ $index }}][product_id]" class="form-select product-select" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}"
                                    data-price="{{ $product->price }}"
                                    data-tax="{{ $product->tax_rate }}"
                                    @selected(($item['product_id'] ?? '') == $product->id)>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="text" name="items[{{ $index }}][description]" class="form-control"
                            value="{{ $item['description'] ?? '' }}" placeholder="Description">
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0.01" name="items[{{ $index }}][quantity]" class="form-control qty"
                            value="{{ $item['quantity'] ?? 1 }}" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0" name="items[{{ $index }}][unit_price]" class="form-control price"
                            value="{{ $item['unit_price'] ?? 0 }}" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0" name="items[{{ $index }}][discount]" class="form-control discount"
                            value="{{ $item['discount'] ?? 0 }}">
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0" max="100" name="items[{{ $index }}][tax_rate]" class="form-control tax"
                            value="{{ $item['tax_rate'] ?? 18 }}" required>
                    </td>
                    <td class="line-total fw-semibold">₹0.00</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-item">Remove</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<button type="button" id="addItem" class="btn btn-sm btn-dark mb-4">+ Add Item</button>

<div class="row justify-content-end mb-4">
    <div class="col-md-4">
        <table class="table table-bordered">
            <tr>
                <th>Subtotal</th>
                <td class="text-end" id="subtotal">₹0.00</td>
            </tr>
            <tr>
                <th>Discount</th>
                <td class="text-end" id="discountTotal">₹0.00</td>
            </tr>
            <tr>
                <th>Tax</th>
                <td class="text-end" id="taxTotal">₹0.00</td>
            </tr>
            <tr class="table-light">
                <th>Grand Total</th>
                <th class="text-end" id="grandTotal">₹0.00</th>
            </tr>
        </table>
    </div>
</div>

<div class="mb-4">
    <label class="form-label">Notes</label>
    <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes...">{{ old('notes', $invoice->notes ?? '') }}</textarea>
</div>

<button type="submit" class="btn btn-primary">Save Invoice</button>
<a href="{{ route('sales-invoices.index') }}" class="btn btn-secondary">Cancel</a>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const products = @json($products->map(fn($p) => [
        'id' => $p->id,
        'name' => $p->name,
        'price' => $p->price,
        'tax_rate' => $p->tax_rate
    ]));

    let itemIndex = {{ count($existingItems) }};
    const itemsBody = document.getElementById('itemsBody');

    function calculateTotals() {
        let subtotal = 0;
        let discountTotal = 0;
        let taxTotal = 0;

        itemsBody.querySelectorAll('tr').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty').value) || 0;
            const price = parseFloat(row.querySelector('.price').value) || 0;
            const discount = parseFloat(row.querySelector('.discount').value) || 0;
            const taxRate = parseFloat(row.querySelector('.tax').value) || 0;

            const gross = qty * price;
            const validDiscount = Math.min(discount, gross);
            const taxable = gross - validDiscount;
            const tax = taxable * taxRate / 100;
            const total = taxable + tax;

            subtotal += gross;
            discountTotal += validDiscount;
            taxTotal += tax;

            row.querySelector('.line-total').textContent = '₹' + total.toFixed(2);
        });

        document.getElementById('subtotal').textContent = '₹' + subtotal.toFixed(2);
        document.getElementById('discountTotal').textContent = '₹' + discountTotal.toFixed(2);
        document.getElementById('taxTotal').textContent = '₹' + taxTotal.toFixed(2);
        document.getElementById('grandTotal').textContent = '₹' + (subtotal - discountTotal + taxTotal).toFixed(2);
    }

    document.getElementById('addItem').addEventListener('click', function () {
        let options = '<option value="">Select Product</option>';
        products.forEach(p => {
            options += `<option value="${p.id}" data-price="${p.price}" data-tax="${p.tax_rate}">${p.name}</option>`;
        });

        const row = `
            <tr>
                <td>
                    <select name="items[${itemIndex}][product_id]" class="form-select product-select" required>
                        ${options}
                    </select>
                </td>
                <td>
                    <input type="text" name="items[${itemIndex}][description]" class="form-control" placeholder="Description">
                </td>
                <td>
                    <input type="number" step="0.01" min="0.01" name="items[${itemIndex}][quantity]" class="form-control qty" value="1" required>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="items[${itemIndex}][unit_price]" class="form-control price" value="0" required>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="items[${itemIndex}][discount]" class="form-control discount" value="0">
                </td>
                <td>
                    <input type="number" step="0.01" min="0" max="100" name="items[${itemIndex}][tax_rate]" class="form-control tax" value="18" required>
                </td>
                <td class="line-total fw-semibold">₹0.00</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-item">Remove</button>
                </td>
            </tr>
        `;

        itemsBody.insertAdjacentHTML('beforeend', row);
        itemIndex++;
        calculateTotals();
    });

    itemsBody.addEventListener('change', function (e) {
        if (e.target.classList.contains('product-select')) {
            const selected = e.target.selectedOptions[0];
            const row = e.target.closest('tr');
            if (selected.value) {
                row.querySelector('.price').value = selected.dataset.price || 0;
                row.querySelector('.tax').value = selected.dataset.tax || 0;
                const descInput = row.querySelector('input[name*="[description]"]');
                if (!descInput.value) {
                    descInput.value = selected.text.trim();
                }
            }
            calculateTotals();
        }
    });

    itemsBody.addEventListener('input', calculateTotals);

    itemsBody.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-item')) {
            if (itemsBody.querySelectorAll('tr').length > 1) {
                e.target.closest('tr').remove();
                calculateTotals();
            } else {
                alert('At least one item is required.');
            }
        }
    });

    calculateTotals();
});
</script>
