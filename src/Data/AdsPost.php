<?php

namespace Supernova\AtRest\Data;

class AdsPost {

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
        if ($author_id > 0) {
            $args['author'] = $author_id;
        }
        return $args;
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