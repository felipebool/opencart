<?php

namespace Mundipagg\Controller;

class CreditCard
{
    private $openCart;

    public function __construct($openCart)
    {
        $this->openCart = $openCart;
    }

    public function isEnabled()
    {
        if ($this->openCart->config->get('payment_mundipagg_credit_card_status') === '0') {
            return false;
        }

        return true;
    }

    public function getStatus()
    {
        return $this->openCart->config->get('payment_mundipagg_credit_card_status');
    }

    public function getPaymentTile()
    {
        return $this->openCart->config->get('payment_mundipagg_credit_card_payment_title');
    }

    public function getInvoiceName()
    {
        return $this->openCart->config->get('payment_mundipagg_credit_card_invoice_name');
    }

    public function getOperationType()
    {
        return $this->openCart->config->get('payment_mundipagg_credit_card_operation');
    }

    public function getCards()
    {
        $cards = array();

        $this->openCart->load->model('extension/payment/mundipagg_credit_card');
        $cCModel = $this->openCart->model_extension_payment_mundipagg_credit_card;

        $brands = $cCModel->getCreditCardBrands();
        $activeCreditCards = $cCModel->getActiveCreditCards();

        foreach ($activeCreditCards as $creditCard) {
            $brandName = $creditCard['brand_name'];
            $cards['brandImages'][] = $brands->$brandName->image;
            $cards['brandNames'][] = $brandName;
        }

        return $cards;
    }

    public function getCreditCardLanguage()
    {
        $this->openCart->load->language('extension/payment/mundipagg');
        return $this->openCart->language->get('credit_card');
    }

    public function getCreditCardPageInfo()
    {
        $info = array();
        $info = array_merge($info, $this->getCards());
        $info = array_merge($info, array('creditCardStatus' => $this->getStatus()));
        $info = array_merge($info, array('creditCardText' => $this->getCreditCardLanguage()));
        $info = array_merge($info, array('installments' => $this->getInstallments()));

        return $info;
    }

    public function getInstallments()
    {
        $this->openCart->load->model('checkout/order');
        $orderDetails = $this->openCart->model_checkout_order->getOrder(
            $this->openCart->session->data['order_id']
        );

        $this->openCart->load->model('extension/payment/mundipagg_credit_card');
        return $this->openCart->model_extension_payment_mundipagg_credit_card->getInstallmentsInfo(
            $orderDetails
        );
    }
}
