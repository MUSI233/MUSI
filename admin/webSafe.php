<?php

include("../includes/common.php");
$title = '安全中心';
include './head.php';

if ($islogin != 1)
    exit("<script>window.location.href='./login.php';</script>");

$act = $_GET['my'];

$typeList = [
    '所有记录',
    '后台登录',
    '配置修改',
    '商品操作',
    '后台加款',
    '后台扣款',
    '后台退款',
    '免费订单',
    '插件管理',
    '推广分享',
];

?>

<div class="col-sm-12 col-md-10 center-block" style="float: none;margin-bottom: 3rem;">
    <div class="block">
        <div class="block-title">
            <h2>日志记录</h2>
            <form class="form-inline" id="search" method="post" style="margin-bottom: 10px;">
                <div class="form-group" style="margin-right: 10px; margin-left: 15px;">
                    <label for="categoryID"><span>日志类型:</span></label>
                    <select id="categoryID" class="form-control"
                            data-default="<?php echo htmlspecialchars(filterParam($_GET['type'], '所有记录')); ?>">
                        <?php
                        foreach ($typeList as $content)
                            echo '<option value="' . $content . '">' . $content . '</option>';
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="operationTime"><span>操作时间:</span></label>
                    <input class="form-control" style="width: 300px;" type="text" id="operationTime"
                           placeholder="请选择时间段">
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <form action="?">
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>类型</th>
                            <th>参数</th>
                            <th>描述</th>
                            <th>操作时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $limit = 30;
                        $page  = intval(filterParam($_GET['page'], 1));
                        $type  = daddslashes(filterParam($_GET['type'], ''));

                        $where = [];

                        if (!empty($_GET['type']) && $_GET['type'] != '所有记录') {
                            $where['action'] = $type;
                            if (isset($_GET['operation_time']) && !empty($_GET['operation_time'])) {
                                $s_time               = explode(' - ', daddslashes(filterParam($_GET['operation_time'], '')));
                                $where['addtime[<>]'] = [$s_time[0], $s_time[1]];
                            }
                        } else if (isset($_GET['operation_time']) && !empty($_GET['operation_time'])) {
                            $s_time               = explode(' - ', daddslashes(filterParam($_GET['operation_time'], '')));
                            $where['addtime[<>]'] = [$s_time[0], $s_time[1]];
                        }

                        $pages = $DB->count('logs', $where);

                        $where['ORDER'] = ['id' => 'DESC'];
                        $where['LIMIT'] = [(($page - 1) * $limit), $limit];

                        $selectResult = $DB->select('logs', ['id', 'action', 'param', 'result', 'addtime'], $where);
                        foreach ($selectResult as $content) {
                            ?>
                            <tr>
                                <td><?php echo $content['id']; ?></td>
                                <td style="min-width: 80px;"><?php echo $content['action']; ?></td>
                                <td><?php echo $content['param']; ?></td>
                                <td><?php echo $content['result'] ?></td>
                                <td style="width: 160px;"><?php echo $content['addtime']; ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                    <?php
                    $link = '';
                    if (!empty($_GET['type']))
                        $link .= '&type=' . urlencode($_GET['type']);
                    if (!empty($_GET['operation_time']))
                        $link .= '&operation_time=' . urlencode($_GET['operation_time']);
                    echo '<ul class="pagination center-block">';
                    $first = 1;
                    $prev  = $page - 1;
                    $next  = $page + 1;
                    $last  = ceil($pages / $limit);
                    if ($page > 1) {
                        echo '<li><a href="webSafe.php?page=' . $first . $link . '">首页</a></li>';
                        echo '<li><a href="webSafe.php?page=' . $prev . $link . '">&laquo;</a></li>';
                    } else {
                        echo '<li class="disabled"><a>首页</a></li>';
                        echo '<li class="disabled"><a>&laquo;</a></li>';
                    }
                    $start = $page - 10 > 1 ? $page - 10 : 1;
                    $end   = $page + 10 < $last ? $page + 10 : $last;
                    for ($i = $start; $i < $page; $i++)
                        echo '<li><a href="webSafe.php?page=' . $i . $link . '">' . $i . '</a></li>';
                    echo '<li class="disabled"><a>' . $page . '</a></li>';
                    for ($i = $page + 1; $i <= $end; $i++)
                        echo '<li><a href="webSafe.php?page=' . $i . $link . '">' . $i . '</a></li>';
                    if ($page < $last) {
                        echo '<li><a href="webSafe.php?page=' . $next . $link . '">&raquo;</a></li>';
                        echo '<li><a href="webSafe.php?page=' . $last . $link . '">尾页</a></li>';
                    } else {
                        echo '<li class="disabled"><a>&raquo;</a></li>';
                        echo '<li class="disabled"><a>尾页</a></li>';
                    }
                    echo '</ul>';
                    ?>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="./assets/third-party/laydate/laydate.js"></script>
<script>
    $(document).ready(function () {
        var tempDom = $('#categoryID');
        tempDom.change(function () {
            var content = $(this).val();
            var time = $('#operationTime').val();
            window.location.href = './webSafe.php?type=' + content + '&operation_time=' + time;
        });
        tempDom.val(tempDom.attr('data-default'));
    });

    //执行一个laydate实例
    laydate.render({
        elem: '#operationTime' //指定元素
        , type: 'datetime'
        , range: true
        , max: '<?php echo date('Y-m-d'); ?>'
        , theme: 'grid'
        , value: '<?php echo htmlspecialchars(filterParam(urldecode($_GET['operation_time']), '')); ?>'
        , done: function (value, date, endDate) {
            var content = $('#categoryID').val();
            window.location.href = './webSafe.php?type=' + content + '&operation_time=' + value;
        }
    });
</script>