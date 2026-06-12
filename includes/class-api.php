<?php

if (!defined('ABSPATH')) exit;

class Altus_API {

    public function get_data($url) {

        $key = get_option('altus_api_key');

        if (empty($key)) {
            return ['error' => 'Missing API key'];
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return ['error' => 'Invalid URL'];
        }

        $endpoint = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';

        $mobile = $this->request($endpoint, $url, $key, 'mobile');
        $desktop = $this->request($endpoint, $url, $key, 'desktop');

        if (isset($mobile['error'])) return $mobile;
        if (isset($desktop['error'])) return $desktop;

        return [
            'mobile' => $mobile,
            'desktop' => $desktop
        ];
    }

    private function request($endpoint, $url, $key, $strategy) {

        $query = add_query_arg([
            'url' => $url,
            'key' => $key,
            'strategy' => $strategy
        ], $endpoint);

        // PageSpeed only returns the "performance" category unless
        // additional categories are explicitly requested. Add each
        // category as its own repeated query parameter.
        foreach (['performance', 'seo', 'accessibility', 'best-practices'] as $cat) {
            $query .= '&category=' . rawurlencode($cat);
        }

        $response = wp_remote_get($query, [
            'timeout' => 60
        ]);

        if (is_wp_error($response)) {
            return ['error' => $response->get_error_message()];
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['error'])) {
            return ['error' => $data['error']['message'] ?? 'API error'];
        }

        return $data;
    }
}