<?php
/**
 * 收支明细
 **/
include("../includes/common.php");
$title = '收支明细';
include './head.php';
if ($islogin == 1) {
} else exit("<script>window.location.href='./login.php';</script>");
?>
<div class="col-md-12 center-block" style="float: none;">
    <?php
    $where = [];
    if (isset($_GET['zid'])) {
        $zid          = intval($_GET['zid']);
        $where['zid'] = $zid;
        $link         = '&zid=' . $zid;
    } else {
        $zid = 0;
    }

    $thtime          = date('Y-m-d') . ' 00:00:00';
    $lastday         = date('Y-m-d', strtotime('-1 day')) . ' 00:00:00';
    $income_today    = $DB->sum('points', 'point', ['AND' => array_merge(['action' => '提成', 'addtime[>]' => $thtime], $where)]);
    $outcome_today   = $DB->sum('points', 'point', ['AND' => array_merge(['action' => '消费', 'addtime[>]' => $thtime], $where)]);
    $recharge_today   = [
        'ali_pay' => $DB->sum('points', 'point', ['AND' => array_merge(['action' => '充值', 'addtime[>]' => $thtime, 'bz[~]' => 'alipay'], $where)]),
        'wx_pay' => $DB->sum('points', 'point', ['AND' => array_merge(['action' => '充值', 'addtime[>]' => $thtime, 'bz[~]' => 'wxpay'], $where)]),
        'qq_pay' => $DB->sum('points', 'point', ['AND' => array_merge(['action' => '充值', 'addtime[>]' => $thtime, 'bz[~]' => 'qqpay'], $where)]),
    ];

    $income_lastday  = $DB->sum('points', 'point', ['AND' => array_merge(['action' => '提成', 'addtime[<>]' => [$lastday, $thtime]], $where)]);
    $outcome_lastday = $DB->sum('points', 'point', ['AND' => array_merge(['action' => '消费', 'addtime[<>]' => [$lastday, $thtime]], $where)]);
    $recharge_lastday = [
        'ali_pay' => $DB->sum('points', 'point', ['AND' => array_merge(['action' => '充值', 'addtime[<>]' => [$lastday, $thtime], 'bz[~]' => 'alipay'], $where)]),
        'wx_pay' => $DB->sum('points', 'point', ['AND' => array_merge(['action' => '充值', 'addtime[<>]' => [$lastday, $thtime], 'bz[~]' => 'wxpay'], $where)]),
        'qq_pay' => $DB->sum('points', 'point', ['AND' => array_merge(['action' => '充值', 'addtime[<>]' => [$lastday, $thtime], 'bz[~]' => 'qqpay'], $where)]),
    ];

    ?>
    <div class="block">
        <div class="modal" align="left" id="search2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span
                                    aria-hidden="true">&times;</span><span
                                    class="sr-only">Close</span></button>
                        <h4 class="modal-title">分类查看</h4>
                    </div>
                    <div class="modal-body">
                        <select name="status" class="form-control">
                            <option value="0">全部</option>
                            <option value="充值">充值</option>
                            <option value="赠送">赠送</option>
                            <option value="消费">消费</option>
                            <option value="购物">购物</option>
                            <option value="提成">提成</option>
                            <option value="加款">加款</option>
                        </select><br/>
                        <button type="button" class="btn btn-primary btn-block" id="search_type_submit">查看</button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="block-title"><h2><?php echo($zid > 0 ? '分站ZID:<b>' . $zid . '</b> ' : '全部分站') ?>收支明细</h2></div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <tbody>
                <tr>
                    <td class="text-center" colspan="2" style="width: 33.33%;"><font color="#808080"><b><span
                                        class="glyphicon glyphicon-tint"></span>今日收益</b></br><?php echo round($income_today, 2) ?>
                            元</font></td>
                    <td class="text-center" colspan="2" style="width: 33.33%;"><font color="#808080"><b><i
                                        class="glyphicon glyphicon-check"></i>今日消费</b></br></span><?php echo round($outcome_today, 2) ?>
                            元</font></td>
                    <td class="text-center" colspan="2" style="width: 33.33%;">
                        <font color="#808080">
                            <b><i class="glyphicon glyphicon-yen"></i>今日充值</b>
                            <br>
                            <span>支付宝：<?php echo round($recharge_today['ali_pay'], 2); ?> 元</span>，
                            <span>微信：<?php echo round($recharge_today['wx_pay'], 2); ?> 元</span>，
                            <span>QQ钱包：<?php echo round($recharge_today['qq_pay'], 2); ?> 元</span>
                        </font>
                    </td>
                </tr>
                <tr>
                    <td class="text-center" colspan="2" style="width: 33.33%;"><font color="#808080"><b><span
                                        class="glyphicon glyphicon-tint"></span>昨日收益</b></br><?php echo round($income_lastday, 2) ?>
                            元</font></td>
                    <td class="text-center" colspan="2" style="width: 33.33%;"><font color="#808080"><b><i
                                        class="glyphicon glyphicon-check"></i>昨日消费</b></br></span><?php echo round($outcome_lastday, 2) ?>
                            元</font></td>
                    <td class="text-center" colspan="2" style="width: 33.33%;">
                        <font color="#808080">
                            <b><i class="glyphicon glyphicon-yen"></i>昨日充值</b>
                            <br>
                            <span>支付宝：<?php echo round($recharge_lastday['ali_pay'], 2); ?> 元</span>，
                            <span>微信：<?php echo round($recharge_lastday['wx_pay'], 2); ?> 元</span>，
                            <span>QQ钱包：<?php echo round($recharge_lastday['qq_pay'], 2); ?> 元</span>
                        </font>
                    </td>
                </tr>
                </tbody>
            </table>
            <a href="#" data-toggle="modal" data-target="#search2" id="search2" class="btn btn-warning">分类查看</a>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>站点ID</th>
                        <th>类型</th>
                        <th>金额</th>
                        <th>详情</th>
                        <th>时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (isset($_GET['status'])) {
                        $where['action'] = filterParam($_GET['status']);
                        $link            = '&status=' . $where['action'];
                    }

                    $numrows = $DB->count('points', $where);

                    $pagesize = 30;
                    $pages    = ceil($numrows / $pagesize);
                    $page     = isset($_GET['page']) ? intval($_GET['page']) : 1;
                    $offset   = $pagesize * ($page - 1);

                    $where['ORDER'] = ['id' => 'DESC'];
                    $where['LIMIT'] = [$offset, $pagesize];

                    $rs = $DB->select('points', '*', $where);


                    foreach ($rs as $res) {
                        echo '<tr><td><b>' . $res['id'] . '</b></td><td><a href="sitelist.php?zid=' . $res['zid'] . '">' . $res['zid'] . '</a></td><td>' . $res['action'] . '</td><td><font color="' . (in_array($res['action'], array('提成', '赠送', '退款', '退回', '充值', '加款')) ? 'red' : 'green') . '">' . $res['point'] . '</font></td><td>' . $res['bz'] . '</td><td>' . $res['addtime'] . '</td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <?php
            echo '<ul class="pagination">';
            $first = 1;
            $prev  = $page - 1;
            $next  = $page + 1;
            $last  = $pages;
            if ($page > 1) {
                echo '<li><a href="record.php?page=' . $first . $link . '">首页</a></li>';
                echo '<li><a href="record.php?page=' . $prev . $link . '">&laquo;</a></li>';
            } else {
                echo '<li class="disabled"><a>首页</a></li>';
                echo '<li class="disabled"><a>&laquo;</a></li>';
            }
            $start = $page - 10 > 1 ? $page - 10 : 1;
            $end   = $page + 10 < $pages ? $page + 10 : $pages;
            for ($i = $start; $i < $page; $i++)
                echo '<li><a href="record.php?page=' . $i . $link . '">' . $i . '</a></li>';
            echo '<li class="disabled"><a>' . $page . '</a></li>';
            for ($i = $page + 1; $i <= $end; $i++)
                echo '<li><a href="record.php?page=' . $i . $link . '">' . $i . '</a></li>';
            if ($page < $pages) {
                echo '<li><a href="record.php?page=' . $next . $link . '">&raquo;</a></li>';
                echo '<li><a href="record.php?page=' . $last . $link . '">尾页</a></li>';
            } else {
                echo '<li class="disabled"><a>&raquo;</a></li>';
                echo '<li class="disabled"><a>尾页</a></li>';
            }
            echo '</ul>';
            #分页
            ?>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        const status = '<?php echo isset($_GET['status']) && !empty($_GET['status']) ? $_GET['status'] : ''; ?>';
        if (status) {
            $('select[name=status]').val(status);
        }
    });
    $("#search_type_submit").click(function () {
        let power = $('select[name=status]').val();
        $("#search2").modal('hide');
        if (power === undefined || power === '0') {
            location.href = '?';
        } else {
            location.href = '?status=' + power;
        }
    });
</script>
