@php
    $isEdit = isset($payment);
@endphp

@if(!$isEdit)
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label class="form-label">Invoice Type *</label>
            <select name="invoice_type" id="invoiceTypeSelect" class="form-select" required>
                <option value="">Select Invoice Type</option>
                <option value="sales" @selected(old('invoice_type', request('invoice_type')) === 'sales')>Sales Invoice</option>
                <option value="purchase" @selected(old('invoice_type', request('invoice_type')) === 'purchase')>Purchase Invoice</option>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Invoice *</label>
            <select name="invoice_id" id="invoiceSelect" class="form-select" required>
                <option value="">Select Invoice</option>
                <optgroup id="salesGroup" label="Sales Invoices">
                    @foreach($salesInvoices as $inv)
                        <option value="{{ $inv->id }}" data-balance="{{ max(0, $inv->grand_total - $inv->paid_amount) }}"
                            @selected(old('invoice_type', request('invoice_type')) === 'sales' && old('invoice_id', request('invoice_id')) == $inv->id)>
                            {{ $inv->invoice_no }} - {{ $inv->customer->name ?? '-' }} (Balance: ₹{{ number_format(max(0, $inv->grand_total - $inv->paid_amount), 2) }})
                        </option>
                    @endforeach
                </optgroup>
                <optgroup id="purchaseGroup" label="Purchase Invoices">
                    @foreach($purchaseInvoices as $inv)
                        <option value="{{ $inv->id }}" data-balance="{{ max(0, $inv->grand_total - $inv->paid_amount) }}"
                            @selected(old('invoice_type', request('invoice_type')) === 'purchase' && old('invoice_id', request('invoice_id')) == $inv->id)>
                            {{ $inv->invoice_no }} - {{ $inv->supplier->name ?? '-' }} (Balance: ₹{{ number_format(max(0, $inv->grand_total - $inv->paid_amount), 2) }})
                        </option>
                    @endforeach
                </optgroup>
            </select>
        </div>
    </div>

    <div id="balanceAlert" class="alert alert-info d-none mb-3">
        <strong>Pending Balance:</strong> <span id="pendingBalanceText">₹0.00</span>
    </div>
@else
    <div class="alert alert-light border mb-3">
        @php
            $isSales = $payment->payable_type === 'App\Models\SalesInvoice';
            $invoice = $payment->payable;
            $partyLabel = $isSales ? 'Customer' : 'Supplier';
            $party = $isSales ? ($invoice->customer->name ?? '-') : ($invoice->supplier->name ?? '-');
        @endphp
        @if($invoice)
            <strong>Invoice:</strong> {{ $invoice->invoice_no }} ({{ $isSales ? 'Sales' : 'Purchase' }}) | 
            <strong>{{ $partyLabel }}:</strong> {{ $party }} | 
            <strong>Grand Total:</strong> ₹{{ number_format($invoice->grand_total, 2) }}
        @endif
    </div>
@endif

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label class="form-label">Payment Amount *</label>
        <input type="number" step="0.01" min="0.01" name="amount" id="amountInput" class="form-control"
            value="{{ old('amount', $payment->amount ?? '') }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Payment Date *</label>
        <input type="date" name="paid_on" class="form-control"
            value="{{ old('paid_on', isset($payment) && $payment->paid_on ? $payment->paid_on->format('Y-m-d') : date('Y-m-d')) }}"
            required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Payment Method *</label>
        <select name="method" class="form-select" required>
            <option value="">Select Method</option>
            @foreach($methods as $method)
                @php
                    $val = is_object($method) ? $method->value : $method;
                @endphp
                <option value="{{ $val }}"
                    @selected(old('method', isset($payment) ? ($payment->method->value ?? $payment->method) : '') === $val)>
                    {{ ucfirst($val) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Reference / Note</label>
        <input type="text" name="reference" class="form-control" placeholder="e.g. UPI Ref, Cheque No, Bank Txn"
            value="{{ old('reference', $payment->reference ?? '') }}">
    </div>
</div>

<button type="submit" class="btn btn-primary">Save Payment</button>
<a href="{{ route('payments.index') }}" class="btn btn-secondary">Cancel</a>

@if(!$isEdit)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('invoiceTypeSelect');
    const invoiceSelect = document.getElementById('invoiceSelect');
    const salesGroup = document.getElementById('salesGroup');
    const purchaseGroup = document.getElementById('purchaseGroup');
    const balanceAlert = document.getElementById('balanceAlert');
    const balanceText = document.getElementById('pendingBalanceText');
    const amountInput = document.getElementById('amountInput');

    function filterInvoices() {
        const type = typeSelect.value;
        if (type === 'sales') {
            salesGroup.style.display = '';
            purchaseGroup.style.display = 'none';
        } else if (type === 'purchase') {
            salesGroup.style.display = 'none';
            purchaseGroup.style.display = '';
        } else {
            salesGroup.style.display = 'none';
            purchaseGroup.style.display = 'none';
        }
        updateBalance();
    }

    function updateBalance() {
        const selected = invoiceSelect.selectedOptions[0];
        if (selected && selected.value && selected.dataset.balance !== undefined) {
            const balance = parseFloat(selected.dataset.balance) || 0;
            balanceText.textContent = '₹' + balance.toFixed(2);
            balanceAlert.classList.remove('d-none');
            amountInput.max = balance;
            if (!amountInput.value || parseFloat(amountInput.value) === 0) {
                amountInput.value = balance;
            }
        } else {
            balanceAlert.classList.add('d-none');
        }
    }

    typeSelect.addEventListener('change', function () {
        invoiceSelect.value = '';
        filterInvoices();
    });

    invoiceSelect.addEventListener('change', updateBalance);

    filterInvoices();
});
</script>
@endif
