<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @property string $id
 * @property string $object
 * @property Billing primary_payment_method
 * @property Billing secondary_payment_method
 */
class PaymentMethod extends EasypostResource
{
    /**
     * Retrieve all payment methods.
     *
     * @param mixed $params
     * @param string $apiKey
     * @return mixed
     */
    public static function all($params = null, $apiKey = null)
    {
        $response = self::_all(get_class(), $params, $apiKey);

        if ($response->id == null) {
            throw new Error('Billing has not been setup for this user. Please add a payment method.');
        }
        return $response;
    }

        /**
     * Fund your EasyPost wallet by charging your primary or secondary payment method.
     *
     * @param string $amount
     * @param string $primary_or_secondary
     * @param string $api_key
     * @return mixed
     */
    public static function fund($amount, $primary_or_secondary = 'primary', $api_key = null)
    {
        $payment_method_info = PaymentMethod::get_payment_info($primary_or_secondary);
        $payment_method_endpoint = $payment_method_info[0];
        $payment_method_id = $payment_method_info[1];

        $url = $payment_method_endpoint . "/$payment_method_id/charges";
        $wrapped_params = ['amount' => $amount];
        $requestor = new Requestor($api_key);
        list($response, $api_key) = $requestor->request('post', $url, $wrapped_params);
        return Util::convertToEasyPostObject($response, $api_key);
    }

    /**
     * Delete a credit card.
     *
     * @param string $credit_card_id
     * @param string $api_key
     * @return mixed
     */
    public static function delete($primary_or_secondary, $api_key = null)
    {
        $payment_method_info = PaymentMethod::get_payment_info($primary_or_secondary);
        $payment_method_endpoint = $payment_method_info[0];
        $payment_method_id = $payment_method_info[1];

        $url = $payment_method_endpoint . "/$payment_method_id";
        $requestor = new Requestor($api_key);
        list($response, $api_key) = $requestor->request('delete', $url);
        return Util::convertToEasyPostObject($response, $api_key);
    }

    private static function get_payment_info($primary_or_secondary = 'primary')
    {
        $payment_methods = PaymentMethod::all();
        $payment_method_map = [
            'primary' => 'primary_payment_method',
            'secondary' => 'secondary_payment_method'
        ];
        $payment_method_to_use = $payment_method_map[$primary_or_secondary] ?? null;

        if ($payment_methods->$payment_method_to_use != null) {
            $payment_method_id = $payment_methods->$payment_method_to_use->id;
        }

        if ($payment_method_to_use !== null && $payment_method_id !== null) {
            if (strpos($payment_method_id, 'card_') !== 0) {
                return array('credit_cards', $payment_methods->$payment_method_to_use->id);
            } else if (strpos($payment_method_id, 'bank_') !== 0) {
                return array('bank_accounts', $payment_methods->$payment_method_to_use->id);
            }
        }

        throw new Error('The chosen payment method is not valid. Please try again.');
    }
}
