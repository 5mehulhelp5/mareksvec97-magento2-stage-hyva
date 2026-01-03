define([
    'jquery',
    'mage/cookies'
], function ($) {
    'use strict';

    $.widget('mageworx.vote', {
        options: {},

        _create: function (options) {

            this.reviewId = this.element.attr('data-index');
            this.url = this.options['ajaxUrl'];
            this.like = this.element.find('.mw-review__meta-rate-like');
            this.dislike = this.element.find('.mw-review__meta-rate-dislike');

            this.like.click($.proxy(function () {
               this.vote('like');
            }, this));

            this.dislike.click($.proxy(function () {
                this.vote('dislike');
            }, this));

            this.initVotes();
        },

        initVotes: function () {

            $.ajax({
                type: 'GET',
                url: this.url + '?uniq=' + Date.now(),
                data: 'id=' + this.reviewId + '&action=init&form_key=' + $.mage.cookies.get('form_key'),
                dataType: 'json',
                success: $.proxy(function (response) {
                    if (response && response.success) {
                        this.updateVote(response);
                    }
                }, this)
            });
        },

        updateVote: function (response) {
            this.like.find('.mw-rate__count').text(response.overall_data.like_count);
            this.dislike.find('.mw-rate__count').text(response.overall_data.dislike_count);

            var likeButton = this.like.find('.mw-rate__icon');
            var dislikeButton = this.dislike.find('.mw-rate__icon');

            if (response.personal_data.like_count > 0) {
                this.like.addClass('voted');
                likeButton.prop('disabled', true);
            } else {
                this.like.removeClass('voted');
                likeButton.prop('disabled', false);
            }

            if (response.personal_data.dislike_count > 0) {
                this.dislike.addClass('voted');
                dislikeButton.prop('disabled', true);
            } else {
                this.dislike.removeClass('voted');
                dislikeButton.prop('disabled', false);
            }
        },

        vote: function (action) {

            $.ajax({
                type: 'POST',
                url: this.url + '?uniq=' + Date.now(),
                data: 'id=' + this.reviewId + '&action=' + action + '&form_key=' + $.mage.cookies.get('form_key'),
                dataType: 'json',
                success: $.proxy(function (response) {
                    if (response.success) {
                        this.updateVote(response);
                    }
                }, this)
            });

            this.element.removeClass('disabled');
        }
    });

    return $.mageworx.vote;
});
