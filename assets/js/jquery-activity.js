
if (typeof jQuery === 'undefined') {
    throw new Error('jquery-activity requires jQuery');
}
var jactivity,Jactivity;
(function ($,window){
	"use strict";
	//funcion publica $.activity();
	$.activity=function(options,option2){

		if (typeof options === 'undefined') options = {};
        if (typeof options === 'string') {
            options = {
                content: options,
                title: (option2) ? option2 : false,
                closeIcon: function () {
                    //close the activity
                }
            };
        }

        if (typeof options['closeIcon'] == 'undefined') {
            // Dialog must have a closeIcon.
            options['closeIcon'] = function () {
            }
        }
		return jactivity(options);
	};
	jactivity=function(options){
		if (typeof options === 'undefined') options = {};
		var pluginOptions=$.extend(true,{},jactivity.pluginDefaults);
		if(jactivity.defaults){
			pluginOptions=$.extend(true,pluginOptions,jactivity.defaults);
		}
		pluginOptions=$.extend(true,{},pluginOptions,options);
		var instance = new Jactivity(pluginOptions);
		jactivity.instances.push(instance);
		return instance;
	};
	Jactivity = function (options) {
        /*
         * constructor function Jactivity,
         * options = user options.
         */
        $.extend(this, options);
        this._init();
    };
    Jactivity.prototype={
    	_init:function(){
    		var that=this;
    		if (!jactivity.instances.length)
                jactivity.lastFocused = $('body').find(':focus');
    		this._id = Math.round(Math.random() * 99999);
            this.contentParsed = $(document.createElement('div'));
            if (!this.lazyOpen) {
                setTimeout(function () {
                    that.open();
                }, 0);
            }
    	},
    	open: function () {
            this._buildHTML();
            this._bindEvents();
            this._open();
            return true;
        },
        _bindEvents(){
            var that = this;
            var box=this.$jactivityBox;
            var header=this.$jactivityBar;
            var num=$(header).offset().top;
            $(box).bind('scroll', function() {
                if ($(box).scrollTop() > num) {
                    $(header).addClass('fixed');
                }
                else {
                    num = $(header).offset().top;
                    $(header).removeClass('fixed');
                }
            });
            this.boxfocused = false;
            this.$jactivityBox.focus(function(){
                that.boxfocused=true;
            });
        },
        _open: function () {
            var that = this;
            if (typeof that.onOpenBefore === 'function')
                that.onOpenBefore();

            //this.$body.removeClass(this.animationParsed);
            //this.$jconfirmBg.removeClass('jconfirm-bg-h');
            this.$body.focus();

            //that.$jconfirmBoxContainer.css('transform', 'translate(' + 0 + 'px, ' + 0 + 'px)');

            setTimeout(function () {
                //that.$body.css(that._getCSS(that.animationSpeed, 1));
                //that.$body.css({
                  //  'transition-property': that.$body.css('transition-property') + ', margin'
                //});
                //that.$jconfirmBoxContainer.addClass('jconfirm-no-transition');
                //that._modalReady.resolve();
                if (typeof that.onOpen === 'function')
                    that.onOpen();

                //that.$el.addClass(that.loadedClass);
            }, this.animationSpeed);
        },
        _unwatchContent: function () {
            clearInterval(this._timer);
        },
        close:function(){
            var that=this;
            this._unwatchContent();
            if (typeof this.onClose === 'function'){
                $(this.$el).animate({
                    'top' : '100%'
                });
                that.close;
            }
            setTimeout(function () {
                //that.$body.addClass(that.closeAnimationParsed);
                //that.$jconfirmBg.addClass('jconfirm-bg-h');
                var closeTimer = (that.closeAnimation === 'none') ? 1 : that.animationSpeed;

                setTimeout(function () {
                    
                    that.$el.remove();

                    var l = jactivity.instances;
                    var i = jactivity.instances.length - 1;
                    for (i; i >= 0; i--) {
                        if (jactivity.instances[i]._id === that._id) {
                            jactivity.instances.splice(i, 1);
                        }
                    }

                    // Focusing a element, scrolls automatically to that element.
                    // no instances should be open, lastFocused should be true, the lastFocused element must exists in DOM
                    if (!jactivity.instances.length) {
                        if (that.scrollToPreviousElement && jconfirm.lastFocused && jconfirm.lastFocused.length && $.contains(document, jconfirm.lastFocused[0])) {
                            var $lf = jconfirm.lastFocused;
                            if (that.scrollToPreviousElementAnimate) {
                                var st = $(window).scrollTop();
                                var ot = jactivity.lastFocused.offset().top;
                                var wh = $(window).height();
                                if (!(ot > st && ot < (st + wh))) {
                                    var scrollTo = (ot - Math.round((wh / 3)));
                                    $('html, body').animate({
                                        scrollTop: scrollTo
                                    }, that.animationSpeed, 'swing', function () {
                                        // gracefully scroll and then focus.
                                        $lf.focus();
                                    });
                                } else {
                                    // the element to be focused is already in view.
                                    $lf.focus();
                                }
                            } else {
                                $lf.focus();
                            }
                            jactivity.lastFocused = false;
                        }
                    }

                    if (typeof that.onDestroy === 'function')
                        that.onDestroy();

                }, closeTimer * 0.40);
            }, 100);
            if(jactivity.instances.length<=1){
                 $('body').css('overflow','auto');
            }
            return true;
        },
        _buildHTML: function () {
        	var that=this;
        	var template=$(this.template);
            $('body').css('overflow','hidden');
        	this.$el = template.appendTo(this.container);
        	this.$content = this.$el.find('.jactivity-content');
            this.$contentPane=this.$el.find('.jactivity-content-pane');
        	this.$jactivityBox = this.$body = this.$el.find('.jactivity-box');
            this.$title = this.$el.find('.jactivity-title');
            this.$closeIcon = this.$el.find('.jactivity-close-c').first();
            this.$jactivityBar = this.$el.find('.jactivity-bar-c');
            this.$jactivityBar.addClass('color-bg-header');
        	$(this.$el).animate({
                'left' : '0'
            });

            // for loading content via URL
            this._contentReady = $.Deferred();
            //this._modalReady = $.Deferred();
            this.setTitle();
            this._setButtons();
        	this._parseContent();
        	if (this.isAjax){
                this.showLoading(false);
        	}
        	$.when(this._contentReady).then(function (v1) {
                //console.log( v1 );
              
                if (that.isAjaxLoading)

                    setTimeout(function () {
                        that.isAjaxLoading = false;
                        that.setContent();
                        that.setTitle();
                        //that.setIcon();
                        setTimeout(function () {
                            that.hideLoading(false);
                            //that._updateContentMaxHeight();
                        }, 100);
                        if (typeof that.onContentReady === 'function'){
                            that.onContentReady();
                            that._unwatchContent();
                        }
                    }, 2000);
                else {
                     that.setContent();
                     //that._updateContentMaxHeight();
                     that.setTitle();
                     //that.setIcon();
                     if (typeof that.onContentReady === 'function')
                        that.onContentReady();
                }
            });
        },
        removeBorderToolBar:function(){
            this.$jactivityBar.css("border","none");
            this.$el.css("border","none");
        },
        setIconToolBar(icon){
            var tb=this.$jactivityBar.first().first().first().first().first();
            $(tb).html('<i class="'+icon+'"></i>');
        },
        setClassActivityPane: function(className){
            this.$jactivityBox.addClass(className);
        },
        setBackgroundActivityPane: function(background){
            this.$jactivityBox.css("background-color",background);
        },
        setBackgroundContentPane: function(background){
            this.$contentPane.css("background-color",background);
        },
        setBackgroundToolBar: function(background){
            this.$jactivityBar.css("background-color",background);
        },
        setClassToolBar: function(className){
            this.$jactivityBar.addClass(className);
        },
        setTitle: function (string, force) {
            force = force || false;
            
            if (typeof string !== 'undefined')
                if (typeof string == 'string')
                    this.title = string;
                else if (typeof string == 'function') {
                    if (typeof string.promise == 'function')
                        //console.error('Promise was returned from title function, this is not supported.');

                    var response = string();
                    if (typeof response == 'string')
                        this.title = response;
                    else
                        this.title = false;
                } else
                    this.title = false;

            if (this.isAjaxLoading && !force)
                return;
            this.$title.html(this.title || '');
            //this.updateTitleContainer();
        },
        loadingSpinner: false,
        showLoading: function (disableButtons) {
            this.loadingSpinner = true;
            this.$content.addClass('loading');
            this.$content.append('<div class="loading-text"><h1 class="text-center"><span class="text-white">Maa</span><span class="text-maabi-light">bi</span></h1><p class="text-center mt-n3 text-maabi-light"><small>Un momento por favor...</small></p></div>');
        },
        hideLoading: function (enableButtons) {
            this.loadingSpinner = false;
            //alert("ya cargo loading");
            this.$content.removeClass('loading');

            //if (enableButtons)
               // this.$btnc.find('button').prop('disabled', false);

        },
        closeIcon: null,
        _setButtons:function(){
            var that = this;
            if (this.closeIcon === null) {
                this.closeIcon = true;
            }

            if (this.closeIcon) {
                    this.$closeIcon.click(function (e) {
                    e.preventDefault();
                    //var buttonName = false;
                    var shouldClose = false;
                    var str;

                    if (typeof that.closeIcon == 'function') {
                        str = that.closeIcon();
                    } else {
                        str = that.closeIcon;
                    }

                    if (typeof str == 'string' && typeof that.buttons[str] != 'undefined') {
                        buttonName = str;
                        shouldClose = false;
                    } else if (typeof str == 'undefined' || !!(str) == true) {
                        shouldClose = true;
                    } else {
                        shouldClose = false;
                    }
                    /*if (buttonName) {
                        var btnResponse = that.buttons[buttonName].action.apply(that);
                        shouldClose = (typeof btnResponse == 'undefined') || !!(btnResponse);
                    }*/
                    if (shouldClose) {
                        that.close();
                    }
                    
                });
                //this.$closeIcon.show();
            } else {
                //this.$closeIcon.hide();
            }
        },
        ajaxResponse: false,
        contentParsed: '',
        isAjax: false,
        isAjaxLoading: false,
        _parseContent:function(){
        	var that = this;
            var e = '&nbsp;';
            if (typeof this.content == 'function') {
                var res = this.content.apply(this);
                
                if (typeof res == 'string') {
                    this.content = res;
                }
                else if (typeof res == 'object' && typeof res.always == 'function') {
                    // this is ajax loading via promise
                    
                    this.isAjax = true;
                    this.isAjaxLoading = true;
                    res.always(function (data, status, xhr) {
                        that.ajaxResponse = {
                            data: data,
                            status: status,
                            xhr: xhr
                        };
                       
                        that._contentReady.resolve(data, status, xhr);
                        if (typeof that.contentLoaded == 'function'){
                            that.contentLoaded(data, status, xhr);
                        }
                            
                    });
                    this.content = e;
                } else {
                    this.content = e;
                }
            }

            if (!this.content)
                this.content = e;
            if (!this.isAjax) {
                this.contentParsed.html(this.content);
                this.setContent();
                that._contentReady.resolve();
            }
            
        },
        setContentPrepend: function (content, force) {
            if (!content)
                return;

            this.contentParsed.prepend(content);
        },
        setContentAppend: function (content) {
            if (!content)
                return;

            this.contentParsed.append(content);
        },
        setContent: function (content, force) {

            force = !!force;
            var that = this;
            if (content)
                this.contentParsed.html('').append(content);
            //if (this.isAjaxLoading && !force)
            //    return;
            this.$content.html('');
            this.$content.append(this.contentParsed);
            setTimeout(function () {
                that.$body.find('input[autofocus]:visible:first').focus();
            }, 1000);
        }
    };
    jactivity.instances = [];
    jactivity.lastFocused = false;
    jactivity.pluginDefaults = {
        template: '' +
        '<div class="jactivity">' +
        ' <div class="jactivity-box">' +
        '   <div class="jactivity-bar-c">' +
        '    <div class="jactivity-close-c"><a class="btn btn-link"><i class="fas fa-arrow-left fa-lg"></i></a></div>' +
        '    <div class="jactivity-title"></div>' +
        '   </div>' +
        '   <div class="jactivity-content-pane">' +
        '    <div class="jactivity-content">' +
        '    </div>' +
        '    <div class="jactivity-clear">' +
        '   </div>' +
        ' </div>' +
        '</div>',
        container: 'body',
        title: '',
        logo:'',
        animationSpeed: 400,
        scrollToPreviousElement: true,
        scrollToPreviousElementAnimate: true,
        lazyOpen: false,
        contentLoaded: function () {
        },
        onContentReady: function () {

        },
        onOpenBefore: function () {

        },
        onOpen: function () {

        },
        onClose: function () {

        },
        onDestroy: function () {

        }
    };
})(jQuery, window);