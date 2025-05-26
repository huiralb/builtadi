<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SalesOrder;
use App\Models\SalesTarget;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesPerformanceController extends Controller
{
    public function index(Request $request)
    {
        // Get query parameters
        $month = $request->get('month') ? Carbon::parse($request->get('month')) : Carbon::now();
        $isUnderperform = $request->get('isUnderperform', null);

        // Base query to get sales with their targets and revenue
        $query = Sale::query()
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->leftJoin('sales_targets', function ($join) use ($month) {
                $join->on('sales.id', '=', 'sales_targets.sales_id')
                    ->whereYear('sales_targets.active_date', $month->year)
                    ->whereMonth('sales_targets.active_date', $month->month);
            })
            ->leftJoin('sales_orders', function ($join) use ($month) {
                $join->on('sales.id', '=', 'sales_orders.sales_id')
                    ->whereYear('sales_orders.created_at', $month->year)
                    ->whereMonth('sales_orders.created_at', $month->month);
            })
            ->leftJoin('sales_order_items', 'sales_orders.id', '=', 'sales_order_items.order_id')
            ->select(
                'users.name as sales',
                DB::raw('COALESCE(SUM(sales_order_items.selling_price * sales_order_items.quantity), 0) as revenue_amount'),
                DB::raw('COALESCE(sales_targets.amount, 0) as target_amount')
            )
            ->groupBy('sales.id', 'users.name', 'sales_targets.amount');

        // Apply underperform filter if provided
        if ($request->has('isUnderperform')) {
            $condition = $isUnderperform == 'true' ? '<' : '>=';
            $query->havingRaw("COALESCE(SUM(sales_order_items.selling_price * sales_order_items.quantity), 0) $condition COALESCE(sales_targets.amount, 0)");
        }

        // Get results
        $results = $query->get();

        // Format response
        $response = [
            'is_underperform' => $isUnderperform,
            'month' => $month->format('F Y'),
            'items' => $results->map(function ($item) {
                $revenueAmount = number_format($item->revenue_amount, 2, '.', '');
                $targetAmount = number_format($item->target_amount, 2, '.', '');
                $percentage = $item->target_amount > 0 ?
                    number_format(($item->revenue_amount / $item->target_amount) * 100, 2) : '0.00';

                return [
                    'sales' => $item->sales,
                    'revenue' => [
                        'amount' => $revenueAmount,
                        'abbreviation' => $this->formatAmountAbbreviation($item->revenue_amount)
                    ],
                    'target' => [
                        'amount' => $targetAmount,
                        'abbreviation' => $this->formatAmountAbbreviation($item->target_amount)
                    ],
                    'percentage' => $percentage
                ];
            })->all()
        ];

        return response()->json($response);
    }

    private function formatAmountAbbreviation($amount)
    {
        if ($amount >= 1000000000) {
            return number_format($amount / 1000000000, 2) . 'B';
        }
        if ($amount >= 1000000) {
            return number_format($amount / 1000000, 2) . 'M';
        }
        if ($amount >= 1000) {
            return number_format($amount / 1000, 2) . 'K';
        }
        return number_format($amount, 2);
    }
}
