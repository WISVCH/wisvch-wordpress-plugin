<?php

namespace WISVCH\Portal;

/**
 * This class is used to communicate with the Dienst2 API.
 * 
 * @package WISVCH\Portal
 */
class Dienst2
{
    const DIENST2_API_URL = 'wisvch_dienst2_api_url';
    const DIENST2_API_TOKEN = 'wisvch_dienst2_api_token';

    /**
     * Dienst2 constructor.
     */
    public function init()
    {
        // add_action('admin_menu', [$this, 'addAdminMenu']);
        // add_action('admin_init', [$this, 'registerSettings']);
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
    public function get(string $path)
    {
        $url = $this->getApiUrl() . $path;
        $headers = [
            'Authorization' => 'Token ' . $this->getApiToken(),
        ];

        $response = wp_remote_get(
            $url,
            [
                'headers' => $headers,
            ]
        );

        $body = wp_remote_retrieve_body($response);
        return json_decode($body);
    }
}
