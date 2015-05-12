<?php

/**
 * Plugin Name: Facebook Open Graph update
 * Plugin URI: https://wordpress.org/plugins/facebook-open-graph-update/
 * Description: Updates Facebook Open Graph when saving/updating a post
 * Version: 1.0.0
 * Author: 7am Oy
 * Author URI: http://www.7am.fi/
 * License: GPLv3
 */

class FacebookOpenGraphUpdate {
    private $api_url = 'https://graph.facebook.com/';

    public function __construct() {
        add_action('transition_post_status', array($this, 'transition_post_status'), 10, 3);
        add_action('save_post', array($this, 'save_post'), 10, 3);
    }

    public function transition_post_status($new_status, $old_status, $post) {
        if ($new_status === 'publish') {
            $this->scrape($post->ID);
        }
    }

    public function save_post($post_id, $post, $update) {
        if ($post->post_status === 'publish') {
            $this->scrape($post->ID);
        }
    }

    public function scrape($post_id = null) {
        if (intval($post_id) > 0) {
            $params = http_build_query(array(
                'id'     => esc_url(get_permalink($post_id)),
                'scrape' => true
            ));

            $response = wp_remote_post($this->api_url .'?'. $params);

            if (is_wp_error($response)) {
                return false;
            } else {
                $body = wp_remote_retrieve_body($response);
                $json = json_decode($body);
                if ($json && isset($json->id)) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }
}

$facebookopengraphupdate = new FacebookOpenGraphUpdate();