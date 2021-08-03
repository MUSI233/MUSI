
function msg_notify_show() {
    $.ajax({
        type: 'GET',
        url: '/user/ajax.php?act=msg_notify',
        dataType: 'json',
        success: function (data) {
            if (data.code === 1) return false;
            if (data.code === 0) {
                layer.open({
                    type: 1,
                    skin: 'layui-layer-lan',
                    anim: 2,
                    btn: ['我知道了', '查看更多通知'],
                    btnAlign: 'c',
                    shadeClose: true,
                    title: '系统通知',
                    yes: function (index, layero) {
                        msg_notify_confirm(data['mid']);
                        layer.close(index);
                    },
                    btn2: function (index, layero) {
                        location.href = '/?mod=adminMessage';
                        return false;
                    },
                    content: '<div class="msg-head"><h4><b>' + data.title + '</b></h4><small><span style="color: grey">管理员  ' + data.date + '</span></small></div><div class="msg-body">' + data.content + '</div>',
                });
            }
        }
    });
}

function msg_notify_confirm(mid) {
    $.ajax({
        type: 'POST'
        , url: '/user/ajax.php?act=msg_notify_confirm'
        , data: {'mid': mid}
        , dataType: 'json'
    });
}

$(document).ready(function () {
    const clipboard = new Clipboard('#copy-btn');
    clipboard.on('success', function (e) {
        layer.msg('复制成功！', {icon: 1});
    });
    clipboard.on('error', function (e) {
        layer.msg('复制失败，请长按链接后手动复制', {icon: 2});
    });
    setTimeout(function () {
        msg_notify_show()
    }, 500);

    $('.loginout').click(function () {
        layer.confirm('您确定要退出登录吗？', {icon: 3, title:'提示',closeBtn: false},function (index) {
            layer.close(index);
            location.href = '/?mod=login&logout';
        });
    });

    $('.click-href').click(function () {
        if (!_is_login) {
            window.location.href = '/?mod=login';
            return;
        }
        const href = $(this).data('href');
        if (href && href.length > 0) {
            window.location.href = href;
        }
    });

    $('.click-menu').click(function () {
        const this_dom = $(this);
        const is_open = parseInt(this_dom.prop('data-open_menu'));
        if (!is_open) {
            this_dom.prop('data-open_menu', 1);
            this_dom.children('.group_right').children('img').css({
                'transform': 'rotate(90deg)',
                '-webkit-transform': 'rotate(90deg)',
                '-ms-transform': 'rotate(90deg)',
                '-moz-transform': 'rotate(90deg)',
                '-o-transform': 'rotate(90deg)',
            });
            this_dom.next().show();
        } else {
            this_dom.prop('data-open_menu', 0);
            this_dom.children('.group_right').children('img').css({
                'transform': 'rotate(0deg)',
                '-webkit-transform': 'rotate(0deg)',
                '-ms-transform': 'rotate(0deg)',
                '-moz-transform': 'rotate(0deg)',
                '-o-transform': 'rotate(0deg)',
            });
            this_dom.next().hide();
        }
    });
});