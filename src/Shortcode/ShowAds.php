<?php

namespace Supernova\AtRest\Shortcode;


class ShowAds {
    public function __construct() {
        $this->init();
    }

    private function init() {
        add_shortcode('at_rest_show_ads', [$this, 'showAds']);
    }

    public function showAds($atts) {
        $atts = shortcode_atts([
            'type' => 'header',
            'max_repeat_count' => 50,
            'duration' => 30,
            'per_page' => 1,
        ], $atts, 'at_rest_show_ads');

        $template = $this->getAdsHtml($atts);
        
        if (!empty($template)) {
            $this->enqueueScripts();
        }
        
        return  $template;
    }



    private function getAdsHtml($atts): string {

        $author_id = -1;

        if ($atts['type'] === 'death-notices') {
            $author_id = $this->getDeathNoticesAuthorId();
            if (!$author_id) {
                return '';
            }
        }


        ob_start();
        include AT_REST_SHOW_ADS_DIR . 'views/ads-show-shortcode.php';
        return ob_get_clean();
    }
    private function getDeathNoticesAuthorId() : ?int {

        global $post;
    

        if (!$post) {
            return null;
        }
        
        if (get_post_type($post->ID) !== 'death-notices') {
            return null;
        }

        $author_id = $post->post_author;
    
        $user = get_userdata($author_id);
        if (!$user || !in_array('funeral_director', (array) $user->roles)) {
            return null;
        }
        return $author_id;
    }
    private function enqueueScripts() {
        wp_enqueue_style( 'at-rest-show-ads', AT_REST_SHOW_ADS_URL . 'assets/css/show-ads.css', [], '1.0.0' );
        wp_enqueue_script( 'at-rest-show-ads', AT_REST_SHOW_ADS_URL . 'assets/js/show-ads.js', [], '1.0.0', true );
        wp_localize_script( 'at-rest-show-ads', 'atRestShowAdsConfig', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        ]);
    }
}