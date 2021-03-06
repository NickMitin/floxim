(function($) {
window.$fx = {
    history:{},       
        
    KEYCODE_ESC: 27,
        
    init: function(options) {
        $fx.settings = options;    
        $fx.buttons_map = options.buttons.map;
        $fx.history = options.history;
        $fx.panel = $('.fx-admin-panel');
        
        $fx.main_menu = new fx_main_menu($fx.settings.mainmenu);
        
        $(function () {
            var ajax_counter = 0;
            $(document).ajaxSend(function() {
                ajax_counter++;
                if (ajax_counter > 0) {
                    $('.fx-progress-line').animate({
                        opacity:1
                    }, 100);
                }
            });
            
            $fx.admin = false;
            $fx.buttons = new fx_buttons($fx.settings.buttons.source);
            
            $fx.additional_menu = new fx_additional_menu();
            $fx.additional_menu.load();
            $(window).hashchange($fx.set_mode);
            
            $(window).hashchange();
            
            if ($fx.mode === 'page') {
                $fx.front = new fx_front();
                $fx.front.load();
            }
            
            $('html')
                .on('click.fx', '.fx_button', $fx.buttons.form_button_click)
                .on('click.fx', 'a[href]', function() {
                    if (this.getAttribute('href') === document.location.hash) {
                        $(window).trigger('hashchange');
                    }
                })
                .on('click.fx', '.fx_menu_item-has_dropdown', function(e) {
                    var $t = $(e.target),
                        $p = $t.parent();
                    if ( (!$t.is('.fx_menu_item-has_dropdown') && !$p.is('.fx_menu_item-has_dropdown')) || $t.closest('a').attr('href')) {
                        return;
                    }
                    var $dd = $('.fx_dropdown', $p);
                    if ($dd.is(':visible')) {
                        $dd.hide();
                    } else {
                        $dd.show();
                        var rect =  $dd[0].getBoundingClientRect(),
                            win_width = $(window).width();
                        if (rect.right > win_width) {
                            $dd.css('left', '-'+ (rect.right - win_width + 10)+'px');
                        }
                        $('html').one('click', function() {
                            $dd.hide();
                        });
                    }
                    return false;
                })
                .on('click.fx', '.fx_main_menu_expander', function() {
                    $('.fx_main_menu_items').toggle();
                })
                .on('click.fx', '.fx_menu_item[data-button]', function() {
                    var $item = $(this),
                        button_data = $item.data('button');
                        
                     $fx.post(
                        button_data,
                        function(json) {
                            $fx.front_panel.show_form(
                                json, 
                                {
                                    onfinish: function () {
                                        $fx.front.reload_layout();
                                    }
                                }
                            );
                        }
                    );
                });
            $(document).ajaxComplete(function(e, jqXHR) {
                ajax_counter--;
                if (ajax_counter === 0) {
                    $('.fx-progress-line').stop().animate({
                        opacity:0
                    }, 100);
                }
                if (!jqXHR || !jqXHR.getResponseHeader) {
                    return;
                }
                var js_assets = jqXHR.getResponseHeader('fx_assets_js');
                
                if (js_assets) {
                    if (!window.fx_assets_js) {
                        window.fx_assets_js = [];
                    }
                    js_assets = $.parseJSON(js_assets);
                    for (var i = 0; i < js_assets.length; i++) {
                        var asset = js_assets[i];
                        var is_loaded = $.inArray(asset, window.fx_assets_js);
                        //console.log('sys', asset, is_loaded, window.fx_assets_js);
                        if (is_loaded !== -1) {
                            continue;
                        }
                        (function(asset) {
                            $.ajax({
                                url:asset,
                                async:false,
                                dataType: 'script',
                                success: function() {
                                    window.fx_assets_js.push(asset);
                                }
                            });
                        })(asset);
                    }
                }
                
                var css_assets = jqXHR.getResponseHeader('fx_assets_css');
                if (css_assets) {
                    css_assets = $.parseJSON(css_assets);
                    for (var i = 0; i < css_assets.length; i++) {
                        var asset = css_assets[i];
                        $('head').append('<link type="text/css" rel="stylesheet" href="'+asset+'" />');
                    }
                }
            });
        });

        $(document).bind('keydown',$fx.key_down);
            
        $('html').click(function(){
            $fx.panel.trigger('fx.click', 'main');
        });
    },

    set_mode: function() {
        $fx.admin_buttons_action = {};
        $fx.parse_hash();
        $fx.panel.trigger('fx.startsetmode');
            
        // admin
        if ( $fx.mode === 'admin' ) {
            if ( !$fx.admin ) {
                $fx.admin = new fx_admin();
            }
            var len = $fx.hash.length;
            if ( len > 2 ) {
                $fx.admin.set_entity($fx.hash[len-2]);
                $fx.admin.set_action($fx.hash[len-1]);
            }
            $fx.admin.load();
        }
    },
        
    parse_hash: function() {
        var fx_path = $fx.settings.action_link;
        if (window.location.pathname.slice(0, fx_path.length) !== fx_path) {
            $fx.mode = 'page';
            return;
        }
        var hash_to_parse = $fx.settings.hash !== undefined ? $fx.settings.hash : window.location.hash.slice(1);
        
        if (hash_to_parse === '' && window.location.pathname === fx_path) {
            hash_to_parse = 'admin.administrate.site.all';
        }
        
        var s_pos = hash_to_parse.indexOf('(');
            
        $fx.hash_param = [];
            
        if ( s_pos > 1 ) {
            $fx.hash_param = hash_to_parse.substr(s_pos).slice(1,-1).split(',');
            hash_to_parse = hash_to_parse.substr(0,s_pos);
        }
        $fx.hash = hash_to_parse.split('.');
          
        if ( $fx.hash[0] === '' || ($fx.hash[0] !== 'page' && $fx.hash[0] !== 'admin' )  ) {
            $fx.hash[0] = 'page';
        }
               
        if ( $fx.hash[1] === undefined || $fx.hash[1] === '' ) {
            $fx.hash[1] = 'view';
        }
        $fx.mode = $fx.hash[0]; // page or admin
    },

    draw_additional_text: function (data) {
        var text = data.text;
        $("#fx_admin_additionl_text").html(text).show();
        var current = 0;
        $("#fx_admin_additionl_text a").each(function(){
            var link = data.link[current];
            $(this).click(function(){
                if ( typeof link === 'function' ) {
                    link();
                }
                else {
                    $fx.post(link);
                }
                return false;
            });
                
            current++;
        });
            
    },
    
    draw_additional_panel: function (data) {
    	$("#fx_admin_buttons").fx_create_form(data);
    },
        
    clear_additional_text: function ( ) {
        $("#fx_admin_additionl_text").html('');
    },
          
    key_down: function ( e ) {
        if ( e.keyCode === 46 ) {
            e.stopPropagation();
        }

        return true;
    },
    
    alert: function(data, status, expire) {
        var b = 'fx-admin-alert',
            $body = $('body'),
            $container = $body.data('fx_alert_container'),
            $alert = $(
                '<div class="'+b+'">'+
                    '<span class="'+b+'__close">&times;</span>'+
                    '<div class="'+b+'__data"></div>'+
                '</div>'
            ),
            $data = $alert.elem('data');
        
        if (!$container) {
            $container = $('<div class="fx-alert-container fx_overlay"></div>').appendTo($body);
            $body.data('fx_alert_container', $container);
        }
        $data.append(data);
        $alert.setMod('status', status);
        $container.append($alert);
        var destroy = function() {
            $alert.css({
                height:$alert.outerHeight(),
                opacity:1,
                overflow:'hidden'
            }).animate({
                height:0,
                opacity:0,
                'padding-top':0,
                'padding-bottom':0,
                'margin-bottom':0
            }, 500, function() {
                $alert.remove();
            });
        };
        $alert.elem('close').click(destroy);
        if (expire) {
            setTimeout(destroy, expire*1000);
        }
    },

    show_status_text: function ( text, status ) { 
        var $block = $("#fx_admin_status_block");
        if (!$block.length) {
            $block = $('<div id="fx_admin_status_block"></div>');
            $('body').append($block);
        }
        $block
            .attr('class', '')
            .html("<span>"+text+"</span>")
            .addClass(status)
            .fadeIn('slow');
    },
    
    show_error: function(json) {
        var errors = [];
        if (!json.errors) {
            errors.push("unknown error");
        } else {
            $.each(json.errors, function(i, e) {
                errors.push(e.text);
            });
        }
        $fx.show_status_text(errors.join("<br />"), 'error');
    },
    
    reload: function(new_location) {
        if (/^#/.test(new_location)) {
            if (new_location === document.location.hash) {
                $(window).trigger('hashchange');
            } else {
                document.location.hash = new_location.replace(/^#/, '');
            }
            
            return;
        }
        $('body').html('').css({
            background:'#EEE',
            color:'#000', 
            margin:'30px 0', 
            font:'bold 22px arial',
            textAlign:'center'
        }).html('<div class="fx_overlay fx_reloading">'+$fx.lang('reloading')+'...</div>');
        document.location.href = typeof new_location === 'string' ? new_location : document.location.href.replace(/#.*$/, '');  
    },
        
    post: function ( data, callback ) {
        data.fx_admin = true;
        if (!data._base_url) {
            data._base_url = document.location.href.replace(/#.*$/, '');
        }
        if (!callback) {
            callback = function() {};
        }
        $.ajax({
            url: $fx.settings.action_link,
            type: "POST",
            data: data,
            dataType: "JSON",
            //async: false,
            success: [
                function(json) {
                    if (json.reload) {
                        $fx.reload(json.reload);
                        return;
                    }
                    if (json.status === 'error') {
                        $fx.show_error(json);
                        return;
                    }
                },
                callback
            ],
            error: function(jqXHR, textStatus, errorThrown) {
                if ( textStatus === 'parsererror') {
                    $fx.show_status_text( $fx.lang('Server error:') + jqXHR.responseText, 'error');
                }
                return false;
            }  
        });
    },
    
    post_front: function (data, callback  ) {
        if ( data.posting === undefined ) {
            data.posting = 1;
        }
        if ( !data.action ) data.action = 'edit';
        
        $fx.show_status_text($fx.lang('Saving...'), 'wait');
            
        $fx.post(data, function(json){
            $fx.show_status_text(json.text, json.status);
            if ( callback ) {
                callback(json);
            }  
        });
    },
    
    regexp: function(regexp, data) {
        return regexp.exec(data);
    },
            
    stop_add_mode: function () {
        $fx.buttons.show_panel();
        $fx.clear_additional_text();
        $('.fx_preview_placeholder').remove();
    },
              
    lang: function(str) {
        var res = this.dictionary && this.dictionary[str] ? this.dictionary[str] : str;
        if (arguments.length > 1) {
            for (var i = 1; i < arguments.length; i++) {
                res = res.replace(/\%s/, arguments[i]);
            }
        }
        return res;
    },
    do_log : true,
    log : function () {
        if (this.do_log) {
            console.log.apply(console, arguments);
        }
    }
};
})($fxj);