<?php

namespace Bamarni\Omnipay\Saferpay\Business\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

class CompleteRegisterCardResponse extends AbstractResponse
{
    protected $successful = false;

    /**
     * @param RequestInterface          $request
     * @param \GuzzleHttp\Psr7\Response $response
     */
    public function __construct(RequestInterface $request, $response)
    {
        $body = (string) $response->getBody();

        if (0 === strpos($body, 'OK:RESULT=0')) {
            $this->successful = true;
        }

        parent::__construct($request, $body);
    }

    public function isSuccessful()
    {
        return $this->successful;
    }
}
