<?php

namespace Tests;

use App\Entity\Product;
use Exception;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testProductCreationWithValidParameters(): void
    {
        $product = new Product('Product 1', ['EUR' => 50, 'USD' => 60], 'other');
        $this->assertEquals('Product 1', $product->getName());
        $this->assertEquals(['EUR' => 50, 'USD' => 60], $product->getPrices());
        $this->assertEquals('other', $product->getType());
    }

    /**
     * @throws Exception
     */
    public function testProductCreationWithNegativePrice(): void
    {
        $product = new Product('Product 1', ['EUR' => -50], 'other');
        $this->assertEquals([], $product->getPrices());
    }

    /**
     * @throws Exception
     */
    public function testProductCreationWithInvalidType(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid type');
        new Product('Product 1', ['EUR' => 50], 'invalid');
    }

    /**
     * @throws Exception
     */
    public function productTVACalculationForFoodProduct(): void
    {
        $product = new Product('Product 1', ['EUR' => 50], 'food');
        $this->assertEquals(0.1, $product->getTVA());
    }

    /**
     * @throws Exception
     */
    public function productTVACalculationForNonFoodProduct(): void
    {
        $product = new Product('Product 2', ['EUR' => 50], 'tech');
        $this->assertEquals(0.2, $product->getTVA());
    }

    /**
     * @throws Exception
     */
    public function productPriceInAvailableCurrency(): void
    {
        $product = new Product('Product 1', ['EUR' => 50], 'other');
        $this->assertEquals(50, $product->getPrice('EUR'));
    }

    /**
     * @throws Exception
     */
    public function productPriceInUnavailableCurrency(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Currency not available for this product');
        $product = new Product('Product 1', ['EUR' => 50], 'other');
        $product->getPrice('USD');
    }

    /**
     * @throws Exception
     */
    public function productPriceInInvalidCurrency(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid currency');
        $product = new Product('Product 1', ['EUR' => 50], 'other');
        $product->getPrice('INVALID');
    }
}
