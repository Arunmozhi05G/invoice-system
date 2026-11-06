<?php

namespace App\Http\Controllers;

use App\Enums\BudgetPeriod;
use App\Http\Requests\StoreBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Models\Budget;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function index(Request $request): View
    {
        $budgets = Budget::query()
            ->withSum('expenses', 'amount')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%")
                        ->orWhere('year', 'like', "%{$search}%");
                });
            })
            ->when($request->period_type, function ($query, $periodType) {
                $query->where('period_type', $periodType);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('budgets.index', compact('budgets'));
    }

    public function create(): View
    {
        $periods = BudgetPeriod::cases();

        return view('budgets.create', compact('periods'));
    }

    public function store(StoreBudgetRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($data['period_type'] === BudgetPeriod::YEARLY->value) {
            $data['month'] = null;
        }

        $exists = Budget::where('category', $data['category'])
            ->where('period_type', $data['period_type'])
            ->where('year', $data['year'])
            ->where('month', $data['month'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'category' => 'A budget for this category and period already exists.',
            ]);
        }

        Budget::create($data);

        return redirect()
            ->route('budgets.index')
            ->with('success', 'Budget created successfully.');
    }

    public function show(Budget $budget): View
    {
        $budget->load('expenses');

        return view('budgets.show', compact('budget'));
    }

    public function edit(Budget $budget): View
    {
        $periods = BudgetPeriod::cases();

        return view('budgets.edit', compact('budget', 'periods'));
    }

    public function update(
        UpdateBudgetRequest $request,
        Budget $budget
    ): RedirectResponse {
        $data = $request->validated();

        if ($data['period_type'] === BudgetPeriod::YEARLY->value) {
            $data['month'] = null;
        }

        $exists = Budget::where('category', $data['category'])
            ->where('period_type', $data['period_type'])
            ->where('year', $data['year'])
            ->where('month', $data['month'])
            ->where('id', '!=', $budget->id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'category' => 'A budget for this category and period already exists.',
            ]);
        }

        $budget->update($data);

        return redirect()
            ->route('budgets.index')
            ->with('success', 'Budget updated successfully.');
    }

    public function destroy(Budget $budget): RedirectResponse
    {
        if ($budget->expenses()->exists()) {
            return back()->with('error', 'Cannot delete budget with existing expenses.');
        }

        $budget->delete();

        return redirect()
            ->route('budgets.index')
            ->with('success', 'Budget deleted successfully.');
    }
}
