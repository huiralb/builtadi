<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\Customer;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionSummaryController extends Controller
{
    /**
     * Display sales transaction summary per month for past year.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $customerId = $request->query('customer');
        $salesId = $request->query('sales');
        $sales = null;
        $customer = null;
        $currentYear = date('Y'); // 2025
        $years = [$currentYear - 2, $currentYear - 1, $currentYear]; // [2023, 2024, 2025]

        $query = SalesOrder::query()
            ->join('sales_order_items', 'sales_orders.id', '=', 'sales_order_items.order_id')
            ->whereYear('sales_orders.created_at', '>=', $years[0]);

        // filter
        $query->when($customerId, function ($query) use ($customerId, &$customer) {
            if( $c = Customer::find($customerId) ) {
                $customer = $c->name;
                return $query->where('sales_orders.customer_id', $customerId);
            }
            return $query->whereNull('sales_orders.customer_id');
        })
        ->when($salesId, function ($query) use ($salesId, &$sales) {
            if ($s = Sale::with('user')->find($salesId)) {
                $sales = $s->user?->name;
                return $query->where('sales_orders.sales_id', $salesId);
            }
            return $query->whereNull('sales_orders.sales_id');
        });

        $monthlySales = $query
            ->select(
                DB::raw('YEAR(sales_orders.created_at) as year'),
                DB::raw('MONTH(sales_orders.created_at) as month'),
                DB::raw('SUM(sales_order_items.selling_price * sales_order_items.quantity) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $response = [
            'customer' => $customer,
            'sales' => $sales,
            'items' => []
        ];

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        foreach ($years as $year) {
            $yearData = [
                'name' => $year,
                'data' => []
            ];

            foreach ($months as $index => $month) {
                $monthNumber = $index + 1;
                $salesData = $monthlySales->first(function ($sale) use ($year, $monthNumber) {
                    return $sale->year == $year && $sale->month == $monthNumber;
                });

                $yearData['data'][] = [
                    'x' => $month,
                    'y' => number_format($salesData ? $salesData->total : 0, 2, '.', '')
                ];
            }

            $response['items'][] = $yearData;
        }

        return response()->json($response);
    }
}
