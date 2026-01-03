define(["jquery", "splide"], function ($, Splide) {
        $(document).ready(function () {
            (function () {
                function createReviewsSlider() {
                    var sliders = document.querySelectorAll(".mw-splide");

                    if (sliders.length === 0) {
                        return;
                    }

                    var ROOT    = "mw-splide";
                    var classes = {
                        root: ROOT,
                        slider: ROOT + "__slider",
                        track: ROOT + "__track",
                        list: ROOT + "__list",
                        slide: ROOT + "__slide",
                        container: ROOT + "__slide__container",
                        arrows: ROOT + "__arrows",
                        arrow: ROOT + "__arrow",
                        prev: ROOT + "__arrow--prev",
                        next: ROOT + "__arrow--next",
                        pagination: ROOT + "__pagination",
                        page: ROOT + "__pagination__page",
                        clone: ROOT + "__slide--clone",
                        progress: ROOT + "__progress",
                        bar: ROOT + "__progress__bar",
                        autoplay: ROOT + "__autoplay",
                        play: ROOT + "__play",
                        pause: ROOT + "__pause",
                        spinner: ROOT + "__spinner",
                        sr: ROOT + "__sr"
                    };

                    sliders.forEach(function (slider) {
                        new Splide(slider, {
                            classes: classes
                        }).mount();
                    });
                }

                createReviewsSlider();
            })();
        })
    }
);
