<?php
include '../includes/common.php';
$title = '后台管理';
include './head.php';
if ($islogin != 1)
    exit('<script>window.location.href="./login.php";</script>');
$act = $_GET['mod'];

{
    $conf = unserialize($CACHE->read());
    //防止出现进分站数据部分
}

echo '<div class="col-md-8 col-md-offset-2" style="margin-top: 2rem;">';

if ($act == 'mailcon_n' || $act == 'shequ_n' || $act == 'qiandao_n' || $act == 'site_n' || $act == 'captcha_n' || $act == 'template_n' || $act == 'pay_n' || $act == 'mail_n' || $act == 'gonggao_n' || $act == 'dwz_n' || $act == 'fenzhan_n') {
    $do = filterParam($_POST['do']);
    if (filterParam($_POST['test_req']) == '发送测试') {
        $receiveEmail = filterParam($_POST['mail_recv'], $_POST['mail_name']);
        if (!empty($receiveEmail)) {
            $apiFailTemplate   = '<html lang="zh"><head><title>测试邮件</title></head><body><h3>这是一封测试邮件，收到该邮件，证明你的邮箱配置参数正常</h3></body></html>';
            $_POST['sitename'] = '祥云团队';
            $res               = sendEmailTest($receiveEmail, '祥云自助下单系统【测试邮件】', $apiFailTemplate, $_POST, true);
            if ($res[0] === false) {
                showmsg($res[1], 4);
            }
            showmsg('测试发送成功！', 1);
        } else {
            showmsg('测试发送失败！，配置参数不正确', 4);
        }
    } else if ($do == 'submit') {
        if ($act == 'mailcon_n') {
            $saveResult = file_put_contents(ROOT . '/template/default/CardSendEmailTemplate.html', $_POST['CardSendEmailTemplate']);
            if ($saveResult === false) {
                $lastError = error_get_last();

                showmsg('保存失败！错误提示:' . $lastError['message'], 4);
            }
            showmsg('修改成功！', 1);
        }
        if ($act == 'captcha_n') {
            $_POST['captcha_open_free']  = filterParam($_POST['captcha_open_free'], 0);
            $_POST['captcha_open_reg']   = filterParam($_POST['captcha_open_reg'], 0);
            $_POST['captcha_open_login'] = filterParam($_POST['captcha_open_login'], 0);
            //设置默认验证码参数
        }
        foreach ($_POST as $key => $value) {
            if ($key == 'do' || $key == 'submit')
                continue;
            saveSetting($key, $value);
        }
        $CACHE->update();
        //更新缓存数据
        if ($act == 'gonggao_n') {
            $DB->update('site', [
                'anounce' => $_POST['anounce'],
                'modal'   => $_POST['modal'],
                'bottom'  => $_POST['bottom'],
                'alert'   => $_POST['alert']
            ], ['template' => $conf['template']]);
        }
        showmsg('修改成功！', 1);
    }
} else if ($act == 'cleanbom') {
    showmsg('没有找到BOM', 2);
} else if ($act == 'epay_n') {
    $account  = filterParam($_POST['account']);
    $username = filterParam($_POST['username']);

    if (empty($account) || empty($username))
        showmsg('结算账户或结算名称不能为空', 3);
    $epayModel = new EpayV1Model($conf['epay_url'], $conf['epay_pid'], $conf['epay_key']);
    $result    = $epayModel->editSettleInfo($account, $username);

    if (!$result[0])
        showmsg($result[1], 3);

    $result = $result[1];
    showmsg($result['msg'], $result['code'] == 0 ? 3 : 1);
} else if ($act == 'account_n') {
    $admin_user = filterParam($_POST['user']);
    $oldpwd     = filterParam($_POST['oldpwd']);
    $newpwd     = filterParam($_POST['newpwd']);
    $newpwd2    = filterParam($_POST['newpwd2']);
    if ($oldpwd != $conf['admin_pwd'])
        showmsg('旧密码不正确', 3);
    if ($newpwd != $newpwd2)
        showmsg('两次新密码不一致', 3);

    saveSetting('admin_user', $admin_user);
    saveSetting('admin_pwd', $newpwd2);
    $CACHE->update();
    showmsg('修改成功！', 1);
} else if ($act == 'proxy_n') {
    $proxy_ip       = filterParam($_POST['proxy_ip']);
    $proxy_port     = filterParam($_POST['proxy_port']);
    $proxy_username = filterParam($_POST['proxy_username']);
    $proxy_password = filterParam($_POST['proxy_password']);
    if (!empty($proxy_ip) || !empty($proxy_port)) {
        if (!preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $proxy_ip) || !is_numeric($proxy_port) || intval($proxy_port) > 65535) {
            showmsg('代理IP格式不正确！', 3);
        }
        if (!is_numeric($proxy_port)) {
            showmsg('代理端口格式不正确！', 3);
        }
        if (intval($proxy_port) > 65535 || intval($proxy_port) < 80) {
            showmsg('代理端口超出范围！', 3);
        }
    }
    saveSetting('proxy_ip', $proxy_ip);
    saveSetting('proxy_port', $proxy_port);
    saveSetting('proxy_username', $proxy_username);
    saveSetting('proxy_password', $proxy_password);
    $CACHE->update();
    showmsg('修改成功！', 1);
} else if ($act == 'recharge_n') {
    $do = filterParam($_POST['do']);
    if ($do != 'submit') {
        showmsg('非法操作', 3);
    }
    $wx_min = !isset($_POST['recharge_wx_min']) || empty($_POST['recharge_wx_min']) ? 0 : trim($_POST['recharge_wx_min']);
    $qq_min = !isset($_POST['recharge_qq_min']) || empty($_POST['recharge_qq_min']) ? 0 : trim($_POST['recharge_qq_min']);
    $ali_min = !isset($_POST['recharge_ali_min']) || empty($_POST['recharge_ali_min']) ? 0 : trim($_POST['recharge_ali_min']);
    if (!is_numeric($wx_min) && !is_numeric($qq_min) && !is_numeric($ali_min)) {
        showmsg('限制金额必须为数字', 3);
    }
    $wx_min = number_format($wx_min, 2, '.', '');
    $qq_min = number_format($qq_min, 2, '.', '');
    $ali_min = number_format($ali_min, 2, '.', '');
    if ($wx_min < 0 || $qq_min < 0 || $ali_min < 0) {
        showmsg('限制金额必须大于等于 0.00', 3);
    }
    $asd = !isset($_POST['forcermb']) ? 0 : trim($_POST['forcermb']);
    saveSetting('recharge_wx_min', $wx_min == 0 ? '' : $wx_min);
    saveSetting('recharge_qq_min', $qq_min == 0 ? '' : $qq_min);
    saveSetting('recharge_ali_min', $ali_min == 0 ? '' : $ali_min);
    if (isset($_POST['forcermb'])) {
        saveSetting('forcermb', trim($_POST['forcermb']));
    }
    $CACHE->update();
    showmsg('修改成功！', 1);
}else if ($act == 'diygoodsname_n'){

    $diy_goodsname = $_POST['diy_goodsname'];
    saveSetting('diy_goodsname', $diy_goodsname);
    showmsg('修改成功！', 1);
}

if ($act == 'account') {
    ?>
    <div class="block">
        <div class="block-title"><h3 class="panel-title">管理员账号配置</h3></div>
        <div class="">
            <form action="./set.php?mod=account_n" method="post" class="form-horizontal" role="form"><input
                        type="hidden" name="do" value="submit">
                <div class="form-group">
                    <label class="col-sm-2 control-label">用户名</label>
                    <div class="col-sm-10"><input type="text" name="user"
                                                  value="<?php echo htmlspecialchars(filterParam($conf['admin_user'])); ?>"
                                                  class="form-control" required=""></div>
                </div>
                <br>
                <div class="form-group">
                    <label class="col-sm-2 control-label">旧密码</label>
                    <div class="col-sm-10"><input type="password" name="oldpwd" value="" class="form-control"
                                                  placeholder="请输入当前的管理员密码"></div>
                </div>
                <br>
                <div class="form-group">
                    <label class="col-sm-2 control-label">新密码</label>
                    <div class="col-sm-10"><input type="password" name="newpwd" value="" class="form-control"
                                                  placeholder="不修改请留空"></div>
                </div>
                <br>
                <div class="form-group">
                    <label class="col-sm-2 control-label">重输密码</label>
                    <div class="col-sm-10"><input type="password" name="newpwd2" value="" class="form-control"
                                                  placeholder="不修改请留空"></div>
                </div>
                <br>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="修改"
                                                                  class="btn btn-primary form-control"><br>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php
} else if ($act == 'shequ') {
    ?>
    <div class="block">
        <div class="block-title"><h3 class="panel-title">网站对接配置</h3></div>
        <div class="">
            <form action="./set.php?mod=shequ_n" method="post" class="form-horizontal" role="form">
                <input type="hidden" name="do" value="submit"/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">社区下单成功后订单状态</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="shequ_status"
                                default="<?php echo filterParam($conf['shequ_status'], 1); ?>">
                            <option value="1">已完成（默认）</option>
                            <option value="2">正在处理</option>
                        </select>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">卡盟下单成功后订单状态</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="kameng_status"
                                default="<?php echo filterParam($conf['kameng_status'], 1); ?>">
                            <option value="1">已完成（默认）</option>
                            <option value="2">正在处理</option>
                        </select>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">下单失败发送提醒邮件</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="shequ_tixing"
                                default="<?php echo filterParam($conf['shequ_tixing'], 0); ?>">
                            <option value="0">关闭</option>
                            <option value="1">开启</option>
                        </select>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" name="submit" value="修改" class="btn btn-primary form-control"/>
                        <br/>
                    </div>
                </div>
            </form>
        </div>
        <div class="panel-footer">
            <span class="glyphicon glyphicon-info-sign"></span>
            使用此功能，用户下单后会自动提交到社区。<br/>
            如果对方网站开启了金盾等防火墙可能导致无法成功提交！
        </div>
    </div>
<?php } else if ($act == 'cloneset') { ?>
    <div class="block">
        <div class="block-title"><h3 class="panel-title">克隆站点配置</h3></div>
        <div class="">
            <form action="./set.php?mod=shequ_n" method="post" class="form-horizontal" role="form">
                <input type="hidden" name="do" value="submit"/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">克隆密钥</label>
                    <div class="col-sm-10">
                        <input type="text" name="key"
                               value="<?php global $password_hash;
                               echo md5($password_hash . md5(SYS_KEY) . $conf['apikey']); ?>"
                               class="form-control"
                               readOnly="readOnly"/>
                    </div>
                </div>
            </form>
        </div>
        <div class="alert alert-info">
            <span class="glyphicon glyphicon-info-sign"></span>
            此密钥是用于其他站点克隆本站商品<br/>
            提示：修改API对接密钥可同时重置克隆密钥。<br/>
        </div>
    </div>
<?php } else if ($act == 'site') { ?>
    <div class="block">
        <div class="block-title"><h3 class="panel-title">网站信息配置</h3></div>
        <div class="">
            <form action="./set.php?mod=site_n" method="post" class="form-horizontal" role="form">
                <input type="hidden" name="do" value="submit"/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">网站名称</label>
                    <div class="col-sm-10">
                        <input type="text" name="sitename" value="<?php echo $conf['sitename']; ?>" class="form-control"
                               required/>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">标题栏后缀</label>
                    <div class="col-sm-10">
                        <input type="text" name="title" value="<?php echo $conf['title']; ?>" class="form-control"/>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">关键字</label>
                    <div class="col-sm-10">
                        <input type="text" name="keywords" value="<?php echo $conf['keywords']; ?>"
                               class="form-control"/>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">网站描述</label>
                    <div class="col-sm-10">
                        <input type="text" name="description" value="<?php echo $conf['description']; ?>"
                               class="form-control"/>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">客服ＱＱ</label>
                    <div class="col-sm-10">
                        <input type="text" name="kfqq" value="<?php echo $conf['kfqq']; ?>" class="form-control"/>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">下单验证模块</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="verify_open" default="<?php echo $conf['verify_open']; ?>">
                            <option value="1">开启</option>
                            <option value="0">关闭</option>
                        </select></div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">显示搜索商品</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="search_open"
                                default="<?php echo isset($conf['search_open']) ? $conf['search_open'] : 1; ?>">
                            <option value="1">开启</option>
                            <option value="0">关闭</option>
                        </select></div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">发卡商品下单标题</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="faka_input" default="<?php echo $conf['faka_input']; ?>">
                            <option value="0">你的邮箱</option>
                            <option value="1">手机号码</option>
                            <option value="2">你的ＱＱ</option>
                            <option value="3">(不填写内容)</option>
                        </select>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">是否开启卡密下单</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="iskami" default="<?php echo $conf['iskami']; ?>">
                            <option value="0">关闭</option>
                            <option value="1">开启</option>
                        </select>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">卡密购买地址</label>
                    <div class="col-sm-10">
                        <input type="text" name="kaurl" value="<?php echo $conf['kaurl']; ?>" class="form-control"/>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">订单详情显示投诉订单按钮</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="show_complain"
                                default="<?php echo htmlspecialchars($conf['show_complain']); ?>">
                            <option value="0">关闭</option>
                            <option value="1">开启</option>
                        </select>
                    </div>
                </div>
                <br/>
                <div id="order_kf_conf"
                     style="display: none;border: 1px solid #dfdae8;border-radius: 2px;padding: 5px;margin-bottom: 20px;">
                    <h4 style="text-align: center;">联系客服设置</h4>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">订单详情显示联系客服按钮</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="show_order_kf"
                                    default="<?php echo htmlspecialchars(empty($conf['show_order_kf']) ? 0 : $conf['show_order_kf']); ?>">
                                <option value="0">关闭</option>
                                <option value="1">开启</option>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div id="show_order_kf_type" style="display: none;">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">联系客服按钮操作类型</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="show_order_kf_type"
                                        default="<?php echo htmlspecialchars(empty($conf['show_order_kf_type']) ? 0 : $conf['show_order_kf_type']); ?>">
                                    <option value="0">打开QQ会话</option>
                                    <option value="1">链接跳转</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div id="show_order_kf_href_modal" style="display: none;">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">客服跳转链接</label>
                            <div class="col-sm-10">
                                <input type="text" name="show_order_kf_href"
                                       value="<?php echo htmlspecialchars($conf['show_order_kf_href']); ?>"
                                       class="form-control" placeholder="例：http://www.baidu.com/">
                            </div>
                        </div>
                    </div>
                    <br>
                    <div id="show_order_kf_qq_modal" style="display: none;">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">客服QQ</label>
                            <div class="col-sm-10">
                                <input type="text" name="show_order_kf_qq"
                                       value="<?php echo htmlspecialchars($conf['show_order_kf_qq']); ?>"
                                       class="form-control" placeholder="例：12345678">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">提交工单后给站长发邮件</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="workorder_mail"
                                default="<?php echo htmlspecialchars($conf['workorder_mail']); ?>">
                            <option value="0">关闭</option>
                            <option value="1">开启</option>
                        </select>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">首页显示订单金额统计信息</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="hide_tongji"
                                default="<?php echo htmlspecialchars($conf['hide_tongji']); ?>">
                            <option value="0">开启</option>
                            <option value="1">关闭</option>
                        </select>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">首页统计数据缓存时间(秒)</label>
                    <div class="col-sm-10">
                        <input type="text" name="tongji_time" value="<?php echo $conf['tongji_time']; ?>"
                               class="form-control" placeholder="留空或0则不缓存，设置缓存可提升网页打开速度"/>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">下单黑名单</label>
                    <div class="col-sm-10">
                        <input type="text" name="blacklist" value="<?php echo $conf['blacklist']; ?>"
                               class="form-control"
                               placeholder="多个账号之间用|隔开"/>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">网站背景图</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="ui_bing" default="<?php echo $conf['ui_bing']; ?>">
                            <option value="0">自定义背景图片</option>
                            <option value="1">随机壁纸</option>
                            <option value="2">Bing每日壁纸</option>
                        </select>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">首页下单显示模式</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="ui_shop" default="<?php echo $conf['ui_shop']; ?>">
                            <option value="0">经典模式</option>
                            <option value="1">分类图片宫格</option>
                            <option value="2">分类图片列表</option>
                            <option value="3">分类图片宫格2</option>
                        </select>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">分站后台界面风格</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="ui_user" default="<?php echo $conf['ui_user']; ?>">
                            <option value="0">明亮风格（默认）</option>
                            <option value="1">黑色风格</option>
                        </select>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">开启购物车功能</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="shoppingcart" default="<?php echo $conf['shoppingcart']; ?>">
                            <option value="0">关闭</option>
                            <option value="1">开启</option>
                        </select>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">网站创建时间</label>
                    <div class="col-sm-10">
                        <input type="date" name="build" value="<?php echo $conf['build']; ?>" class="form-control"/>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">QQ等级代挂开通网址</label>
                    <div class="col-sm-10">
                        <input type="text" name="daiguaurl" value="<?php echo $conf['daiguaurl']; ?>"
                               class="form-control"
                               placeholder="填写后将在首页显示代挂功能，没有请留空"/>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">手机QQ打开网站跳转其他浏览器</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="qqjump" default="<?php echo $conf['qqjump']; ?>">
                            <option value="0">关闭</option>
                            <option value="1">开启</option>
                        </select>
                        <pre>此功能没有任何防红效果，理论上直接在QQ发域名推广都会拦截，建议<a href="./set.php?mod=dwz">生成防红链接</a>进行推广</pre>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">API对接密钥</label>
                    <div class="col-sm-10">
                        <input type="text" name="apikey" value="<?php echo $conf['apikey']; ?>" class="form-control"
                               placeholder="用于下单软件，随便填写即可"/>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">监控密钥</label>
                    <div class="col-sm-10">
                        <input type="text" name="cronkey" value="<?php echo $conf['cronkey']; ?>" class="form-control"
                               placeholder="用于易支付补单监控使用"/>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">开启只能使用余额下单</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="forcermb" default="<?php echo $conf['forcermb']; ?>">
                            <option value="0">关闭</option>
                            <option value="1">开启</option>
                        </select>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" name="submit" value="修改" class="btn btn-primary form-control"/>
                        <br/>
                    </div>
                </div>
                高级功能：<a href="./set.php?mod=cleanbom">清理BOM头部</a>｜<a href="./set.php?mod=defend">防CC模块设置</a>｜<a
                        href="./set.php?mod=proxy">代理服务器设置</a>
            </form>
        </div>
        <script>
            $('select[name="show_complain"]').change(function () {
                let v = $(this).val();
                const order_kf_conf_dom = $('#order_kf_conf');
                if (v === '0') {
                    order_kf_conf_dom.hide();
                } else {
                    order_kf_conf_dom.show();
                }
            });

            const show_order_kf_type_dom = $('#show_order_kf_type');
            const show_order_kf_href_modal_dom = $('#show_order_kf_href_modal');
            const select_show_order_kf_type_dom = $('select[name="show_order_kf_type"]');
            const select_show_order_kf_dom = $('select[name="show_order_kf"]');
            const show_order_kf_qq_modal = $('#show_order_kf_qq_modal');

            select_show_order_kf_dom.change(function () { // 订单详情显示联系客服按钮
                let v = $(this).val();
                if (v === '0') {
                    show_order_kf_type_dom.hide();
                    show_order_kf_href_modal_dom.hide();
                    show_order_kf_qq_modal.hide();
                } else if (select_show_order_kf_type_dom.val() === '1') {
                    show_order_kf_type_dom.show();
                    show_order_kf_href_modal_dom.show();
                } else {
                    show_order_kf_type_dom.show();
                }
                if (v === '1' && select_show_order_kf_type_dom.val() === '0') {
                    show_order_kf_qq_modal.show();
                }
            });
            select_show_order_kf_type_dom.change(function () {
                let v = $(this).val();
                if (v === '0') {
                    show_order_kf_href_modal_dom.hide();
                    if (select_show_order_kf_dom.val() === '1') {
                        show_order_kf_qq_modal.show();
                    }
                } else {
                    show_order_kf_href_modal_dom.show();
                    show_order_kf_qq_modal.hide();
                }
            });
        </script>
        <?php } else if ($act == 'captcha') { ?>
            <div class="block">
                <div class="block-title"><h3 class="panel-title">滑动验证码设置</h3></div>
                <div class="">
                    <form action="./set.php?mod=captcha_n" method="post" class="form-horizontal" role="form">
                        <input type="hidden" name="do" value="submit"/>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">验证码选择</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="captcha_open"
                                        default="<?php echo $conf['captcha_open']; ?>">
                                    <option value="0">关闭</option>
                                    <option value="1">极限滑动验证码</option>
                                    <option value="2">顶象滑动验证码</option>
                                </select>
                            </div>
                        </div>
                        <br/>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">接口ID</label>
                            <div class="col-sm-10">
                                <input type="text" name="captcha_id" value="<?php echo $conf['captcha_id']; ?>"
                                       class="form-control"/>
                            </div>
                        </div>
                        <br/>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">接口KEY</label>
                            <div class="col-sm-10">
                                <input type="text" name="captcha_key" value="<?php echo $conf['captcha_key']; ?>"
                                       class="form-control"/>
                            </div>
                        </div>
                        <br/>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">开启验证场景</label>
                            <div class="col-sm-10">
                                <label>
                                    <input name="captcha_open_free" type="checkbox"
                                           value="1" <?php echo $conf['captcha_open_free'] ? 'checked' : ''; ?>/>
                                    购买免费商品
                                </label>
                                <br/>
                                <label>
                                    <input name="captcha_open_reg" type="checkbox"
                                           value="1" <?php echo $conf['captcha_open_reg'] ? 'checked' : ''; ?>/>
                                    用户注册
                                </label>
                                <br/>
                                <label>
                                    <input name="captcha_open_login"
                                           type="checkbox" <?php echo $conf['captcha_open_login'] ? 'checked' : ''; ?>
                                           value="1"/>
                                    用户登录
                                </label>
                                <br/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <input type="submit" name="submit" value="修改" class="btn btn-primary form-control"/>
                                <br/>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="panel-footer">
                    <span class="glyphicon glyphicon-info-sign"></span>
                    极限验证码：<a href="https://www.geetest.com/Register" rel="noreferrer" target="_blank">点击进入</a><br/>
                    顶象验证码：<a href="https://www.dingxiang-inc.com/business/captcha" rel="noreferrer"
                             target="_blank">点击进入</a>
                </div>
            </div>
        <?php } else if ($act == 'upimg') {
            $isUploadImg = false;
            if (!empty($_POST['s'])) {
                $isUploadImg = true;
                $field       = 'file';
                if ($_FILES[$field]['error'] > 0)
                    showmsg('上传文件失败,' . fileCodeToMessage($_FILES['file']['error']), 3);
                $result = imageUpload($field, 'assets/img/', 'logo', 'png');
                if ($result['code'] == 0) {
                    $isUploadImg = true;
                } else {
                    $isUploadImg = false;
                    showmsg($result['msg'], 3);
                }
            }
            ?>
            <div class="block">
                <div class="block-title">
                    <h3 class="panel-title">更改首页 LOGO</h3>
                    <div class="block-options pull-right">
                        <a class="btn btn-default" href="set.php?mod=upbgimg">更改背景图</a>
                    </div>
                </div>
                <div>
                    <?php if ($isUploadImg): ?>
                        成功上传文件!<br>（可能需要清空浏览器缓存才能看到效果，按Ctrl+F5即可一键刷新缓存）
                    <?php endif; ?>
                    <form action="set.php?mod=upimg" method="POST" enctype="multipart/form-data">
                        <label for="file"></label>
                        <input type="file" name="file" id="file"/>
                        <input type="hidden" name="s" value="1"/>
                        <br>
                        <input type="submit" class="btn btn-primary btn-block" value="确认上传"/>
                    </form>
                    <br>现在的图片：<br>
                    <img src="../assets/img/logo.png?r=<?php echo rand(1000, 9999); ?>" style="max-width:100%"
                         alt="LOGO">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">首页LOGO状态</div>
                            <select id="logo_status" name="logo_status" class="form-control">
                                <option value="1" <?php echo $conf['logo_status']==1?'selected':''; ?>>开启</option>
                                <option value="0" <?php echo $conf['logo_status']==0?'selected':''; ?>>关闭</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">QQ登录-关闭/开启</div>
                            <select id="qq_status" name="qq_status" class="form-control">
                                <option value="1" <?php echo $conf['qq_status']==1?'selected':''; ?>>开启</option>
                                <option value="0" <?php echo $conf['qq_status']==0?'selected':''; ?>>关闭</option>
                            </select>

                        </div>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
	            $("select[name='logo_status']").change(function(){
                    $.ajax({
                        type: "GET",
                        url: "ajax.php?act=editLogoStatus&logo_status=" + $(this).val(),
                        dataType: 'json',
                        success: function (data){
                            alert(data.msg);
                        }
                    });
                });
            </script>
            <script type="text/javascript">
                $("select[name='qq_status']").change(function(){
                    $.ajax({
                        type: "GET",
                        url: "ajax.php?act=editQqStatus&qq_status=" + $(this).val(),
                        dataType: 'json',
                        success: function (data){
                            alert(data.msg);
                        }
                    });
                });
            </script>
        <?php } else if ($act == 'upbgimg') {
            $isUploadImg = false;
            if (!empty($_POST['s'])) {
                $isUploadImg = true;

                if (empty($_FILES['file']) && empty($_POST['ui_background']))
                    showmsg('什么都没上传。。。可能有点问题哦~');

                saveSetting('ui_background', $_POST['ui_background']);
                $CACHE->update();

                if ($_FILES['file']['size'] != 0) {
                    if ($_FILES["file"]["error"] > 0)
                        showmsg('上传文件失败,' . fileCodeToMessage($_FILES['file']['error']), 3);
                    $result = move_uploaded_file($_FILES["file"]["tmp_name"], SYSTEM_ROOT . '../assets/img/bj.png');
                    if (!$result)
                        showmsg('保存文件失败，请重试。', 3);
                } else {
                    $isUploadImg = false;
                }
            }
            ?>
            <div class="block">
                <div class="block-title">
                    <h3 class="panel-title">更改首页背景图</h3>
                    <div class="block-options pull-right">
                        <a class="btn btn-default" href="set.php?mod=upimg">更改LOGO</a>
                    </div>
                </div>
                <div class="">
                    <?php
                    if ($isUploadImg) {
                        ?>
                        成功上传文件!<br>（可能需要清空浏览器缓存才能看到效果，按Ctrl+F5即可一键刷新缓存）
                        <?php
                    }
                    ?>
                    <form action="set.php?mod=upbgimg" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="s" value="1"/>
                        <div class="form-group">
                            <label for="file"></label>
                            <input type="file" name="file" id="file"/>
                        </div>
                        <div class="form-group">
                            <label>显示效果:</label><br>
                            <select class="form-control" name="ui_background"
                                    default="<?php echo $conf['ui_background']; ?>">
                                <option value="0">纵向和横向重复</option>
                                <option value="1">横向重复,纵向拉伸</option>
                                <option value="2">纵向重复,横向拉伸</option>
                                <option value="3">不重复,全屏拉伸</option>
                            </select>
                        </div>
                        <input type="submit" class="btn btn-primary btn-block" value="确认上传"/></form>
                    <br>现在的图片：<br>
                    <img src="../assets/img/bj.png?r=<?php echo rand(1000, 9999); ?>" style="max-width:100%">
                </div>
            </div>
        <?php } else if ($act == 'template') { ?>
            <div class="block">
                <div class="block-title"><h3 class="panel-title">首页模板设置</h3></div>
                <div class="">
                    <div class="alert alert-info">
                        祥云新模板（蓝色海岸）上线啦，行业首创UI 提升用户体验 有助于支付接口提升稳定性
                    </div>
                    <form action="./set.php?mod=template_n" method="post" class="form-horizontal" role="form"><input
                                type="hidden" name="do" value="submit"/>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">选择模板</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="template" default="<?php echo $conf['template']; ?>">
                                    <?php
                                    $scanPath  = SYSTEM_ROOT . '../template';
                                    $list = Template::getListInfo();
                                    if (isset($list['official'])) {
                                        echo '<option value="' . $list['official'][0] . '">'.(empty($list['official'][1]) ? $list['official'][0] : $list['official'][0].' ('.$list['official'][1].')').'</option>';
                                    }
                                    foreach ($list as $k => $v) {
                                        if ($k == 'official') continue;
                                        echo '<option value="' . $v[0] . '">'.(empty($v[1]) ? $v[0] : $v[0].' ('.$v[1].')').'</option>';
                                    }
                                    ?>
                                </select>
                                <a id="templateSet" style="float: right;position: relative;top: 6px;cursor: pointer;">设置模板参数</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">公共静态资源CDN</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="cdnpublic"
                                        default="<?php echo $conf['cdnpublic']; ?>">
                                    <option value="0">七牛云CDN</option>
                                    <option value="1">360CDN</option>
                                    <option value="2">BootCDN</option>
                                    <option value="4">今日头条CDN</option>
                                </select>
                            </div>
                        </div>
                        <br/>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">专有静态资源CDN</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="cdnserver"
                                        default="<?php echo empty($conf['cdnserver']) ? 0 : $conf['cdnserver']; ?>">
                                    <option value="0">关闭</option>
                                </select>
                            </div>
                        </div>
                        <br/>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <input type="submit" name="submit" value="修改"
                                       class="btn btn-primary form-control"/><br/>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="panel-footer">
                    <span class="glyphicon glyphicon-info-sign"></span>
                    网站模板对应template目录里面的名称，会自动获取，如需修改模板公告代码，点击上方 “设置模板参数” ，仅部分模板可用
                </div>
            </div>
            <script>
                $('#templateSet').click(function () {
                    const templateName = $('select[name="template"] option:checked').val();
                    window.location.href = 'template.php?mod=set&name=' + escape(templateName);
                });
            </script>
        <?php } else if($act == 'recharge') { ?>
            <div class="block">
                <div class="block-title"><h3 class="panel-title">用户余额充值配置</h3></div>
                <div>
                    <form action="./set.php?mod=recharge_n" method="post" class="form-horizontal" role="form">
                        <input type="hidden" name="do" value="submit"/>
                        <div style="border: 1px solid rgb(223, 218, 232); border-radius: 2px; padding: 5px; margin-bottom: 20px;">
                            <h4 style="text-align: center;font-weight: bold;">最低充值金额限制</h4>
                            <div class="form-group">
                                <label class="col-lg-3 control-label" for="recharge_wx_min">微信</label>
                                <div class="col-lg-8">
                                    <input type="text" id="recharge_wx_min" placeholder="请输入金额，不填关闭限制" name="recharge_wx_min" class="form-control"
                                           value="<?php echo $conf['recharge_wx_min']; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3 control-label" for="recharge_ali_min">支付宝</label>
                                <div class="col-lg-8">
                                    <input type="text" id="recharge_ali_min" placeholder="请输入金额，不填关闭限制" name="recharge_ali_min" class="form-control"
                                           value="<?php echo $conf['recharge_ali_min']; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3 control-label" for="recharge_qq_min">QQ钱包</label>
                                <div class="col-lg-8">
                                    <input type="text" id="recharge_qq_min" placeholder="请输入金额，不填关闭限制" name="recharge_qq_min" class="form-control"
                                           value="<?php echo $conf['recharge_qq_min']; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="forcermb">开启只能使用余额下单</label>
                            <div class="col-sm-8">
                                <select class="form-control" name="forcermb" id="forcermb">
                                    <option value="0" <?php echo empty($conf['forcermb']) ? 'selected' : ''; ?>>关闭</option>
                                    <option value="1" <?php echo $conf['forcermb'] == 1 ? 'selected' : ''; ?>>开启</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-8">
                                <input type="submit" name="submit" value="修改" class="btn btn-primary form-control"/>
                                <br/>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php } else if ($act == 'pay') { ?>
            <div class="block">
                <div class="block-title"><h3 class="panel-title">支付接口配置</h3></div>
                <div class="">
                    <form action="./set.php?mod=pay_n" method="post" class="form-horizontal" role="form">
                        <input type="hidden" name="do" value="submit"/>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">支付宝支付接口</label>
                            <div class="col-lg-8">
                                <select class="form-control" name="alipay_api"
                                        default="<?php echo filterParam($conf['alipay_api'], 0); ?>">
                                    <option value="0">关闭</option>
                                    <option value="1">支付宝官方即时到账接口</option>
                                    <option value="2">易支付接口</option>
                                    <option value="3">支付宝当面付扫码支付</option>
                                    <option value="5">码支付免签约接口</option>
                                </select>
                                <pre id="payapi_06" style="display:none;"><font color="green"><a
                                                href="https://b.alipay.com/signing/productSetV2.htm" rel="noreferrer"
                                                target="_blank">申请地址</a>支付宝当面付接口配置请修改other/f2fpay/config.php</font></pre>
                            </div>
                        </div>
                        <div id="payapi_01" style="">
                            <div class="form-group">
                                <label class="col-lg-3 control-label">合作者身份(PID)</label>
                                <div class="col-lg-8">
                                    <input type="text" name="alipay_pid" class="form-control"
                                           value="<?php echo $conf['alipay_pid']; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3 control-label">安全校验码(Key)</label>
                                <div class="col-lg-8">
                                    <input type="text" name="alipay_key" class="form-control"
                                           value="<?php echo $conf['alipay_key']; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-3 control-label">支付宝手机网站支付</label>
                                <div class="col-lg-8">
                                    <select class="form-control" name="alipay2_api"
                                            default="<?php echo filterParam($conf['alipay2_api'], 0); ?>">
                                        <option value="0">关闭</option>
                                        <option value="1">支付宝手机网站支付接口</option>
                                    </select>
                                    <pre id="payapi_02" style="">开启前请确保已开通支付宝手机网站支付，否则会导致手机用户无法支付！</pre>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">QQ钱包支付接口</label>
                            <div class="col-lg-8">
                                <select class="form-control" name="qqpay_api"
                                        default="<?php echo filterParam($conf['qqpay_api'], 0); ?>">
                                    <option value="0">关闭</option>
                                    <option value="2">易支付接口</option>
                                    <option value="1">QQ钱包官方支付接口</option>
                                    <option value="5">码支付免签约接口</option>
                                </select>
                                <pre id="payapi_05" style="display:none;"><font color="green"><a
                                                href="https://qpay.qq.com/"
                                                rel="noreferrer"
                                                target="_blank">申请地址</a>QQ钱包支付接口配置请修改other/qqpay/qpayMch.config.php</font></pre>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">微信支付接口</label>
                            <div class="col-lg-8">
                                <select class="form-control" name="wxpay_api"
                                        default="<?php echo filterParam($conf['wxpay_api'], 0); ?>">
                                    <option value="0">关闭</option>
                                    <option value="2">易支付接口</option>
                                    <option value="1">微信官方扫码+公众号支付接口</option>
                                    <option value="3">微信官方扫码+H5支付接口</option>
                                    <option value="5">码支付免签约接口</option>
                                </select>
                                <pre id="payapi_04" style="display:none;"><font color="green"><a
                                                href="https://pay.weixin.qq.com/" rel="noreferrer"
                                                target="_blank">申请地址</a>微信支付接口配置请修改other/wxpay/WxPay.Config.php</font></pre>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-8">
                                <input type="submit" name="submit" value="修改" class="btn btn-primary form-control"/>
                                <br/>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--QQ钱包-易支付-->
            <div class="block">
                <div class="block-title">
                    <h3 class="panel-title">QQ钱包易支付配置</h3>
                    <div class="block-options pull-right">
                          <a href="http://47.111.175.54/" rel="noreferrer" target="_blank" class="btn btn-default" id="epayurl"
                           >进入易支付网站</a> <a href="http://47.111.175.54/" rel="noreferrer" target="_blank" class="btn btn-default" id="epayurl"
                           >进入易支付网站</a>
                    </div>
                </div>
                <form action="./set.php?mod=pay_n" method="post" class="form-horizontal" role="form"><input
                            type="hidden" name="do" value="submit">
                    <div class="form-group" style="">
                        <label class="col-lg-3 control-label">易支付接口网址</label>
                        <div class="col-lg-8">
                            <input type="text" name="epay_qq_url" class="form-control"
                                   value="<?php echo filterParam($conf['epay_qq_url']); ?>"
                                   placeholder="http://47.111.175.54/">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">易支付商户ID</label>
                        <div class="col-lg-8">
                            <input type="text" name="epay_qq_pid" class="form-control"
                                   value="<?php echo filterParam($conf['epay_qq_pid']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">易支付商户密钥</label>
                        <div class="col-lg-8">
                            <input type="text" name="epay_qq_key" class="form-control"
                                   value="<?php echo $conf['epay_qq_key']; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="epay_qq_notify_verify">订单回调验证</label>
                        <div class="col-lg-8">
                            <select class="form-control" name="epay_qq_notify_verify" id="epay_qq_notify_verify">
                                <option value="1" <?php echo empty($conf['epay_qq_notify_verify']) ? '' : 'selected'; ?>>
                                    开启
                                </option>
                                <option value="0" <?php echo empty($conf['epay_qq_notify_verify']) ? 'selected' : ''; ?>>
                                    关闭
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-8">
                            <input type="submit" name="submit" value="修改" class="btn btn-primary form-control"><br><br>
                            <!--                            <a href="set.php?mod=epay">进入易支付结算设置及订单查询页面</a>-->
                        </div>
                    </div>
                </form>
            </div>
            <!--微信-易支付-->
            <div class="block">
                <div class="block-title"><h3 class="panel-title">微信易支付配置</h3>
                    <div class="block-options pull-right">
                        <a href="http://47.111.175.54/" rel="noreferrer" target="_blank" class="btn btn-default" id="epayurl"
                           >进入易支付网站</a>
                    </div>
                </div>
                <form action="./set.php?mod=pay_n" method="post" class="form-horizontal" role="form"><input
                            type="hidden" name="do" value="submit">
                    <div class="form-group" id="payapi_07" style="">
                        <label class="col-lg-3 control-label" for="epay_wx_url">易支付接口网址</label>
                        <div class="col-lg-8">
                            <input type="text" id="epay_wx_url" name="epay_wx_url" class="form-control"
                                   value="<?php echo filterParam($conf['epay_wx_url']); ?>"
                                   placeholder="http://47.111.175.54/">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="epay_wx_pid">易支付商户ID</label>
                        <div class="col-lg-8">
                            <input type="text" id="epay_wx_pid" name="epay_wx_pid" class="form-control"
                                   value="<?php echo filterParam($conf['epay_wx_pid']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="epay_wx_key">易支付商户密钥</label>
                        <div class="col-lg-8">
                            <input id="epay_wx_key" type="text" name="epay_wx_key" class="form-control"
                                   value="<?php echo $conf['epay_wx_key']; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="epay_wx_notify_verify">订单回调验证</label>
                        <div class="col-lg-8">
                            <select class="form-control" name="epay_wx_notify_verify" id="epay_wx_notify_verify">
                                <option value="1" <?php echo empty($conf['epay_wx_notify_verify']) ? '' : 'selected'; ?>>
                                    开启
                                </option>
                                <option value="0" <?php echo empty($conf['epay_wx_notify_verify']) ? 'selected' : ''; ?>>
                                    关闭
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-8">
                            <input type="submit" name="submit" value="修改" class="btn btn-primary form-control"><br><br>
                        </div>
                    </div>
                </form>
            </div>
            <!--支付宝-易支付-->
            <div class="block">
                <div class="block-title"><h3 class="panel-title">支付宝易支付配置</h3>
                    <div class="block-options pull-right">
                        <a href="http://47.111.175.54/" rel="noreferrer" target="_blank" class="btn btn-default" id="epayurl"
                           >进入易支付网站</a>
                    </div>
                </div>
                <form action="./set.php?mod=pay_n" method="post" class="form-horizontal" role="form"><input
                            type="hidden" name="do" value="submit">
                    <div class="form-group" id="payapi_07" style="">
                        <label class="col-lg-3 control-label" for="epay_ali_url">易支付接口网址</label>
                        <div class="col-lg-8">
                            <input type="text" id="epay_ali_url" name="epay_ali_url" class="form-control"
                                   value="<?php echo filterParam($conf['epay_ali_url']); ?>"
                                   placeholder="http://47.111.175.54/">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="epay_ali_pid">易支付商户ID</label>
                        <div class="col-lg-8">
                            <input type="text" id="epay_ali_pid" name="epay_ali_pid" class="form-control"
                                   value="<?php echo filterParam($conf['epay_ali_pid']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="epay_ali_key">易支付商户密钥</label>
                        <div class="col-lg-8">
                            <input id="epay_ali_key" type="text" name="epay_ali_key" class="form-control"
                                   value="<?php echo $conf['epay_ali_key']; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label" for="epay_ali_notify_verify">订单回调验证</label>
                        <div class="col-lg-8">
                            <select class="form-control" name="epay_ali_notify_verify" id="epay_ali_notify_verify">
                                <option value="1" <?php echo empty($conf['epay_ali_notify_verify']) ? '' : 'selected'; ?>>
                                    开启
                                </option>
                                <option value="0" <?php echo empty($conf['epay_ali_notify_verify']) ? 'selected' : ''; ?>>
                                    关闭
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-8">
                            <input type="submit" name="submit" value="修改" class="btn btn-primary form-control"><br><br>
                        </div>
                    </div>
                </form>
            </div>
            <!--码支付-->
            <div class="block">
                <div class="block-title"><h3 class="panel-title">码支付配置</h3>
                    <div class="block-options pull-right">
                        <a href="https://codepay.fateqq.com/" rel="noreferrer" target="_blank" class="btn btn-default">进入码支付网站</a>
                    </div>
                </div>
                <form action="./set.php?mod=pay_n" method="post" class="form-horizontal" role="form">
                    <input type="hidden" name="do" value="submit">
                    <div class="form-group">
                        <label class="col-lg-3 control-label">码支付ID</label>
                        <div class="col-lg-8">
                            <input type="text" name="codepay_id" class="form-control"
                                   value="<?php echo filterParam($conf['codepay_id']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label">码支付通信密钥</label>
                        <div class="col-lg-8">
                            <input type="text" name="codepay_key" class="form-control"
                                   value="<?php echo filterParam($conf['codepay_key']); ?>">
                            <pre><font color="green"><a href="https://codepay.fateqq.com/" rel="noreferrer"
                                                        target="_blank">申请地址</a>码支付需要挂电脑软件，用户支付需要手动输入金额</font></pre>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-8">
                            <input type="submit" name="submit" value="修改" class="btn btn-primary form-control">
                        </div>
                    </div>
                </form>
            </div>
            <!--商品名称自定义-->
            <div class="block">
                <div class="block-title"><h3 class="panel-title">其他设置</h3>
                    <div class="block-options pull-right">
                        <a href="" rel="noreferrer" target="_blank" class="btn btn-default"></a>
                    </div>
                </div>
                <form action="./set.php?mod=diygoodsname_n " method="post" class="form-horizontal" role="form">
                    <input type="hidden" name="" value="submit">
                    <div class="form-group">
                        <label class="col-lg-3 control-label">商品名称自定义</label>
                        <div class="col-lg-8">
                            <input type="text" name="diy_goodsname" class="form-control"
                                   value="<?php
                                   $config_arr = $DB->get('config','*',['k'=>'diy_goodsname']);
                                   if (empty($config_arr) || empty($config_arr['v']) ){
                                       echo '';
                                   }else{
                                       echo $config_arr['v'];
                                   } ?>">
                            <pre><font color="green">此选项可以替换支付宝当面付官方接口的商品名称,留空使用原商品名称</font></pre>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-8">
                            <input type="submit" name="submit" value="修改" class="btn btn-primary form-control">
                        </div>
                    </div>
                </form>
            </div>
            <script>
                $("select[name='alipay_api']").change(function () {
                    if ($(this).val() == 1) {
                        $("#payapi_01").css("display", "inherit");
                        $("#payapi_06").css("display", "none");
                    } else if ($(this).val() == 3) {
                        $("#payapi_01").css("display", "none");
                        $("#payapi_06").css("display", "inherit");
                    } else {
                        $("#payapi_01").css("display", "none");
                        $("#payapi_06").css("display", "none");
                    }
                });
                $("select[name='wxpay_api']").change(function () {
                    if ($(this).val() == 1 || $(this).val() == 3) {
                        $("#payapi_04").css("display", "inherit");
                        $("#payapi_03").css("display", "none");
                        $("#epayurl").hide();
                    } else {
                        $("#payapi_04").css("display", "none");
                        $("#payapi_03").css("display", "none");
                        $("#epayurl").hide();
                    }
                });
                $("select[name='qqpay_api']").change(function () {
                    if ($(this).val() == 1) {
                        $("#payapi_05").css("display", "inherit");
                    } else {
                        $("#payapi_05").css("display", "none");
                    }
                });
                $("select[name='alipay2_api']").change(function () {
                    if ($(this).val() == 1) {
                        $("#payapi_02").css("display", "inherit");
                    } else {
                        $("#payapi_02").css("display", "none");
                    }
                });
                $("select[name='payapi']").change(function () {
                    if ($(this).val() == -1) {
                        $("#payapi_07").css("display", "inherit");
                    } else {
                        $("#payapi_07").css("display", "none");
                    }
                    $.ajax({
                        type: "GET",
                        url: "ajax.php?act=epayurl&id=" + $(this).val(),
                        dataType: 'json',
                        success: function (data) {
                            if (data.code == 0) {
                                $("#epayurl").attr("href", data.url);
                                $("#epayurl").html('进入' + $("select[name='payapi'] option:selected").html() + '商户申请页面');
                                $("#epayurl").show();
                            } else {
                                $("#epayurl").hide();
                            }
                        }
                    });
                });
            </script>
        <?php } else if ($act == 'epay') {
            $payID          = $conf['epay_pid'];
            $balance        = '';
            $type           = '支付宝';
            $settleAccount  = '';
            $settleUsername = '';

            if (!empty($conf['epay_key']) && !empty($conf['epay_pid']) && !empty($conf['epay_url'])) {
                $epayModel = new EpayV1Model($conf['epay_url'], $conf['epay_pid'], $conf['epay_key']);
                $result    = $epayModel->getUserInfo();

                if ($result[0]) {
                    $result = $result[1];

                    $balance        = $result['money'];
                    $settleAccount  = $result['account'];
                    $settleUsername = $result['username'];
                }
            } else {
                showmsg('尚未配置易支付接口，无法使用本功能', 3);
            }

            ?>
            <div class="block">
                <div class="block-title"><h3 class="panel-title">易支付设置</h3></div>
                <div class="">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#">易支付设置</a></li>
                        <li><a href="./set.php?mod=epay_order">订单记录</a></li>
                        <li><a href="./set.php?mod=epay_settle">结算记录</a></li>
                    </ul>
                    <h4>商户信息查看：</h4>
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">商户ID</label>
                            <div class="col-sm-10">
                                <input type="text" name="pid" value="<?php echo $payID; ?>" class="form-control"
                                       disabled/>
                            </div>
                        </div>
                        <br/>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">商户KEY</label>
                            <div class="col-sm-10">
                                <input type="text" name="key" value="****************" class="form-control"
                                       disabled/>
                            </div>
                        </div>
                        <br/>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">商户余额</label>
                            <div class="col-sm-10">
                                <input type="text" name="money" value="<?php echo $balance; ?>" class="form-control"
                                       disabled/>
                            </div>
                        </div>
                    </div>
                    <br/>
                    <h4>收款账号设置：</h4>
                    <form action="./set.php?mod=epay_n" method="post" class="form-horizontal" role="form">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">结算方式</label>
                            <div class="col-sm-10">
                                <input type="text" value="<?php echo $type; ?>" class="form-control" disabled/>
                            </div>
                        </div>
                        <br/>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">结算账号</label>
                            <div class="col-sm-10">
                                <input type="text" name="account" value="<?php echo $settleAccount; ?>"
                                       class="form-control"/>
                            </div>
                        </div>
                        <br/>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">真实姓名</label>
                            <div class="col-sm-10">
                                <input type="text" name="username" value="<?php echo $settleUsername; ?>"
                                       class="form-control"/>
                            </div>
                        </div>
                        <br/>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <input type="submit" name="submit" value="确定修改"
                                       class="btn btn-primary form-control"/><br/>
                            </div>
                        </div>
                        <h4>
                            <span class="glyphicon glyphicon-info-sign"></span>
                            注意事项
                        </h4>
                        1.结算账号和真实姓名请仔细核对，一旦错误将无法结算到账！
                        <br/>2.每笔交易会有1%的手续费，这个手续费是支付宝、微信和财付通收取的，非本接口收取。
                        <br/>3.结算为T+1规则，当天满0元在第二天会自动结算
                    </form>
                </div>
            </div>
        <?php }else if ($act == 'epay_order') {
        $orderList = [];

        if (!empty($conf['epay_key']) && !empty($conf['epay_pid']) && !empty($conf['epay_url'])) {
            $epayModel = new EpayV1Model($conf['epay_url'], $conf['epay_pid'], $conf['epay_key']);
            $result    = $epayModel->getOrderList(1, 30);
            if ($result[0]) {
                if ($result[1]['code'] == 1)
                    $orderList = $result[1]['data'];
            }
        } else {
            showmsg('尚未配置易支付接口，无法使用本功能', 3);
        }
        ?>
    </div>
    <div class="col-xs-12 col-sm-10 col-lg-12 center-block" style="float: none;">
        <div class="block">
            <div class="block-title"><h3 class="panel-title">易支付订单记录</h3></div>
            订单只展示前30条[<a href="set.php?mod=epay">返回</a>]
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>交易号/商户订单号</th>
                        <th>付款方式</th>
                        <th>商品名称/金额</th>
                        <th>创建时间/完成时间</th>
                        <th>状态</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    //error_reporting(E_ERROR | E_WARNING | E_PARSE);
                    //error_reporting(E_ALL);
                    ini_set("display_errors", "On");
                    //                exit(var_dump($orderList));
                    foreach ($orderList as $content) {
//                    exit(var_dump($orderList));
                        echo '<tr>';
                        echo '<td>' . $content['trade_no'] . '<br/>' . $content['out_trade_no'] . '</td>';
                        if ($content['type'] == 1) {
                            echo '<td>微信</td>';
                        } else if ($content['type'] == 2) {
                            echo '<td>QQ钱包</td>';
                        } else if ($content['type'] == 3) {
                            echo '<td>支付宝</td>';
                        } else {
                            echo '<td>未知支付方式</td>';
                        }
                        echo '<td>' . $content['name'] . '<br>￥<b>' . $content['money'] . '</b></td>';
                        echo '<td>' . $content['addtime'] . '<br>' . (empty($content['endtime']) ? '尚未支付' : $content['endtime']) . '</td>';
                        echo '<td>' . ($content['status'] == 1 ? '<span class="text-success">已支付</span>' : '<span class="text-danger">未支付</span>') . '</td>';
                        echo '</tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php } else if ($act == 'epay_settle') {
        $settleList = [];

        if (!empty($conf['epay_key']) && !empty($conf['epay_pid']) && !empty($conf['epay_url'])) {
            $epayModel = new EpayV1Model($conf['epay_url'], $conf['epay_pid'], $conf['epay_key']);
            $result    = $epayModel->getSettleList();
            if ($result[0]) {
                if ($result[1]['code'] == 1)
                    $settleList = $result[1]['data'];
            }
        } else {
            showmsg('尚未配置易支付接口，无法使用本功能', 3);
        }
        ?>
    </div>
    <div class="col-xs-12 col-sm-10 col-lg-8 center-block" style="float: none;">
        <div class="block">
            <div class="block-title w h"><h3 class="panel-title">易支付结算记录</h3></div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>结算账号</th>
                        <th>结算金额</th>
                        <th>结算状态</th>
                        <th>生成时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($settleList as $content) {
                        echo '<tr>';
                        echo '<td><b>' . $content['id'] . '</b></td>';
                        echo '<td>' . $content['account'] . '</td>';
                        echo '<td>' . $content['money'] / 100 . '</td>';
                        echo '<td>' . ($content['status'] ? '<span class="text-success">已结算</span>' : '<span class="text-danger">未结算</span>') . '</td>';
                        echo '<td>' . $content['createTime'] . '</td>';
                        echo '</tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php } else if ($act == 'mailSetting') { ?>
    <div class="block">
        <div class="block-title"><h3 class="panel-title">发信邮箱配置</h3></div>
        <div class="">
            <form action="./set.php?mod=mail_n" method="post" class="form-horizontal" role="form">
                <input type="hidden" name="do" value="submit"/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">SMTP服务器</label>
                    <div class="col-sm-10">
                        <input type="text" name="mail_smtp"
                               value="<?php echo filterParam($conf['mail_smtp'], 'smtp.qq.com'); ?>"
                               class="form-control" placeholder="smtp邮件服务器地址"/></div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">SMTP端口</label>
                    <div class="col-sm-10">
                        <input type="text" name="mail_port"
                               value="<?php echo filterParam($conf['mail_port'], 465); ?>"
                               class="form-control" placeholder="smtp邮件服务器端口"/>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">SMTP链接方式</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="mail_secure"
                                default="<?php echo filterParam($conf['mail_secure'], '0'); ?>">
                            <option value="0">不加密传输</option>
                            <option value="1">SSL 传输</option>
                            <option value="2">TLS 传输</option>
                        </select>
                        <pre><span style="color: green;">务必慎重选择此项，请按照教程进行填写，“不加密传输”可选SMTP端口：587</span></pre>
                    </div>
                </div>
                <br>
                <div class="form-group">
                    <label class="col-sm-2 control-label">邮箱账号</label>
                    <div class="col-sm-10">
                        <input type="text" name="mail_name" value="<?php echo filterParam($conf['mail_name']); ?>"
                               class="form-control"/>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">邮箱密码</label>
                    <div class="col-sm-10">
                        <input type="text" name="mail_pwd"
                               value="<?php echo filterParam($conf['mail_pwd']); ?>"
                               class="form-control"/>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">收信邮箱</label>
                    <div class="col-sm-10">
                        <input type="text" name="mail_recv" value="<?php echo filterParam($conf['mail_recv']); ?>"
                               class="form-control"
                               placeholder="不填默认为发信邮箱"/>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" name="test_req" value="发送测试" class="btn btn-info form-control"/><br/></div>
                    <br/>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" name="submit" value="修改" class="btn btn-primary form-control"/><br/></div>
                    <br/>
                </div>
            </form>
        </div>
        <div class="panel-footer">
            <span class="glyphicon glyphicon-info-sign"></span>
            此功能为用户下单时给自己发邮件提醒以及发卡商品发送给用户的邮件。<br/>使用普通模式发信时，建议使用QQ邮箱，SMTP服务器smtp.qq.com，端口465，密码不是QQ密码也不是邮箱独立密码，是QQ邮箱设置界面生成的<a
                    href="https://service.mail.qq.com/cgi-bin/help?subtype=1&&no=1001256&&id=28" target="_blank"
                    rel="noreferrer">授权码</a>。为确保发信成功率，发信邮箱和收信邮箱最好同一个
        </div>
    </div>
    <script>
        $("select[name='mail_cloud']").change(function () {
            if ($(this).val() == 0) {
                $("#frame_set1").css("display", "inherit");
                $("#frame_set2").css("display", "none");
            } else {
                $("#frame_set1").css("display", "none");
                $("#frame_set2").css("display", "inherit");
            }
        });
    </script>
<?php } else if ($act == 'gonggao') { ?>
    <div class="block">
        <div class="block-title"><h3 class="panel-title">网站公告配置</h3></div>
        <div class="">
            <form action="./set.php?mod=gonggao_n" method="post" class="form-horizontal" role="form">
                <input type="hidden" name="do" value="submit"/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">首页公告</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="anounce"
                                  rows="6"><?php echo htmlspecialchars($conf['anounce']); ?></textarea>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">首页弹出公告</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="modal"
                                  rows="5"><?php echo htmlspecialchars($conf['modal']); ?></textarea>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">首页底部排版（部分模板显示）</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="bottom"
                                  rows="5"><?php echo htmlspecialchars($conf['bottom']); ?></textarea>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">在线下单提示（部分模板显示）</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="alert"
                                  rows="5"><?php echo htmlspecialchars($conf['alert']); ?></textarea>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">订单查询页面公告</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="gg_search"
                                  rows="5"><?php echo htmlspecialchars($conf['gg_search']); ?></textarea>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">分站后台公告</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="gg_panel"
                                  rows="5"><?php echo htmlspecialchars($conf['gg_panel']); ?></textarea>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">所有分站显示首页公告</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="gg_announce" rows="5"
                                  placeholder="此处公告内容将在所有分站首页公告显示。顺序是先显示此公告再显示分站自定义公告"><?php echo htmlspecialchars($conf['gg_announce']); ?></textarea>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">首页聊天代码</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="chatframe"
                                  rows="3"><?php echo htmlspecialchars($conf['chatframe']); ?></textarea>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">APP下载地址</label>
                    <div class="col-sm-10">
                        <input type="text" name="appurl" value="<?php echo daddslashes($conf['appurl']); ?>"
                               class="form-control"/>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">APP启动弹出内容</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="appalert"
                                  rows="3"><?php echo htmlspecialchars($conf['appalert']); ?></textarea>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">首页背景音乐</label>
                    <div class="col-sm-10">
                        <input type="text" name="musicurl" value="<?php echo daddslashes($conf['musicurl']); ?>"
                               class="form-control"
                               placeholder="填写音乐的URL"/>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" name="submit" value="修改" class="btn btn-primary form-control"/><br/>
                    </div>
                </div>
            </form>
        </div>
        <div class="panel-footer">
            <span class="glyphicon glyphicon-info-sign"></span>
            实用工具：<a href="set.php?mod=copygg">一键复制其他站点排版</a>｜<a href="http://www.runoob.com/runcode" target="_blank"
                                                                rel="noreferrer">HTML在线测试</a>｜<a
                    href="http://pic.xiaojianjian.net/" target="_blank" rel="noreferrer">图床</a>｜<a
                    href="https://link.hhtjim.com/" target="_blank" rel="noreferrer">音乐外链1</a>｜<a
                    href="http://music.cccyun.cc/" target="_blank" rel="noreferrer">音乐外链2</a><br/>
            聊天代码获取地址：<a href="http://changyan.kuaizhan.com/" target="_blank" rel="noreferrer">搜狐畅言</a>
        </div>
    </div>
<?php } else if ($act == 'copygg' || $act == 'copygg_n') {
    if ($act == 'copygg_n') {
        showmsg('功能尚未开放', 2);
    }
    ?>
    <div class="block">
        <div class="block-title"><h3 class="panel-title">一键复制其他站点排版</h3></div>
        <div class="">
            <form action="./set.php?mod=copygg_n" method="post" class="form-horizontal" role="form">
                <input type="hidden" name="do" value="submit"/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">站点URL</label>
                    <div class="col-sm-10">
                        <input type="text" name="url" value="" class="form-control" placeholder="http://www.qq.com/"
                               required/>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">复制内容</label>
                    <div class="col-sm-10">
                        <label>
                            <input name="content[]" type="checkbox" value="anounce" checked/>
                            首页公告
                        </label>
                        <br/>
                        <label>
                            <input name="content[]" type="checkbox" value="modal" checked/>
                            弹出公告
                        </label>
                        <br/>
                        <label>
                            <input name="content[]" type="checkbox" value="bottom" checked/>
                            底部排版
                        </label>
                        <br/>
                        <label>
                            <input name="content[]" type="checkbox" value="alert" checked/>
                            下单提示
                        </label>
                        <br/>
                        <label>
                            <input name="content[]" type="checkbox" value="gg_search" checked/>
                            订单查询公告
                        </label>
                        <br/>
                        <label>
                            <input name="content[]" type="checkbox" value="gg_panel" checked/>
                            分站后台公告
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" name="submit" value="修改" class="btn btn-primary form-control"/><br/>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php } else if ($act == 'qiandao') { ?>
    <div class="block">
        <div class="block-title"><h3 class="panel-title">签到模块设置</h3></div>
        <div class="alert alert-info">
            计算方式：每天递增的金额 = 奖励余额初始值 x 每日递增倍数 x 连续签到数(第一天不算)，再加上[奖励余额初始值]
            ，当达到连续签到天数等于[最多递增天数]时，连续签到金额等于[奖励余额初始值]，连续签到中途中断，签到金额重新计算。
            </b>
        </div>
        <div class="">
            <form action="./set.php?mod=qiandao_n" method="post" class="form-horizontal" role="form">
                <input type="hidden" name="do" value="submit"/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">奖励余额初始值</label>
                    <div class="col-sm-10">
                        <input type="text" name="qiandao_reward" value="<?php echo $conf['qiandao_reward']; ?>"
                               class="form-control"/>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">每日递增倍数</label>
                    <div class="col-sm-10">
                        <input type="text" name="qiandao_mult" value="<?php echo $conf['qiandao_mult']; ?>"
                               class="form-control"/>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">最多递增天数</label>
                    <div class="col-sm-10">
                        <input type="text" name="qiandao_day" value="<?php echo $conf['qiandao_day']; ?>"
                               class="form-control"/>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" name="submit" value="修改" class="btn btn-primary form-control"/><br/>
                    </div>
                </div>
            </form>
        </div>
        <div class="panel-footer">
            <span class="glyphicon glyphicon-info-sign"></span>
            奖励余额初始值填写一个值代表所有类型分站都一样，填写3个值并用|隔开代表不同类型分站不一样，例如0.01|0.02|0.03 分别是普通用户、普及版分站、专业版分站的奖励余额初始值。
        </div>
    </div>
    <div class="block">
        <div class="block-title"><h3 class="panel-title" id="title">签到统计</h3></div>
        <div class="">
            <table class="table table-bordered">
                <tbody>
                <tr>
                    <th style="font-size: 13px;" class="text-center">
                        <i class="fa fa-user-circle-o"></i> 今日签到<br><span id="count1"></span>人
                    </th>
                    <th style="font-size: 13px;" class="text-center">
                        <i class="fa fa-user-circle"></i> 昨日签到<br><span id="count2"></span>人
                    </th>
                    <th style="font-size: 13px;" class="text-center">
                        <i class="fa fa-pie-chart"></i> 累计签到<br><span id="count3"></span>人
                    </th>
                </tr>
                <tr>
                    <th style="font-size: 13px;" class="text-center">
                        <i class="fa fa-money"></i> 今日送出余额<br><span id="count4"></span>元
                    </th>
                    <th style="font-size: 13px;" class="text-center">
                        <i class="fa fa-money"></i> 昨日送出余额<br><span id="count5"></span>元
                    </th>
                    <th style="font-size: 13px;" class="text-center">
                        <i class="fa fa-bar-chart"></i> 累计送出余额<br><span id="count6"></span>元
                    </th>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            layer.load(2);
            $('#title').html('正在加载数据中...');
            $.ajax({
                type: "GET",
                url: "ajax.php?act=qdcount",
                dataType: 'json',
                async: true,
                success: function (data) {
                    layer.closeAll('loading');
                    $('#count1').html(data.count1);
                    $('#count2').html(data.count2);
                    $('#count3').html(data.count3);
                    $('#count4').html(data.count4);
                    $('#count5').html(data.count5);
                    $('#count6').html(data.count6);
                    $('#title').html('签到统计');
                },
                error: function () {
                    layer.closeAll('loading');
                }
            });
        })
    </script>
<?php } else if ($act == 'invite_remove') { // 新版本分享推广弃用 ?>
    <div class="block">
        <div class="block-title"><h3 class="panel-title">推广链接设置</h3></div>
        <div class="">
            <form action="./set.php?mod=invite_n" method="post" role="form">
                <input type="hidden" name="do" value="submit"/>
                <div class="form-group">
                    <label>赠送商品ID</label>
                    <input type="text" name="invite_tid" value="<?php echo $conf['invite_tid']; ?>" class="form-control"
                           placeholder="不填写则关闭推广链接功能"/>
                    <pre>在商品列表，点击商品进入，在地址栏中tid=后面的数字即为商品ID</pre>
                </div>
                <div class="form-group">
                    <label>赠送商品名称</label>
                    <input type="text" name="invite_name" value="<?php echo $conf['invite_name']; ?>"
                           class="form-control" disabled/>
                </div>
                <div class="form-group">
                    <label>被推荐人下单金额超过多少才赠送商品</label>
                    <input type="text" name="invite_money" value="<?php echo $conf['invite_money']; ?>"
                           class="form-control" placeholder="不填写则不限最小金额"/>
                </div>
                <div class="form-group">
                    <input type="submit" name="submit" value="修改" class="btn btn-primary form-control"/><br/>
                </div>
            </form>
        </div>
        <div class="panel-footer">
            <span class="glyphicon glyphicon-info-sign"></span>
            推广链接生成地址：/?mod=invite<br/>
            别人用生成后的链接访问，并成功下单之后，推荐人可以获得相应的赠送商品。<br/>
            推广页面模板文件：/template/default/invite.php
        </div>
    </div>
    <script>
        $(function ($) {
            $("input[name='invite_tid']").blur(function () {
                const tid = $("input[name='invite_tid']").val();
                if (tid === '') return;
                $.ajax({
                    type: 'POST',
                    url: 'ajax.php?act=tool',
                    data: {tid: tid},
                    dataType: 'json',
                    success: function (res) {
                        if (0 === res['code']) {
                            $("input[name='invite_name']").val(res['data']['name']);
                        } else {
                            layer.msg(res['msg'], {icon: 8});
                        }
                    }
                });
            }).blur();
        });
    </script>
<?php } else if ($act == 'dwz') { ?>
    <div class="block">
        <div class="block-title"><h3 class="panel-title">防红链接生成接口设置</h3></div>
        <div class="">
            <form action="./set.php?mod=dwz_n" method="post" role="form"><input type="hidden" name="do" value="submit"/>
                <div class="form-group">
                    <label>防红接口选择：</label>
                    <select class="form-control" name="fanghong_api"
                            default="<?php echo filterParam($conf['fanghong_api'], 0); ?>">
                        <option value="0">不使用防红接口</option>
                        <option value="9" data-url="http://ty-kmurlfh.api.wxzp.top:88/dwz.php?longurl=">酷猫防红-免费接口
                        </option>
                        <option value="9" data-url="https://www.qqfh.wang/dwz.php?longurl=">酷猫防红-VIP接口
                        </option>
                        <option value="11">自定义防红接口</option>
                    </select>
                </div>
                <?php if ($conf['fanghong_api'] == 2) { ?>
                    <div class="form-group" id="fanghong_type" style="display: inherit;">
                        <label>分站默认生成防红方式：</label>
                        <select class="form-control" name="fanghong_type"
                                default="<?php echo filterParam($conf['fanghong_type'], 1); ?>">
                            <option value="1">跳转防红（跳转到其它浏览器）</option>
                            <option value="2">直接防红（QQ内直接打开,仅安卓）</option>
                            <option value="3">微信防红（专门用于微信内访问）</option>
                        </select>
                    </div>
                <?php } ?>
                <div class="form-group" id="fanghong_diy" style="display:none;">
                    <label>自定义接口地址：</label>
                    <div class="input-group">
                        <input type="text" name="fanghong_url" value="<?php echo filterParam($conf['fanghong_url']); ?>"
                               class="form-control"
                               placeholder="不填写则关闭防红链接生成"/>
                        <div class="input-group-addon" onclick="checkurl()"><small>检测地址</small></div>
                    </div>
                </div>
                <div class="form-group">
                    <input type="submit" name="submit" value="修改" class="btn btn-primary form-control"/><br/>
                </div>
            </form>
        </div>
        <div class="panel-footer">
            <span class="glyphicon glyphicon-info-sign"></span>
            <a href="http://payurl.kmsix.com" rel="noreferrer" target="_blank"> 酷猫防红 - Vip版购买地址</a>
        </div>
    </div>
    <?php if ($conf['fanghong_api'] != '0') { ?>
        <div class="block">
            <div class="block-title"><h3 class="panel-title">获取防红链接</h3></div>
            <div class="">
                <div class="input-group">
                    <span class="input-group-addon">代刷网址</span>
                    <input class="form-control" id="longurl"
                           value="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']; ?>">
                </div>
                <div class="well well-sm" style="margin-top: 10px;">如果您的网址在QQ内报毒或者打不开，您可以使用此功能生成防毒链接！</div>
                <a class="btn btn-block btn-success"
                   id="create_url<?php echo ($conf['fanghong_api'] != 9 && $conf['fanghong_api'] != 10) ? '2' : ''; ?>">生成我的防红链接</a>
            </div>
        </div>
    <?php } ?>
    <script src="//cdn.staticfile.org/clipboard.js/1.7.1/clipboard.min.js"></script>
    <div class="modal fade in" id="fanghongurl" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">�</span><span
                                class="sr-only">关闭</span></button>
                    <h4 class="modal-title">防红链接生成</h4></div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">防红链接</div>
                            <input type="text" id="target_url" value="" class="form-control" disabled=""></div>
                    </div>
                    <center>
                        <button class="btn btn-info btn-sm" id="recreate_url">重新生成</button>&nbsp;<button
                                class="btn btn-warning btn-sm copy-btn" id="copyurl" data-clipboard-text="">一键复制链接
                        </button>
                    </center>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade in" id="fanghongvip" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">�</span><span
                                class="sr-only">关闭</span></button>
                    <h4 class="modal-title">防红链接生成</h4></div>
                <div class="modal-body" id="fanghonglist">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function checkurl() {
            var url = $("input[name='fanghong_url']").val();
            if (url.indexOf('http') >= 0 && url.indexOf('=') >= 0) {
                var ii = layer.load(2, {shade: [0.1, '#fff']});
                $.ajax({
                    type: "POST",
                    url: "ajax.php?act=checkdwz",
                    data: {url: url},
                    dataType: 'json',
                    success: function (data) {
                        layer.close(ii);
                        if (data.code == 1) {
                            layer.msg('检测正常');
                        } else if (data.code == 2) {
                            layer.alert('链接无法访问或返回内容不是json格式');
                        } else {
                            layer.alert('该链接无法访问');
                        }
                    },
                    error: function (data) {
                        layer.close(ii);
                        layer.msg('目标URL连接超时');
                        return false;
                    }
                });
            } else {
                layer.alert('链接地址错误');
            }
        }

        $(document).ready(function () {
            $("select[name='fanghong_api']").change(function () {
                const v = parseInt($(this).val());
                const fanghong_url_dom = $('input[name="fanghong_url"]');
                $("#fanghong_diy").hide();
                if (v === 9) {
                    // $('#fanghong_diy').show();
                    // $('#fanghong_diy1').show();
                    const url = $("select[name='fanghong_api'] :selected").data('url');
                    if (url !== undefined) {
                        fanghong_url_dom.val(url);
                    }
                } else if (v === 11) {
                    $("#fanghong_diy").show();
                    // $("#fanghong_diy1").show();
                    fanghong_url_dom.val(fanghong_url_dom.attr('value'));
                } else {
                    $("#fanghong_diy").hide();
                    $("#fanghong_diy1").hide();
                }
            });
            var url = $("input[id='longurl']").val();
            var clipboard = new Clipboard('.copy-btn', {
                container: document.getElementById('fanghongurl')
            });
            clipboard.on('success', function (e) {
                layer.msg('复制成功！');
            });
            clipboard.on('error', function (e) {
                layer.msg('复制失败，请长按链接后手动复制');
            });
            var clipboard = new Clipboard('.copy-btn', {
                container: document.getElementById('fanghongvip')
            });
            clipboard.on('success', function (e) {
                layer.msg('复制成功！');
            });
            clipboard.on('error', function (e) {
                layer.msg('复制失败，请长按链接后手动复制');
            });
            $("#create_url").click(function () {
                var self = $(this);
                if (self.attr("data-lock") === "true") return;
                else self.attr("data-lock", "true");
                var ii = layer.load(1, {shade: [0.1, '#fff']});
                $.getJSON('ajax.php', {
                    'act': 'create_url',
                    'longurl': url
                }, function (data) {
                    layer.close(ii);
                    if (data.code == 0) {
                        $("#target_url").val(data.url);
                        $("#copyurl").attr('data-clipboard-text', data.url);
                        $('#fanghongurl').modal('show');
                    } else {
                        layer.alert(data.msg);
                    }
                    self.attr("data-lock", "false");
                }, 'json');
            });
            $("#recreate_url").click(function () {
                var self = $(this);
                if (self.attr("data-lock") === "true") return;
                else self.attr("data-lock", "true");
                var ii = layer.load(1, {shade: [0.1, '#fff']});
                $.getJSON('ajax.php', {
                    'act': 'create_url',
                    'force': 1,
                    'longurl': url
                }, function (data) {
                    layer.close(ii);
                    if (data.code == 0) {
                        layer.msg('生成链接成功');
                        $("#target_url").val(data.url);
                        $("#copyurl").attr('data-clipboard-text', data.url);
                    } else {
                        layer.alert(data.msg);
                    }
                    self.attr("data-lock", "false");
                });
            });
            $("#create_url2").click(function () {
                var self = $(this);
                if (self.attr("data-lock") === "true") return;
                else self.attr("data-lock", "true");
                var ii = layer.load(1, {shade: [0.1, '#fff']});
                $("#fanghonglist").empty();
                $.get("ajax.php?act=create_dwz&longurl=" + url, function (data) {
                    layer.close(ii);
                    if (data.code == 0) {
                        $.each(data.data, function (k, v) {
                            $('#fanghonglist').append('<div class="form-group"><div class="input-group"><div class="input-group-addon">' + v.name + '</div><input type="text" value="' + v.url + '" class="form-control" disabled=""><div class="input-group-addon"><a class="copy-btn" data-clipboard-text="' + v.url + '" href="javascript:;"><i class="fa fa-copy"></i></a></div></div></div>');
                        });
                        $('#fanghongvip').modal('show');
                    } else {
                        layer.alert(data.msg);
                    }
                    self.attr("data-lock", "false");
                }, 'json');
            });
            $("select[name='fanghong_api']").change();
        });
    </script>
<?php } else if ($act == 'proxy') { ?>
    <div class="block">
        <div class="block-title"><h3 class="panel-title">代理服务器设置</h3></div>
        <div class="">
            <form action="./set.php?mod=proxy_n" method="post" class="form-horizontal" role="form">
                <input type="hidden" name="do" value="submit"/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">代理IP</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="proxy_ip" value="<?php echo $conf['proxy_ip']; ?>"
                               placeholder="例：127.0.0.1">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">代理端口</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="proxy_port"
                               value="<?php echo $conf['proxy_port']; ?>" placeholder="例：8080">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">代理账号</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="proxy_username"
                               value="<?php echo $conf['proxy_username']; ?>" placeholder="代理账号 如设置白名单可不填">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">代理密码</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="proxy_password"
                               value="<?php echo $conf['proxy_password']; ?>" placeholder="代理密码 如设置白名单可不填">
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <div class="col-sm-12">
                        <input type="submit" name="submit" value="修改" class="btn btn-primary form-control"/><br/>
                    </div>
                </div>
                <?php if (empty($conf['proxy_ip']) || empty($conf['proxy_port'])) { ?>
                    <div class="alert alert-info">你当前的未使用代理服务器。</div>
                <?php } else { ?>
                    <div class="alert alert-success">你当前的已使用代理服务器。</div>
                <?php } ?>
            </form>
        </div>
        <div class="panel-footer" style="margin-bottom: 20px;border-top: none;">
            <span class="glyphicon glyphicon-info-sign"></span>本功能适用于国外服务器对接一些屏蔽国外访问的社区或者卡盟，开启后使用国内代理服务器进行对接。<br/>
            Tips：以上两项都填写的话，代表开启，否则关闭代理。<br/>
            注意：如果网站更换主机之后需要重新修改当前配置。<br>
            <code>注意：如果您使用“快代理”请购买独享型 静态IP那款，免得浪费钱，且只能使用“HTTP端口”。</code><br>
            不想输入账号密码就在快代理后台添加“白名单”
            <p class="text-danger">如已经配置代理，只需要刷新页面即可查看代理结果</p>
        </div>
        <?php if (!empty($conf['proxy_ip']) && !empty($conf['proxy_port'])) { ?>
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">代理测试结果</h3>
                </div>
                <div class="panel-body">
                <pre>
                    <?php
                    $result = curl2('http://www.cip.cc/', ['User-Agent: curl/7.58.0']);
                    if ($result === false) {
                        echo '代理设置无效，请重新配置';
                    } else {
                        $result = trim($result);
                        echo htmlspecialchars($result);
                    }
                    ?>
                </pre>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } else if ($act == 'defend') { ?>
    <div class="block">
        <div class="block-title"><h3 class="panel-title">防CC模块设置</h3></div>
        <div class="">
            <form action="./set.php?mod=defend_n" method="post" class="form-horizontal" role="form">
                <input type="hidden" name="do" value="submit"/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">CC防护等级</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="defendid"
                                default="<?php echo filterParam($conf['defendid'], '0'); ?>">
                            <option value="0">关闭</option>
                            <option value="1">低(推荐)</option>
                            <option value="2">中</option>
                            <option value="3">高</option>
                        </select></div>
                </div>
                <br/>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" name="submit" value="修改" class="btn btn-primary form-control"/><br/>
                    </div>
                </div>
            </form>
        </div>
        <div class="panel-footer">
            <span class="glyphicon glyphicon-info-sign"></span>CC防护说明<br/>
            高：全局使用防CC，会影响网站APP和对接软件的正常使用<br/>
            中：会影响搜索引擎的收录，建议仅在正在受到CC攻击且防御不佳时开启<br/>
            低：用户首次访问进行验证（推荐）<br/>
        </div>
    </div>
<?php } else if ($act == 'fenzhan') { ?>
    <div class="block">
        <div class="block-title"><h3 class="panel-title">分站相关配置</h3></div>
        <div class="">
            <form action="./set.php?mod=fenzhan_n" method="post" class="form-horizontal" role="form">
                <input type="hidden" name="do" value="submit"/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">开启提现</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="fenzhan_tixian"
                                default="<?php echo $conf['fenzhan_tixian']; ?>">
                            <option value="0">关闭</option>
                            <option value="1">开启</option>
                        </select></div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">是否启用收款图</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="fenzhan_skimg"
                                default="<?php echo $conf['fenzhan_skimg']; ?>">
                            <option value="0">关闭</option>
                            <option value="1">开启</option>
                        </select>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">提现余额比例</label>
                    <div class="col-sm-10">
                        <input type="text" name="tixian_rate" value="<?php echo $conf['tixian_rate']; ?>"
                               class="form-control" placeholder="填写百分数"/>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">提现最低余额</label>
                    <div class="col-sm-10">
                        <input type="text" name="tixian_min" value="<?php echo $conf['tixian_min']; ?>"
                               class="form-control"/>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">生成卡密功能</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="fenzhan_kami" default="<?php echo $conf['fenzhan_kami']; ?>">
                            <option value="1">开启</option>
                            <option value="0">关闭</option>
                        </select>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">用户注册功能</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="user_open" default="<?php echo $conf['user_open']; ?>">
                            <option value="0">关闭</option>
                            <option value="1">开启</option>
                        </select>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">新注册用户价格等级</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="user_level" default="<?php echo $conf['user_level']; ?>">
                            <option value="0">商品售价</option>
                            <option value="1">普及版价格</option>
                        </select>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">自助开通分站</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="fenzhan_buy"
                                default="<?php echo $conf['fenzhan_buy']; ?>">
                            <option value="1">开启</option>
                            <option value="0">关闭</option>
                        </select>
                    </div>
                </div>
                <br/>
                <div id="frame_set1" style="">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">分站默认有效期</label>
                        <div class="col-sm-10">
                            <input type="text" name="fenzhan_expiry" value="<?php echo $conf['fenzhan_expiry']; ?>"
                                   class="form-control"/>
                            <pre>填写月数，如果为0则是永久不过期</pre>
                        </div>
                    </div>
                    <br/>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">专业版价格</label>
                        <div class="col-sm-10">
                            <input type="text" name="fenzhan_price2" value="<?php echo $conf['fenzhan_price2']; ?>"
                                   class="form-control"/>
                        </div>
                    </div>
                    <br/>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">普及版价格</label>
                        <div class="col-sm-10">
                            <input type="text" name="fenzhan_price" value="<?php echo $conf['fenzhan_price']; ?>"
                                   class="form-control"/>
                        </div>
                    </div>
                    <br/>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">专业版成本价格</label>
                        <div class="col-sm-10">
                            <input type="text" name="fenzhan_cost2" value="<?php echo $conf['fenzhan_cost2']; ?>"
                                   class="form-control"/>
                            <pre>注意：分站成本价格请勿低于初始赠送余额</pre>
                        </div>
                    </div>
                    <br/>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">普及版成本价格</label>
                        <div class="col-sm-10">
                            <input type="text" name="fenzhan_cost" value="<?php echo $conf['fenzhan_cost']; ?>"
                                   class="form-control"/>
                            <pre>注意：分站成本价格请勿低于初始赠送余额</pre>
                        </div>
                    </div>
                    <br/>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">初始赠送余额</label>
                        <div class="col-sm-10">
                            <input type="text" name="fenzhan_free" value="<?php echo $conf['fenzhan_free']; ?>"
                                   class="form-control"/>
                        </div>
                    </div>
                    <br/>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">普及版升级价格</label>
                        <div class="col-sm-10">
                            <input type="text" name="fenzhan_upgrade" value="<?php echo $conf['fenzhan_upgrade']; ?>"
                                   class="form-control" placeholder="不填写则不开启自助升级专业版功能"/>
                        </div>
                    </div>
                    <br/>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">自助修改域名价格</label>
                        <div class="col-sm-10">
                            <input type="text" name="fenzhan_editd" value="<?php echo $conf['fenzhan_editd']; ?>"
                                   class="form-control" placeholder="不填写则不开启自助修改域名"/>
                        </div>
                    </div>
                    <br/>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">分站自动复制主站公告代码</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="fenzhan_html"
                                    default="<?php echo empty($conf['fenzhan_html']) ? 0 : $conf['fenzhan_html']; ?>">
                                <option value="0">关闭</option>
                                <option value="1">开启</option>
                            </select></div>
                    </div>
                    <br/>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">分站可选择域名</label>
                        <div class="col-sm-10">
                            <input type="text" name="fenzhan_domain" value="<?php echo $conf['fenzhan_domain']; ?>"
                                   class="form-control"/>
                            <pre>多个域名用,隔开！</pre>
                        </div>
                    </div>
                    <br/>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">主站预留域名</label>
                    <div class="col-sm-10">
                        <input type="text" name="fenzhan_remain" value="<?php echo $conf['fenzhan_remain']; ?>"
                               class="form-control"/>
                        <pre>主站域名无法被分站绑定，多个域名用,隔开！</pre>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">未绑定域名显示404页面</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="fenzhan_page" default="<?php echo $conf['fenzhan_page']; ?>">
                            <option value="0">关闭</option>
                            <option value="1">开启</option>
                        </select>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">分站可更换首页模板</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="fenzhan_template"
                                default="<?php echo $conf['fenzhan_template']; ?>">
                            <option value="0">关闭</option>
                            <option value="1">开启</option>
                        </select>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <label class="col-sm-2 control-label">分站是否显示排名</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="fenzhan_isShowRank"
                                default="<?php echo isset($conf['fenzhan_isShowRank']) ? $conf['fenzhan_isShowRank'] : 0; ?>">
                            <option value="0">隐藏</option>
                            <option value="1">显示</option>
                        </select>
                    </div>
                </div>
                <br/>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" name="submit" value="修改" class="btn btn-primary form-control"/><br/>
                    </div>
                </div>
                高级功能：<a href="./sitelist.php?my=replace">分站域名批量修改</a>
            </form>
        </div>
    </div>
    <script>
        $("select[name='fenzhan_buy']").change(function () {
            if ($(this).val() == 1) {
                $("#frame_set1").css("display", "inherit");
            } else {
                $("#frame_set1").css("display", "none");
            }
        });
        $("select[name='fenzhan_page']").change(function () {
            if ($(this).val() == 1) {
                alert('开启后必须保证主站预留域名已正确填写，否则主站将无法访问。注意！！www.qq.com与qq.com是两个域名！！');
            }
        });
    </script>
<?php } else if ($act == 'mailcon') { ?>
    <div class="block">
        <div class="block-title"><h3 class="panel-title">发信邮件模板设置</h3></div>
        <div style="padding: 0 10px;">
            <form action="./set.php?mod=mailcon_n" method="post" class="form-horizontal" role="form"><input
                        type="hidden" name="do" value="submit"/>
                <div class="form-group">
                    <label class="col-sm-12">发卡邮件模板</label>
                    <div class="col-sm-12">
                        <textarea id="CardSendEmailTemplate" name="CardSendEmailTemplate">
                            <?php
                            $filePath = ROOT . '/template/default/CardSendEmailTemplate.html';
                            if (!file_exists($filePath)) {
                                echo '发卡模板不存在，请联系管理员更新系统或手动修改发卡模板';
                            } else {
                                $fileContent = file_get_contents($filePath);
                                echo $fileContent;
                            }
                            ?>
                        </textarea>
                    </div>
                </div>
                <div class="form-group" style="padding: 0 15px;">
                    <input style="color: #FFF7FB"
                           type="submit" name="submit" value="保存模板" class="btn btn-primary form-control"/>
                    <!--                    <br/>-->
                    <!--                    <br/>-->
                    <!--                    <a href="./set.php?mod=mailcon_reset" class="btn btn-warning btn-block"-->
                    <!--                       onclick="return confirm('确定要重置吗？');">重置模板设置</a><br/>-->
                </div>
            </form>
        </div>
        <div class="panel-footer">
            <font color="green">变量代码：<br/>
                <a href="javascript:void();">[kmdata]</a>&nbsp;卡密内容<br/>
                <a href="javascript:void();">[name]</a>&nbsp;商品名称<br/>
                <a href="javascript:void();">[alert]</a>&nbsp;商品简介<br/>
                <a href="javascript:void();">[date]</a>&nbsp;购买时间<br/>
                <a href="javascript:void();">[email]</a>&nbsp;收信人邮箱<br/></font>
        </div>
    </div>
    <script src="https://cdn.staticfile.org/tinymce/5.2.0/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        $(function ($) {
            var contentDom = tinymce.init({
                plugins: 'formatpainter code',
                toolbar: 'undo redo | formatselect | ' +
                    ' bold italic backcolor | alignleft aligncenter ' +
                    ' alignright alignjustify | bullist numlist outdent indent |' +
                    ' formatpainter code',
                selector: '#CardSendEmailTemplate',
                menubar: false,
                height: 500,
            });
            setTimeout(function () {
                $('a[aria-label="Powered by Tiny"]').parent().remove();
            }, 1000);
        });
    </script>
<?php } ?>
<script>
    $(function ($) {
        $('select[default]').each(function (dom) {
            $(this).val($(this).attr('default')).change();
        });
    });
</script>
