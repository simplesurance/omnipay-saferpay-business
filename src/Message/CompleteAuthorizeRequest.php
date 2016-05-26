<?php

namespace Bamarni\Omnipay\Saferpay\Business\Message;

use Omnipay\Common\Exception\InvalidRequestException;

class CompleteAuthorizeRequest extends AbstractRequest
{
    protected $endpoint = 'execute.asp';

    public function getData()
    {
        $this->validate('accountId', 'spPassword', 'amount');

        $data = [
            'ACCOUNTID' => $this->getAccountId(),
            'spPassword' => $this->getSpPassword(),
            'AMOUNT' => $this->getAmountInteger(),
            'CURRENCY' => $this->getCurrency(),
        ];

        // In case of recurring payments we can return right away.
        if ('yes' === $this->getRecurring()) {
            return $this->prepareRecurringData($data);
        }

        $data = array_merge(
            $data,
            [
                'NAME' => $this->getCardHolderName(),
                'IBAN' => $this->getIban(),
                'MANDATEID' => $this->getMandateId()
            ]
        );

        if ($card = $this->getCard()) {
            $data['PAN'] = $card->getNumber();
            $data['EXP'] = $card->getExpiryDate('my');
            $data['CVC'] = $card->getCvv();
        } elseif (!$data['IBAN']) {
            $this->validate('bankCode', 'bankAccountNumber');

            $data['TRACK2'] = ';59'.$this->getBankCode().'='.$this->getBankAccountNumber();
        }

        if (!$card && !$data['IBAN'] && !$data['TRACK2']) {
            throw new InvalidRequestException(
                'Either a "card", "IBAN" or a "bank code"/"bank account number" pair is required'
            );
        }

        return $data;
    }

    public function getMandateId()
    {
        return $this->getParameter('mandateId');
    }

    public function setMandateId($value)
    {
        return $this->setParameter('mandateId', $value);
    }

    public function getCardHolderName()
    {
        return htmlspecialchars_decode($this->getParameter('cardHolderName'));
    }

    public function setCardHolderName($value)
    {
        return $this->setParameter('cardHolderName', htmlspecialchars($value));
    }

    public function getIBAN()
    {
        return $this->getParameter('IBAN');
    }

    public function setIBAN($value)
    {
        return $this->setParameter('IBAN', $value);
    }

    public function getBankCode()
    {
        return $this->getParameter('bankCode');
    }

    public function setBankCode($value)
    {
        return $this->setParameter('bankCode', $value);
    }

    public function getBankAccountNumber()
    {
        return $this->getParameter('bankAccountNumber');
    }

    public function setBankAccountNumber($value)
    {
        return $this->setParameter('bankAccountNumber', $value);
    }

    public function getCardRefId()
    {
        return $this->getParameter('cardRefId');
    }

    public function setCardRefId($value)
    {
        return $this->setParameter('cardRefId', $value);
    }

    public function getRecurring()
    {
        return $this->getParameter('recurring');
    }

    public function setRecurring($value)
    {
        return $this->setParameter('recurring', $value);
    }

    public function getRefId()
    {
        return $this->getParameter('refId');
    }

    public function setRefId($value)
    {
        return $this->setParameter('refId', $value);
    }

    protected function createResponse($response)
    {
        return new CompleteAuthorizeResponse($this, $response);
    }

    /**
     * Returns the required data for recurring payments.
     *
     * @param array $data
     *
     * @return array
     */
    protected function prepareRecurringData(array $data)
    {
        return array_merge(
            $data,
            [
                'RECURRING' => 'YES',
                'REFID' => $this->getRefId(),
                'CARDREFID' => $this->getCardRefId()
            ]
        );
    }
}
