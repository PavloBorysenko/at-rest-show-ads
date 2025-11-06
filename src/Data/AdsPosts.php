<?php

namespace Supernova\AtRest\Data;

class AdsPosts {

    private string $postType;

    public function __construct($postType) {
        $this->postType = $postType;
    }

    public function getAdsPostIds($author_id, $type, $perPage, $excludedPosts) : array {
        $args = $this->getBaseQueryArgs();
        $args = $this->addAuthorId($args, $author_id);
        $args = $this->addType($args, $type);
        $args = $this->addPerPage($args, $perPage);
        $args = $this->addExcludedPosts($args, $excludedPosts);
        $posts = get_posts($args);
        error_log('Posts: ' . count($posts));
        error_log('Args: ' . print_r($args, true));
        error_log('***************************************');

        return $posts;
    }
    private function getBaseQueryArgs() : array {
        return [
            'post_type'      => $this->postType,
            'post_status'    => 'publish',
            'orderby'        => 'rand',
            'cache_results'  => false,
            'fields'         => 'ids',
            'ads_query'      => uniqid(),
            'suppress_filters' => true,
            'meta_query'     => [
                'relation' => 'AND',
                [
                    'key'     => 'is_active',
                    'value'   => '1',
                    'compare' => '='
                ]
            ]
        ];
    }
    private function addAuthorId($args, $author_id) : array {
       
        // This isn't death notices page.
        if ($author_id < 0) {
            return $args;
        }

        $advertiser_id = $this->getAdvertiserId($author_id);

        $args['meta_query'][] = [
            'key' => 'advertiser',
            'value' => $advertiser_id,
            'compare' => '='
        ];

        return $args;
    }
    private function getAdvertiserId($author_id) : int {

        $email = get_the_author_meta('user_email', $author_id);
        
        if (empty($email)) {
            // There is no advertiser for this user.
            return -1;
        }

        $args = [
            'post_type' => 'advertiser',
            'fields' => 'ids',
            'meta_query' => [
                [
                    'key' => 'email',
                    'value' => $email,
                    'compare' => '='
                ]
            ]
        ];
        $advertiser = get_posts($args);

        return (int) $advertiser[0] ?? -1;
    }

    private function addExcludedPosts($args, $excludedPosts) : array {
        if (!empty($excludedPosts)) {
            $args['post__not_in'] = $excludedPosts;
        }
        return $args;
    }
    private function addPerPage($args, $perPage) : array {
        if ($perPage < 1) {
            $perPage = 1;
        }
        $args['posts_per_page'] = $perPage;
        return $args;
    }
    private function addType($args, $type) : array {
        if (!empty($type)) {
            $args['meta_query'][] = [
                'key'     => 'ad_type',
                'value'   => $type,
                'compare' => '='
            ];
        }
        return $args;
    }
}