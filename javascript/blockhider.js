YUI.add('moodle-theme_ufsm2-blockhider', function(Y) {
    /**
     * Class blockhider for Clean UdeM theme.
     * Init this class by calling M.theme_ufsm2.init_block_hider
     */
    var blockhider = function() {
        blockhider.superclass.constructor.apply(this, arguments);
    };
    blockhider.prototype = {
        initializer : function(config) {
            this.set('block', '#' + this.get('id'));
            var b = this.get('block'),
                c = b.one('.content'),
                t = b.one('.title'),
                a = null,
                hide,
                show;
            if (t && (a = t.one('.block_action')) && !a.one('img') && !t.hasClass('ui-mobile-title')) {
                hide = Y.Node.create('<i>')
                    .addClass('block-hider-hide fa fa-chevron-up icone-titulo')
                    .setAttrs({
                        alt:        config.tooltipVisible,
                       // src:        this.get('iconVisible'),
                        tabindex:   0,
                        'title':    config.tooltipVisible
                    });
                hide.on('keypress', this.updateStateKey, this, true);
                hide.on('click', this.updateState, this, true);

                show = Y.Node.create('<i>')
                    .addClass('block-hider-show fa fa-chevron-down icone-titulo')
                    .setAttrs({
                        alt:        config.tooltipHidden,
                        //src:        this.get('iconHidden'),
                        tabindex:   0,
                        'title':    config.tooltipHidden
                    });
                show.on('keypress', this.updateStateKey, this, false);
                show.on('click', this.updateState, this, false);

                t.on('dblclick', this.updateState, this);
                a.insert(show, 0).insert(hide, 0);
            }
            c.setStyle('overflow','hidden');

            var anim = new Y.Anim({
                node: b.one('.content'),
                from: {
                    opacity: 0,
                    height: 0
                },
                to: {
                    opacity: 1,
                    height: function(node) {
                        var p1 = parseFloat(node.getComputedStyle('paddingTop').replace(/[A-Za-z$-]/g, ""));
                        var p2 = parseFloat(node.getComputedStyle('paddingBottom').replace(/[A-Za-z$-]/g, ""));
                        return (node.get('scrollHeight') - p1 - p2);
                    }
                },
                easing: Y.Easing.easeOut,
                duration: 0.2
            });
            anim.on('end',function(e ,a ,b, c) {
                c.setStyle('display','');
                if (b.hasClass('hidden') && !b.get('parentNode').get('parentNode').hasClass('has_dock')) {
                    c.setStyle('height','0');
                    c.setStyle('opacity','0');
                }else{
                    c.setStyle('height','');
                    c.setStyle('opacity','');
                }
            },anim,a,b,c);
            this.set('anim', anim);
        },
        updateState : function(e, hide) {
            e.preventDefault();
            this.clearSelection();
            var a = this.get('anim'),
                b = this.get('block'),
                c = b.one('.content'),
                t = b.one('.title');
            if(hide == null) {
                hide = !b.hasClass('hidden');
                t.blur();
            }
            M.util.set_user_preference(this.get('preference'), hide);
            c.setStyle('display','block');
            b.toggleClass('hidden');
            a.set('reverse', hide);
            a.run();
            e.stopPropagation();
        },
        clearSelection : function() {
            if(document.selection && document.selection.empty) {
                document.selection.empty();
            } else if(window.getSelection) {
                var sel = window.getSelection();
                sel.removeAllRanges();
            }
        },
        updateStateKey : function(e, hide) {
            if (e.keyCode == 13) { //allow hide/show via enter key
                this.updateState(this, hide);
            }
        }
    };
    Y.extend(blockhider, Y.Base, blockhider.prototype, {
        NAME : 'Clean UdeM blockhider',
        ATTRS : {
            id : {},
            preference : {},
            iconVisible : {
                value : "<i class='fa fa-plus'></i>"//M.util.image_url('t/switch_minus_white', 'moodle')
            },
            iconHidden : {
                value : "<i class='fa fa-plus'></i>"//M.util.image_url('t/switch_plus_white', 'moodle')
            },
            block : {
                setter : function(node) {
                    return Y.one(node);
                }
            }
        }
    });
    M.theme_ufsm2 = M.theme_ufsm2 || {};
    M.theme_ufsm2.init_block_hider = function(config) {
        //return new blockhider(config);
        M.theme_ufsm2.blockhider = new blockhider(config);
        return M.theme_ufsm2.blockhider;
    };


}, '@VERSION@', {requires:['base','node','anim']});

