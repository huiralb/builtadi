<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SalesOrder;
use App\Models\SalesTarget;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $year = Carbon::now()->year;
        $salesId = $request->input('sales');
        $salesName = null;

        // Base query for targets
        $targetQuery = SalesTarget::query()
            ->whereYear('active_date', $year)
            ->select(
                DB::raw("DATE_FORMAT(active_date, '%b') as month"),
                DB::raw('SUM(amount) as amount')
            )
            ->groupBy('month', DB::raw('MONTH(active_date)'))
            ->orderBy(DB::raw('MONTH(active_date)'));

        // Base query for transactions
        $transactionQuery = SalesOrder::query()
            ->whereYear('created_at', $year)
            ->join('sales_order_items', 'sales_orders.id', '=', 'sales_order_items.order_id')
            ->select(
                DB::raw("DATE_FORMAT(sales_orders.created_at, '%b') as month"),
                DB::raw('SUM(sales_order_items.selling_price * sales_order_items.quantity) as revenue'),
                DB::raw('SUM((sales_order_items.selling_price - sales_order_items.production_price) * sales_order_items.quantity) as income')
            )
            ->groupBy('month', DB::raw('MONTH(sales_orders.created_at)'))
            ->orderBy(DB::raw('MONTH(sales_orders.created_at)'));

        // Apply sales filter if provided
        if ($request->has('sales') && $salesId) {
            $sales = Sale::with('user')->find($salesId);
            $salesName = $sales?->user?->name;
            $targetQuery->where('sales_id', $salesId);
            $transactionQuery->where('sales_orders.sales_id', $salesId);
        }

        // Get results
        $targets = $targetQuery->get()->pluck('amount', 'month');
        $transactions = $transactionQuery->get()->keyBy('month');

        // Prepare monthly data
        $months = collect(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']);

        // Format the response
        $response = [
            'sales' => $salesName,
            'year' => (string) $year,
            'items' => [
                [
                    'name' => 'Target',
                    'data' => $months->map(function($month) use ($targets) {
                        return [
                            'x' => $month,
                            'y' => number_format($targets->get($month, 0), 2, '.', '')
                        ];
                    })->values()
                ],
                [
                    'name' => 'Revenue',
                    'data' => $months->map(function($month) use ($transactions) {
                        return [
                            'x' => $month,
                            'y' => number_format($transactions->get($month)?->revenue ?? 0, 2, '.', '')
                        ];
                    })->values()
                ],
                [
                    'name' => 'Income',
                    'data' => $months->map(function($month) use ($transactions) {
                        return [
                            'x' => $month,
                            'y' => number_format($transactions->get($month)?->income ?? 0, 2, '.', '')
                        ];
                    })->values()
                ]
            ]
        ];

        return response()->json($response);
    }
}
