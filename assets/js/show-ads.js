document.addEventListener('DOMContentLoaded', function () {
    const ads = document.querySelectorAll('.at-rest-show-ads');
    ads.forEach((ad) => {
        const type = ad.dataset.type;
        const duration = parseInt(ad.dataset.duration) || 30;
        const perPage = parseInt(ad.dataset.perPage) || 1;
        const authorId = parseInt(ad.dataset.authorId) || -1;
        atRestStartAds(type, duration, perPage, authorId, ad);
    });
});

function atRestStartAds(type, duration, perPage, authorId, ad) {
    const excludedPosts = atRestGetExcludedPosts(type);

    atRestFetchAdsContent(type, perPage, excludedPosts, authorId).then(
        (adsContent) => {
            if (adsContent.ids && adsContent.ids.length > 0) {
                const currentIds = JSON.stringify(adsContent.ids.sort());
                const lastIds = ad.dataset.lastIds || '';

                if (lastIds === currentIds) {
                    console.log('Same ads repeating, stopping rotation');
                    return;
                }

                ad.dataset.lastIds = currentIds;
                atRestSetExcludedPosts(type, adsContent.ids);
            }

            if (adsContent.is_reset) {
                atRestResetExcludedPosts(type);
                delete ad.dataset.lastIds;
            }

            if (adsContent.html) {
                ad.innerHTML = adsContent.html;
            }

            if (!adsContent.is_stop) {
                setTimeout(
                    () => atRestStartAds(type, duration, perPage, authorId, ad),
                    duration * 1000
                );
            }
        }
    );
}

function atRestFetchAdsContent(type, perPage, excludedPosts, authorId) {
    const params = new URLSearchParams({
        action: 'at_rest_get_ads_content',
        type: type,
        per_page: perPage,
        excluded_posts: JSON.stringify(excludedPosts),
        author_id: authorId,
    });

    return fetch(`${atRestShowAdsConfig.ajaxUrl}?${params.toString()}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    })
        .then((response) => response.json())
        .then((data) => {
            console.log('data', data);
            if (!data) {
                return {
                    is_reset: false,
                    is_stop: true,
                    html: '',
                    ids: [],
                };
            }
            return data;
        })
        .catch((error) => {
            console.error('Error fetching ads:', error);
            return {
                is_reset: false,
                is_stop: true,
                html: '',
                ids: [],
            };
        });
}

function atRestGetExcludedPosts(type) {
    const stored = localStorage.getItem(
        `at-rest-show-ads-excluded-posts-${type}`
    );
    if (!stored) return [];
    try {
        return JSON.parse(stored);
    } catch (e) {
        return [];
    }
}

function atRestSetExcludedPosts(type, posts) {
    const excludedPosts = atRestGetExcludedPosts(type);
    excludedPosts.push(...posts);
    localStorage.setItem(
        `at-rest-show-ads-excluded-posts-${type}`,
        JSON.stringify(excludedPosts)
    );
}

function atRestResetExcludedPosts(type) {
    localStorage.setItem(
        `at-rest-show-ads-excluded-posts-${type}`,
        JSON.stringify([])
    );
}
