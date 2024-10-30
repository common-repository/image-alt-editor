(function ($) {

    $(window).on('load', function () {

        var imageAltEditor = {

            init: function () {

                this.column = '.image-alt-editor-column';
                this.form = '.image-alt-editor-form';
                this.field = '.image-alt-editor-field';
                this.label = '.image-alt-editor-label';
                this.button = '.image-alt-editor-button';
                this.change = '.image-alt-editor-action-change';
                this.remove = '.image-alt-editor-action-remove';

                this.$button = $(this.button);
                this.$field = $(this.field);
                this.$change = $(this.change);
                this.$remove = $(this.remove);

                this.initActions();

            },

            initActions: function () {

                var self = this;

                this.$button.on('click', function () {
                    self.editAtt($(this));
                    return false;
                });

                this.$field.keyup(function (e) {
                    if (e.keyCode == 13) {
                        self.editAtt($(this));
                        return false;
                    }
                });

                // Disable form submit event when enter on field //
                this.$field.parents('form').submit(function (e) {
                    if ($(self.field + ':focus').length > 0) {
                        e.preventDefault();
                        return false;
                    }
                });

                this.$change.on('click', function () {
                    self.changeAtt($(this));
                    return false;
                });

                this.$remove.on('click', function () {
                    self.removeAtt($(this));
                    return false;
                });

            },

            editAtt: function ($button) {

                var $form = $button.parent(this.form);
                var $field = $form.find(this.field);

                if ($form.hasClass('is-loading')) {
                    return;
                }

                if ($field === undefined) {
                    alert('Error ...');
                    return;
                }

                $form.addClass('is-loading');

                var id = $field.data('id');
                var value = $field.val();

                $.ajax({
                    method: 'POST',
                    url: IAEadminAjax,
                    data: {
                        action: 'image_alt_editor_edit',
                        id: id,
                        value: value
                    },
                    success: function (response) {

                        setTimeout(function () {

                            $form.removeClass('is-loading');

                            if (response.type === 'success') {

                                $form.addClass('is-success');

                            } else if (response.type === 'error') {

                                $form.addClass('is-error');

                                if (response.message != '') {
                                    alert(response.message)
                                }
                            }

                            setTimeout(function () {
                                $form.removeClass('is-error').removeClass('is-success');
                            }, 3000);

                        }, 500);
                    }
                });
            },


            changeAtt: function ($button) {

                var $column = $button.parents(this.column);
                $column.find(this.label).addClass('is-hidden');
                $column.find(this.form).removeClass('is-hidden');

            },


            removeAtt: function ($button) {

                var $column = $button.parents(this.column);
                var $form = $column.find(this.form);
                var $label = $column.find(this.label);
                var id = $button.data('id');

                $column.addClass('is-loading');

                $.ajax({
                    method: 'POST',
                    url: IAEadminAjax,
                    data: {
                        action: 'image_alt_editor_remove',
                        id: id
                    },
                    success: function (response) {

                        setTimeout(function () {

                            $column.removeClass('is-loading');

                            if (response.type === 'success') {

                                $form.find('input[type="text"]').val('');
                                $form.removeClass('is-hidden');
                                $label.addClass('is-hidden');

                            } else if (response.type === 'error') {

                                if (response.message != '') {
                                    alert(response.message)
                                }
                            }

                        }, 500);
                    }
                });
            }

        };

        imageAltEditor.init();

    });

})(jQuery);