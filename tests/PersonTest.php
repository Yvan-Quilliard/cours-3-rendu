<?php

namespace Tests;

use App\Entity\Person;
use App\Entity\Product;
use App\Entity\Wallet;
use Exception;
use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase
{
    public function testPerson(): void
    {
        $person = new Person('John Doe', 'EUR');
        $this->assertEquals('John Doe', $person->getName());
        $this->assertInstanceOf(Wallet::class, $person->getWallet());
        $this->assertEquals('EUR', $person->getWallet()->getCurrency());
    }

    /**
     * @throws Exception
     */
    public function testHasFund(): void
    {
        $person = new Person('John Doe', 'EUR');
        $this->assertFalse($person->hasFund());
    }

    /**
     * @throws Exception
     */
    public function testHasFundWithFund(): void
    {
        $person = new Person('John Doe', 'EUR');
        $person->getWallet()->setBalance(100);
        $this->assertTrue($person->hasFund());
    }

    /**
     * @throws Exception
     */
    public function testTransfertFund(): void
    {
        $person1 = new Person('John Doe', 'EUR');
        $person2 = new Person('Jane Doe', 'EUR');
        $person1->getWallet()->setBalance(100);
        $person1->transfertFund(100, $person2);
        $this->assertEquals(100, $person2->getWallet()->getBalance());
        $this->assertEquals(0, $person1->getWallet()->getBalance());
    }

    public function testTransfertFundWithInvalidCurrency(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Can\'t give money with different currencies');
        $person1 = new Person('John Doe', 'EUR');
        $person2 = new Person('Jane Doe', 'USD');
        $person1->getWallet()->setBalance(100);
        $person1->transfertFund(100, $person2);
    }

    public function testTransfertFundWithInvalidAmount(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid amount');
        $person1 = new Person('John Doe', 'EUR');
        $person2 = new Person('Jane Doe', 'EUR');
        $person1->getWallet()->setBalance(100);
        $person1->transfertFund(-200, $person2);
    }

    public function testTransfertFundWithInsufficientFunds(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Insufficient funds');
        $person1 = new Person('John Doe', 'EUR');
        $person2 = new Person('Jane Doe', 'EUR');
        $person1->getWallet()->setBalance(100);
        $person1->transfertFund(200, $person2);
    }

    /**
     * @throws Exception
     */
    public function walletDivisionAmongSameCurrencyPersons(): void
    {
        $person1 = new Person('John Doe', 'EUR');
        $person2 = new Person('Jane Doe', 'EUR');
        $person3 = new Person('Jack Doe', 'EUR');
        $person1->getWallet()->setBalance(100);
        $person1->divideWallet([$person2, $person3]);
        $this->assertEquals(33.33, $person2->getWallet()->getBalance());
        $this->assertEquals(33.33, $person3->getWallet()->getBalance());
        $this->assertEquals(33.34, $person1->getWallet()->getBalance());
    }

    /**
     * @throws Exception
     */
    public function walletDivisionAmongDifferentCurrencyPersons(): void
    {
        $person1 = new Person('John Doe', 'EUR');
        $person2 = new Person('Jane Doe', 'USD');
        $person3 = new Person('Jack Doe', 'EUR');
        $person1->getWallet()->setBalance(100);
        $person1->divideWallet([$person2, $person3]);
        $this->assertEquals(0, $person2->getWallet()->getBalance());
        $this->assertEquals(50, $person3->getWallet()->getBalance());
        $this->assertEquals(50, $person1->getWallet()->getBalance());
    }

    /**
     * @throws Exception
     */
    public function walletDivisionWithNoOtherPersons(): void
    {
        $person1 = new Person('John Doe', 'EUR');
        $person1->getWallet()->setBalance(100);
        $person1->divideWallet([]);
        $this->assertEquals(100, $person1->getWallet()->getBalance());
    }

    /**
     * @throws Exception
     */
    public function walletDivisionWithInsufficientFunds(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Insufficient funds');
        $person1 = new Person('John Doe', 'EUR');
        $person2 = new Person('Jane Doe', 'EUR');
        $person1->getWallet()->setBalance(0);
        $person1->divideWallet([$person2]);
    }

    /**
     * @throws Exception
     */
    public function buyingProductWithMatchingCurrency(): void
    {
        $person = new Person('John Doe', 'EUR');
        $product = new Product('Product 1', ['EUR' => 50], 'other');
        $person->getWallet()->setBalance(100);
        $person->buyProduct($product);
        $this->assertEquals(50, $person->getWallet()->getBalance());
    }

    /**
     * @throws Exception
     */
    public function buyingProductWithNonMatchingCurrency(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Can\'t buy product with this wallet currency');
        $person = new Person('John Doe', 'EUR');
        $product = new Product('Product 1', ['USD' => 50], 'other');
        $person->getWallet()->setBalance(100);
        $person->buyProduct($product);
    }

    /**
     * @throws Exception
     */
    public function buyingProductWithInsufficientFunds(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Insufficient funds');
        $person = new Person('John Doe', 'EUR');
        $product = new Product('Product 1', ['EUR' => 150], 'other');
        $person->getWallet()->setBalance(100);
        $person->buyProduct($product);
    }
}
