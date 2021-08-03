<?php
/**
 * 检查版本更新
 **/
include '../includes/common.php';
$title = '检查版本更新';
include './head.php';

if ($islogin != 1) exit('<script>window.location.href="./login.php";</script>');

{
    ignore_user_abort();
    set_time_limit(0);
}

/**
 * 解压文件函数
 * @param $source //zip压缩文件
 * @param $target //解压到的目标目录
 * @return array //处理结果
 */
function zipDecompression($source, $target)
{
    $zipModel = new ZipArchive();
    $resource = $zipModel->open($source);
    if ($resource !== true) {
        switch ($resource) {
            case $zipModel::ER_EXISTS:
                $errorMsg = 'File already exists.';
                break;
            case $zipModel::ER_INCONS:
                $errorMsg = 'Zip archive inconsistent.';
                break;
            case $zipModel::ER_INVAL:
                $errorMsg = 'Invalid argument.';
                break;
            case $zipModel::ER_MEMORY:
                $errorMsg = 'Malloc failure.';
                break;
            case $zipModel::ER_NOENT:
                $errorMsg = 'No such file.';
                break;
            case $zipModel::ER_NOZIP:
                $errorMsg = 'Not a zip archive.';
                break;
            case $zipModel::ER_OPEN:
                $errorMsg = 'Can\'t open file.';
                break;
            case $zipModel::ER_READ:
                $errorMsg = 'Read error.';
                break;
            case $zipModel::ER_SEEK:
                $errorMsg = 'Seek error.';
                break;
            default:
                $errorMsg = json_encode(error_get_last());
                break;
        }
        return [false, $errorMsg];
    }
    $flag = $zipModel->extractTo($target);
    $zipModel->close();
    if ($flag === false) {
        return [false, '解压文件失败，请重试'];
    }
    return [true, '解压文件成功'];
}

function deldir($dir)
{
    if (!is_dir($dir)) return false;
    $dh = opendir($dir);
    while ($file = readdir($dh)) {
        if ($file != "." && $file != "..") {
            $fullpath = $dir . "/" . $file;
            if (!is_dir($fullpath)) {
                unlink($fullpath);
            } else {
                deldir($fullpath);
            }
        }
    }
    closedir($dh);
    if (rmdir($dir)) {
        return true;
    } else {
        return false;
    }
}

$scriptpath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$scriptpath = substr($scriptpath, 0, strrpos($scriptpath, '/'));
$admin_path = substr($scriptpath, strrpos($scriptpath, '/') + 1);
?>
<div class="col-xs-12 col-sm-10 col-lg-8 center-block" style="float: none;">
    <div class="block">
        <div class="block-title"><h3 class="panel-title">检查更新</h3></div>
        <div>
            <?php
            $act = isset($_GET['act']) ? $_GET['act'] : null;
            switch ($act) {
                default:
                    $res = updateVersion();
                    if ($res['status'] == 0) {
                        echo '<div class="alert alert-danger">' . $res['msg'] . '</div>';
                    } else if ($res['status'] == 1) {
                        echo '<div class="alert alert-success">' . $res['msg'] . '</div>';
                    } else if ($res['status'] == 2) {
                        echo '<div class="alert alert-info"> 最新版本号 ：<span style="font-weight: 600;">' . $res['data']['title'] . '</span>';
                        echo '<ol>';
                        foreach ($res['data']['specify'] as $content) {
                            echo '<li>' . $content . '</li>';
                        }
                        echo '</ol>';
                        echo '</div>';
                    }

                    echo '<hr/>';

                    if ($res['status'] == 2) { // 待更新状态
                        if (!class_exists('ZipArchive') || defined('SAE_ACCESSKEY') || defined('SAE_ACCESSKEY')) {
                            ?>
                            您的空间不支持自动更新，请手动下载更新包并覆盖到程序根目录！<br/>
                            更新包下载：<a href="<?= $res['data']['downloadPath']; ?>" class="btn btn-sm btn-primary">点击下载</a>
                            <?php
                        } else {
                            echo '<a href="update.php?act=do" class="btn btn-primary btn-block">立即更新到最新版本</a>';
                        }
                    }
                    break;
                case 'do':
                    $res = updateVersion();
                    if (isset($res['status'])) {
                        if ($res['status'] == 0)
                            exit('获取更新信息失败 => ' . $res['msg']);
                    }
                    $downloadPath = $res['data']['downloadPath'];
                    $savePath = ROOT . 'Archive.zip';

                    if (copy($downloadPath, $savePath) === false) {
                        $lastError = error_get_last();
                        exit('无法下载更新文件！<a href="update.php">返回上一步</a><br><p>错误类型：' . $lastError['type'] . '，' . $lastError['message'] . '</p>');
                    }
                    $unzipResult = zipDecompression($savePath, ROOT);
                    if ($unzipResult[0] === false) {
                        if (file_exists($savePath))
                            unlink($savePath);
                        exit('解压失败 => ' . $unzipResult[1] . '<br><a href="update.php">返回上级</a>');
                    }

                    if ($admin_path != 'admin') { //修改后台地址
                        deldir(ROOT . $admin_path);
                        rename(ROOT . 'admin', ROOT . $admin_path);
                    }
                    if (function_exists('opcache_reset'))
                        @opcache_reset();
                    if (!empty($res['data']['sql'])) {
                        $sql = $res['data']['sql'];
                        $t = 0;
                        $e = 0;
                        $error = '';
                        $DB->query("ALTER DATABASE `{$dbconfig['dbname']}` CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'");
                        for ($i = 0; $i < count($sql); $i++) {
                            if (trim($sql[$i]) == '')
                                continue;
                            $executeResult = executeSql($sql[$i], $dbconfig['dbqz'] . '_');
                            if ($executeResult !== false) {
                                ++$t;
                            } else {
                                ++$e;
                                $error .= $DB->error() . '<br/>';
                            }
                        }
                        saveUpdateLog(['update_sql' => $error, 'auto_update' => autoUpdate(), 'new_version' => $res['data']['title']]);
                        if (IS_DEBUG)
                            echo $error;
                        $updateResult = '<br/>数据库更新成功。SQL成功' . $t . '句';
                        echo '程序更新成功！' . $updateResult . '<br>';
                        echo '<a href="./">返回首页</a>';
                    }
                    unlink($savePath);

                    break;
            }
            echo '</div></div>';
            ?>
        </div>
    </div>
</div>