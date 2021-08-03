<?php
// 必须继承插件接口类
use plugin\PluginInterface;

class testAdvertPlugin extends PluginInterface
{
    // 插件的基础信息
    public $info = [
        'name'        => 'testAdvert', // 插件标识
        'title'       => '抽奖活动(测试版)', // 插件名称
        'description' => '在首页显示抽奖活动链接按钮', // 插件简介
        'status'      => 0,  // 默认安装状态
        'author'      => '尐 〃呆萌',
        'version'     => '1.0.0',
    ];

    /**
     * @var Smarty
     */
    private static $smarty;

    /**
     * @var testAdvertModel
     */
    private static $model;

    public function _initialize()
    {
        if (!$this::$smarty instanceof Smarty) {
            $this::$smarty = new \Smarty();
            $this::$smarty->force_compile = true;
        }
        if (!$this::$model instanceof testAdvertModel) {
            if (class_exists('testAdvertModel')) {
                $this::$model = new \testAdvertModel();
            }
        }
    }

    public function homeLoaded()
    {
        global $conf;
        $this::$smarty->assign('static', '/assets/plugins/' . $this->info['name']);
        if ($conf['template'] == 'official') { // 当存在该模板，跳到指定模板页
            $url = '/?mod=activity';
        } else {
            $url = 'https://interaction.clotfun.online/horse?appkey=9084968c8ee86eeeb87eea4d2a603d7e&adSpaceKey=3baabd1fae1fc951f0b8d241ace4fdf2&from=H5&1=1';
        }
        $this::$smarty->assign('location_url', $url);
        $this::$smarty->display($this->plugin_path . 'template/index.html');
    }

    public function install()
    {
        $flag = $this::$model->install();
        if ($flag) { // 安装成功后务必更新缓存
            global $CACHE;
            $CACHE->clear('plugins');
        }
        $old_path = str_replace(['/', '\\'], DS, $this->plugin_path . 'template/assets');
        $new_path = str_replace(['/', '\\'], DS, ROOT . 'assets/plugins/' . $this->getName());
        return xCopy($old_path, $new_path); // 复制静态资源文件
    }

    public function uninstall()
    {
        $flag = $this::$model->uninstall();
        if ($flag) { // 卸载成功后务必更新缓存
            global $CACHE;
            $CACHE->clear('plugins');
        }
        $path = str_replace(['/', '\\'], DS, ROOT . 'assets/plugins/' . $this->getName());
        recursiveDelete($path); // 删除静态资源文件
        return true;
    }

    public function enable()
    {
        global $CACHE;
        $CACHE->clear('plugins');
        return true;
    }

    public function disable()
    {
        global $CACHE;
        $CACHE->clear('plugins');
        return true;
    }

    public function saveConfig($data = [])
    {
        // TODO: Implement saveConfig() method.
    }
}