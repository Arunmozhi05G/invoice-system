<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    // Customer List
    public function index(Request $request): View
    {
    $customers = Customer::query()
        ->when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

        return view('customers.index', compact('customers'));
    }

    // Create Page
    public function create(): View
    {
        return view('customers.create');
    }

    // Store Customer
    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        Customer::create($request->validated());

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    // View Customer
    public function show(Customer $customer): View
    {
        return view('customers.show', compact('customer'));
    }

    // Edit Page
    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    // Update Customer
    public function update(
        UpdateCustomerRequest $request,
        Customer $customer
    ): RedirectResponse {

        $customer->update($request->validated());

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    // Delete Customer
    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}