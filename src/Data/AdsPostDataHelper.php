<?php

namespace Supernova\AtRestShowAds\Data;

class AdsPostDataHelper {
    public function getImageSrc($id) : string {
        $img_data = get_field('banner_image', $id);
        $img_url  = is_array($img_data) ? $img_data['url'] : '';
        return $img_url;
    }
    public function getTargetLink($id) : string {
        $link = esc_url(get_field('banner_link', $id));
        return $link;
    }
    public function updateViewsPlusOne($id) {
        $views = (int) $this->getViews($id);
        $this->updateViews($id, $views + 1);
    }
    public function getViews($id) : int {
        $views = (int) get_field('views', $id);
        return $views;
    }
    public function updateViews($id, $views) {
        update_field('views', $views, $id);
    }
}