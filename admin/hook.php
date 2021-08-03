<?php
include_once '../includes/common.php';
$title = '钩子管理';
include_once './head.php';
if (1 != $islogin) {
    exit("<script>window.location.href='./login.php';</script>");
}
// 检查是否初始化插件功能
if (empty($conf['is_init_plugin'])) {
    $is_init = !checkPluginInit();
} else {
    $is_init = false;
}
// 未初始化，加载初始化视图
if ($is_init) { ?>
    <div class="col-md-12 center-block" style="float: none;">
        <div class="block">
            <div class="alert alert-warning">监测到您的钩子未初始化，点击下方按钮即可启动钩子功能</div>
            <div style="text-align: center;">
                <button class="btn btn-primary" id="initPlugin">初始化钩子功能</button>
            </div>
        </div>
    </div>
    <script>
        $('#initPlugin').click(function () {
            const that = $(this);
            that.attr('disabled', true).text('正在初始化...');
            layer.load(2);
            $.post('ajax.php?act=init_plugin', {'k': 'AFme9qhWkaGz0mF3qPoO4jQO5ukYXybN'}).done(function (res) {
                layer.closeAll('loading');
                that.removeAttr('disabled').text('初始化钩子功能');
                if (res['code'] === 0) {
                    return layer.msg(res['msg'], {icon: 1}, function () {
                        location.reload();
                    });
                }
                layer.msg(res['msg'], {icon: 2});
            }).error(function () {
                layer.closeAll('loading');
                that.removeAttr('disabled').text('初始化钩子功能');
                layer.msg('系统异常', {icon: 5});
            });
        });
    </script>
<?php
    exit;
}
?>
<div class="col-md-12 center-block" style="float: none;">
    <div class="block">
        <div class="block-title clearfix">
            <form onsubmit="return false" class="form-inline" id="formSearch">
                <div class="form-group">
                    <label class="control-label" style="margin: 10px 15px 9px;" for="kw">搜索钩子</label>
                    <input type="text" id="kw" class="form-control" name="kw" placeholder="钩子名称">
                </div>
                <button type="submit" class="btn btn-primary">搜索</button>
            </form>
        </div>
        <div id="listTable">
            <form name="form1" id="form1">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-vcenter">
                        <thead>
                        <tr>
                            <th>序号</th>
                            <th>名称</th>
                            <th>描述</th>
                            <th>对应插件</th>
                        </tr>
                        </thead>
                        <tbody id="pluginsTable">

                        </tbody>
                    </table>
                </div>
            </form>
            <div id="tablePage"></div>
        </div>
    </div>
</div>
<script>
    // 监听表单提交
    $('#formSearch').submit(function () {
        const kw = $('#kw').val();
        loadList(kw);
    });

    // 加载列表数据
    function loadList(kw = '', p = 1) {
        layer.load(2);
        $.ajax({
            url: 'ajax.php?act=hook_page_query'
            ,method: 'GET'
            ,data: {
                'keyWords': kw,
                'page': p,
                'limit': 20
            }
            , dataType: 'json'
            ,success: function (res) {
                if (res['status'] === 0) {
                    let html = '';
                    const list = res['items'];
                    for (let i in list) {
                        if (!list.hasOwnProperty(i)) {
                            continue;
                        }
                        html += `<tr>
                            <td>${parseInt(i) + 1}</td>
                            <td>${list[i]['name']}</td>
                            <td>${list[i]['hook_remarks']}</td>
                            <td>${list[i]['addons'] ? list[i]['addons'] : ''}</td>`;
                        html += `</tr>`;
                    }
                    if (list.length === 0) {
                        $('#pluginsTable').html(`<tr><td colspan="8" style="text-align: center;"><span style="color: #c9302c;">即将上线...</span></td></tr>`);
                    } else {
                        $('#pluginsTable').html(html);
                    }
                    $('#tablePage').html(res['page']);
                    $('.table-page').click(function () {
                        const page = $(this).data('page');
                        const kw = $('#kw').val();
                        loadList(kw, page);
                    });
                } else {
                    layer.msg(res['msg'] ? res['msg'] : '加载数据异常', {icon: 2});
                }
                layer.closeAll('loading');
            }
            ,error: function () {
                layer.closeAll('loading');
                layer.msg('加载数据异常', {icon: 7});
            }
        });
    }

    $(document).ready(function () {
        loadList();
    });
</script>
