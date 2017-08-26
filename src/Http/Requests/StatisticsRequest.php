<?php

namespace Mediumart\Orange\SMS\Http\Requests;

use Mediumart\Orange\SMS\Http\SMSClientRequest;

class StatisticsRequest extends SMSClientRequest
{
    /**
     * International country code.
     *
     * @see http://fr.wikipedia.org/wiki/ISO_3166-1#Table_de_codage
     * @var string
     */
    protected $countryCode;

    /**
     * Api app ID.
     *
     * @var string
     */
    protected $appID;

    /**
     * SMSAdminStatsRequest constructor.
     *
     * @param $countryCode
     * @param $appID
     */
    public function __construct($countryCode = null, $appID = null)
    {
        $this->countryCode = $countryCode;
        $this->appID = $appID;
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
        $filters = $this->queryFilters();

        $uri = static::BASE_URI . '/sms/admin/v1/statistics';

        return count($filters) ? $uri.'?'.http_build_query($filters) : $uri;
    }

    /**
     * @return array
     */
    private function queryFilters()
    {
        $filters = [];

        if ($this->countryCode) {
            $filters['country'] = $this->countryCode;
        }

        if ($this->appID) {
            $filters['appid'] = $this->appID;
        }

        return $filters;
    }
}
