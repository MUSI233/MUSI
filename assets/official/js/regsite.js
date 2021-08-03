function dopay(type, orderid) {
    if (type === 'rmb') {
        var ii = layer.msg('正在提交订单请稍候...', {icon: 16, shade: 0.5, time: 15000});
        $.ajax({
            type: "POST",
            url: "/ajax.php?act=payrmb",
            data: {orderid: orderid},
            dataType: 'json',
            success: function (data) {
                layer.close(ii);
                if (data.code === 1) {
                    layer.alert(data['msg'], function () {
                        window.location.href = '/?buyok=1';
                    });
                } else if (data.code === -2) {
                    alert(data.msg);
                    window.location.href = '?buyok=1';
                } else if (data.code === -3) {
                    layer.confirm('你的余额不足，请充值！', {
                        btn: ['立即充值', '取消']
                    }, function () {
                        window.location.href = '/?mod=recharge';
                    }, function (layera) {
                        layer.close(layera);
                    });
                } else if (data.code === -4) {
                    layer.confirm('你还未登录，是否现在登录？', {
                        btn: ['登录', '注册', '取消']
                    }, function () {
                        window.location.href = '/?mod=login';
                    }, function () {
                        window.location.href = '/?mod=reg';
                    }, function (layera) {
                        layer.close(layera);
                    });
                } else {
                    layer.alert(data['msg']);
                }
            }
        });
    } else {
        window.location.href = '/other/submit.php?type=' + type + '&orderid=' + orderid;
    }
}

function cancel(orderid) {
    layer.closeAll();
    $.ajax({
        type: "POST",
        url: "/ajax.php?act=cancel",
        data: {orderid: orderid, hashsalt: hashsalt},
        dataType: 'json',
        success: function (data) {
            if (data['code'] !== 0) {
                layer.msg(data['msg']);
                window.location.reload();
            }
        },
        error: function () {
            window.location.reload();
        }
    });
}

const handlerEmbed = function (captchaObj) {
    captchaObj.appendTo('#captcha');
    captchaObj.onReady(function () {
        $("#captcha_wait").hide();
    }).onSuccess(function () {
        let result = captchaObj.getValidate();
        if (!result) {
            alert('请完成验证');
            return;
        }
        const kind = $("select[name='kind']").val();
        const qz = $("input[name='qz']").val();
        const domain = $("select[name='domain']").val();
        const input_name_dom = $("input[name='user']");
        const name = input_name_dom.val();
        let qq = '';
        let user = '';
        let pwd = '';
        if (input_name_dom.length > 0) {
            qq = $("input[name='qq']").val();
            user = input_name_dom.val();
            pwd = $("input[name='pwd']").val();
        }
        const ii = layer.load(2, {shade: [0.1, '#fff']});
        $.ajax({
            type: "POST",
            url: "/user/ajax.php?act=paysite",
            data: {
                kind: kind,
                qz: qz,
                domain: domain,
                name: name,
                qq: qq,
                user: user,
                pwd: pwd,
                hashsalt: hashsalt,
                geetest_challenge: result.geetest_challenge,
                geetest_validate: result.geetest_validate,
                geetest_seccode: result.geetest_seccode
            },
            dataType: 'json',
            success(data) {
                layer.close(ii);
                if (data.code >= 0) {
                    layer.alert('开通分站成功！', {
                        icon: 1,
                        closeBtn: false
                    }, function () {
                        window.location.href = '/?mod=regok&zid=' + data.zid;
                    });
                } else {
                    layer.alert(data.msg);
                    captchaObj.reset();
                }
            }
        });
    });
};
const handlerEmbed2 = function (token) {
    if (!token) {
        alert('请完成验证');
        return;
    }
    const kind = $("select[name='kind']").val();
    const qz = $("input[name='qz']").val();
    const domain = $("select[name='domain']").val();
    const name = $("input[name='name']").val();
    const input_name_dom = $("input[name='user']");
    let qq = '';
    let user = '';
    let pwd = '';
    if (input_name_dom.length > 0) {
        qq = $("input[name='qq']").val();
        user = input_name_dom.val();
        pwd = $("input[name='pwd']").val();
    }
    const ii = layer.load(2, {shade: [0.1, '#fff']});
    $.ajax({
        type: "POST",
        url: "/user/ajax.php?act=paysite",
        data: {
            kind: kind,
            qz: qz,
            domain: domain,
            name: name,
            qq: qq,
            user: user,
            pwd: pwd,
            hashsalt: hashsalt,
            token: token
        },
        dataType: 'json',
        success(data) {
            layer.close(ii);
            if (data.code >= 0) {
                layer.alert('开通分站成功！', {
                    icon: 1,
                    closeBtn: false
                }, function () {
                    window.location.href = '/?mod=regok&zid=' + data.zid;
                });
            } else {
                layer.alert(data.msg);
                captchaObj.reset();
            }
        }
    });
};
$(document).ready(function () {
    function fiexdTop() {
        const flex_top_black = document.getElementsByClassName('flex-top-black')[0];
        const docH = document.documentElement.clientHeight;
        const docW = document.documentElement.clientWidth;
        if (docW / docH > 1) {
            flex_top_black.classList.add('flex-js');
        }
    }
    fiexdTop();
    function historyGo() {
        const flex_left_img = document.getElementsByClassName('flex-left-img')[0];
        flex_left_img.onclick = function () {
            window.history.go(-1);
        }
    }
    historyGo();

    $("input[name='qz']").blur(function () {
        const qz = $(this).val();
        const domain = $("select[name='domain']").val();
        if (qz) {
            $.get("/user/ajax.php?act=checkdomain", {'qz': qz, 'domain': domain}, function (data) {
                if (parseInt(data) === 1) {
                    layer.alert('你所填写的域名已被使用，请更换一个！');
                    //$("input[name='qz']").focus();
                }
            });
        }
    });
    $("input[name='user']").blur(function () {
        var user = $(this).val();
        if (user) {
            $.get("/user/ajax.php?act=checkuser", {'user': user}, function (data) {
                if (parseInt(data) === 1) {
                    layer.alert('你所填写的用户名已存在！');
                    //$("input[name='user']").focus();
                }
            });
        }
    });
    $("#submit_buy").click(function () {
        const kind = $("select[name='kind']").val();
        const qz = $("input[name='qz']").val();
        const domain = $("select[name='domain']").val();
        const name = $("input[name='name']").val();
        if (qz === '' || name === '') {
            layer.alert('请确保每项不能为空！');
            return false;
        }
        if (qz.length < 2) {
            layer.alert('域名前缀太短！');
            return false;
        } else if (qz.length > 16) {
            layer.alert('域名前缀太长！');
            return false;
        } else if (name.length < 2) {
            layer.alert('网站名称太短！');
            return false;
        }
        const input_name_dom = $("input[name='user']");
        let qq = '';
        let user = '';
        let pwd = '';
        if (input_name_dom.length > 0) {
            qq = $("input[name='qq']").val();
            user = input_name_dom.val();
            pwd = $("input[name='pwd']").val();
            if (qq === '' || user === '' || pwd === '') {
                layer.alert('请确保每项不能为空！');
                return false;
            }
            if (qq.length < 5) {
                layer.alert('QQ格式不正确！');
                return false;
            } else if (user.length < 3) {
                layer.alert('用户名太短');
                return false;
            } else if (user.length > 20) {
                layer.alert('用户名太长');
                return false;
            } else if (pwd.length < 6) {
                layer.alert('密码不能低于6位');
                return false;
            } else if (pwd.length > 30) {
                layer.alert('密码太长');
                return false;
            }
        }
        const ii = layer.load(2, {shade: [0.1, '#fff']});
        $.ajax({
            type: "POST",
            url: "/user/ajax.php?act=paysite",
            data: {kind: kind, qz: qz, domain: domain, name: name, qq: qq, user: user, pwd: pwd, hashsalt: hashsalt},
            dataType: 'json',
            success: function (data) {
                layer.close(ii);
                if (data['code'] === 4) {
                    layer.confirm('开通分站需要注册用户后使用余额开通？', {
                        btn: ['登录', '注册', '取消']
                    }, function () {
                        window.location.href = '/?mod=login';
                    }, function () {
                        window.location.href = '/?mod=reg';
                    }, function (layera) {
                        layer.close(layera);
                    });
                    return;
                }
                if (data.code === 0) {
                    let paymsg = '';
                    paymsg += `<center><h2>￥ ${data['need']}</h2><hr>`;
                    let isPayTools = !!data['pay_tools'];
                    if (data['pay_alipay'] > 0) {
                        if (isPayTools && data['pay_tools']['alipay']['btn_type'] === '2' && data['pay_tools']['alipay']['btn_href']) {
                            paymsg += `<a class="btn btn-default btn-block" href="${data['pay_tools']['alipay']['btn_href']}" style="margin-top:10px;">
                                        <img width="20" src="/assets/icon/alipay.ico" alt="alipay_logo" class="logo">
                                        ${data['pay_tools']['alipay']['btn_text'] ? data['pay_tools']['alipay']['btn_text'] : '支付宝'}
                                    </a>`;
                        } else {
                            paymsg += `<button class="btn btn-default btn-block" onclick="dopay('alipay', '${data['trade_no']}')" style="margin-top:10px;">
                                        <img width="20" src="/assets/icon/alipay.ico" alt="alipay_logo" class="logo">支付宝
                                    </button>`;
                        }
                    }
                    if (data.pay_qqpay > 0) {
                        if (isPayTools && data['pay_tools']['qqpay']['btn_type'] === '2' && data['pay_tools']['qqpay']['btn_href']) {
                            paymsg += `<a class="btn btn-default btn-block" href="${data['pay_tools']['qqpay']['btn_href']}" style="margin-top:10px;">
                                        <img width="20" src="/assets/icon/qqpay.ico" alt="qqpay_logo" class="logo">
                                        ${data['pay_tools']['qqpay']['btn_text'] ? data['pay_tools']['qqpay']['btn_text'] : 'QQ钱包'}
                                    </a>`;
                        } else {
                            paymsg += `<button class="btn btn-default btn-block" onclick="dopay('qqpay', '${data['trade_no']}')" style="margin-top:10px;">
                                        <img width="20" src="/assets/icon/qqpay.ico" alt="qqpay_logo" class="logo">QQ钱包
                                    </button>`;
                        }
                    }
                    if (data.pay_wxpay > 0) {
                        if (isPayTools && data['pay_tools']['wxpay']['btn_type'] === '2' && data['pay_tools']['wxpay']['btn_href']) {
                            paymsg += `<a class="btn btn-default btn-block" href="${data['pay_tools']['wxpay']['btn_href']}" style="margin-top:10px;">
                                        <img width="20" src="/assets/icon/wechat.ico" alt="wechat_logo" class="logo">
                                        ${data['pay_tools']['wxpay']['btn_text'] ? data['pay_tools']['wxpay']['btn_text'] : '微信支付'}
                                    </a>`;
                        } else {
                            paymsg += `<button class="btn btn-default btn-block" onclick="dopay('wxpay', '${data['trade_no']}')" style="margin-top:10px;">
                                        <img width="20" src="/assets/icon/wechat.ico" alt="wechat_logo" class="logo">微信支付
                                    </button>`;
                        }
                    }
                    if (data.pay_rmb > 0) {
                        paymsg += `<button class="btn btn-success btn-block" onclick="dopay('rmb', '${data['trade_no']}')" style="margin-top:10px;">
                                        使用余额支付（剩${data['user_rmb']}元）
                                    </button>`;
                    }
                    if (isPayTools) {
                        let pay_tools = data['pay_tools'];
                        if (pay_tools['desc'] && pay_tools['url_href'] && pay_tools['url_title']) {
                            paymsg += `<hr><div style="text-align: center; margin-top:5px;">
                                            <h5>温馨提示</h5>
                                            <p style="color: red; font-size: 14px; ">${pay_tools['desc']}</p>
                                            <a href="${pay_tools['url_href']}" target="_blank" style="color:#0099CC;text-decoration:none;font-size:14px;">${pay_tools['url_title']}</a>
                                        </div>`;
                        } else if (pay_tools['desc'] && pay_tools['url_href'].length <= 0) {
                            paymsg += `<hr><div style="text-align: center; margin-top:5px;">
                                            <h5>温馨提示</h5>
                                            <p style="color: red; font-size: 14px; ">${pay_tools['desc']}</p>
                                        </div>`;
                        }
                    }
                    paymsg += `<hr><a class="btn btn-default btn-block" onclick="cancel('${data['trade_no']}')">取消订单</a>`;
                    paymsg += `</center>`;
                    layer.alert(paymsg, {
                        btn: [],
                        title: '提交订单成功',
                        closeBtn: false
                    });
                } else if (data.code === 1) {
                    layer.alert('开通分站成功！', {
                        icon: 1,
                        closeBtn: false
                    }, function () {
                        window.location.href = '/?mod=regok&zid=' + data.zid;
                    });
                } else if (data.code === 2) {
                    if (data.type === 1) {
                        layer.open({
                            type: 1,
                            title: '完成验证',
                            skin: 'layui-layer-rim',
                            area: ['320px', '100px'],
                            content: '<div id="captcha"><div id="captcha_text">正在加载验证码</div><div id="captcha_wait"><div class="loading"><div class="loading-dot"></div><div class="loading-dot"></div><div class="loading-dot"></div><div class="loading-dot"></div></div></div></div>',
                            success: function () {
                                $.getScript("//static.geetest.com/static/tools/gt.js", function () {
                                    $.ajax({
                                        url: "/user/ajax.php?act=captcha&t=" + (new Date()).getTime(),
                                        type: "get",
                                        dataType: "json",
                                        success: function (data) {
                                            $('#captcha_text').hide();
                                            $('#captcha_wait').show();
                                            initGeetest({
                                                gt: data.gt,
                                                challenge: data.challenge,
                                                new_captcha: data.new_captcha,
                                                product: "popup",
                                                width: "100%",
                                                offline: !data.success
                                            }, handlerEmbed);
                                        }
                                    });
                                });
                            }
                        });
                    } else if (data.type === 2) {
                        layer.open({
                            type: 1,
                            title: '完成验证',
                            skin: 'layui-layer-rim',
                            area: ['320px', '260px'],
                            content: '<div id="captcha" style="margin: auto;"><div id="captcha_text">正在加载验证码</div></div>',
                            success: function () {
                                $.getScript("//cdn.dingxiang-inc.com/ctu-group/captcha-ui/index.js", function () {
                                    const myCaptcha = _dx.Captcha(document.getElementById('captcha'), {
                                        appId: data.appid,
                                        type: 'basic',
                                        style: 'embed',
                                        success: handlerEmbed2
                                    })
                                    myCaptcha.on('ready', function () {
                                        $('#captcha_text').hide();
                                    })
                                });
                            }
                        });
                    }
                } else {
                    layer.alert(data.msg);
                }
            }
        });
    });
});
