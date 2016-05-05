/*使用js跳转*/
$('[data-href]').on('click', function(event) {
    event.preventDefault();
    if ($(this).hasClass('ajax-get') || $(this).hasClass('ajax-post')) {
        return;
    }
    location.href = $(this).data('href');
});

$(".tomin").click(function() {
    $("#context-menu").toggleClass("min");
});

$('.item').tooltip({
    placement: 'right',
    container: 'body'
});

+ function($){
    'use strict';
    var Button = function(element, options) {
        this.$element = $(element);
        this.options = $.extend({}, Button.DEFAULTS, options);
        this.isLoading = false;
    }
    Button.DEFAULTS = {
        loadingText: 'loading...'
    }
    Button.prototype.setState = function(state) {
        var d    = 'disabled';
        var $el  = this.$element;
        var val  = $el.is('input') ? 'val' : 'html';
        var data = $el.data();
        state    = state + 'Text';
        if (!data.resetText) {
            $el.data('resetText', $el[val]());
        }
        $el[val](data[state] || this.options[state]);
        setTimeout($.proxy(function() {
            if (state == 'loadingText') {
                this.isLoading = true;
                $el.addClass(d).attr(d, d)
            } else if (this.isLoading) {
                this.isLoading = false;
                $el.removeClass(d).removeAttr(d);
            }
        }, this), 0);
    }
    Button.prototype.toggle = function() {
        var changed = true
        var $parent = this.$element.closest('[data-toggle="buttons"]')
        if ($parent.length) {
            var $input = this.$element.find('input')
            if ($input.prop('type') == 'radio') {
                if ($input.prop('checked') && this.$element.hasClass('active')) {
                    changed = false;
                } else {
                    $parent.find('.active').removeClass('active');
                }
            }
            if (changed) {
                $input.prop('checked', !this.$element.hasClass('active')).trigger('change');
            }
        }
        if (changed) {
            this.$element.toggleClass('active');
        }
    }
    var old = $.fn.button
    $.fn.button = function(option) {
        return this.each(function() {
            var $this   = $(this);
            var data    = $this.data('zui.button');
            var options = typeof option == 'object' && option;
            if (!data) {
                $this.data('zui.button', (data = new Button(this, options)));
            }
            if (option == 'toggle') {
                data.toggle();
            } else if (typeof option == 'object') {
                data.setState(option.state);
            } else if (option) {
                data.setState(option);
            }
        });
    }
    $.fn.button.Constructor = Button
    $.fn.button.noConflict  = function() {
        $.fn.button = old;
        return this;
    }
    $(document).on('click.zui.button.data-api', '[data-toggle^=button]', function(e) {
        var $btn = $(e.target);
        if (!$btn.hasClass('btn')) {
            $btn = $btn.closest('.btn');
        }
        $btn.button('toggle');
        e.preventDefault();
    })
    $(document).on('click.zui.button', '[data-loading]', function(event) {
        var btn = $(this).button({
            state       : 'loading',
            loadingText : $(this).attr('data-loading')
        });
        var time = $(this).attr('data-loading-time') || 1400;
        if(time != 0) {
            setTimeout(function() {
                btn.button('reset');
            }, time);
        }
    });

    //ajax get请求
    $(document).on('click', '.ajax-get', function(event) {
        event.preventDefault();
        var target;
        var $this = $(this);
        var $tips = $this.attr('tip') || '确认要执行该操作吗?';
        if ($this.hasClass('confirm')) {
            if(!confirm($tips)){
                return false;
            }
        }
        if ((target = $this.attr('href')) || (target = $this.attr('url'))) {
            $.get(target, function(data) {
                if (data.code == 1) {
                    updateAlert(data.msg, 'success');
                    if(data.data == 'delete'){
                        $this.parents('tr').slideUp(500, function() {
                            $this.parents('tr').remove();
                        });
                        return false;
                    }
                    setTimeout(function(){
                        if ($this.hasClass('no-refresh')) {
                            return false;
                        } else if (data.url) {
                            location.href = data.url;
                        } else {
                            location.reload();
                        }
                    }, 1500);
                } else {
                    updateAlert(data.msg, 'warning');
                    setTimeout(function(){
                        if ($this.hasClass('no-refresh')) {
                            return false;
                        } else if (data.url == '') {
                            return false;
                        } else if (data.url) {
                            location.href = data.url;
                        } else {
                            location.reload();
                        }
                    }, 1500);
                }
            }, 'json')
        }
        return false;
    });

    // 表单焦点在非textarea处，回车触发表单自动提交
    // AJAX-POST方式，强制不跳转页面
    $(document).on('keypress', 'form', function(e) {
        if (typeof $(':focus').attr('type') != 'undefined') {
            if (e.keyCode == 13) {
                e.preventDefault();
                $(this).find('.ajax-post').click();
            }
        }
    });

    // ajax-post
    $(document).on('click', '.ajax-post', function(event) {
        event.preventDefault();
        var $this   = $(this);
        var $form   = $this.parents('form');
        // 如果没有上层表单，就不提交
        if (typeof $form.html() === 'undefined') {
            return false;
        }
        var $action = $this.attr("action") || $form.attr("action");
        if ($this.hasClass('confirm')) {
            if(!confirm('确认要执行该操作吗?')){
                return false;
            }
        }
        var query = $form.serialize();
        $.post($action, query, function(data) {
            if (data.code == 1) {
                updateAlert(data.msg, 'success');
                setTimeout(function(){
                    if ($this.hasClass('no-refresh')) {
                        return false;
                    } else if (data.url) {
                        location.href = data.url;
                    } else {
                        location.reload();
                    }
                }, 1500);
            } else {
                updateAlert(data.msg, 'warning');
                setTimeout(function(){
                    if ($this.hasClass('no-refresh')) {
                        return false;
                    } else if (data.url == '') {
                        return false;
                    } else if (data.url) {
                        location.href = data.url;
                    } else {
                        location.reload();
                    }
                }, 1500);
            }
        }, 'json');
    });
}(jQuery);

function updateAlert(text, cls) {
    cls = cls || 'special';
    var icn = '';
    if (cls == 'success') {
        icn = 'ok-sign';
    } else if (cls == 'error') {
        icn = 'remove-sign';
    } else {
        icn = 'info-sign';
    }
    var msg = new $.zui.Messager(text, {
        type : cls,
        placement: 'top-right',
        time : 5000,
        icon : icn
    });
    msg.show();
};
