<?php
include '../includes/common.php';
$title = '模板管理';
include './head.php';
if (1 != $islogin) {
    exit("<script>window.location.href='./login.php';</script>");
}

$mod          = $_GET['mod'];
$templateName = $_GET['name'];

if ($mod == 'saveTemplateSet') {
    $templateName = $_POST['templateName'];
    if (empty($templateName)) {
        showmsgAuto('系统繁忙，请稍后再试', 3);
    }
    saveSetting('template_config_' . $templateName, json_encode($_POST['config']));
    $CACHE->update();
    showmsgAuto('保存模板配置成功', 1, 'set.php?mod=template');
} else if ($mod == 'set' && !empty($templateName)) {
    if (!Template::exists($templateName)) {
        showmsgAuto('模板不存在', 3);
    }

    $ret = Template::getConfigSet($templateName);
    if ($ret['ret'] != 1) {
        if ($ret['msg'] == '事件不存在') {
            $ret['msg'] = '此模板无需配置参数';
        }
        showmsgAuto($ret['msg'], 4);
    }
    $ret = $ret['data'];


    $tempData = $conf['template_config_' . $templateName];
    if (!is_null($tempData)) {
        $tempData = json_decode($tempData, true);
        foreach ($tempData as $key => $value) {
            if (isset($ret['config'][$key]))
                $ret['config'][$key]['value'] = $value;
        }
    }
    ?>
    <link href="assets/css/common.css" rel="stylesheet" type="text/css"/>
    <div class="col-md-12 center-block" style="float: none;">
        <div class="block">
            <div>
                <form action="template.php?mod=saveTemplateSet" method="post" style="line-height: 30px;margin:10px;"
                      autocomplete="off">
                    <div class="main-title cf">
                        <div class="addoncfg-title">模板配置 [ <?php echo $ret['title']; ?> ] <?php echo $ret['name']; ?></div>
                    </div>
                    <?php if (is_array($ret['config'])): if (count($ret['config']) == 0) : echo ""; else: foreach ($ret['config'] as $o_key => $form): ?>
                        <div class="form-item cf">
                            <?php if (isset($form['title'])): ?>
                                <label class="item-label">
                                    <span style="font-weight: bold;"><?php echo(isset($form['title']) && ($form['title'] !== '') ? $form['title'] : ''); ?></span>

                                </label>
                            <?php endif;
                            switch ($form['type']): case "tips": ?>
                                <div>
                                    <?php echo $form['value']; ?>
                                </div>
                                <?php break;
                                case "text": ?>
                                    <div>
                                        <input type="text" name="config[<?php echo $o_key; ?>]" class="text input-large"
                                               value="<?php echo $form['value']; ?>"
                                               style="width:400px;"><?php if (isset($form['tips'])) { ?>
                                            <span><?php echo $form['tips']; ?></span><?php } ?>
                                    </div>
                                    <?php break;
                                case "password": ?>
                                    <div>
                                        <input type="password" name="config[<?php echo $o_key; ?>]"
                                               class="text input-large" value="<?php echo $form['value']; ?>">
                                    </div>
                                    <?php break;
                                case "hidden": ?>
                                    <input type="hidden" name="config[<?php echo $o_key; ?>]"
                                           value="<?php echo $form['value']; ?>">
                                    <?php break;
                                case "radio": ?>
                                    <div class="layui-form">
                                        <?php if (is_array($form['options'])): if (count($form['options']) == 0) : echo ""; else: foreach ($form['options'] as $opt_k => $opt): ?>
                                            <label class="radio">
                                                <input type="radio" name="config[<?php echo $o_key; ?>]"
                                                       value="<?php echo $opt_k; ?>" <?php if ($form['value'] == $opt_k): ?> checked<?php endif; ?>
                                                       title="<?php echo $opt; ?>">
                                            </label>
                                        <?php endforeach; endif; else: echo "";endif; ?>
                                    </div>
                                    <?php break;
                                case "checkbox": ?>
                                    <div>
                                        <?php if (is_array($form['options'])): if (count($form['options']) == 0) : echo ""; else: foreach ($form['options'] as $opt_k => $opt): ?>
                                            <label class="checkbox">
                                                <?php
                                                is_null($form["value"]) && $form["value"] = array();
                                                ?>
                                                <input type="checkbox" name="config[<?php echo $o_key; ?>][]"
                                                       value="<?php echo $opt_k; ?>"
                                                       <?php if (in_array(($opt_k), is_array($form['value']) ? $form['value'] : explode(',', $form['value']))): ?>checked<?php endif; ?>><?php echo $opt; ?>
                                            </label>
                                        <?php endforeach; endif; else: echo "";endif; ?>
                                    </div>
                                    <?php break;
                                case "select": ?>
                                    <div>
                                        <select name="config[<?php echo $o_key; ?>]">
                                            <?php if (is_array($form['options'])): if (count($form['options']) == 0) : echo ""; else: foreach ($form['options'] as $opt_k => $opt): ?>
                                                <option value="<?php echo $opt_k; ?>" <?php if ($form['value'] == $opt_k): ?> selected<?php endif; ?>><?php echo $opt; ?></option>
                                            <?php endforeach; endif; else: echo "";endif; ?>
                                        </select>
                                    </div>
                                    <?php break;
                                case "textarea": ?>
                                    <div>
                                        <label class="textarea input-large">
                                            <textarea name="config[<?php echo $o_key; ?>]"
                                                      style="width:500px;height:200px;"><?php echo $form['value']; ?></textarea>
                                        </label>
                                    </div>
                                    <?php break;
                                case "group": ?>
                                    <ul class="tab-nav nav">
                                        <?php if (is_array($form['options'])): $i = 0;
                                            $__LIST__ = $form['options'];
                                            if (count($__LIST__) == 0) : echo "";
                                            else: foreach ($__LIST__ as $key => $li): $mod = ($i % 2);
                                                ++$i; ?>
                                                <li data-tab="tab<?php echo $i; ?>"
                                                    <?php if ($i == '1'): ?>class="current" <?php endif; ?> ><a
                                                            href="javascript:void(0);"><?php echo $li['title']; ?></a>
                                                </li>
                                            <?php endforeach; endif; else: echo "";endif; ?>
                                        <div style="clear: both;"></div>
                                    </ul>
                                    <div class="tab-content">
                                        <?php if (is_array($form['options'])): $i = 0;
                                            $__LIST__ = $form['options'];
                                            if (count($__LIST__) == 0) : echo "";
                                            else: foreach ($__LIST__ as $key => $tab): $mod = ($i % 2);
                                                ++$i; ?>
                                                <div id="tab<?php echo $i; ?>"
                                                     class="tab-pane <?php if ($i == '1'): ?>in<?php endif; ?> tab<?php echo $i; ?>">
                                                    <?php if (is_array($tab['options'])): if (count($tab['options']) == 0) : echo ""; else: foreach ($tab['options'] as $o_tab_key => $tab_form): ?>
                                                        <label class="item-label">
                                                            <span style="font-weight: bold;"><?php echo(isset($tab_form['title']) && ($tab_form['title'] !== '') ? $tab_form['title'] : ''); ?></span>
                                                            <?php if (isset($tab_form['tip'])): ?>
                                                                <span class="check-tips"><?php echo $tab_form['tip']; ?></span>
                                                            <?php endif; ?>
                                                        </label>
                                                        <div>
                                                            <?php switch ($tab_form['type']): case "tips": ?>
                                                                <div>
                                                                    <?php echo $form['value']; ?>
                                                                </div>
                                                                <?php break;
                                                                case "text": ?>
                                                                    <input type="text"
                                                                           name="config[<?php echo $o_tab_key; ?>]"
                                                                           class="text input-large"
                                                                           value="<?php echo $tab_form['value']; ?>"
                                                                           style="width:400px;">
                                                                    <?php break;
                                                                case "password": ?>
                                                                    <input type="password"
                                                                           name="config[<?php echo $o_tab_key; ?>]"
                                                                           class="text input-large"
                                                                           value="<?php echo $tab_form['value']; ?>">
                                                                    <?php break;
                                                                case "hidden": ?>
                                                                    <input type="hidden"
                                                                           name="config[<?php echo $o_tab_key; ?>]"
                                                                           value="<?php echo $tab_form['value']; ?>">
                                                                    <?php break;
                                                                case "radio":
                                                                    if (is_array($tab_form['options'])): if (count($tab_form['options']) == 0) : echo "";
                                                                    else: foreach ($tab_form['options'] as $opt_k => $opt): ?>
                                                                        <label class="layui-form radio">
                                                                            <input type="radio"
                                                                                   name="config[<?php echo $o_tab_key; ?>]"
                                                                                   value="<?php echo $opt_k; ?>" <?php if ($tab_form['value'] == $opt_k): ?> checked<?php endif; ?>
                                                                                   title="<?php echo $opt; ?>">
                                                                        </label>
                                                                    <?php endforeach; endif;
                                                                    else: echo "";endif;
                                                                    break;
                                                                case "checkbox":
                                                                    if (is_array($tab_form['options'])): if (count($tab_form['options']) == 0) : echo "";
                                                                    else: foreach ($tab_form['options'] as $opt_k => $opt): ?>
                                                                        <label class="checkbox">
                                                                            <?php
                                                                            is_null($tab_form["value"]) && $tab_form["value"] = array();
                                                                            ?>
                                                                            <input type="checkbox"
                                                                                   name="config[<?php echo $o_tab_key; ?>][]"
                                                                                   value="<?php echo $opt_k; ?>" <?php if (in_array(($opt_k), is_array($tab_form['value']) ? $tab_form['value'] : explode(',', $tab_form['value']))): ?> checked<?php endif; ?>><?php echo $opt; ?>
                                                                        </label>
                                                                    <?php endforeach; endif;
                                                                    else: echo "";endif;
                                                                    break;
                                                                case "select": ?>
                                                                    <select name="config[<?php echo $o_tab_key; ?>]">
                                                                        <?php if (is_array($tab_form['options'])): if (count($tab_form['options']) == 0) : echo ""; else: foreach ($tab_form['options'] as $opt_k => $opt): ?>
                                                                            <option value="<?php echo $opt_k; ?>" <?php if ($tab_form['value'] == $opt_k): ?> selected<?php endif; ?>><?php echo $opt; ?></option>
                                                                        <?php endforeach; endif; else: echo "";endif; ?>
                                                                    </select>
                                                                    <?php break;
                                                                case "textarea": ?>
                                                                    <label>
                                                                        <textarea
                                                                                name="config[<?php echo $o_tab_key; ?>]"><?php echo $tab_form['value']; ?></textarea>
                                                                    </label>
                                                                    <?php break; ?>

                                                                <?php endswitch; ?>
                                                        </div>
                                                    <?php endforeach; endif; else: echo "";endif; ?>
                                                </div>
                                            <?php endforeach; endif; else: echo "";endif; ?>
                                    </div>
                                    <?php break; ?>
                                <?php endswitch; ?>
                        </div>
                    <?php endforeach; endif; else: echo "";endif; ?>
                    <div class="form-item cf wst-bottombar" style='padding-left:130px;padding-top:10px'>
                        <input type="hidden" name="templateName" value="<?php echo filterParam($templateName); ?>"
                               readonly/>
                        <button type="submit" class="btn submit-btn ajax-post btn-primary btn-mright"><i
                                    class="fa fa-check"></i> 确定
                        </button>&nbsp;&nbsp;&nbsp;&nbsp;
                        <button type="button" class='btn btn-default' onclick="location.href='set.php?mod=template';"><i
                                    class="fa fa-angle-double-left"></i> 返回
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $(".tab-nav li").click(function () {
                var self = $(this), target = self.data("tab");
                self.addClass("current").siblings(".current").removeClass("current");
                //window.location.hash = "#" + target.substr(3);
                $(".tab-pane.in").removeClass("in");
                $("." + target).addClass("in");
            }).filter("[data-tab=tab" + window.location.hash.substr(1) + "]").click();
        });
    </script>
    <?php
}
