<?php

/**

 * 自助下单系统

 **/

include '../includes/common.php';

//待处理工单条数
$workorder_total = $DB->count('workorder', [
    "status" => 0
]);

$title = $allow_domain_auth_title . '自助下单系统管理中心';

include './head.php';

if ($islogin != 1)

    exit("<script>window.location.href='./login.php';</script>");

?>

<?php

$mysqlVersion = $DB->query('select VERSION();')->fetch(PDO::FETCH_ASSOC)['VERSION()'];

$sec_msg = getSafeMessage();

//数组显示安全问题

$checkUpdate = getCheckUpdateUrl();
$getAdUrl = getAdUrl();

//无更新地址则无需更新

?>

<div>
<div>
<div class="col-sm-6 col-lg-4">

    <a href="javascript:void(0)" class="widget">

        <div class="widget-content widget-content-mini text-right clearfix">

            <div class="widget-icon pull-left themed-background">

                <i class="fa fa-list-ol text-light-op"></i>

            </div>

            <h2 class="widget-heading h3">

                <strong><span id="count1"></span></strong>

            </h2>

            <span class="text-muted">订单总数</span>

        </div>

    </a>

</div>

<div class="col-sm-6 col-lg-4">

    <a href="javascript:void(0)" class="widget">

        <div class="widget-content widget-content-mini text-right clearfix">

            <div class="widget-icon pull-left themed-background-success">

                <i class="fa fa-first-order text-light-op"></i>

            </div>

            <h2 class="widget-heading h3 text-success">

                <strong><span id="count3"></span></strong>

            </h2>

            <span class="text-muted">待处理订单</span>

        </div>

    </a>

</div>

<div class="col-sm-6 col-lg-4">

    <a href="javascript:void(0)" class="widget">

        <div class="widget-content widget-content-mini text-right clearfix">

            <div class="widget-icon pull-left themed-background-warning">

                <i class="fa fa-briefcase text-light-op"></i>

            </div>

            <h2 class="widget-heading h3 text-warning">

                <strong>+ <span id="count4"></span></strong>

            </h2>

            <span class="text-muted">今日订单数</span>

        </div>

    </a>

</div>

<div class="col-sm-6 col-lg-4">

    <a href="javascript:void(0)" class="widget">

        <div class="widget-content widget-content-mini text-right clearfix">

            <div class="widget-icon pull-left themed-background-danger">

                <i class="fa fa-rmb text-light-op"></i>

            </div>

            <h2 class="widget-heading h3 text-danger">

                <strong>$ <span id="count5"></span></strong>

            </h2>

            <span class="text-muted">今日交易额</span>

        </div>

    </a>

</div>

<div class="col-sm-6 col-lg-4">

    <a href="javascript:void(0)" class="widget">

        <div class="widget-content widget-content-mini text-right clearfix">

            <div class="widget-icon pull-left themed-background-danger">

                <i class="fa fa-rmb text-light-op"></i>

            </div>

            <h2 class="widget-heading h3 text-danger">

                <strong>$ <span id="count100"></span></strong>

            </h2>

            <span class="text-muted">今日收益</span>

        </div>

    </a>

</div>


<div class="col-sm-6 col-lg-4">

    <a href="javascript:void(0)" class="widget">

        <div class="widget-content widget-content-mini text-right clearfix">

            <div class="widget-icon pull-left themed-background-danger">

                <i class="fa fa-rmb text-light-op"></i>

            </div>

            <h2 class="widget-heading h3 text-danger">

                <strong>$ <span id="count101"></span></strong>

            </h2>

            <span class="text-muted">昨日收益</span>

        </div>

    </a>

</div>
<div class="ad"></div>
</div>

<div class="row">

    <div class="col-sm-6 col-lg-8">

        <div class="widget">

            <a href="workorder.php" style="color: red;">
                <div class="widget-content border-bottom">
                    待处理工单总条数:<?php  echo '&nbsp;&nbsp;' . $workorder_total ?>
                </div>
            </a>

            <div class="widget-content border-bottom">

                一周交易与订单统计

            </div>

            <div class="widget-content border-bottom themed-background-muted">

                <div id="chart-classic-dash" style="height: 393px;">

                </div>

            </div>

            <div class="widget-content widget-content-full">

                <div class="row text-center">

                    <div class="col-xs-4 push-inner-top-bottom border-right">

                        <h4 class="widget-heading"><i class="fa fa-qq text-dark push-bit"></i>&nbsp;QQ钱包交易额<br>

                            <center><span id="count12"></span>元</center>

                        </h4>

                    </div>

                    <div class="col-xs-4 push-inner-top-bottom">

                        <h4 class="widget-heading"><i class="fa fa-wechat text-dark push-bit"></i>&nbsp;微信交易额<br>

                            <center><span id="count13"></span>元</center>

                        </h4>

                    </div>

                    <div class="col-xs-4 push-inner-top-bottom border-left">

                        <h4 class="widget-heading"><i class="fa fa-credit-card text-dark push-bit"></i>&nbsp;支付宝交易额<br>

                            <center><span id="count14"></span>元</center>

                        </h4>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="col-sm-4">

        <div class="widget">

            <div class="widget-content border-bottom">

                <span class="pull-right text-muted"><i class="fa fa-circle"></i></span>

                分站统计

            </div>

            <div class="widget-content widget-content-full-top-bottom border-bottom">

                <div class="row text-center">

                    <div class="col-xs-6 push-inner-top-bottom border-right">

                        <h4 class="widget-heading"><i class="fa fa-sitemap text-dark push"></i>&nbsp;分站/用户总数<br>

                            <center><span id="count6"></span>个</font></center>

                        </h4>

                    </div>

                    <div class="col-xs-6 push-inner-top-bottom">

                        <h4 class="widget-heading"><i class="fa fa-cloud text-dark push"></i>&nbsp;今日新开分站<br>

                            <center><span id="count7"></span>个</center>

                        </h4>

                    </div>

                </div>

            </div>

            <div class="widget-content widget-content-full-top-bottom border-bottom">

                <div class="row text-center">

                    <div class="col-xs-6 push-inner-top-bottom border-right">

                        <h4 class="widget-heading"><i class="fa fa-rmb text-dark push"></i>&nbsp;今日分站提成<br>

                            <center><span id="count8"></span>元</center>

                        </h4>

                    </div>

                    <div class="col-xs-6 push-inner-top-bottom">

                        <h4 class="widget-heading"><i class="fa fa-money text-dark push"></i>&nbsp;待处理提现<br>

                            <center><span id="count11"></span>元</center>

                        </h4>

                    </div>

                </div>

            </div>

            <div class="widget-content widget-content-full">

                <div class="row text-center">

                    <div class="col-xs-6 push-inner-top-bottom border-right">

                        <h4 class="widget-heading"><i class="glyphicon glyphicon-gbp push"></i>&nbsp;今日分站总资金<br>

                            <span id="today_total">0.00</span>元

                        </h4>

                    </div>

                    <div class="col-xs-6 push-inner-top-bottom">

                        <h4 class="widget-heading"><i class="glyphicon glyphicon-gbp push"></i>&nbsp;昨日分站总资金<br>

                            <span id="yesterday_total">0.00</span>元

                        </h4>

                    </div>

                </div>

            </div>

        </div>

        <div class="widget">

            <div class="widget-content border-bottom">

                <span class="pull-right text-muted"><i class="fa fa-circle"></i></span>

                系统信息

            </div>

            <ul class="nav nav-pills nav-stacked">

                <li>

                    <a href="javascript:">PHP版本：<?php echo PHP_VERSION; ?>

                        <span class="pull-right text-muted">信息</span>

                    </a>

                </li>

                <li>

                    <a href="javascript:">MySQL版本：<?php echo $mysqlVersion; ?>

                        <span class="pull-right text-muted">信息</span>

                    </a>

                </li>

                <li>

                    <a href="javascript:">程序版本：<?php echo $conf['version']; ?>

                        <span class="pull-right text-muted">信息</span>

                    </a>

                </li>

            </ul>

        </div>

    </div>

</div>

<div class="row">

    <div class="col-sm-6 col-lg-8">

        <div class="widget">

            <div class="widget-content border-bottom">

                <span class="pull-right text-muted"><i class="fa fa-check"></i></span>

                安全中心

            </div>

            <ul class="nav nav-pills nav-stacked">

                <?php

                foreach ($sec_msg as $row) {

                    echo '<li>' . $row . '</li>';

                }

                if (count($sec_msg) == 0) echo '<li><span class="btn-sm btn-success">正常</span>&nbsp;暂未发现网站安全问题</li>';

                ?>

            </ul>

        </div>

    </div>

    <div class="col-sm-4">

        <div class="widget">

            <div class="widget-content border-bottom text-dark">

                <span class="pull-right text-muted"><i class="fa fa-check-square"></i></span>

                检测更新

            </div>

            <ul class="nav nav-pills nav-stacked" id="updateInfo">

                <li><a href="javascript:void(0);">请稍后，正在检测更新文件中......<span

                                class="pull-right text-info"><i class="fa fa-spinner fa-spin fa-fw"></i></span></a>

                </li>

            </ul>

        </div>

    </div>

</div>
</div>

<script>

    $(document).ready(function(){

        $('#title').html('正在加载数据中...');

         $.ajax({

            url: '<?php echo $getAdUrl?>',

            type: 'get',

            dataType: 'jsonp',

            jsonpCallback: 'adCallback'

        }).done(function (data){
            if(data['status'] == 0){
	            var html = '';
                $.each(data.data,function(i,v){
	                if(v.type == 'img'){
		                html+='<div class="col-sm-6 col-lg-4" style="margin-top: -10px;"><a class="" style="display:block;width:100%;" href="'+v.href+'" target="_blank"><img style="width:100%;height:35px;" src="'+v.img+'"/></a></div>';
	                }
	                if(v.type == 'text'){
		                html+='<div class="col-sm-6 col-lg-4" style="margin-top: -10px;"><a class="" style="line-height:36px;padding:10px;color:#000;font-size:16px;box-shadow:0px;" href="'+v.href+'" target="_blank">'+v.text+'</a></div>';
	                }
                });
                $('.ad').html(html);
            }    
        });

        $.ajax({

            url: '<?php echo $checkUpdate?>',

            type: 'get',

            dataType: 'jsonp',

            jsonpCallback: 'updateCallback'

        }).done(function (data) {

            var html = '';

            if (data['status'] === 0) {

                html += `<li><a href="javascript:void(0);">${data['msg']}<span class="pull-right text-warning">警告</span></a></li>`;

            } else if (data['status'] === 2) {

                html += `<li><a href="./update.php">${data['msg']}<span class="pull-right text-danger">信息</span><div><span class="text-danger">最新版本</span><span style="margin-left:5px;" class="text-info">${data['data']['title']}</span></div></a></li>${data['data']['desc']}`;

            } else if (data['status'] === 1) {

                html += `<li><a href="javascript:void(0);">${data['msg']}<span class="pull-right text-success">提示</span></a></li>`;

            }

            $('#updateInfo').html(html);

        });

        $.ajax({

            type: "GET",

            url: "ajax.php?act=getcount",

            dataType: 'json',

            success: function (data) {

                $('#title').html('后台管理首页');

                $('#yxts').html(data['yxts']);

                $('#count1').html(data['count1']);

                $('#count2').html(data['count2']);

                $('#count3').html(data['count3']);

                $('#count4').html(data['count4']);

                $('#count5').html(data['count5']);

                $('#count6').html(data['count6']);

                $('#count7').html(data['count7']);

                $('#count8').html(data['count8']);

                // $('#count9').html(data.count9);

                // $('#count10').html(data.count10);

                $('#count11').html(data['count11']);

                $('#count12').html(data['count12']);

                $('#count13').html(data['count13']);

                $('#count14').html(data['count14']);
                
                $('#count100').html(data['count100']);
                
                $('#count101').html(data['count101']);



                $('#today_total').html(data['today_total']);

                $('#yesterday_total').html(data['yesterday_total']);



                var t = $("#chart-classic-dash");

                $.plot(t, [{

                    label: "订单量",

                    data: data.chart.orders,

                    lines: {show: !0, fill: !0, fillColor: {colors: [{opacity: .6}, {opacity: .6}]}},

                    points: {show: !0, radius: 5}

                }, {

                    label: "交易量",

                    data: data.chart.money,

                    lines: {show: !0, fill: !0, fillColor: {colors: [{opacity: .6}, {opacity: .6}]}},

                    points: {show: !0, radius: 5}

                }], {

                    colors: ["#5ccdde", "#454e59"],

                    legend: {show: !0, position: "nw", backgroundOpacity: 0},

                    grid: {borderWidth: 0, hoverable: !0, clickable: !0},

                    yaxis: {show: !1, tickColor: "#f5f5f5", ticks: 3},

                    xaxis: {ticks: data.chart.date, tickColor: "#f9f9f9"}

                });

                var s = null, r = null;

                t.bind("plothover", function (o, t, i) {

                    if (i) {

                        if (s !== i.dataIndex) {

                            s = i.dataIndex;

                            $("#chart-tooltip").remove();

                            var l = (i.datapoint[0], i.datapoint[1]);

                            r = 1 === i.seriesIndex ? "$ <strong>" + l + "</strong>" : 0 === i.seriesIndex ? "<strong>" + l + "</strong> sales" : "<strong>" + l + "</strong> tickets", $('<div id="chart-tooltip" class="chart-tooltip">' + r + "</div>").css({

                                top: i.pageY - 45,

                                left: i.pageX + 5

                            }).appendTo("body").show()

                        }

                    } else {

                        $("#chart-tooltip").remove();

                        s = null

                    }

                });

            }

        });

    })

</script>