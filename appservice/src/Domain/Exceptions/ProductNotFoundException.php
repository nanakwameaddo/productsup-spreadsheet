<?php

namespace App\Domain\Exceptions;

use DomainException;

class ProductNotFoundException extends DomainException
{
    public function __construct($companyName, $productName = 'coffeeFeed')
    {
        parent::__construct("Product {$productName} does not exist for {$companyName}..");
    }

}
