<?php
if (!defined('IN_CRONLITE')) exit();
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
    <title><?php echo $conf['sitename'] ?> - <?php echo $conf['title'] ?></title>
    <meta name="keywords" content="<?php echo $conf['keywords'] ?>">
    <meta name="description" content="<?php echo $conf['description'] ?>">
    <link rel="shortcut icon" href="img/favicon.png">
    <link href="<?php echo $cdnpublic ?>twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="<?php echo $cdnpublic ?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    <link type="text/css" href="/assets/user/css/load.css" rel="stylesheet"/>
    <link rel="stylesheet" href="<?php echo $cdnserver ?>assets/simple/css/plugins.css">
    <link rel="stylesheet" href="<?php echo $cdnserver ?>assets/simple/css/main.css">
    <link rel="stylesheet" href="<?php echo $cdnserver ?>assets/simple/css/oneui.css">
    <link rel="stylesheet" href="<?php echo $cdnserver ?>assets/css/common.css?ver=<?php echo VERSION ?>">
    <script src="<?php echo $cdnpublic ?>modernizr/2.8.3/modernizr.min.js"></script>
    <!--[if lt IE 9]>
    <script src="<?php echo $cdnpublic ?>html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="<?php echo $cdnpublic ?>respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
    <img src="<?php echo $background_image; ?>" alt="Full Background" class="full-bg full-bg-bottom animated pulse "
         ondragstart="return false;" oncontextmenu="return false;">
</head>
<body>
<div class="loading-back" id="sk-three-bounce">
    <div class="sk-three-bounce">
        <div class="sk-child sk-bounce1"></div>
        <div class="sk-child sk-bounce2"></div>
        <div class="sk-child sk-bounce3"></div>
    </div>
</div>
<br/>
<div class="col-xs-12 col-sm-10 col-md-8 col-lg-5 center-block" style="float: none;">
    <!--弹出公告-->
    <div class="modal fade" align="left" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header-tabs">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo $conf['sitename'] ?></h4>
                </div>
                <div class="modal-body">
                    <?php echo $conf['modal'] ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">知道啦</button>
                </div>
            </div>
        </div>
    </div>
    <!--弹出公告-->
    <!--公告-->
    <div class="modal fade" align="left" id="anounce" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span
                                class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">公告</h4>
                </div>
                <div class="modal-body">
                    <?php echo $conf['anounce'] ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>
    <!--公告-->
    <!--查单说明开始-->
    <div class="modal fade" align="left" id="cxsm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">查询内容是什么？该输入什么？</h4>
                </div>
                <li class="list-group-item"><font color="red">请在右侧的输入框内输入您下单时，在第一个输入框内填写的信息</font></li>
                <li class="list-group-item">例如您购买的是QQ名片赞，输入下单的QQ账号即可查询订单</li>
                <li class="list-group-item">例如您购买的是邮箱类商品，需要输入您的邮箱号，输入QQ号是查询不到的</li>
                <li class="list-group-item">例如您购买的是KF商品，需要输入作品链接里“userid=”后面的数字，输入KF号是一般是查询不到的</li>
                <li class="list-group-item">例如您购买的是全民K歌商品，需要输入歌曲链接里“shareuid=”后面的，&amp;前面的一串英文数字，输入歌曲链接是查询不到的</li>
                <li class="list-group-item"><font color="red">如果您不知道下单账号是什么，可以不填写，直接点击查询，则会根据浏览器缓存查询</font></li>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>
    <!--查单说明结束-->
    <div class="widget">
        <!--logo-->
        <div class="widget-content themed-background-flat text-center"
             style="background-image: url(assets/simple/img/userbg.jpg);background-size: 100% 100%;">
            <a href="javascript:void(0)">
                <img src="//q4.qlogo.cn/headimg_dl?dst_uin=<?php echo $conf['kfqq']; ?>&spec=100" alt="Avatar"
                     width="80" alt="avatar"
                     style="height: auto filter: alpha(Opacity=80);-moz-opacity: 0.80;opacity: 0.80;"
                     class="img-circle img-thumbnail img-thumbnail-avatar-1x animated zoomInDown">
            </a>
        </div>
        <img width="100%" src="assets/simple/img/jh3.jpg">
        <!--logo-->
        <!--logo下面按钮-->
        <div class="widget-content themed-background-muted text-center ">
            <div class="btn-group themed-background-muted ">
                <a href="#anounce" data-toggle="modal" class="btn btn-effect-ripple btn-default collapsed "><b><font
                                color="#ff0000"><i class="fa fa-wifi fa-fw"></i> <span
                                    style="font-weight:bold">平台公告</span></font></b></a>
                <?php if ($islogin2 == 1) { ?>
                    <a href="./user/" class="btn btn-effect-ripple btn-default"><i class="glyphicon glyphicon-user"></i>
                        <span style="font-weight:bold">管理后台</span></a>
                <?php } else { ?>
                    <a href="./user/login.php" class="btn btn-effect-ripple btn-default"><i
                                class="glyphicon glyphicon-user"></i> <span style="font-weight:bold">登录</span></a>
                    <a href="./user/reg.php" class="btn btn-effect-ripple btn-default"><i
                                class="glyphicon glyphicon-plus"></i> <span style="font-weight:bold">注册</span></a>
                <?php } ?>
            </div>

            <div id="mustsee" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                <div id="mustsee" class="panel-collapse collapse in" aria-expanded="true" style="">

                </div>
            </div>
        </div>

        <!--免费拉圈-->
        <div class="modal fade" align="left" id="circles" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                                    class="sr-only">Close</span></button>
                        <h4 class="modal-title" id="myModalLabel">免费拉圈圈99+</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon">请输入QQ</div>
                                <input type="text" name="qq" id="qq4" value="" class="form-control" required/>
                            </div>
                        </div>
                        <input type="submit" id="submit_lqq" class="btn btn-primary btn-block" value="立即提交">
                        <div id="result3" class="form-group text-center" style="display:none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    </div>
                </div>
            </div>
        </div>
        <!--免费拉圈-->
    </div>
    <div class="block full2">
        <!--TAB标签-->
        <div class="block-title">

            <ul class="nav nav-tabs" data-toggle="tabs">
                <li style="width: 25%;" align="center" class="active"><a href="#shop" data-toggle="tab"><span
                                style="font-weight:bold"><i class="fa fa-shopping-bag fa-fw"></i> 下单</span></a></li>
                <li style="width: 25%;" align="center"><a href="#search" data-toggle="tab" id="tab-query"><span
                                style="font-weight:bold"><i class="fa fa-search"></i> 查询</span></a></li>
                <li style="width: 25%;" align="center" <?php if ($conf['fenzhan_buy'] == 0){ ?>class="hide"<?php } ?>><a
                            href="#Substation" data-toggle="tab"><span style="font-weight:bold"><font color="#ff0000"><i
                                        class="fa fa-coffee fa-fw"></i> 赚钱</span></font></a></li>
                <li style="width: 25%;" align="center"
                    <?php if ($conf['gift_open'] == 0 || $conf['fenzhan_buy'] == 1){ ?>class="hide"<?php } ?>><a
                            href="#gift" data-toggle="tab"><span style="font-weight:bold"><i
                                    class="fa fa-gift fa-fw"></i> 抽奖</span></a></li>
                <li style="width: 25%;" align="center"
                    <?php if ($conf['iskami'] == 0 || $conf['fenzhan_buy'] == 1 || $conf['gift_open'] == 1){ ?>class="hide"<?php } ?>>
                    <a href="#cardbuy" data-toggle="tab"><span style="font-weight:bold"><i
                                    class="glyphicon glyphicon-th"></i> 卡密</span></a></li>
                <li style="width: 25%;" align="center"><a href="#more" data-toggle="tab"><span style="font-weight:bold"><i
                                    class="fa fa-folder-open"></i> 更多</span></a></li>
            </ul>
        </div>
        <!--TAB标签-->
        <div class="tab-content">
            <!--在线下单-->
            <div class="tab-pane active" id="shop">
                <?php include TEMPLATE_ROOT . 'default/shop.inc.php'; ?>
            </div>
            <!--在线下单-->
            <!--查询订单-->
            <div class="tab-pane" id="search">
                <div class="col-xs-12 well well-sm animation-pullUp"
                     <?php if (empty($conf['gg_search'])){ ?>style="display:none;"<?php } ?>><?php echo $conf['gg_search'] ?></div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-btn">
                            <select class="form-control" id="searchtype" style="padding: 6px 4px;width:90px">
                                <option value="0">下单账号</option>
                                <option value="1">订单号</option>
                            </select>
                        </div>
                        <input type="text" name="qq" id="qq3" value="<?php echo $qq ?>" class="form-control"
                               placeholder="请输入要查询的内容（留空则显示最新订单）"
                               onkeydown="if(event.keyCode==13){submit_query.click()}" required/>
                        <span class="input-group-btn"><a href="#cxsm" data-toggle="modal" class="btn btn-warning"><i
                                        class="glyphicon glyphicon-exclamation-sign"></i></a></span>
                    </div>
                </div>
                <input type="submit" id="submit_query" class="btn btn-primary btn-block" value="立即查询">
                <div id="result2" class="form-group" style="display:none;">
                    <center><small><font color="#ff0000">手机用户可以左右滑动</font></small></center>
                    <div class="table-responsive">
                        <table class="table table-vcenter table-condensed table-striped">
                            <thead>
                            <tr>
                                <th>下单账号</th>
                                <th>商品名称</th>
                                <th>数量</th>
                                <th class="hidden-xs">购买时间</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody id="list">
                            </tbody>
                        </table>
                    </div>
                </div>
                <br/>
            </div>
            <!--查询订单-->
            <!--开通分站-->
            <div class="tab-pane" id="Substation">
                <table class="table table-borderless table-pricing">
                    <tbody>
                    <tr class="active">
                        <td>
                            <h4><i class="fa fa-cny fa-fw"></i><strong><?php echo $conf['fenzhan_price'] ?>元</strong> /
                                <i class="fa fa-cny fa-fw"></i><strong><?php echo $conf['fenzhan_price2'] ?>
                                    元</strong><br><small>普及版 / 专业版两种分站供你选择</small></h4>
                        </td>
                    </tr>
                    <tr>
                        <td>无聊时可以赚点零花钱</td>
                    </tr>
                    <tr>
                        <td>还可以锻炼自己销售口才</td>
                    </tr>
                    <tr>
                        <td>宝妈、学生等网络兼职首选</td>
                    </tr>
                    <tr>
                        <td>分站满<?php echo $conf['tixian_min']; ?>元即可申请提现</td>
                    </tr>
                    <tr>
                        <td><strong>轻轻松松推广日赚100+不是梦</td>
                    </tr>
                    <tr class="active">
                        <td>
                            <a href="#userjs" data-toggle="modal" class="btn btn-effect-ripple  btn-info"><i
                                        class="fa fa-th-list"></i><span class="btn-ripple animate"
                                                                        style="height: 100px; width: 100px; top: -34.4px; left: 2.58749px;"></span>
                                功能介绍</a>
                            <a href="user/regsite.php" target="_blank" class="btn btn-effect-ripple  btn-danger"><i
                                        class="fa fa-arrow-right"></i> 马上开通</a>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">
                            <small><em>* 欢迎加入网赚大家庭！</em></small>
                        </td>
                    </tr>
                    </tbody>
                </table>
                </table>
            </div>
            <!--开通分站-->
            <!--抽奖-->
            <div class="tab-pane" id="gift">
                <div class="panel-body text-center">
                    <div id="roll">点击下方按钮开始抽奖</div>
                    <hr>
                    <p>
                        <a class="btn btn-info" id="start" style="display:block;">开始抽奖</a>
                        <a class="btn btn-danger" id="stop" style="display:none;">停止</a>
                    </p>
                    <div id="result"></div>
                    <br/>
                    <div class="giftlist" style="display:none;"><strong>最近中奖记录</strong>
                        <ul id="pst_1"></ul>
                    </div>
                </div>
            </div>
            <!--抽奖-->
            <!--更多-->
            <div class="tab-pane" id="more">
                <div class="row">
                    <div class="col-sm-6<?php if ($conf['gift_open'] == 0) { ?> hide<?php } ?>">
                        <a href="#gift" data-toggle="tab" class="widget">
                            <div class="widget-content themed-background-info text-right clearfix" style="color: #fff;">
                                <div class="widget-icon pull-left">
                                    <i class="fa fa-gift"></i>
                                </div>
                                <h2 class="widget-heading h3">
                                    <strong>抽奖</strong>
                                </h2>
                                <span>在线抽奖领取免费商品</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6<?php if (empty($conf['appurl']) || $conf['gift_open'] == 1 || $is_fenzhan) { ?> hide<?php } ?>">
                        <a href="<?php echo $conf['appurl']; ?>" target="_blank" class="widget">
                            <div class="widget-content themed-background-info text-right clearfix" style="color: #fff;">
                                <div class="widget-icon pull-left">
                                    <i class="fa fa-cloud-download"></i>
                                </div>
                                <h2 class="widget-heading h3">
                                    <strong>APP下载</strong>
                                </h2>
                                <span>下载APP，下单更方便</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6<?php if (empty($conf['invite_tid'])) { ?> hide<?php } ?>">
                        <a href="./?mod=invite" target="_blank" class="widget">
                            <div class="widget-content themed-background-warning text-right clearfix"
                                 style="color: #fff;">
                                <div class="widget-icon pull-left">
                                    <i class="fa fa-paper-plane-o"></i>
                                </div>
                                <h2 class="widget-heading h3">
                                    <strong>免费领赞</strong>
                                </h2>
                                <span>推广本站免费领取名片赞</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6<?php if (empty($conf['daiguaurl'])) { ?> hide<?php } ?>">
                        <a href="./?mod=daigua" class="widget">
                            <div class="widget-content themed-background-success text-right clearfix"
                                 style="color: #fff;">
                                <div class="widget-icon pull-left">
                                    <i class="fa fa-rocket"></i>
                                </div>
                                <h2 class="widget-heading h3">
                                    <strong>QQ等级代挂</strong>
                                </h2>
                                <span>管理自己的QQ代挂</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6<?php if (empty($conf['chatframe'])) { ?> hide<?php } ?>">
                        <a href="#chat" data-toggle="tab" class="widget">
                            <div class="widget-content themed-background-danger text-right clearfix"
                                 style="color: #fff;">
                                <div class="widget-icon pull-left">
                                    <i class="fa fa-comments"></i>
                                </div>
                                <h2 class="widget-heading h3">
                                    <strong>在线聊天</strong>
                                </h2>
                                <span>你我更亲近</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6<?php if ($conf['iskami'] == 0) { ?> hide<?php } ?>">
                        <a href="#cardbuy" data-toggle="tab" class="widget">
                            <div class="widget-content themed-background-warning text-right clearfix"
                                 style="color: #fff;">
                                <div class="widget-icon pull-left">
                                    <i class="fa fa-credit-card"></i>
                                </div>
                                <h2 class="widget-heading h3">
                                    <strong>卡密下单</strong>
                                </h2>
                                <span>卡密下单方便快捷</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <a href="./user/" target="_blank" class="widget">
                            <div class="widget-content themed-background-info text-right clearfix" style="color: #fff;">
                                <div class="widget-icon pull-left">
                                    <i class="fa fa-certificate"></i>
                                </div>
                                <h2 class="widget-heading h3">
                                    <strong>分站后台</strong>
                                </h2>
                                <span>登录分站后台</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <!--更多-->
            <!--版本介绍-->
            <div class="modal fade" align="left" id="userjs" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel">版本介绍</h4>
                        </div>
                        <div class="block">
                            <div class="table-responsive">
                                <table class="table table-borderless table-vcenter">
                                    <thead>
                                    <tr>
                                        <th style="width: 100px;">功能</th>
                                        <th class="text-center" style="width: 20px;">普及版/专业版</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr class="active">
                                        <td>专属代刷平台</td>
                                        <td class="text-center">
                                            <span class="btn btn-effect-ripple btn-xs btn-success"><i
                                                        class="fa fa-check"></i></span>
                                            <span class="btn btn-effect-ripple btn-xs btn-success"><i
                                                        class="fa fa-check"></i></span>
                                        </td>
                                    </tr>
                                    <tr class="">
                                        <td>三种在线支付接口</td>
                                        <td class="text-center">
                                            <span class="btn btn-effect-ripple btn-xs btn-success"><i
                                                        class="fa fa-check"></i></span>
                                            <span class="btn btn-effect-ripple btn-xs btn-success"><i
                                                        class="fa fa-check"></i></span>
                                        </td>
                                    </tr>
                                    <tr class="success">
                                        <td>专属网站域名</td>
                                        <td class="text-center">
                                            <span class="btn btn-effect-ripple btn-xs btn-success"><i
                                                        class="fa fa-check"></i></span>
                                            <span class="btn btn-effect-ripple btn-xs btn-success"><i
                                                        class="fa fa-check"></i></span>
                                        </td>
                                    </tr>
                                    <tr class="">
                                        <td>赚取用户提成</td>
                                        <td class="text-center">
                                            <span class="btn btn-effect-ripple btn-xs btn-success"><i
                                                        class="fa fa-check"></i></span>
                                            <span class="btn btn-effect-ripple btn-xs btn-success"><i
                                                        class="fa fa-check"></i></span>
                                        </td>
                                    </tr>
                                    <tr class="info">
                                        <td>赚取下级分站提成</td>
                                        <td class="text-center">
                                            <span class="btn btn-effect-ripple btn-xs btn-danger"><i
                                                        class="fa fa-close"></i></span>
                                            <span class="btn btn-effect-ripple btn-xs btn-success"><i
                                                        class="fa fa-check"></i></span>
                                        </td>
                                    </tr>
                                    <tr class="">
                                        <td>设置商品价格</td>
                                        <td class="text-center">
                                            <span class="btn btn-effect-ripple btn-xs btn-success"><i
                                                        class="fa fa-check"></i></span>
                                            <span class="btn btn-effect-ripple btn-xs btn-success"><i
                                                        class="fa fa-check"></i></span>
                                        </td>
                                    </tr>
                                    <tr class="warning">
                                        <td>设置下级分站商品价格</td>
                                        <td class="text-center">
                                            <span class="btn btn-effect-ripple btn-xs btn-danger"><i
                                                        class="fa fa-close"></i></span>
                                            <span class="btn btn-effect-ripple btn-xs btn-success"><i
                                                        class="fa fa-check"></i></span>
                                        </td>
                                    </tr>
                                    <tr class="">
                                        <td>搭建下级分站</td>
                                        <td class="text-center">
                                            <span class="btn btn-effect-ripple btn-xs btn-danger"><i
                                                        class="fa fa-close"></i></span>
                                            <span class="btn btn-effect-ripple btn-xs btn-success"><i
                                                        class="fa fa-check"></i></span>
                                        </td>
                                    </tr>
                                    <tr class="danger">
                                        <td>赠送专属精致APP</td>
                                        <td class="text-center">
                                            <span class="btn btn-effect-ripple btn-xs btn-danger"><i
                                                        class="fa fa-close"></i></span>
                                            <span class="btn btn-effect-ripple btn-xs btn-success"><i
                                                        class="fa fa-check"></i></span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <center style="color: #b2b2b2;"><small><em>* 自己的能力决定着你的收入！</em></small></center>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--版本介绍-->
            <!--卡密下单-->
            <div class="tab-pane" id="cardbuy">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">输入卡密</div>
                        <input type="text" name="km" id="km" value="" class="form-control"
                               onkeydown="if(event.keyCode==13){submit_checkkm.click()}" required/>
                    </div>
                </div>
                <input type="submit" id="submit_checkkm" class="btn btn-primary btn-block" value="检查卡密">
                <div id="km_show_frame" style="display:none;">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">商品名称</div>
                            <input type="text" name="name" id="km_name" value="" class="form-control" disabled/>
                        </div>
                    </div>
                    <div id="km_inputsname"></div>
                    <div id="km_alert_frame" class="alert alert-success animation-pullUp"
                         style="display:none;font-weight: bold;"></div>
                    <input type="submit" id="submit_card" class="btn btn-primary btn-block" value="立即购买">
                    <div id="result1" class="form-group text-center" style="display:none;">
                    </div>
                </div>
                <br/>
            </div>
            <!--卡密下单-->
            <!--聊天-->
            <div class="tab-pane" id="chat">
                <?php echo $conf['chatframe'] ?>
            </div>
            <!--聊天-->
        </div>
    </div>
    <!--关于我们弹窗-->
    <div class="modal fade" align="left" id="customerservice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">客服与帮助</h4>
                </div>
                <div class="modal-body" id="accordion">
                    <div class="panel panel-default" style="margin-bottom: 6px;">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">为什么订单显示已完成了却一直没到账？</a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="panel-collapse in" style="height: auto;">
                            <div class="panel-body">
                                订单显示（已完成）就证明已经提交到服务器内！并不是订单已刷完。<br>
                                如果长时间没到账请联系客服处理！<br>
                                订单长时间显示（待处理）请联系客服！
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default" style="margin-bottom: 6px;">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"
                                   class="collapsed">QQ会员/钻类等什么时候到账？</a>
                            </h4>
                        </div>
                        <div id="collapseTwo" class="panel-collapse collapse" style="height: 0px;">
                            <div class="panel-body">
                                下单后的48小时内到账（会员或钻全部都是一样48小时内到账）！<br>
                                如果超过48小时，请联系客服退款或补单，提供QQ号码！
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default" style="margin-bottom: 6px;">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree"
                                   class="collapsed">卡密/CDK没有发送我的邮箱？</a>
                            </h4>
                        </div>
                        <div id="collapseThree" class="panel-collapse collapse" style="height: 0px;">
                            <div class="panel-body">没有收到请检查自己邮箱的垃圾箱！也可以去查单区：输入自己下单时填写的邮箱进行查单。<br>
                                查询到订单后点击（详细）就可以看到自己购买的卡密/cdk！
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default" style="margin-bottom: 6px;">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseFourth"
                                   class="collapsed">已付款了没有查询到我订单？</a>
                            </h4>
                        </div>
                        <div id="collapseFourth" class="panel-collapse collapse" style="height: 0px;">
                            <div class="panel-body" style="margin-bottom: 6px;">联系客服处理，请提供（付款详细记录截图）（下单商品名称）（下单账号）<br>直接把三个信息发给客服，然后等待客服回复处理（请不要发抖动窗口或者QQ电话）！
                            </div>
                        </div>
                    </div>
                    <ul class="list-group" style="margin-bottom: 0px;">
                        <li class="list-group-item">
                            <div class="media">
                                <span class="pull-left thumb-sm"><img
                                            src="//q4.qlogo.cn/headimg_dl?dst_uin=<?php echo $conf['kfqq'] ?>&spec=100"
                                            alt="..." class="img-circle img-thumbnail img-avatar"></span>
                                <div class="pull-right push-15-t">
                                    <a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $conf['kfqq'] ?>&site=qq&menu=yes"
                                       target="_blank" class="btn btn-sm btn-info">联系</a>
                                </div>
                                <div class="pull-left push-10-t">
                                    <div class="font-w600 push-5">订单售后客服</div>
                                    <div class="text-muted"><b>QQ：<?php echo $conf['kfqq'] ?></b>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            想要快速回答你的问题就请把问题描述讲清楚!<br>
                            下单账号+业务名称+问题，直奔主题，按顺序回复!<br>
                            有问题直接留言，请勿抖动语音否则直接无视。<br>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!--关于我们弹窗-->
    <div class="block">
        <!--网站日志-->
        <div class="row text-center" <?php if ($conf['hide_tongji'] == 1 || $conf['order_set'] == 1){ ?>style="display:none;"<?php } ?>>

            <div class="col-xs-4">
                <h5 class="widget-heading"><small>订单总数</small><br><a href="javascript:void(0)"
                                                                     class="themed-color-flat"><span
                                id="count_orders"></span>条</a></h5>
            </div>
            <div class="col-xs-4">
                <h5 class="widget-heading"><small>今日订单</small><br><a href="javascript:void(0)"
                                                                     class="themed-color-flat"><span
                                id="count_orders2"></span>条</a></h5>
            </div>
            <div class="col-xs-4">
                <h5 class="widget-heading"><small>运营天数</small><br><a href="javascript:void(0)"
                                                                     class="themed-color-flat"><span
                                id="count_yxts"></span>天</a></h5>
            </div>
        </div>
        <!--网站日志-->
        <!--底部导航-->
        <div class="block-content text-center border-t">
            <p><span style="font-weight:bold"><?php echo $conf['sitename'] ?> <i class="fa fa-heart text-danger"></i> 2019 | </span><a
                        class="" href="#customerservice" style="font-weight:bold" data-toggle="modal">客服与帮助</span></a>
            </p>
        </div>
        <!--底部导航-->
    </div>

</div>
<!--音乐代码-->
<div id="audio-play" <?php if (empty($conf['musicurl'])){ ?>style="display:none;"<?php } ?>>
    <div id="audio-btn" class="on" onclick="audio_init.changeClass(this,'media')">
        <audio loop="loop" src="<?php echo $conf['musicurl'] ?>" id="media" preload="preload"></audio>
    </div>
</div>
<!--音乐代码-->

<script src="<?php echo $cdnpublic ?>jquery/1.12.4/jquery.min.js"></script>
<script src="<?php echo $cdnpublic ?>jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
<script src="<?php echo $cdnpublic ?>twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="<?php echo $cdnpublic ?>jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script src="<?php echo $cdnpublic ?>layer/2.3/layer.js"></script>
<script src="<?php echo $cdnserver ?>assets/appui/js/app.js"></script>
<script type="text/javascript">
    var isModal =<?php echo empty($conf['modal']) ? 'false' : 'true';?>;
    var homepage = true;
    var hashsalt =<?php echo $addsalt_js?>;
    $(function () {
        $("img.lazy").lazyload({effect: "fadeIn"});
    });
</script>
<script src="assets/js/main.js?ver=<?php echo VERSION ?>"></script>
<script src="/assets/user/js/load.js"></script>
</body>
</html>