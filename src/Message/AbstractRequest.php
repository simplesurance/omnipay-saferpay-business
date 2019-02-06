<?php

namespace Bamarni\Omnipay\Saferpay\Business\Message;

use Bamarni\Omnipay\Saferpay\Business\Parameters;
use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;

abstract class AbstractRequest extends BaseAbstractRequest
{
    const BASE_URL = 'https://www.saferpay.com/hosting/';
    const BASE_URL_TEST = 'https://test.saferpay.com/hosting/';

    use Parameters;

    protected $endpoint;

    public function send()
    {
        $url = $this->getEndpoint().'?'.http_build_query($this->getData());

        $httpResponse = $this->httpClient->request('GET', $url);

        return $this->response = $this->createResponse($httpResponse);
    }

    public function sendData($data)
    {
        throw new \BadMethodCallException('This method is unimplemented');
    }

    protected function getEndpoint()
    {
        return ($this->getTestMode() ? self::BASE_URL_TEST : self::BASE_URL) . $this->endpoint;
    }

    abstract protected function createResponse($response);
}
