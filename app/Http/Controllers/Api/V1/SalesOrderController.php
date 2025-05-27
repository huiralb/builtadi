<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSalesOrderRequest;
use App\Models\Product;
use App\Models\SalesOrder;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SalesOrderController extends Controller
{
    public function store(StoreSalesOrderRequest $request): JsonResponse
    {
        try {
            // DB::beginTransaction();

            // Create sales order
            $salesOrder = SalesOrder::create([
                'reference_no' => $request->reference_no,
                'sales_id' => $request->sales_id,
                'customer_id' => $request->customer_id,
            ]);

            // Create sales order items
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                $salesOrder->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'selling_price' => $item['selling_price'],
                    'production_price' => $product->production_price,
                ]);
            }

            // DB::commit();

            return response()->json([
                'message' => 'Sales order created successfully',
                'data' => $salesOrder->load('items.product', 'customer', 'sale')
            ], 201);

        } catch (\Exception $e) {
            // DB::rollBack();

            return response()->json([
                'message' => 'Failed to create sales order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
