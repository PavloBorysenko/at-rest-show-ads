<?php

namespace Supernova\AtRest\Ajax;

use Supernova\AtRest\Data\AdsPost;
class AdsContent {

    private AdsPost $adsPost;
    public function __construct( AdsPost $adsPost) {
        $this->adsPost = $adsPost;
        add_action('init', [$this, 'init']);
    }

    public function init() {
        add_action('wp_ajax_at_rest_get_ads_content', [$this, 'getAdsContent']);
        add_action('wp_ajax_nopriv_at_rest_get_ads_content', [$this, 'getAdsContent']);
    }

    public function getAdsContent() {
        $author_id = intval($_GET['author_id']);
        $type = sanitize_key($_GET['type']);
        $perPage = intval($_GET['per_page']);
        if ($perPage < 1) {
            $perPage = 1;
        }
        $excludedPosts = json_decode(sanitize_text_field($_GET['excluded_posts']), false);
        
        $data = $this->getAdsContentData($author_id, $type, $perPage, $excludedPosts);
        $data = $this->prepareData($data);
        wp_send_json($data);
    }
    private function getAdsContentData($author_id, $type, $perPage, $excludedPosts) {
        $ids = $this->adsPost->getAdsPostIds($author_id, $type, $perPage, $excludedPosts);

        $data = [];
        $data['is_reset'] = false;
        $data['is_stop'] = false;

        if (count($ids) < $perPage) {
            if(empty($excludedPosts)) {
                $data = ['is_stop' => true];
                return $data;
            } else {
                $excludedPosts = $ids;
                $perPage = $perPage - count($ids);
                $ids_more = $this->adsPost->getAdsPostIds($author_id, $type, $perPage, $excludedPosts);
                $ids = array_merge($ids, $ids_more);
                $data['is_reset'] = true;
            }
        }
        $data['ids'] = $ids;
        $data['html'] = $this->getAdsHtml($ids, $type);
        return $data;
    }

    private function prepareData($data) {
        return [
            'ids' => isset($data['ids']) ? $data['ids'] : [],
            'is_reset' => isset($data['is_reset']) ? $data['is_reset'] : false,
            'is_stop' => isset($data['is_stop']) ? $data['is_stop'] : true,
            'html' => isset($data['html']) ? $data['html'] : '',
        ];
    }
    private function getAdsHtml($ids, $type) {
        $html = '';
        foreach ($ids as $id) {
            $html .= $this->getItemHtml($id, $type);
        }
        return $html;
    }
    private function getItemHtml($id, $type) {
        switch ($type) {
            case 'death-notices':
                return $this->getDeathNoticesItemHtml($id);
            case 'sidebar':
                return $this->getSidebarItemHtml($id);
            case 'header':
                return $this->getHeaderItemHtml($id);
            default:
                return '';
        }

    }
    private function getDeathNoticesItemHtml($id) {
        $title = get_the_title($id);
        ob_start();
        include AT_REST_SHOW_ADS_DIR . 'views/templates/death-notices-ads-item.php';
        return ob_get_clean();
    }
    private function getSidebarItemHtml($id) {
        $title = get_the_title($id);
        ob_start();
        include AT_REST_SHOW_ADS_DIR . 'views/templates/sidebar-ads-item.php';
        return ob_get_clean();
    }
    private function getHeaderItemHtml($id) {
        $title = get_the_title($id);
        ob_start();
        include AT_REST_SHOW_ADS_DIR . 'views/templates/header-ads-item.php';
        return ob_get_clean();
    }
}    