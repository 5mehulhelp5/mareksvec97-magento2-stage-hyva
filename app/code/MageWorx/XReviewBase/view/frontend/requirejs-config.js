/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

var config = {
    map: {
        '*': {
            reviewListToolbarForm: 'MageWorx_XReviewBase/js/review/list/toolbar',
            vote: 'MageWorx_XReviewBase/js/review/vote',
            xReviewGallery: 'MageWorx_XReviewBase/js/xreview-gallery',
            xReviewSlider: 'MageWorx_XReviewBase/js/xreview-slider'
        }
    },
    paths: {
        'splide': 'MageWorx_XReviewBase/js/lib/splide-2.4.21.min',
        'fslightbox': 'MageWorx_XReviewBase/js/lib/fslightbox-basic-3.2.3.min'
    },
    shim: {
        'splide': {
            exports: 'Splide'
        }
    }
};
