<?php

namespace App\Application\Product\Factory\CoffeeFeed;

use App\Application\Product\Contract\ProductFactoryContract;
use App\Domain\Exceptions\ProductNotFoundException;

class ProductCoffeeFeedFactory
{
    public function create(string $companyName): ProductFactoryContract
    {
        return match (strtolower($companyName)) {
            'productsup' => new ProductSupCoffeeFeed(),
            default => throw new ProductNotFoundException($companyName),
        };
    }

}
