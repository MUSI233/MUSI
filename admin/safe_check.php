<?php
require '../includes/common.php';
$title = '安全检查';
require './head.php';
if (1 != $islogin) {
    exit("<script type='text/javascript'>window.location.href='./login.php';</script>");
}
$act = filterParam($_GET['act']);
// 需要检查的路径
$pathname = ROOT . 'assets' . DS . 'img';
// 缓存名称
$cache_k = 'safe_check';
if (empty($act)) {
    $safe_check = unserialize($CACHE->read($cache_k));
    $safe_check['time'] = isset($safe_check['time']) ? $safe_check['time'] : time();
    $day = date('d', time() - $safe_check['time']);
    $day = intval($day) - 1;
?>
<style>
    .status-logo {
        text-align: center;
        font-size: 100px;
        color: #00a680;
    }
    .status-logo-color-1 {
        color: #5FB878;
    }
    .status-logo-color-2 {
        color: #FFB800;
    }
    .alert {
        text-align: center;
    }
    .start-check {
        text-align: center;
    }
    .check-day {
        font-size: 30px;
    }
</style>
<div class="col-sm-12 col-md-10 center-block" style="float: none;">
    <div class="block">
        <?php
        if ($day <= 0) {
            echo '<div class="status-logo"><i class="glyphicon glyphicon-grain"></i></div>';
            echo '<div class="alert alert-warning" style="background-color: #00a680;">你已检查过，记得定期检查哦</div>';
        } elseif ($day >= 1 && $day <= 7) {
            echo '<div class="status-logo status-logo-color-1"><i class="glyphicon glyphicon-grain"></i></div>';
            echo '<div class="alert alert-warning" style="background-color: #5FB878;">距上次检查已<span class="check-day"> '.$day.' </span>天</div>';
        } else {
            echo '<div class="status-logo status-logo-color-2"><i class="glyphicon glyphicon-grain"></i></div>';
            echo '<div class="alert alert-warning" style="background-color: #FFB800;">距上次检查已<span class="check-day"> '.$day.' </span>天，建议重新检查</div>';
        }
        ?>
        <div class="start-check">
            <a href="./safe_check.php?act=check" class="btn btn-success" style="background-color: #00a680;border-color: #00a680;width: 100%;"><i class="glyphicon glyphicon-search"></i>&nbsp;开始检查</a>
        </div>

    </div>
</div>
<?php
} elseif ($act == 'check') {
if (!is_dir($pathname)) {
    mkdir($pathname, 0755, true);
    showmsgAuto('文件路径异常或不存在，已经为您自动创建，请刷新页面后重试', 4);
}
// 查询
$file_list = ds\FileScanner::starch($pathname);
// 过滤
$illegalFileList = ds\FileScanner::filterExtension($file_list, ['png', 'jpg', 'jpeg']);
$CACHE->save($cache_k, ['time' => time()]);
?>
<style>
    .check-btn {
        margin-bottom: 10px;
    }
    .check-icon {
        text-align: center;
        margin-bottom: 20px;
    }
</style>
<div class="col-sm-12 col-md-10 center-block" style="float: none;">
    <div class="block">
        <div class="block-title clearfix">
            <?php
            $file_count = count($illegalFileList);
            if ($file_count > 0) {
                echo '<h2 style="color: red;">系统检测出以下路径存在 <b>'.$file_count.'</b> 个异常文件</h2>';
            } else {
                echo '<h2>安全性概览</h2>';
            }
            ?>
        </div>
        <?php
        echo $file_count > 0 ? '<div class="check-btn"><a href="./safe_check.php?act=all" class="btn btn-danger"><i class="glyphicon glyphicon-flash"></i>&nbsp;一键清除</a></div>' : '';
        ?>
        <?php if ($file_count > 0): ?>
        <span>路径：</span><span style="color: red;"><?php echo $pathname;?></span>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>序号</th>
                        <th>文件名</th>
                        <th>文件类型</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(is_array($illegalFileList)): if( count($illegalFileList)==0 ) : echo '';else: foreach($illegalFileList as $key => $item): ?>
                    <tr>
                        <td><?php echo $key + 1; ?></td>
                        <td>
                            <span><?php echo htmlspecialchars(filterParam($item['filename']));?></span>
                        </td>
                        <td><?php echo htmlspecialchars(filterParam($item['extension'], '未知')); ?></td>
                        <td>
                            <a href="./safe_check.php?act=del&file=<?php echo htmlspecialchars(filterParam($item['basename'])) ;?>" class="btn btn-xs btn-danger"
                               onclick="return confirm('你确实要清除此文件吗？');">
                                清除
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; endif; else: echo '';endif; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="check-icon">
                <i class="glyphicon glyphicon-ok" style="font-size: 100px; color: #00a65a;"></i>
            </div>
            <div class="alert alert-success" style="background-color: #00a680;">当前没有威胁</div>
        <?php endif; ?>
    </div>
</div>
<?php
} elseif ($act == 'del') {
    $file_name = filterParam($_GET['file']);
    if (empty($file_name)) {
        showmsgAuto('非法操作', 4);
    }
    $file_name = $pathname . DS .$file_name;
    if (!file_exists($file_name)) {
        showmsgAuto('文件不存在', 4);
    }
    if (php_uname('s') == 'Linux') { // 当系统是 Linux 时
        if (function_exists('exec')) {
            exec('chattr -i ' . $file_name);
        }
    }
    if (unlink($file_name)) {
        showmsgAuto('删除成功', 1);
    }
    showmsgAuto('删除失败', 4);
} else if ($act == 'all') {
    // 查询
    $file_list = ds\FileScanner::starch($pathname);
    // 过滤
    $illegalFileList = ds\FileScanner::filterExtension($file_list, ['png', 'jpg', 'jpeg']);
    $err_num = 0; // 删除成功数
    $ok_num = 0; // 删除失败数
    $not_write = 0; // 目录下只读文件数
    $del_not_write_ok = 0; // 删除只读文件成功数 文件属性 + i
    $del_not_write_err = 0; // 删除只读文件失败数
    foreach ($illegalFileList as $v) {
        $file_name = $v['dirname'] . DIRECTORY_SEPARATOR . $v['basename'];
        if (!is_writable($file_name)) {
            $not_write++;
            if (php_uname('s') == 'Linux') { // 当系统是 Linux 时
                if (function_exists('exec')) {
                    $res = exec('chattr -i ' . $file_name);
                    if (empty($res)) {
                        $del_not_write_ok++;
                    }
                } else {
                    $del_not_write_err++;
                }
            }
        }
        if (@unlink($file_name))
            $ok_num++;
        else
            $err_num++;
    }
    $msg = "成功清除{$ok_num}个文件";
    if ($err_num > 0)
        $msg .= "；失败{$err_num}个文件";
    if ($not_write > 0)
        $msg .= "；其中包含{$not_write}个只读文件";
    if ($del_not_write_ok > 0)
        $msg .= "；成功清除{$not_write}个只读文件";
    if ($del_not_write_err > 0) {
        $msg .= "；失败{$not_write}个";
        $msg .= "；该操作失败，可能是PHP禁用了某些函数";
    }
    showmsgAuto($msg, ($err_num > 0 || $del_not_write_err > 0) ? 4 : 1);
}