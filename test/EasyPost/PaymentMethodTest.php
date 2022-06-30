<?php

namespace EasyPost\Test;

use EasyPost\EasyPost;
use EasyPost\PaymentMethod;
use VCR\VCR;

class PaymentMethodTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Setup the testing environment for this file.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        EasyPost::setApiKey(getenv('EASYPOST_PROD_API_KEY'));

        VCR::turnOn();
    }

    /**
     * Cleanup the testing environment once finished.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        VCR::eject();
        VCR::turnOff();
    }

    /**
     * Test funding a EasyPost wallet by using either primary or secondary payment method.
     *
     * @return void
     */
    public function testFund()
    {
        $this->markTestSkipped('Skipping due to the lack of an available real payment method in tests.');

        VCR::insertCassette('payment_method/fund.yml');

        $payment_method_fund = PaymentMethod::fund(2000, 'primary');

        $this->assertTrue($payment_method_fund != null);
    }

    /**
     * Test deleting a payment method.
     *
     * @return void
     */
    public function testDelete()
    {
        $this->markTestSkipped('Skipping due to the lack of an available real payment method in tests.');

        VCR::insertCassette('payment_method/delete.yml');

        $delete_payment_method = PaymentMethod::delete('primary');

        $this->assertTrue($delete_payment_method != null);
    }

    /**
     * Test retrieving all payment methods.
     *
     * @return void
     */
    public function testAll()
    {
        VCR::insertCassette('payment_method/all.yml');

        $payment_methods = PaymentMethod::all();

        $this->assertTrue($payment_methods->primary_payment_method != null);
        $this->assertTrue($payment_methods->secondary_payment_method != null);
    }
}
