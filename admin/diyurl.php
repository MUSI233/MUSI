<?php
/**
 * 自定义访问URL/POST对接日志
 **/
include("../includes/common.php");
$title = '自定义访问URL/POST对接日志';
include './head.php';

if ($islogin == 1) {
} else exit("<script>window.location.href='./login.php';</script>");
?>
<div class="col-md-12 col-lg-10 center-block" style="float: none;">
    <div class="block">
        <div class="block-title">
            <h2>自定义访问URL/POST对接日志</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                <?php
                    $numrows  = $DB->count('logs', ['action' => '自定义访问URL/POST']);
                    $pagesize = 30;
                    $pages    = ceil($numrows / $pagesize);
                    $page     = isset($_GET['page']) ? intval($_GET['page']) : 1;
                    $offset   = $pagesize * ($page - 1);

                    $rs = $DB->select('logs', ['addtime', 'param', 'result'], [
                        'action' => '自定义访问URL/POST',
                        'ORDER'  => [
                            'id' => 'DESC'
                        ],
                        'LIMIT'  => [$offset, $pagesize]
                    ]);

                if (!empty($rs) || !isset($rs)){

                    foreach ($rs as $res) {
                        $param = json_decode($res['param'],true);
                        $result = unserialize($res['result']);
                        echo '<tr><td><b>时间：</b>' . $param['time'] . '<br/><b>url：</b>' . $param['url'] . '<br/><b>提交参数：</b>' . json_encode($param['goods_param']) . '<br/><b>返回结果：</b>' . json_encode($result) . '</td></tr>';
                    }

                }else{
                    echo '<tr><td><b>暂无数据</b><br/>';
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
            echo '<li><a href="diyurl.php?page=' . $first . $link . '">首页</a></li>';
            echo '<li><a href="diyurl.php?page=' . $prev . $link . '">&laquo;</a></li>';
        } else {
            echo '<li class="disabled"><a>首页</a></li>';
            echo '<li class="disabled"><a>&laquo;</a></li>';
        }
        $start = $page - 10 > 1 ? $page - 10 : 1;
        $end   = $page + 10 < $pages ? $page + 10 : $pages;
        for ($i = $start; $i < $page; $i++)
            echo '<li><a href="diyurl.php?page=' . $i . $link . '">' . $i . '</a></li>';
        echo '<li class="disabled"><a>' . $page . '</a></li>';
        for ($i = $page + 1; $i <= $end; $i++)
            echo '<li><a href="diyurl.php?page=' . $i . $link . '">' . $i . '</a></li>';
        if ($page < $pages) {
            echo '<li><a href="diyurl.php?page=' . $next . $link . '">&raquo;</a></li>';
            echo '<li><a href="diyurl.php?page=' . $last . $link . '">尾页</a></li>';
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
</div>