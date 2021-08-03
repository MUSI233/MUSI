<?php

namespace extend;

/**
 * 对接发傲
*/

class FaAo
{
    /**
     * 提交出卡：提交订单
     * @param string $url 接口请求网关
     * @param string $username 接口平台用户 ID
     * @param string $password 接口平台 token
     * @param array $formData 下单数据
     * @return array
     */
    public function submit($url, $username, $password, array $formData)
    {
        if (!isset($formData['cid']) || empty($formData['cid'])) {
            return [false, '缺少“分类ID”参数'];
        }
        if (!isset($formData['ce']) || empty($formData['ce'])) {
            return [false, '缺少“收货人”参数'];
        }
        if (!isset($formData['ce_m']) || empty($formData['ce_m'])) {
            return [false, '缺少“收货人手机”参数'];
        }
        if (!isset($formData['ce_a']) || empty($formData['ce_a'])) {
            return [false, '缺少“收货人地址”参数'];
        }
        if (!isset($formData['num']) || empty($formData['num'])) {
            $formData['num'] = 1;
        }
        // 接口地址
        $uri = '/card/public/index.php/api/api/submit_info';
        $requestData = [
            'userid' => intval($username), // 用户 id
            'keys' => trim($password), // 用户 token
            'timestamp' => date('Y-m-d H:i:s'),
            'c_id' => $formData['cid'], // 分类ID
            'ce' => $formData['ce'], // 收货人
            'ce_m' => $formData['ce_m'], // 收货人手机
            'ce_a' => $formData['ce_a'], // 收货人地址
            'num' => $formData['num'], // 出卡数量
        ];
        $requestData['sign'] = $this->sign($requestData, $requestData['keys']);
        $result = self::curl(trim($url) . $uri, [], 'get', $requestData);
        if ($result == false) {
            return [false, $result[1]];
        }
        $result = json_decode($result[1], true);
        if ($result == false) {
            return [false, json_last_error_msg()];
        }
        if (!isset($result['code']) || $result['code'] != 0) {
            return [false, isset($result['message']) ? $result['message'] : '对接站点返回错误，请联系相关人员'];
        }
        return [true, isset($result['msg']) ? $result['msg'] : (isset($result['message']) ? $result['message'] : '成功')];
    }

    /**
     * 获取可用商品分类信息
     * @param string $url 接口请求网关
     * @param string $username 接口平台用户 ID
     * @param string $password 接口平台 token
     * @return array
    */
    public function getClassInfo($url, $username, $password)
    {
        // 接口地址
        $uri = '/card/public/index.php/api/api/cate_info';
        $requestData = [
            'userid' => intval($username), // 用户 id
            'keys' => trim($password), // 用户 token
            'timestamp' => date('Y-m-d H:i:s')
        ];
        $requestData['sign'] = $this->sign($requestData, $requestData['keys']);
        $result = self::curl(trim($url) . $uri, [], 'get', $requestData);
        if ($result == false) {
            return [false, $result[1]];
        }
        $result = json_decode($result[1], true);
        if ($result == false) {
            return [false, json_last_error_msg()];
        }
        if (!isset($result['code']) || $result['code'] != '0000') {
            return [false, isset($result['message']) ? '对接站点返回错误：' . $result['message'] : '对接站点返回错误，请联系相关人员'];
        }
        if (isset($result['result']) && !empty($result['result'])) {
            $result = is_string($result['result']) ? json_decode($result['result'], true) : $result['result'];
            if ($result == false) {
                return [false, '对接站点返回数据异常，错误信息：' . json_last_error_msg() . '请联系相关人员'];
            }
            return [true, $result];
        }
        return [true, '对接站点返回数据异常，请联系相关人员'];
    }

    /**
     * 生成签名
     * @param array $params 请求参数集
     * @param string $key 密钥
     * @return string
    */
    private function sign(array $params, $key)
    {
        ksort($params);
        $str = $key;
        foreach ($params as $k => $v) {
            $str .= $k . '=' . $v . '&';
        }
        $str .= 'key=' . $key;
        return strtoupper(md5($str));
    }

    /**
     * 封装专用函数请求类 v3.1.0
     * @param string $url 请求地址
     * @param array $addHeaders 请求头
     * @param string $requestType 请求类型
     * @param array $requestData 请求数据
     * @param string $postType post类型
     * @return array
     */
    private static function curl($url, array $addHeaders = [], $requestType = 'get', array $requestData = [], $postType = 'x-www-form-urlencoded')
    {
        $headers  = [
            'User-Agent' => 'AliPayApiV3.1.0 Model 2020-04-13'
        ];
        $postType = strtolower($postType);
        if ($requestType == 'get') {
            $tempBuff = '';
            foreach ($requestData as $key => $value) {
                $tempBuff .= urlencode($key) . '=' . urlencode($value) . '&';
            }
            $requestData = trim($tempBuff, '&');
            //保存数据
            $url .= '?' . $requestData;
        }
        //build http query
        if (!empty($addHeaders))
            $headers = array_merge($headers, $addHeaders);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
        curl_setopt($ch, CURLOPT_TIMEOUT, 12);
        //await 时间
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //设置允许302转跳
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        //gzip
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        if ($requestType == 'get') {
            curl_setopt($ch, CURLOPT_HEADER, false);
        } else if ($requestType == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($requestType));
        }
        //处理类型
        if ($requestType != 'get') {
            if ($postType == 'json') {
                $headers['Content-Type'] = 'application/json; charset=utf-8';
                $requestData             = json_encode($requestData);
            } else if ($postType == 'x-www-form-urlencoded') {
                if (!empty($requestData)) {
                    $temp = '';
                    foreach ($requestData as $key => $value) {
                        $temp .= urlencode($key) . '=' . urlencode($value) . '&';
                    }
                    $requestData = substr($temp, 0, strlen($temp) - 1);
                }
                $headers['Content-Type']   = 'application/x-www-form-urlencoded; charset=utf-8';
                $headers['Content-Length'] = strlen($requestData);
            } else if ($postType == 'form-data') {
                foreach ($requestData as $key => &$value) {
                    if (is_string($value) && strlen($value) > 0 && $value[0] === '@' && class_exists('CURLFile')) {
                        $file = substr($value, 1);
                        $file = realpath($file);
                        if (is_file($file)) {
                            $value = new \CURLFile($file);
                        }
                    }
                }
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
        }
        //只要不是get姿势都塞东西给他post

        $httpHeaders = [];

        foreach ($headers as $name => $content) {
            $httpHeaders[] = $name . ': ' . $content;
        }
        //构建header头 改变方式

        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
        $httpCode = 0;
        $errorMsg = '';
        $result   = curl_exec($ch);

        if ($result === false)
            $errorMsg = curl_error($ch);
        else
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = mb_convert_encoding($result, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');

        return [
            $result !== false,
            $result !== false ? $result : $errorMsg,
            $httpCode
        ];

    }
}