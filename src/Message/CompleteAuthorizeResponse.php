<?php

namespace Bamarni\Omnipay\Saferpay\Business\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

class CompleteAuthorizeResponse extends AbstractResponse
{
    protected $successful = false;

    /**
     * @param RequestInterface          $request
     * @param \GuzzleHttp\Psr7\Response $response
     */
    public function __construct(RequestInterface $request, $response)
    {
        $body = (string) $response->getBody();

        if (0 === strpos($body, 'OK:')) {
            $data = simplexml_load_string(substr($body, 3));
            $result = (string) $data->attributes()->RESULT;

            if ('0' === $result) {
                $this->successful = true;
            }
        }

        if (!$this->successful) {
            $data = $body;
        }

        parent::__construct($request, $data);
    }

    public function isSuccessful()
    {
        return $this->successful;
    }

    public function getTransactionReference()
    {
        if ($this->successful) {
            return (string) $this->data->attributes()->ID;
        }

        return null;
    }

    public function getMessage()
    {
        if (preg_match('/AUTHMESSAGE="([^"]*)"/i', $this->data, $authMessage)) {
            return $authMessage[1];
        }

        return $this->data;
    }
}
