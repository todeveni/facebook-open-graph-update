<?php

/**
 * Plugin Name: Facebook Open Graph update
 * Plugin URI: https://wordpress.org/plugins/facebook-open-graph-update/
 * Description: Updates Facebook Open Graph when saving/updating a post
 * Version: 1.6.0
 * Author: Toni Viemerö
 * Author URI: https://selfdestruct.net/
 * License: GPLv3
 */

class FacebookOpenGraphUpdate {
    private $api_url = 'https://graph.facebook.com/';

    public function __construct() {
        add_action('transition_post_status', array($this, 'transition_post_status'), 10, 3);
        add_action('save_post', array($this, 'save_post'), 10, 3);
        add_action('publish_post', array($this, 'publish_post'), 10, 2);
        add_action('admin_footer', array($this, 'admin_footer'));
        add_filter('page_row_actions', array($this, 'edit_row_actions'), 10, 2);
        add_filter('post_row_actions', array($this, 'edit_row_actions'), 10, 2);
        add_action('wp_ajax_facebook_open_graph_update', array($this, 'ajax'));
    }

    public function transition_post_status($new_status, $old_status, $post) {
        if ($new_status === 'publish' && $post->post_type !== 'nav_menu_item') {
            $this->scrape($post->ID);
        }
    }

    public function save_post($post_id, $post, $update) {
        if ($post->post_status === 'publish' && $post->post_type !== 'nav_menu_item') {
            $this->scrape($post->ID);
        }
    }

    public function publish_post($post_id, $post) {
        if ($post->post_type !== 'nav_menu_item') {
            $this->scrape($post->ID);
        }
    }

    public function admin_footer() {
        $screen = get_current_screen();

        if (in_array($screen->id, array('edit-page', 'edit-post'))) {
            ?>
            <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('span.facebook_open_graph_update a').on('click', function(e) {
                    e.preventDefault();

                    var data = {
                        'action'  : 'facebook_open_graph_update',
                        'post_id' : $(this).data('post-id'),
                        'nonce'   : '<?php print wp_create_nonce('facebook-open-graph-update') ?>'
                    };

                    $.post(ajaxurl, data, function(response) {
                    });
                });
            });
            </script>
            <?php
        }
    }

    public function edit_row_actions($actions, $post) {
        if (current_user_can('edit_post', $post->ID)) {
            $actions = array_merge($actions, array(
                'facebook_open_graph_update' => sprintf('<a href="#" data-post-id="%d">'. __('Facebook Open Graph update', 'facebook-open-graph-update') . '</a>', $post->ID)
            ));
        }

        return $actions;
    }

    public function ajax() {
        $status = false;

        if (check_ajax_referer('facebook-open-graph-update', 'nonce')) {
            if (isset($_POST['post_id']) && intval($_POST['post_id']) > 0) {
                if ( $this->scrape(intval($_POST['post_id'])) ) {
                    $status = true;
                }
            }
        }

        print $status;
        wp_die();
    }

    public function scrape($post_id = null) {
        if (intval(get_option('blog_public')) === 0) {
            return;
        }

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

function facebookopengraphupdate_load() {
    $facebookopengraphupdate = new FacebookOpenGraphUpdate();
}
add_action('plugins_loaded', 'facebookopengraphupdate_load');
