<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace ds;

use Psr\Log\LoggerInterface;

class Log implements LoggerInterface
{
    const EMERGENCY = 'emergency';
    const ALERT     = 'alert';
    const CRITICAL  = 'critical';
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const INFO      = 'info';
    const DEBUG     = 'debug';
    const SQL       = 'sql';

    /**
     * 日志信息
     * @var array
     */
    protected $log = [];

    /**
     * 配置参数
     * @var array
     */
    protected $config = [
        // 日志记录方式，内置 file socket 支持扩展
        'type'        => 'File',
        // 日志保存目录
        'path'        => '',
        // 日志记录级别
        'level'       => ['error', 'info', 'log'],
        // 单文件日志写入
        'single'      => false,
        // 独立日志级别
        'apart_level' => [],
        // 最大日志文件数量
        'max_files'   => 0,
        // 是否关闭日志写入
        'close'       => false,
    ];

    /**
     * 日志写入驱动
     * @var object
     */
    protected $driver;

    /**
     * 日志授权key
     * @var string
     */
    protected $key;

    /**
     * 是否允许日志写入
     * @var bool
     */
    protected $allowWrite = true;

    public function __construct($config = [])
    {
        $this->init($config);
    }

    public static function __callStatic($name, $args)
    {
        array_push($args, $name);
        return call_user_func_array('\\ds\\Log::record', $args);
    }

    /**
     * 日志初始化
     * @access public
     * @param  array $config
     * @return $this
     */
    public function init($config = [])
    {
        $type = isset($config['type']) ? $config['type'] : 'File';

        $this->config = $config;

        unset($config['type']);

        if (!empty($config['close'])) {
            $this->allowWrite = false;
        }
        if ($type == 'File') {
            $this->driver = new \driver\File($config);
        } else {
            exit('暂不支持其它方式的日志写入');
        }

        return $this;
    }

    /**
     * 获取日志信息
     * @access public
     * @param  string $type 信息类型
     * @return array
     */
    public function getLog($type = '')
    {
        return $type ? $this->log[$type] : $this->log;
    }

    /**
     * 记录日志信息
     * @access public
     * @param  mixed  $msg       日志信息
     * @param  string $type      日志级别
     * @param  array  $context   替换内容
     * @return $this
     */
    public function record($msg, $type = 'info', array $context = [])
    {
        if (!$this->allowWrite) {
            return;
        }

        if (is_string($msg) && !empty($context)) {
            $replace = [];
            foreach ($context as $key => $val) {
                $replace['{' . $key . '}'] = $val;
            }

            $msg = strtr($msg, $replace);
        }

        if (PHP_SAPI == 'cli') {
            // 命令行日志实时写入
            $this->write($msg, $type, true);
        } else {
            $this->log[$type][] = $msg;
        }

        return $this;
    }

    /**
     * 清空日志信息
     * @access public
     * @return $this
     */
    public function clear()
    {
        $this->log = [];

        return $this;
    }

    /**
     * 当前日志记录的授权key
     * @access public
     * @param  string  $key  授权key
     * @return $this
     */
    public function key($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * 检查日志写入权限
     * @access public
     * @param  array  $config  当前日志配置参数
     * @return bool
     */
    public function check($config)
    {
        if ($this->key && !empty($config['allow_key']) && !in_array($this->key, $config['allow_key'])) {
            return false;
        }

        return true;
    }

    /**
     * 关闭本次请求日志写入
     * @access public
     * @return $this
     */
    public function close()
    {
        $this->allowWrite = false;
        $this->log        = [];

        return $this;
    }

    /**
     * 保存调试信息
     * @access public
     * @return bool
     */
    public function save()
    {
        if (empty($this->log) || !$this->allowWrite) {
            return true;
        }

        if (!$this->check($this->config)) {
            // 检测日志写入权限
            return false;
        }

        $log = [];

        foreach ($this->log as $level => $info) {
            if (!IS_DEBUG && 'debug' == $level) {
                continue;
            }

            if (empty($this->config['level']) || in_array($level, $this->config['level'])) {
                $log[$level] = $info;

//                $this->app['hook']->listen('log_level', [$level, $info]);
            }
        }

        $result = $this->driver->save($log, true);

        if ($result) {
            $this->log = [];
        }

        return $result;
    }

    /**
     * 实时写入日志信息 并支持行为
     * @access public
     * @param  mixed  $msg   调试信息
     * @param  string $type  日志级别
     * @param  bool   $force 是否强制写入
     * @return bool
     */
    public function write($msg, $type = 'info', $force = false)
    {
        // 封装日志信息
        if (empty($this->config['level'])) {
            $force = true;
        }

        if (true === $force || in_array($type, $this->config['level'])) {
            $log[$type][] = $msg;
        } else {
            return false;
        }

        // 监听log_write
//        $this->app['hook']->listen('log_write', $log);

        // 写入日志
        return $this->driver->save($log, false);
    }

    /**
     * 记录日志信息
     * @access public
     * @param  string $level     日志级别
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        $this->record($message, $level, $context);
    }

    /**
     * 记录emergency信息
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function emergency($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录警报信息
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function alert($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录紧急情况
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function critical($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录错误信息
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function error($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录warning信息
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function warning($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录notice信息
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function notice($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录一般信息
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function info($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录调试信息
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function debug($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录sql信息
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function sql($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }

    public function __debugInfo()
    {
        $data = get_object_vars($this);
        unset($data['app']);

        return $data;
    }
}
