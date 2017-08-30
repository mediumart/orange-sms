<?php

namespace Mediumart\Orange\SMS\Http\Requests;

use Mediumart\Orange\SMS\Http\SMSClientRequest;

class ContractsRequest extends SMSClientRequest
{
    /**
     * International country code.
     *
     * @see http://fr.wikipedia.org/wiki/ISO_3166-1#Table_de_codage
     * @var string
     */
    protected $countryCode;

    /**
     * ContractsRequest constructor.
     * @param string|null $countryCode
     */
    public function __construct($countryCode = null)
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @inherit
     *
     * @return string
     */
    public function method()
    {
        return 'GET';
    }

    /**
     * @inherit
     *
     * @return string
     */
    public function uri()
    {
        $uri = static::BASE_URI . '/sms/admin/v1/contracts';

        return $this->countryCode ? $uri.'?country='.$this->countryCode : $uri;
    }
}
