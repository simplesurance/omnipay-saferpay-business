<?php

namespace Bamarni\Omnipay\Saferpay\Business\Message;

class RegisterCardRequest extends AbstractRequest
{
    protected $endpoint = 'CreatePayInit.asp';

    public function getCardRefId()
    {
        return $this->getParameter('cardRefId');
    }

    public function setCardRefId($value)
    {
        return $this->setParameter('cardRefId', $value);
    }

    public function getSuccessLink()
    {
        return $this->getParameter('successLink');
    }

    public function setSuccessLink($value)
    {
        return $this->setParameter('successLink', $value);
    }

    public function getFailLink()
    {
        return $this->getParameter('failLink');
    }

    public function setFailLink($value)
    {
        return $this->setParameter('failLink', $value);
    }

    public function getData()
    {
        $data = array(
            'ACCOUNTID' => $this->getAccountId(),
            'CARDREFID' => $this->getCardRefId(),
            'SUCCESSLINK' => $this->getSuccessLink(),
            'FAILLINK' => $this->getFailLink(),
            'LIFETIME' => 1,
        );

        return $data;
    }

    protected function createResponse($response)
    {
        /** @var \GuzzleHttp\Psr7\Response $response */
        return $this->response = new RegisterCardResponse($this, (string) $response->getBody());
    }
}
