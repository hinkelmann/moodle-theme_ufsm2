define(['jquery', 'core/config', 'theme_ufsm2/select2', 'theme_ufsm2/bootstrap'],
    function ($, config, s2) {
        $.getScript('//www.google-analytics.com/analytics.js')
            .done(function () {
                gaExec();
            });
        function mobile() {
            URL = config.wwwroot + "/message/mobile.php";
            if ($(window).width() < 986) {
                if (window.location.pathname.match('/message/index.php')
                ) {
                    window.location.pathname = window.location.pathname.replace('index', 'mobile');
                }
                if (/\/$/.test(window.location.pathname)) {
                    //window.location.pathname  = window.location.pathname.replace('message/','message/mobile.php');
                }
                if (getQueryParam('m') == 3) {
                    $('body').animate({scrollTop: $('body').height()}, 100);
                }
                $('.panel-collapse:not(:first)').collapse('hide');
                $(document).find("[data-block]").addClass('hidden');
                $('#menu-messages>a').removeAttr('data-toggle');
                $('#menu-messages>a').attr('href', URL + '?m=1');
                $('#menu-notification>a').removeAttr('data-toggle');
                $('#menu-notification>a').attr('href', URL + '?m=2');


            } else {
                if (window.location.pathname == "/message/mobile.php") {
                    window.location.pathname = "/message/index.php"
                }
                $('#menu-messages>a').attr('data-toggle', 'dropdown');
                $('#menu-messages>a').attr('href', '#');
                $('#menu-notification>a').attr('data-toggle', 'dropdown');
                $('#menu-notification>a').attr('href', '#');
            }
        }

        function getQueryParam(param) {
            var result = window.location.search.match(
                new RegExp("(\\?|&)" + param + "(\\[\\])?=([^&]*)")
            );
            return result ? result[3] : false;
        }

        return {
            init: function (parametros) {
                window.$ = $;
                urlAtual = window.location.href;
                var $menuMesage = $('#menu-messages');
                var $msgList = $('#msg-list');
                var $menuNotificacao = $('#menu-notification');
                var $notifList = $('#notif-list');
                var URL = config.wwwroot + '/theme/ufsm2/ajax/notification.ajax.php?sesskey=' + config.sesskey;


                $('.input-select2').select2({
                    width: '100%',
                    dropdownAutoWidth: true,
                    tags: "true",
                });
                $('.closeall').click(function () {
                    $('.panel-collapse.in:not(:first)')
                        .collapse('hide');
                });
                $('.openall').click(function () {
                    $('.panel-collapse:not(".in")')
                        .collapse('show');
                });
                $('.readAllNotif').click(function () {
                    $.post(URL, {op: 'notif-read'});

                    setTimeout(function () {
                        verifyUpdate();
                    }, 1000);
                });
                $('.readAllMsg').click(function () {
                    $.post(URL, {op: 'msg-read'});
                    setTimeout(function () {
                        verifyUpdate();
                    }, 1000);
                });

                /**
                 * Função inicial
                 */
                function startAction() {
                    $.post(URL, {url: urlAtual}).done(function (x) {
                        var OBJ = {msg: '', dtEnvio: 0};
                        if (x.msgLast.length) {
                            for (var prop in x.msgLast) {
                                $msgList.prepend(templateMsg(x.msgLast[prop]));
                            }
                        }
                        else {
                            OBJ.msg = "Parabéns! <br> Você leu todas as mensagens da sua caixa de entrada."
                            $msgList.prepend(templateMsg(OBJ));
                        }
                        if (x.notifLast.length) {
                            for (var prop in x.notifLast) {
                                $notifList.prepend(templateNotif(x.notifLast[prop]));
                            }
                        }
                        else {
                            OBJ.msg = "Parabéns! <br> Você leu todas as notificações da sua caixa de entrada."
                            $notifList.prepend(templateNotif(OBJ));
                        }
                    });
                }

                /**
                 * Função de TimeOut
                 */
                function timeout() {
                    setTimeout(function () {
                        verifyUpdate();
                        timeout();
                    }, 120000);
                }


                /**
                 * Função que verifica se existem atualizações e informa o usuário no menu superior
                 * Esta função é invocado pelo timeout
                 */
                function verifyUpdate() {
                    var OBJ = {msg: ''};
                    $.post(URL, {op: 'exists', url: urlAtual})
                        .done(function (x) {
                            if (x.msgExist) {
                                $('.msg-null').remove();
                                if ($('#menu-messages>a sup').length > 0)
                                    $('#menu-messages>a sup').html(x.msgExist);
                                else
                                    $('#menu-messages>a').append("<sup>" + x.msgExist + "</sup>");
                                getMsg(x.msgExist);
                                $('.icon-message').addClass('icon-help');
                            }
                            else {
                                $('.icon-message').removeClass('icon-help');
                                $msgList.children().remove();
                                OBJ.msg = "Parabéns! <br> Você leu todas as mensagens da sua caixa de entrada."
                                $msgList.prepend(templateMsg(OBJ));
                                $('#menu-messages>a sup').remove();


                            }
                            if (x.notifExist) {
                                $('.notif-null').remove();
                                if ($('#menu-notification>a sup').length > 0)
                                    $('#menu-notification>a sup').html(x.notifExist);
                                else
                                    $('#menu-notification>a').append("<sup>" + x.notifExist + "</sup>");
                                getNotif(x.notifExist);
                            }
                            else {
                                $notifList.children().remove();
                                OBJ.msg = "Parabéns! <br> Você leu todas as notificações da sua caixa de entrada."
                                $notifList.prepend(templateNotif(OBJ));
                                $('#menu-notification>a sup').remove();

                            }
                        });
                }

                /**
                 * Template da Mensagem
                 * @param obj
                 * @returns {string}
                 */
                function templateMsg(obj) {

                    var msg = '';
                    if (obj.dtEnvio > 0) {
                        obj.dtEnvio = new Date(obj.dtEnvio * 1000);
                        msg += '<a class="content ord msg-' + obj.id + ' ' + obj.classe + '" data-t="' + obj.dtEnvio.getTime() + '" href="' + obj.url + ' ">';
                        msg += '<div class="notification-img">';
                        msg += '<img class="userpicture" src="' + obj.foto + '">';
                        msg += '</div>';
                        msg += '<div class="notification-item">';
                        msg += '<h4 class="item-title">' + obj.nome + '</h4>';
                        msg += '<p class="item-info">' + obj.msg;
                        msg += '<br><small>'
                            + padding(obj.dtEnvio.getDate(), 2) + "/"
                            + padding(obj.dtEnvio.getMonth() + 1, 2) + "/"
                            + obj.dtEnvio.getFullYear() + " "
                            + padding(obj.dtEnvio.getHours(), 2) + ":"
                            + padding(obj.dtEnvio.getMinutes(), 2)
                        '</small>' + '</p>';
                        msg += '</div>';
                        msg += '</a>';
                    }
                    else {
                        msg += '<a class="content ord msg-null" href="#">';
                        msg += '<div class="notification-img"></div>';
                        msg += '<div class="notification-item">';
                        msg += '<h4 class="item-title"></h4>';
                        msg += '<p class="item-info">' + obj.msg;
                        msg += '</div>';
                        msg += '</a>';
                    }

                    return msg;
                }

                /**
                 * Template da Mensagem
                 * @param obj
                 * @returns {string}
                 */
                function templateNotif(obj) {

                    var msg = '';

                    if (obj.dtEnvio > 0) {
                        obj.dtEnvio = new Date(obj.dtEnvio * 1000);

                        msg += '<a class="content ord notif-' + obj.dtEnvio.getTime() + ' ' + obj.classe + '" data-t="' + obj.dtEnvio.getTime() + '" href="' + obj.url + ' ">';
                        msg += '<div class="notification-img">';
                        msg += '<img class="userpicture" src="' + obj.foto + '">';
                        msg += '</div>';
                        msg += '<div class="notification-item">';
                        msg += '<p class="item-info">' + obj.msg;
                        msg += '<br><small>';
                        msg += padding(obj.dtEnvio.getDate(), 2) + "/";
                        msg += padding(obj.dtEnvio.getMonth() + 1, 2) + "/";
                        msg += obj.dtEnvio.getFullYear() + " ";
                        msg += padding(obj.dtEnvio.getHours(), 2) + ":";
                        msg += padding(obj.dtEnvio.getMinutes(), 2) + '</small>' + '</p>';
                        msg += '</div>';
                        msg += '</a>';
                    }
                    else {
                        msg += '<a class="content ord notif-null" href="#">';
                        msg += '<div class="notification-img"></div>';
                        msg += '<div class="notification-item">';
                        msg += '<p class="item-info">' + obj.msg + '</p>';
                        msg += '</div>';
                        msg += '</a>';
                    }
                    return msg;
                }

                /**
                 * Formata com zeros a esquerda
                 * @param value
                 * @param length
                 * @returns {*}
                 */
                function padding(value, length) {
                    for (var i = 0; i < length - String(value).length; i++) {
                        value = '0' + value;
                    }
                    return value;
                }

                function getMsg(msg) {
                    $.post(URL, {op: "msg-new", msg: msg}).done(function (x) {
                        for (var prop in x) {
                            if ($('#msg-list > .msg-' + x[prop].id).length) {
                                var dt = new Date(x[prop].dtEnvio.date);
                                if ($('.msg-' + x[prop].id).data('t') != dt.getTime()) {
                                    $('#msg-list > .msg-' + x[prop].id).remove();
                                    $msgList.prepend(templateMsg(x[prop]));
                                }
                            }
                            else $msgList.prepend(templateMsg(x[prop]));
                            orderMsg($('#msg-list'));
                        }
                    });
                }

                function getNotif(msg) {
                    $.post(URL, {op: "notif-new", msg: msg, url: urlAtual}).done(function (x) {
                        for (var prop in x) {
                            var dt = new Date(x[prop].dtEnvio * 1000);
                            if (!$('.notif-' + dt.getTime()).length) {
                                $notifList.prepend(templateNotif(x[prop]));
                            }
                            orderMsg($('#notif-list'));
                        }
                    });
                }

                function orderMsg(lista) {
                    lista.find('.ord').sort(function (a, b) {
                        return +b.dataset.t - +a.dataset.t;
                    }).appendTo(lista);
                }

                startAction();
                timeout();
                mobile();
                setTimeout(function () {verifyUpdate();},1000);

                hmessage = $('.messagehistory>.messagehistory').height();
                function resizeGrid() {
                    hwindow = $(window).innerHeight();
                    hfooter = $('footer').outerHeight(true)
                    hheader = $(".navbar.navbar-inverse.navbar-fixed-top").outerHeight(true);
                    hinput = $('.messagesend').outerHeight(true);
                    finalHeight = hwindow - hfooter - hheader - hinput - 40;

                    $('.messagehistory>.messagehistory').height(finalHeight);
                    $('.messagehistory>.messagehistory').animate({scrollTop: hmessage}, 100);


                }

                $(window).resize(function () {
                    resizeGrid();
                });
                resizeGrid();
            }
        };
    });
