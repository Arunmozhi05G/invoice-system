<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Enums\ProductType;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::latest()->paginate(10);

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        $types = ProductType::cases();

        return view('products.create', compact('types'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        Product::create($request->validated());

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product): View
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $types = ProductType::cases();

        return view('products.edit', compact('product', 'types'));
    }

    public function update(
        UpdateProductRequest $request,
        Product $product
    ): RedirectResponse {

        $product->update($request->validated());

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}