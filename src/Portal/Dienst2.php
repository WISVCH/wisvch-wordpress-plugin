<?php

namespace WISVCH\Portal;

/**
 * This class is used to communicate with the Dienst2 API.
 * 
 * @package WISVCH\Portal
 */
class Dienst2
{
    public const DIENST2_API_URL = 'wisvch_dienst2_api_url';
    public const DIENST2_API_TOKEN = 'wisvch_dienst2_api_token';
    public const CONNECT_SUBJECT_PREFIX = 'WISVCH.';

    protected $dienst2_person_base_url;

    public function __construct()
    {
        $is_ch_member = Member::_is_ch_member();
        if (!$is_ch_member) {
            return;
        }

        // Get CH Connect data
        $claim = Member::get_user_claim();
        $sub = str_replace(self::CONNECT_SUBJECT_PREFIX, '', $claim['sub']);
        $sub = 1;

        $this->dienst2_person_base_url = $this->getApiUrl() . 'people/' . $sub . '/';
    }

    /**
     * Get the API token from the database.
     * 
     * @return string
     */
    private function getApiToken()
    {
        return get_option(self::DIENST2_API_TOKEN);
    }

    /**
     * Get the API URL from the database.
     * 
     * @return string
     */
    private function getApiUrl()
    {
        return get_option(self::DIENST2_API_URL);
    }

    /**
     * Get request with authentication.
     * 
     * @param string $path
     */
    public function get(string $path = '')
    {
        $url = $this->dienst2_person_base_url . $path;
        $headers = [
            'Authorization' => 'Token ' . $this->getApiToken(),
        ];

        $response = wp_remote_get(
            $url,
            [
                'headers' => $headers,
            ]
        );

        if (is_wp_error($response)) {
            throw new \Exception($response->get_error_message());
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        return $body;
    }
}
