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
            'NAME' => $this->getCardHolderName(),
            'IBAN' => $this->getIban(),
            'MANDATEID' => $this->getMandateId(),
        ];

        if ($card = $this->getCard()) {
            $data['PAN'] = $card->getNumber();
            $data['EXP'] = $card->getExpiryDate('my');
            $data['CVC'] = $card->getCvv();

            if ('yes' === $this->getInstallment()) {
                $data['INSTALLMENT']    = 'yes';
                $data['INSTCOUNT']      = $this->getInstCount();
                $data['REFID']          = $this->getRefId();
            }
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

    /**
     * @return string
     */
    public function getInstallment()
    {
        return $this->getParameter('installment');
    }

    /**
     * Flags the payment request as installment payment.
     * Must be set for initial installment payment and
     * all following installment payments.
     *
     * @param   $value    Values: "yes" or "no"
     *
     * @return  $this
     *
     * @throws  InvalidRequestException
     */
    public function setInstallment($value)
    {
        if (!in_array($value, ['yes', 'no'])) {
            throw new InvalidRequestException(
                sprintf('Invalid installment value "%s". Allowed values are "yes" and "no".', $value)
            );
        }

        return $this->setParameter('installment', $value);
    }

    /**
     * @return int
     */
    public function getInstCount()
    {
        return $this->getParameter('instCount');
    }

    /**
     * Number of installments as agreed between merchant and
     * customer. INSTCOUNT is mandatory for the initial
     * installment payment and not necessary for following
     * installment payments!
     *
     * @param   $value  Minimum value is "2"
     *
     * @return  $this
     *
     * @throws  InvalidRequestException
     */
    public function setInstCount($value)
    {
        if (!is_int($value) || $value < 2) {
            throw new InvalidRequestException(
                sprintf('Invalid instCount value "%s". The value must be an "integer" and at least "2".', $value)
            );
        }

        return $this->setParameter('instCount', $value);
    }

    /**
     * @return string
     */
    public function getRefId()
    {
        return $this->getParameter('refId');
    }

    /**
     * Uses the transaction identifier of the initial payment to refer to
     * following recurring or installment payments.
     *
     * @param   $value  ID of the initial payment
     *
     * @return  $this
     */
    public function setRefId($value)
    {
        return $this->setParameter('refId', $value);
    }

    public function getRefOid()
    {
        return $this->getParameter('refOid');
    }

    /**
     * Uses the reference number of the initial payment to refer to
     * following recurring or installment payments.
     *
     * @param   $value  ORDERID of the initial payment
     *
     * @return  $this
     */
    public function setRefOid($value)
    {
        return $this->setParameter('refOid', $value);
    }

    protected function createResponse($response)
    {
        return new CompleteAuthorizeResponse($this, $response);
    }
}
