<?php

namespace App\Lib;

use nusoap_client;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment as PaymentModel;

class Payment
{
    public $gateway;
    public $token;
    public $client;
    public $callback;

    public function __construct() {
        $this->gateway = config("app.payment.default");
        $this->token = config("app.payment.providers.$this->gateway.token");
        $this->client = config("app.payment.providers.$this->gateway.client");
        $this->callback = config("app.payment.providers.$this->gateway.callback");
    }

    public function pay($value, $description) {

        $client = new nusoap_client($this->client['argument_one'], $this->client['argument_two']);
        $client->soap_defencoding = 'UTF-8';
        $result = $client->call('PaymentRequest', [
            [
                'MerchantID' => $this->token,
                'Amount' => $value,
                'Description' => $description,
                'CallbackURL' => $this->callback,
            ],
        ]);
    
        if ($result['Status'] == 100) {
            return $result['Authority'];
        } else {
            return false;
        }
    }

    public function verify($status, $authority) {

        $payment = PaymentModel::where('code', $authority)->first();
        $invoice = Invoice::where('invoice_id', $payment->invoice_id)->first();

        if ($payment->exists()) {
            switch ($status) {
                case 'NOK':
                    $payment->update(['status' => 99]);
                    return false;
                break;
                case 'OK':
                default:
                    $client = new nusoap_client($this->client['argument_one'], $this->client['argument_two']);
                    $client->soap_defencoding = 'UTF-8';
                    $result = $client->call('PaymentVerification', [
                        [
                            'MerchantID' => $this->token,
                            'Amount' => $invoice->value,
                            'Authority'  => $authority,
                        ],
                    ]);
                    if ($result['Status'] == 100) {
                        $payment->update(['status' => 100, 'paid_at' => date('Y-m-d H:i:s')]);
                        $invoice->update(['is_paid' => 1, 'paid_at' => date('Y-m-d H:i:s')]);
                        return $this->submit($invoice, $payment->id);
                    } else {
                        $payment->update(['status' => 101]);
                        return false;
                    }
                break;
            }
        }

    }

    public function submit($invoice, $payment_id) {

        $order = Order::create([
            'order_id' => uuid('ordr'),
            'user_id' => $invoice->user_id,
            'payment_id' => $payment_id,
            'value' => $invoice->value - $invoice->value_discount
        ]);
        return $order ? true : false;
    }

}