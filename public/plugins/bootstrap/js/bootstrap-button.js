// Loading button plugin (removed from BS4)
(function ($) {
    $.fn.button = function (action) {
        if (action === 'loading' && this.data('loading-text')) {
            if (this.data('loading-text').indexOf('spinner-border') !== -1) {
                this.data('original-text', this.html()).html(`${this.data('loading-text')}`).prop('disabled', true);
            } else {
                this.data('original-text', this.html()).html(`<div class="spinner-border-input text-light"></div>&nbsp;${this.data('loading-text')}`).prop('disabled', true);
            }
             
        }
        if (action === 'reset' && this.data('original-text')) {
            this.html(this.data('original-text')).prop('disabled', false);
        }
    };
}(jQuery));