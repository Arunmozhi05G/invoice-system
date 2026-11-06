<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Expense;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = date('Y-m-d');

        // Financial totals
        $totalSales = SalesInvoice::sum('grand_total');
        $salesCollected = SalesInvoice::sum('paid_amount');
        $salesPending = max(0, $totalSales - $salesCollected);

        $totalPurchases = PurchaseInvoice::sum('grand_total');
        $purchasesPaid = PurchaseInvoice::sum('paid_amount');
        $purchasesPending = max(0, $totalPurchases - $purchasesPaid);

        $totalExpenses = Expense::sum('amount');
        $totalBudgets = Budget::sum('amount');

        // Payment status counts (Sales Invoices)
        $paidInvoicesCount = SalesInvoice::whereColumn('paid_amount', '>=', 'grand_total')->count();
        $overdueInvoicesCount = SalesInvoice::whereColumn('paid_amount', '<', 'grand_total')
            ->where('due_date', '<', $today)
            ->count();
        $pendingInvoicesCount = SalesInvoice::whereColumn('paid_amount', '<', 'grand_total')
            ->where('due_date', '>=', $today)
            ->count();

        // Recent records
        $recentSales = SalesInvoice::with('customer')
            ->latest()
            ->take(5)
            ->get();

        $recentExpenses = Expense::with('budget')
            ->latest('expense_date')
            ->take(5)
            ->get();

        // Public API Integration: Currency Exchange Rates
        $exchangeRates = $this->getExchangeRates();

        return view('dashboard', compact(
            'totalSales',
            'salesCollected',
            'salesPending',
            'totalPurchases',
            'purchasesPaid',
            'purchasesPending',
            'totalExpenses',
            'totalBudgets',
            'paidInvoicesCount',
            'overdueInvoicesCount',
            'pendingInvoicesCount',
            'recentSales',
            'recentExpenses',
            'exchangeRates'
        ));
    }

    private function getExchangeRates(): array
    {
        $apiKey = config('services.exchange_rate.key');

        $fallbackData = [
            'status' => 'fallback',
            'base' => 'USD',
            'last_updated' => now()->format('d M Y, h:i A'),
            'rates' => [
                'USD' => 83.50,
                'EUR' => 90.25,
                'GBP' => 105.80,
                'AED' => 22.75,
                'CAD' => 61.20,
                'SGD' => 62.40,
            ],
        ];

        if (empty($apiKey)) {
            return $fallbackData;
        }

        return Cache::remember('currency_rates_data', 3600, function () use ($apiKey, $fallbackData) {
            try {
                $response = Http::timeout(3)->get("https://v6.exchangerate-api.com/v6/{$apiKey}/latest/USD");

                if ($response->successful() && isset($response['conversion_rates'])) {
                    $rates = $response['conversion_rates'];
                    $usdToInr = $rates['INR'] ?? 83.50;

                    return [
                        'status' => 'live',
                        'base' => 'USD',
                        'last_updated' => !empty($response['time_last_update_utc'])
                            ? date('d M Y, h:i A', strtotime($response['time_last_update_utc']))
                            : now()->format('d M Y, h:i A'),
                        'rates' => [
                            'USD' => round($usdToInr, 2),
                            'EUR' => isset($rates['EUR']) && $rates['EUR'] > 0 ? round($usdToInr / $rates['EUR'], 2) : 90.25,
                            'GBP' => isset($rates['GBP']) && $rates['GBP'] > 0 ? round($usdToInr / $rates['GBP'], 2) : 105.80,
                            'AED' => isset($rates['AED']) && $rates['AED'] > 0 ? round($usdToInr / $rates['AED'], 2) : 22.75,
                            'CAD' => isset($rates['CAD']) && $rates['CAD'] > 0 ? round($usdToInr / $rates['CAD'], 2) : 61.20,
                            'SGD' => isset($rates['SGD']) && $rates['SGD'] > 0 ? round($usdToInr / $rates['SGD'], 2) : 62.40,
                        ],
                    ];
                }
            } catch (\Exception $e) {
                // Return fallback if offline or request fails
            }

            return $fallbackData;
        });
    }
}
