<?php

namespace EasyPost\Test;

use EasyPost\Billing;
use EasyPost\EasyPost;
use VCR\VCR;

class BillingTest extends \PHPUnit\Framework\TestCase
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

        VCR::insertCassette('billing/fund.yml');

        $billing = Billing::fund(2000, 'primary');

        $this->assertTrue($billing != null);
    }

    /**
     * Test deleting a payment method.
     *
     * @return void
     */
    public function testDelete()
    {
        $this->markTestSkipped('Skipping due to the lack of an available real payment method in tests.');

        VCR::insertCassette('billing/delete.yml');

        $delete_billing = Billing::delete('primary');

        $this->assertTrue($delete_billing != null);
    }
}
