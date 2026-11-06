
@php
    $isEdit = isset($invoice);
    $existingItems = old('items', $isEdit
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
        ]]);
@endphp

<form action="{{ $isEdit
    ? route('purchase-invoices.update', $invoice)
    : route('purchase-invoices.store') }}"
    method="POST">

    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="card mb-4">
        <div class="card-header fw-semibold">Invoice Information</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Supplier *</label>
                    <select name="supplier_id" class="form-select" required>
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}"
                                @selected(old('supplier_id', $isEdit ? $invoice->supplier_id : '') == $supplier->id)>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Supplier Bill Number</label>
                    <input type="text" name="bill_no" class="form-control"
                        value="{{ old('bill_no', $isEdit ? $invoice->bill_no : '') }}">
                    @error('bill_no')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Invoice Date *</label>
                    <input type="date" name="invoice_date" class="form-control"
                        value="{{ old('invoice_date', $isEdit ? $invoice->invoice_date->format('Y-m-d') : date('Y-m-d')) }}"
                        required>
                    @error('invoice_date')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Due Date *</label>
                    <input type="date" name="due_date" class="form-control"
                        value="{{ old('due_date', $isEdit ? $invoice->due_date->format('Y-m-d') : '') }}"
                        required>
                    @error('due_date')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Invoice Items</strong>
            <button type="button" id="addItem" class="btn btn-primary btn-sm">
                + Add Item
            </button>
        </div>

        <div class="card-body">
            @if($products->isEmpty())
                <div class="alert alert-warning">
                    No products available. Please add products first.
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Discount</th>
                            <th>Tax %</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        @foreach($existingItems as $index => $item)
                            <tr>
                                <td>
                                    <select name="items[{{ $index }}][product_id]"
                                        class="form-select product-select" required>
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
                                    <input type="hidden"
                                        name="items[{{ $index }}][description]"
                                        value="{{ $item['description'] ?? '' }}">
                                </td>
                                <td>
                                    <input type="number"
                                        name="items[{{ $index }}][quantity]"
                                        class="form-control qty"
                                        value="{{ $item['quantity'] ?? 1 }}"
                                        min="0.01" step="0.01" required>
                                </td>
                                <td>
                                    <input type="number"
                                        name="items[{{ $index }}][unit_price]"
                                        class="form-control price"
                                        value="{{ $item['unit_price'] ?? 0 }}"
                                        min="0" step="0.01" required>
                                </td>
                                <td>
                                    <input type="number"
                                        name="items[{{ $index }}][discount]"
                                        class="form-control discount"
                                        value="{{ $item['discount'] ?? 0 }}"
                                        min="0" step="0.01">
                                </td>
                                <td>
                                    <input type="number"
                                        name="items[{{ $index }}][tax_rate]"
                                        class="form-control tax"
                                        value="{{ $item['tax_rate'] ?? 18 }}"
                                        min="0" max="100" step="0.01" required>
                                </td>
                                <td class="line-total">0.00</td>
                                <td>
                                    <button type="button"
                                        class="btn btn-danger btn-sm remove-item">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @error('items')
                <div class="text-danger small">{{ $message }}</div>
            @enderror

            <div class="row justify-content-end">
                <div class="col-md-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <strong id="subtotal">0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Discount</span>
                        <strong id="discountTotal">0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax</span>
                        <strong id="taxTotal">0.00</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Grand Total</strong>
                        <strong id="grandTotal">0.00</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $isEdit ? $invoice->notes : '') }}</textarea>
        </div>
    </div>

    <button type="submit" class="btn btn-success">
        {{ $isEdit ? 'Update Invoice' : 'Save Invoice' }}
    </button>
    <a href="{{ route('purchase-invoices.index') }}"
        class="btn btn-secondary">Cancel</a>
</form>

<script>
const products = {{ Illuminate\Support\Js::from(
    $products->map(fn($product) => [
        'id' => $product->id,
        'name' => $product->name,
        'price' => $product->price,
        'tax_rate' => $product->tax_rate,
    ])->values()
) }};

let itemIndex = {{ count($existingItems) }};

function addItem() {
    const options = products.map(product =>
        `<option value="${product.id}"
            data-price="${product.price}"
            data-tax="${product.tax_rate}">
            ${product.name}
        </option>`
    ).join('');

    const row = `
        <tr>
            <td>
                <select name="items[${itemIndex}][product_id]"
                    class="form-select product-select" required>
                    <option value="">Select Product</option>
                    ${options}
                </select>
                <input type="hidden"
                    name="items[${itemIndex}][description]">
            </td>
            <td><input type="number"
                name="items[${itemIndex}][quantity]"
                class="form-control qty" value="1"
                min="0.01" step="0.01" required></td>
            <td><input type="number"
                name="items[${itemIndex}][unit_price]"
                class="form-control price" value="0"
                min="0" step="0.01" required></td>
            <td><input type="number"
                name="items[${itemIndex}][discount]"
                class="form-control discount" value="0"
                min="0" step="0.01"></td>
            <td><input type="number"
                name="items[${itemIndex}][tax_rate]"
                class="form-control tax" value="18"
                min="0" max="100" step="0.01" required></td>
            <td class="line-total">0.00</td>
            <td><button type="button"
                class="btn btn-danger btn-sm remove-item">Remove</button></td>
        </tr>`;

    document.getElementById('itemsBody')
        .insertAdjacentHTML('beforeend', row);

    itemIndex++;
}

function calculateTotals() {
    let subtotal = 0;
    let discountTotal = 0;
    let taxTotal = 0;

    document.querySelectorAll('#itemsBody tr').forEach(row => {
        const qty = Number(row.querySelector('.qty').value) || 0;
        const price = Number(row.querySelector('.price').value) || 0;
        const discount = Number(row.querySelector('.discount').value) || 0;
        const taxRate = Number(row.querySelector('.tax').value) || 0;

        const gross = qty * price;
        const validDiscount = Math.min(discount, gross);
        const taxable = gross - validDiscount;
        const tax = taxable * taxRate / 100;
        const total = taxable + tax;

        subtotal += gross;
        discountTotal += validDiscount;
        taxTotal += tax;

        row.querySelector('.line-total').textContent = total.toFixed(2);
    });

    document.getElementById('subtotal').textContent = subtotal.toFixed(2);
    document.getElementById('discountTotal').textContent = discountTotal.toFixed(2);
    document.getElementById('taxTotal').textContent = taxTotal.toFixed(2);
    document.getElementById('grandTotal').textContent =
        (subtotal - discountTotal + taxTotal).toFixed(2);
}

document.getElementById('addItem').addEventListener('click', addItem);

document.getElementById('itemsBody').addEventListener('change', function(e) {
    if (e.target.classList.contains('product-select')) {
        const option = e.target.selectedOptions[0];
        const row = e.target.closest('tr');

        row.querySelector('.price').value = option.dataset.price || 0;
        row.querySelector('.tax').value = option.dataset.tax || 0;
        row.querySelector('input[type="hidden"]').value = option.text.trim();

        calculateTotals();
    }
});

document.getElementById('itemsBody').addEventListener('input', calculateTotals);

document.getElementById('itemsBody').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-item')) {
        if (document.querySelectorAll('#itemsBody tr').length > 1) {
            e.target.closest('tr').remove();
            calculateTotals();
        }
    }
});

calculateTotals();
</script>
