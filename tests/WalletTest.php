<?php

namespace Tests;

use App\Entity\Wallet;
use Exception;
use PHPUnit\Framework\TestCase;

class WalletTest extends TestCase
{
    public function testGetBalance(): void
    {
        $wallet = new Wallet('USD');
        $this->assertSame(0.0, $wallet->getBalance());
    }

    public function testGetCurrency(): void
    {
        $wallet = new Wallet('USD');
        $this->assertSame('USD', $wallet->getCurrency());
    }

    /**
     * @throws \Exception
     */
    public function testSetBalance(): void
    {
        $wallet = new Wallet('USD');
        $wallet->setBalance(100);
        $this->assertSame(100.0, $wallet->getBalance());
    }

    public function testSetBalanceThrowsException(): void
    {
        $wallet = new Wallet('USD');
        $this->expectException(\Exception::class);
        $wallet->setBalance(-100);
    }

    /**
     * @throws \Exception
     */
    public function testSetCurrency(): void
    {
        $wallet = new Wallet('USD');
        $wallet->setCurrency('EUR');
        $this->assertSame('EUR', $wallet->getCurrency());
    }

    public function testSetCurrencyThrowsException(): void
    {
        $wallet = new Wallet('USD');
        $this->expectException(\Exception::class);
        $wallet->setCurrency('RUB');
    }

    /**
     * @throws Exception
     */
    public function removingFundsFromWalletWithSufficientBalance(): void
    {
        $wallet = new Wallet('EUR');
        $wallet->setBalance(100);
        $wallet->removeFund(50);
        $this->assertEquals(50, $wallet->getBalance());
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function removingFundsFromWalletWithInsufficientBalance(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Insufficient funds');
        $wallet = new Wallet('EUR');
        $wallet->setBalance(50);
        $wallet->removeFund(100);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function removingNegativeFundsFromWallet(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid amount');
        $wallet = new Wallet('EUR');
        $wallet->setBalance(100);
        $wallet->removeFund(-50);
    }

    /**
     * @throws Exception
     */
    public function addingPositiveFundsToWallet(): void
    {
        $wallet = new Wallet('EUR');
        $wallet->setBalance(100);
        $wallet->addFund(50);
        $this->assertEquals(150, $wallet->getBalance());
    }

    /**
     * @throws Exception
     */
    public function addingZeroFundsToWallet(): void
    {
        $wallet = new Wallet('EUR');
        $wallet->setBalance(100);
        $wallet->addFund(0);
        $this->assertEquals(100, $wallet->getBalance());
    }

    /**
     * @throws Exception
     */
    public function addingNegativeFundsToWallet(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid amount');
        $wallet = new Wallet('EUR');
        $wallet->setBalance(100);
        $wallet->addFund(-50);
    }
}
