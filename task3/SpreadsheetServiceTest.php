<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Services\SpreadsheetService;
use App\Jobs\ProcessProductImage;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Queue::fake();
});

it('imports valid products and dispatches jobs', function () {
    // mock importer response
    $data = [
        ['product_code' => 'sku1', 'quantity' => 5],
        ['product_code' => 'sku2', 'quantity' => 12],
    ];

    app()->bind('importer', fn() => new class($data) {
        public function __construct(public $data) {}
        public function import($path) { return $this->data; }
    });

    (new SpreadsheetService())->processSpreadsheet('test.csv');

    // check db
    expect(Product::count())->toBe(2);
    $this->assertDatabaseHas('products', ['code' => 'sku1']);
    
    // make sure jobs fired
    Queue::assertPushed(ProcessProductImage::class, 2);
});

it('skips invalid rows', function () {
    $data = [
        ['product_code' => '', 'quantity' => 10], // missing code
        ['product_code' => 'sku3', 'quantity' => 0], // bad qty
    ];

    app()->bind('importer', fn() => new class($data) {
        public function __construct(public $data) {}
        public function import($path) { return $this->data; }
    });

    (new SpreadsheetService())->processSpreadsheet('test.csv');

    expect(Product::count())->toBe(0);
    Queue::assertNothingPushed();
});

it('prevents duplicate codes based on db unique constraint', function () {
    Product::create(['code' => 'EXISTING', 'quantity' => 1]);

    $data = [
        ['product_code' => 'EXISTING', 'quantity' => 5],
    ];

    app()->bind('importer', fn() => new class($data) {
        public function __construct(public $data) {}
        public function import($path) { return $this->data; }
    });

    (new SpreadsheetService())->processSpreadsheet('test.csv');

    // should still onl  have the one we created manually
    expect(Product::count())->toBe(1);
    Queue::assertNothingPushed();
});
it('rejects non-integer quantities', function () {
    $data = [
        ['product_code' => 'sku4', 'quantity' => 'ten'],  // string
        ['product_code' => 'sku5', 'quantity' => 3.5],    // float
    ];
    app()->bind('importer', fn() => new class($data) {
        public function __construct(public $data) {}
        public function import($path) { return $this->data; }
    });
    
    (new SpreadsheetService())->processSpreadsheet('test.csv');
    
    expect(Product::count())->toBe(0);
    Queue::assertNothingPushed();


});

it('dispatches job with correct product instance', function () {
    $data = [
        ['product_code' => 'sku-test', 'quantity' => 7],
    ];
    app()->bind('importer', fn() => new class($data) {
        public function __construct(public $data) {}
        public function import($path) { return $this->data; }
    });
    
    (new SpreadsheetService())->processSpreadsheet('test.csv');
    
    Queue::assertPushed(ProcessProductImage::class, function ($job) {
        return $job->product->code === 'sku-test' 
            && $job->product->quantity === 7;
    });
});
