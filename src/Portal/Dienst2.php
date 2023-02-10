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
    public const DIENST2_ALLOWED_FIELDS = [
        'pronouns',
        'email',
        'phone_mobile',
        'street_name',
        'house_number',
        'postcode',
        'city',
        'country',
        'machazine',
        'mail_announcements',
        'mail_company',
        'mail_education',
    ];

    public const DIENST2_CHECKBOX_FIELDS = [
        'machazine',
        'mail_announcements',
        'mail_company',
        'mail_education',
    ];

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
    private function get(string $path = '')
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

    /**
     * Patch request with authentication.
     * 
     * @param string $path
     * @param array $data
     */
    private function patch(string $path = '', array $data = [])
    {
        $url = $this->dienst2_person_base_url . $path;
        $headers = [
            'Authorization' => 'Token ' . $this->getApiToken(),
        ];

        $response = wp_remote_request(
            $url,
            [
                'method' => 'PATCH',
                'headers' => $headers,
                'body' => $data,
            ]
        );

        if (is_wp_error($response)) {
            throw new \Exception($response->get_error_message());
        }

        return json_decode(wp_remote_retrieve_body($response), true);
    }

    /**
     * Get the user's profile.
     * 
     * @return array
     */
    public function getPerson()
    {
        return $this->get();
    }

    /**
     * Update the user's profile.
     * 
     * @param array $data
     * @return array
     */
    public function updatePerson(array $data)
    {
        $allowed_fields = self::DIENST2_ALLOWED_FIELDS;
        $data = array_filter($data, function ($key) use ($allowed_fields) {
            return in_array($key, $allowed_fields);
        }, ARRAY_FILTER_USE_KEY);

        # Set the required fields to the current values
        $person = $this->getPerson();
        $data['initials'] = $person['initials'];
        $data['firstname'] = $person['firstname'];
        $data['surname'] = $person['surname'];
        $data['revision_comment'] = "Updated by {$person['formatted_name']} via the website.";

        return $this->patch('', $data);
    }
}
