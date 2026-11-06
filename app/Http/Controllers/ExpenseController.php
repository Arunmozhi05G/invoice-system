<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Budget;
use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $budgets = Budget::orderBy('name')->get();

        $expenses = Expense::with('budget')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%")
                        ->orWhereHas('budget', function ($bq) use ($search) {
                            $bq->where('name', 'like', "%{$search}%")
                                ->orWhere('category', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->budget_id, function ($query, $budgetId) {
                $query->where('budget_id', $budgetId);
            })
            ->latest('expense_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('expenses.index', compact('expenses', 'budgets'));
    }

    public function create(): View
    {
        $budgets = Budget::orderBy('name')->get();

        return view('expenses.create', compact('budgets'));
    }

    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        Expense::create($request->validated());

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense recorded successfully.');
    }

    public function show(Expense $expense): View
    {
        $expense->load('budget');

        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense): View
    {
        $budgets = Budget::orderBy('name')->get();

        return view('expenses.edit', compact('expense', 'budgets'));
    }

    public function update(
        UpdateExpenseRequest $request,
        Expense $expense
    ): RedirectResponse {
        $expense->update($request->validated());

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }
}
