<?php

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalesOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

uses(TestCase::class);
uses(RefreshDatabase::class);

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

    expect(SalesOrder::count())->toBe(1);

    $order = SalesOrder::first();
    Log::info('Created Sales Order: {order}', ['order' => $order]);


    expect($order->reference_no)->toBe($no)
        ->and($order->sales_id)->toBe($this->sale->id)
        ->and($order->customer_id)->toBe($this->customer->id);

    $items = $order->items;
    expect($items)->toHaveCount(2);

    expect($items[0]->product_id)->toBe($this->product1->id)
        ->and($items[0]->quantity)->toBe(2)
        ->and($items[0]->selling_price)->toBe("150000.00")
        ->and($items[0]->production_price)->toBe("100000.00");

    expect($items[1]->product_id)->toBe($this->product2->id)
        ->and($items[1]->quantity)->toBe(1)
        ->and($items[1]->selling_price)->toBe("200000.00")
        ->and($items[1]->production_price)->toBe("150000.00");
});

test('cannot create sales order with duplicate reference number', function () {
    // Create first order
    SalesOrder::create([
        'reference_no' => 'SO-2025-001',
        'sales_id' => $this->sale->id,
        'customer_id' => $this->customer->id,
    ]);

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

    expect(SalesOrder::count())->toBe(1);
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

    expect(SalesOrder::count())->toBe(0);
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

    expect(SalesOrder::count())->toBe(0);
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

    expect(SalesOrder::count())->toBe(0);
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

    expect(SalesOrder::count())->toBe(0);
});
