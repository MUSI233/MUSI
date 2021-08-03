<?php
/**
 * 关键词管理
 **/
include("../includes/common.php");
if ($islogin != 1)
    exit("<script>window.location.href='./login.php';</script>");

function getParam($data, $default = '')
{
    if (!empty($data))
        return daddslashes($data);
    return $default;
}

$act = isset($_GET['act']) ? $_GET['act'] : '';
if ($act == 'save') {
    $type           = getParam($_POST['type']);
    $id             = getParam($_POST['id']);
    $keyword_name   = getParam($_POST['keyword_name']);
    $keyword_url    = getParam($_POST['keyword_url']);
    if ($type != 'update' && $type != 'add')
        exit(json_encode(['status' => 0, 'msg' => '请求类型有误']));

    $updateData = [
        'keyword_name'   => $keyword_name,
        'keyword_url'  	 => $keyword_url,
        'addtime'     => time()
    ];

    if ($type == 'add')
        $sql = $DB->insert('keyword', $updateData);
    else
        $sql = $DB->update('keyword', $updateData, ['id' => $id, 'LIMIT' => 1]);

    if ($sql->rowCount() > 0)
        exit(json_encode(['status' => 1, 'msg' => '操作成功']));
    else {
        exit(json_encode(['status' => 0, 'msg' => '操作失败 => ' . $DB->error()]));
    }
}

if($act == 'del_some'){
	if(!isset($_POST['ids'])){
		exit('<script>alert("请选中关键词再进行操作");window.location.href = \'./Keywords.php?mod=keywordsList\'</script>');
		die();
	}
	$ids = $_POST['ids'];
	$ids_string = join($ids,',');
    $sql='DELETE FROM shua_keyword WHERE id in ('.$ids_string.')';
    $res = $DB->query($sql);
    if($res){
        exit('<script>alert("删除成功");window.location.href = \'./Keywords.php?mod=keywordsList\'</script>');
    }else{
        exit('<script>alert("删除失败");window.location.href = \'./Keywords.php?mod=keywordsList\'</script>');
    }
}

if($act == 'del_all'){
	$sql='DELETE FROM shua_keyword where id > 0';
	$res = $DB->query($sql);
    if($res !== false){
		exit('<script>alert("删除成功");window.location.href = \'./Keywords.php?mod=keywordsList\'</script>');
	} else {
		exit('<script>alert("删除失败");window.location.href = \'./Keywords.php?mod=keywordsList\'</script>');
	}
}


$title = '未知页面';
switch ($_GET['mod']) {
    case 'keywordsList':
        $title = '关键词列表';
        break;
    case 'keywordsAdd':
        $title = '添加关键词';
        break;
    case 'keywordsUpdate':
        $title = '更新关键词';
        break;
}

include './head.php';
//没登录

if ($_GET['mod'] == 'keywordsList') {
    $searchTitle = getParam(urldecode($_GET['title']), '');
    $page        = getParam($_GET['page'], 1);
    $limit       = 25;

    $where = [];

    if (!empty($searchTitle)) {
        $where['keyword_name[~]'] = $searchTitle;
    }
    $pages = $DB->count('keyword', $where);

    $where['ORDER'] = ['id' => 'DESC'];
    $where['LIMIT'] = [($page - 1) * $limit, $limit];

    $contents = $DB->select('keyword', ['id', 'keyword_name', 'keyword_url', 'addtime'], $where);
    ?>
    <div class="col-md-12 center-block" style="float: none;">
        <div class="block">
            <div class="block-title clearfix">
                <h2>
                    <?php echo $title; ?>
                </h2>
            </div>
            <form action="Keywords.php" method="get">
                <input type="text" name="mod" value="keywordsList" style="display: none;">
                <input type="hidden" name="my" value="search">
                <div class="input-group xs-mb-5">
                    <input type="text" placeholder="请输入搜索关键词" name="title" class="form-control text-center" value="<?php echo $searchTitle ?>" style="width:300px;">
                    <span class="input-group-btn" style="width:auto;">
			            <button type="submit" id="search" class="btn btn-primary">立即搜索</button>
			        </span>
                </div>
            </form>
             <form action="" method="post" id="tableform" style="margin-top:20px">
                <div class="input-group xs-mb-5">
                    <span class="input-group-btn" style="width:auto;">
			            <button type="submit" id="search" class="btn btn-primary table-btn"  data-action="?act=del_some" style="margin-right:20px;">批量删除</button>
			            <button type="submit" id="search" class="btn btn-primary table-btn"  data-action="?act=del_all">全部删除</button>
			        </span>
                </div>
            
            <div id="listTable">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th><input type="checkbox" id="keyword_selectAll" onclick="selectAll()"></th>
                            <th>关键词</th>
                            <th>链接</th>
                            <th>更新时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($contents as $content) { ?>
                            <tr>
                                <td><input class="ky_id" type="checkbox" name="ids[]" value="<?php echo $content['id']; ?>"></td>
                                <td><?php echo strip_tags($content['keyword_name']); ?></td>
                                <td><?php echo strip_tags($content['keyword_url']); ?></td>
                                <td><?php echo date('Y-m-d H:i:s',$content['addtime']);?></td>
                                <td>
                                	<a href="./Keywords.php?mod=keywordsUpdate&id=<?php echo $content['id']; ?>" class="btn btn-info btn-xs">编辑</a>&nbsp;
                                	<a href="./Keywords.php?mod=keywordsDelete&id=<?php echo $content['id']; ?>" class="btn btn-xs btn-danger" onclick="return confirm('你确实要删除此记录吗？');">删除</a></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php
                    $link = '&mod=keywordsList';
                    if (!empty($searchTitle))
                        $link .= '&title=' . urlencode($searchTitle);
                    echo '<ul class="pagination">';
                    $first = 1;
                    $prev  = $page - 1;
                    $next  = $page + 1;
                    $last  = $pages;
                    if ($page > 1) {
                        echo '<li><a href="Keywords.php?page=' . $first . $link . '">首页</a></li>';
                        echo '<li><a href="Keywords.php?page=' . $prev . $link . '">&laquo;</a></li>';
                    } else {
                        echo '<li class="disabled"><a>首页</a></li>';
                        echo '<li class="disabled"><a>&laquo;</a></li>';
                    }
                    $start = $page - 10 > 1 ? $page - 10 : 1;
                    $end   = $page + 10 < $pages ? $page + 10 : $pages;
                    for ($i = $start; $i < $page; $i++)
                        echo '<li><a href="Keywords.php?page=' . $i . $link . '">' . $i . '</a></li>';
                    echo '<li class="disabled"><a>' . $page . '</a></li>';
                    for ($i = $page + 1; $i <= $end; $i++)
                        echo '<li><a href="Keywords.php?page=' . $i . $link . '">' . $i . '</a></li>';
                    if ($page < $pages) {
                        echo '<li><a href="Keywords.php?page=' . $next . $link . '">&raquo;</a></li>';
                        echo '<li><a href="Keywords.php?page=' . $last . $link . '">尾页</a></li>';
                    } else {
                        echo '<li class="disabled"><a>&raquo;</a></li>';
                        echo '<li class="disabled"><a>尾页</a></li>';
                    }
                    echo '</ul>';
                    ?>
                </div>
            </div>
            </form>
        </div>
    </div>
    <script>
	    $('.table-btn').click(function(){
			$('#tableform').attr('action',$(this).data('action'));
		});
	    function selectAll(){
			if($('#keyword_selectAll').is(':checked')){
				$(".ky_id").prop("checked", true); //全部选中
			}else{
				$(".ky_id").prop("checked", false);//全部取消
			}
		}
        function setkeywordsStatus(id, st) {
            layer.load(2);
            $.post('ajax.php?act=change_article_st', {'id': id,'st': st}).success(function (res) {
                layer.closeAll('loading');
                if (res['status'] === 0) {
                    layer.msg(res['msg'], {icon: 1, time: 1000}, function () {
                        location.reload();
                    });
                } else {
                    layer.msg(res['msg'], {icon: 2, time: 1000}, function () {
                        location.reload();
                    });
                }
            }).error(function () {
                layer.closeAll('loading');
            });
        }

    </script>
<?php
} else if ($_GET['mod'] == 'keywordsAdd' || $_GET['mod'] == 'keywordsUpdate') {
    $id             = 0;
    $title          = '';
    $content        = '';
    $author         = '';
    $status         = 1;
    $createTime     = '';
    $seoTitle       = '';
    $seoKeywords    = '';
    $seoDescription = '';
    $articleImg     = '';

    if ($_GET['mod'] == 'keywordsUpdate') {
        $id = intval($_GET['id']);
        if (!empty($id)) {
            $contents = $DB->get('keyword', '*', ['id' => $id]);
        }
    }
    ?>
    <div class="col-md-12 center-block" id="article" data-article-id="<?php echo $id; ?>">
        <div class="block">
            <form method="post" class="clearfix" style="margin-bottom: 1rem;">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">关键词</div>
                        <input type="text" class="form-control" id="keyword_name" name="keyword_name" placeholder="请输入关键词" value="<?php echo $contents['keyword_name']; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">链&nbsp;&nbsp;&nbsp;接</div>
                        <input type="text" class="form-control" id="keyword_url" name="keyword_url" placeholder="请输入链接" value="<?php echo $contents['keyword_url']; ?>">
                    </div>
                </div>
                <button type="button" class="btn btn-primary pull-right" id="addkeywords"><?php echo $_GET['mod'] == 'keywordsAdd' ? '新增' : '修改'; ?>关键词</button>
            </form>
        </div>
</div>
    <script>
        $(function ($) {
            $('#addkeywords').click(function () {
                var keyword_name = $('#keyword_name').val();
                var keyword_url = $('#keyword_url').val();
                var id = $('#article').attr('data-article-id');

                if (keyword_name == ''){
                    layer.msg('关键词不能为空');
                    return;
                }

                var requestData = {
                    keyword_name: keyword_name,
                    keyword_url: keyword_url
                };
                if (id !== '0') {
                    requestData['type'] = 'update';
                    requestData['id'] = id;
                } else {
                    requestData['type'] = 'add';
                }
                var loadFlag = layer.msg('正在读取数据，请稍候……', {icon: 16, shade: [0.01, '#fff'], shadeClose: false}, 60000);
                $.post('./Keywords.php?act=save', requestData, function (data) {
                    layer.close(loadFlag);
                    layer.alert(data['msg']);
                    setTimeout(function () {
                        window.location.href = './Keywords.php?mod=keywordsList';
                    }, 1500);
                }, 'json');

            });
        });
    </script>
<?php
} else if ($_GET['mod'] == 'keywordsDelete') {
    $id = intval($_GET['id']);
    if ($id <= 0)
        exit('<script>alert("关键词ID无效");window.location.href = \'./Keywords.php?mod=keywordsList\'</script>');
    if ($DB->delete('keyword', ['id' => $id])->rowCount()) {
        exit('<script>alert("删除成功");window.location.href = \'./Keywords.php?mod=keywordsList\'</script>');
    } else {
        exit('<script>alert("删除失败");window.location.href = \'./Keywords.php?mod=keywordsList\'</script>');
    }

}
?>
<script src="//cdn.staticfile.org/layer/2.3/layer.js"></script>