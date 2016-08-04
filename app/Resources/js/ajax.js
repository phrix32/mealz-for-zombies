var Ajax = function () {
    $(document).ajaxError(this.errorHandler.bind(this));
};

Ajax.prototype = {

    constructor: Ajax,

    errorHandler: function(event, result) {
        if (!result.responseJSON) {
            return;
        }
        var json = result.responseJSON;
        if (typeof json.error !== 'string') {
            return;
        }
        this.url = null;
        this.logout = false;
        if (typeof json.url === 'string' && json.logout === true) {
            this.url = json.url;
            this.logout = json.logout;
        }
        this.createErrorDialog(json.error);
    },

    createErrorDialog: function(asset) {
        var $close = $('<i class="glyphicon glyphicon-remove-sign"></i>');
        this.$container = $('<div class="ajax-overlay"></div>');
        this.$dialog = $('<div class="ajax-dialog"></div>');

        $('body').append(this.$container);
        this.$dialog.append('<div>' + asset + '</div>');
        this.$dialog.append($close);
        this.$container.append(this.$dialog);
        this.$dialog.css('transform');
        this.openDialog();
        this.$dialog.addClass('show');

        this.$container.click(this.closeDialog.bind(this));
        $close.click(this.closeDialog.bind(this));
        this.$dialog.click(function(event) {
            event.stopPropagation();
        });
    },

    openDialog: function() {
        this.$dialog.addClass('show');
    },

    closeDialog: function() {
        if (this.logout === true) {
            window.location.replace(this.url);
            return;
        }

        var that = this;

        that.$dialog.on('transitionend webkitTransitionEnd oTransitionEnd', function () {
            that.$container.remove();
        });
        this.$dialog.removeClass('show');
    }

};

$(document).ready(function () {
    new Ajax();
});