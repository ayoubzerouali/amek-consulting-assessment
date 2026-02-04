<?php

namespace App\Services;

use App\Jobs\ProcessProductImage;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class Collection
{
    public function index()
    {
    $output = collect($offices)->groupBy('city')
    ->map(function ($cityOffices, $city) use ($employees) {
    $names = collect($employees)
        ->where('city', $city)
        ->pluck('name')
        ->values()
        ->all();
        
        return $cityOffices->pluck('office')->mapWithKeys(function ($officeName) use ($names) {
            return [$officeName => $names];
        });
    })
    ->toArray();
    }
}
