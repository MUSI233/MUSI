<?php
if (!defined('IN_CRONLITE')) exit();

$clientip = real_ip();

if (isset($_COOKIE["admin_token"])) {
    $token = authcode(daddslashes($_COOKIE['admin_token']), 'DECODE', SYS_KEY);
    list($user, $sid) = explode("\t", $token);
    $session = md5($conf['admin_user'] . $conf['admin_pwd'] . $password_hash);
    if ($session === $sid) {
        $islogin = 1;
    }
}
if (isset($_COOKIE["admin_account_token"])) {
    $token = authcode(daddslashes($_COOKIE['admin_account_token']), 'DECODE', SYS_KEY);
    list($uid, $sid) = explode("\t", $token);

    $result = $DB->get('admin_member', [
        'id', 'password', 'salt', 'whiteUrl'
    ], [
        'id' => intval($uid)
    ]);

    if (!empty($result)) {
        $session = md5($uid . $result['password'] . SYS_KEY);
        if ($session === $sid) {
            $whiteUrl = json_decode($result['whiteUrl'], true);
            if (!empty($whiteUrl)) {
                $scriptName = explode('/', $_SERVER['SCRIPT_NAME']);
                $scriptName = $scriptName[count($scriptName) - 1];
                if (in_array($scriptName, $whiteUrl))
                    exit("<script language='javascript'>alert('页面尚未授权使用！');window.history.back(-1);</script>");
            }
            $islogin = 1;
        }
    }
}

{
    $userrow = [];
    //用户数据 后续需要使用
    do {
        if (isset($_COOKIE['userToken']) && empty($_COOKIE['userToken'])) {
            setcookie('userToken', '', time() - 604800, '/');
            break;
        }
        //如果存在键值 但是内容为空
        $userToken = aesDecrypt($_COOKIE['userToken'], SYS_KEY);
        if ($userToken === false) {
            setcookie('userToken', '', time() - 604800, '/');
            break;
        }
        //解密失败 可能数据被修改
        list($zid, $userToken) = explode(PHP_EOL, $userToken);
        //分割数据内容
        $zid = intval($zid);

        $userrow = $DB->query("select * from `{$dbconfig['dbqz']}_site` where `zid` = :zid for update", [':zid' => $zid])->fetch(2);

        if (!empty($userrow)) {
            $tempData = siteAttr($zid, 'loginToken');
            if ($tempData != $userToken) {
                setcookie('userToken', '', time() - 604800, '/');
                break;
            };
            //如果Token不正确
            if ($userrow['status'] == 1)
                $islogin2 = 1;
        }
    } while (false);
}
//用户登录部分开始