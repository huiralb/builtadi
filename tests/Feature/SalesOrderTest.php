<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalesOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesOrderTest extends TestCase
{
    use RefreshDatabase;e App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalesOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;

// uses(RefreshDatabase::class);

beforeEach(function () {
    $this->sale = Sale::factory()->create();
    $this->customer = Customer::factory()->create();
    $this->product1 = Product::factory()->create([
        'production_price' => 100000,
        'selling_price' => 150000,
    ]);
    $this->product2 = Product::factory()->create([
        'production_price' => 150000,
        'selling_price' => 200000,
    ]);
});

test('can create a sales order with items', function () {
    $no = uniqid('INV2025-');
    $payload = [
        'reference_no' => $no,
        'sales_id' => $this->sale->id,
        'customer_id' => $this->customer->id,
        'items' => [
            [
                'product_id' => $this->product1->id,
                'quantity' => 2,
                'selling_price' => 150000,
            ],
            [
                'product_id' => $this->product2->id,
                'quantity' => 1,
                'selling_price' => 200000,
            ],
        ],
    ];

    $response = $this->postJson('/api/v1/sales-orders', $payload);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'reference_no',
                'sales_id',
                'customer_id',
                'items' => [
                    '*' => [
                        'id',
                        'quantity',
                        'production_price',
                        'selling_price',
                        'product_id',
                        'order_id',
                    ],
                ],
                'customer' => [
                    'id',
                    'name',
                ],
                'sale' => [
                    'id',
                ],
            ],
        ]);

    $this->assertDatabaseHas('sales_orders', [
        'reference_no' => $no,
        'sales_id' => $this->sale->id,
        'customer_id' => $this->customer->id,
    ]);

    $this->assertDatabaseHas('sales_order_items', [
        'product_id' => $this->product1->id,
        'quantity' => 2,
        'selling_price' => 150000,
        'production_price' => 100000,
    ]);

    $this->assertDatabaseHas('sales_order_items', [
        'product_id' => $this->product2->id,
        'quantity' => 1,
        'selling_price' => 200000,
        'production_price' => 150000,
    ]);
});

test('cannot create sales order with duplicate reference number', function () {
    // Create first order
    SalesOrder::create([
        'reference_no' => 'SO-2025-001',
        'sales_id' => $this->sale->id,
        'customer_id' => $this->customer->id,
    ]);

    // Try to create another order with same reference
    $payload = [
        'reference_no' => 'SO-2025-001',
        'sales_id' => $this->sale->id,
        'customer_id' => $this->customer->id,
        'items' => [
            [
                'product_id' => $this->product1->id,
                'quantity' => 1,
                'selling_price' => 150000,
            ],
        ],
    ];

    $response = $this->postJson('/api/v1/sales-orders', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['reference_no']);
});

test('cannot create sales order with invalid sales id', function () {
    $payload = [
        'reference_no' => 'SO-2025-001',
        'sales_id' => 999999,
        'customer_id' => $this->customer->id,
        'items' => [
            [
                'product_id' => $this->product1->id,
                'quantity' => 1,
                'selling_price' => 150000,
            ],
        ],
    ];

    $response = $this->postJson('/api/v1/sales-orders', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['sales_id']);
});

test('cannot create sales order with invalid customer id', function () {
    $payload = [
        'reference_no' => 'SO-2025-001',
        'sales_id' => $this->sale->id,
        'customer_id' => 999999,
        'items' => [
            [
                'product_id' => $this->product1->id,
                'quantity' => 1,
                'selling_price' => 150000,
            ],
        ],
    ];

    $response = $this->postJson('/api/v1/sales-orders', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['customer_id']);
});

test('cannot create sales order with invalid product id', function () {
    $payload = [
        'reference_no' => 'SO-2025-001',
        'sales_id' => $this->sale->id,
        'customer_id' => $this->customer->id,
        'items' => [
            [
                'product_id' => 999999,
                'quantity' => 1,
                'selling_price' => 150000,
            ],
        ],
    ];

    $response = $this->postJson('/api/v1/sales-orders', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['items.0.product_id']);
});

test('cannot create sales order without items', function () {
    $payload = [
        'reference_no' => 'SO-2025-001',
        'sales_id' => $this->sale->id,
        'customer_id' => $this->customer->id,
        'items' => [],
    ];

    $response = $this->postJson('/api/v1/sales-orders', $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['items']);
});
