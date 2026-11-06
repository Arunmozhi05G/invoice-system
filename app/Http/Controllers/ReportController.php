<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Expense;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        // Sales data
        $salesQuery = SalesInvoice::query()
            ->when($fromDate, fn($q) => $q->whereDate('invoice_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('invoice_date', '<=', $toDate));

        $totalSales = (float) $salesQuery->sum('grand_total');
        $salesCollected = (float) $salesQuery->sum('paid_amount');
        $salesPending = max(0, $totalSales - $salesCollected);

        // Purchase data
        $purchaseQuery = PurchaseInvoice::query()
            ->when($fromDate, fn($q) => $q->whereDate('invoice_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('invoice_date', '<=', $toDate));

        $totalPurchases = (float) $purchaseQuery->sum('grand_total');
        $purchasesPaid = (float) $purchaseQuery->sum('paid_amount');
        $purchasesPending = max(0, $totalPurchases - $purchasesPaid);

        // Operating Expenses data
        $expenseQuery = Expense::query()
            ->when($fromDate, fn($q) => $q->whereDate('expense_date', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('expense_date', '<=', $toDate));

        $totalExpenses = (float) $expenseQuery->sum('amount');

        // Budget vs Expenses
        $budgets = Budget::withSum('expenses', 'amount')->get();

        return view('reports.index', compact(
            'fromDate',
            'toDate',
            'totalSales',
            'salesCollected',
            'salesPending',
            'totalPurchases',
            'purchasesPaid',
            'purchasesPending',
            'totalExpenses',
            'budgets'
        ));
    }
}

