<?php

namespace Sisu\Omnipay\Saferpay\Business\Message;

class CompleteRegisterCardRequest extends AbstractRequest
{
    protected $endpoint = 'VerifyPayConfirm.asp';

    public function getData()
    {
        $data = array(
            'ACCOUNTID' => $this->getAccountId(),
            'spPassword' => $this->getSpPassword(),
            'DATA' => $this->httpRequest->query->get('DATA'),
            'SIGNATURE' => $this->httpRequest->query->get('SIGNATURE'),
        );

        return $data;
    }

    protected function createResponse($response)
    {
        return $this->response = new CompleteRegisterCardResponse($this, $response);
    }
}
