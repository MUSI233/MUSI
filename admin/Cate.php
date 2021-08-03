<?php

/**

 * 分类管理

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
    $cid          = intval($_POST['cid']);
    $tid          = intval($_POST['tid']);
    $seo_title        = getParam($_POST['seo_title']);
    $seo_keywords        = getParam($_POST['seo_keywords']);
    $seo_description        = getParam($_POST['seo_description']);


    if ($type != 'update' && $type != 'add')

        exit(json_encode(['status' => 0, 'msg' => '请求类型有误']));



    $updateData = [

        'cid'          => $cid,
        'tid'          => $tid,
        'seo_title'    => $seo_title,
        'seo_keywords' => $seo_keywords,
        'seo_description'          => $seo_description
    ];

    if ($type == 'add'){
	   // $sql_str='insert into shua_article(title,contents,author,ispublic,pic,tit,keyw,desc,addtime) values ("'.$updateData['title'].'","'.$updateData['contents'].'","'.$updateData['author'].'",'.$updateData['ispublic'].',"'.$updateData['tit'].'","'.$updateData['keyw'].'","'.$updateData['desc'].'",'.$updateData['addtime'].')';
       // $sql = $DB->query($sql_str);
        $sql = $DB->insert('cate', $updateData);
    }else{
	    $sql = $DB->update('cate', $updateData, ['id' => $id, 'LIMIT' => 1]);
    }

    if ($sql->rowCount() > 0)

        exit(json_encode(['status' => 1, 'msg' => '更新成功']));

    else {

        exit(json_encode(['status' => 0, 'msg' => '更新失败 => ' . $DB->error()]));

    }

}



$title = '未知页面';

switch ($_GET['mod']) {
    case 'cateList':
        $title = 'SEO列表';
        break;
    case 'cateAdd':
        $title = '添加SEO';
        break;
    case 'cateUpdate':
        $title = '更新SEO';
        break;
}



include './head.php';

//没登录





if ($_GET['mod'] == 'cateList') {

    $searchTitle = getParam(urldecode($_GET['cid']), '');

    $page        = getParam($_GET['page'], 1);

    $limit       = 25;



    $where = [];



    if (!empty($searchTitle)) {

        $where['cid'] = $searchTitle;

    }

    $pages = $DB->count('cate', $where);

    $where['ORDER'] = ['id' => 'DESC'];

    $where['LIMIT'] = [($page - 1) * $limit, $limit];

    $contents = $DB->select('cate', ['id','cid','tid','seo_title','seo_keywords','seo_description'], $where);

    ?>

    <div class="col-md-12 center-block" style="float: none;">

        <div class="block">

            <form action="Cate.php" method="get">

                <input type="text" name="mod" value="cateList" style="display: none;">

                <input type="hidden" name="my" value="search">

                <div class="input-group xs-mb-15">

                    <input type="text" placeholder="请输入tid" name="cid"

                           class="form-control text-center"

                           value="<?php echo $searchTitle ?>">

                    <span class="input-group-btn">

			            <button type="submit" id="search" class="btn btn-primary">立即搜索</button>

			        </span>

                </div>

            </form>

            <div id="listTable">

                <div class="table-responsive">

                    <table class="table table-striped">

                        <thead>

                        <tr>

                            <th>#</th>
                            <th>前台CID</th>
                            <th>前台TID</th>
                            <th>SEO标题</th>
                            <th>SEO关键词</th>
                            <th>SEO描述</th>
                            <th>操作</th>

                        </tr>

                        </thead>

                        <tbody>

                        <?php foreach ($contents as $content) { ?>

                            <tr>

                                <td><?php echo $content['id']; ?></td>

                                <td><?php echo $content['cid']; ?></td>
                                <td><?php echo $content['tid']; ?></td>
                                <td><?php echo $content['seo_title']; ?></td>
                                <td><?php echo $content['seo_keywords']; ?></td>
                                <td><?php echo $content['seo_description']; ?></td>
                                <td><a href="./Cate.php?mod=cateUpdate&id=<?php echo $content['id']; ?>"

                                       class="btn btn-info btn-xs">编辑</a>&nbsp;<a

                                            href="./Cate.php?mod=cateDelete&id=<?php echo $content['id']; ?>"

                                            class="btn btn-xs btn-danger"

                                            onclick="return confirm('你确实要删除此记录吗？');">删除</a></td>

                            </tr>

                        <?php } ?>

                        </tbody>

                    </table>

                    <?php

                    $link = '&mod=cateList';

                    if (!empty($searchTitle))

                        $link .= '&title=' . urlencode($searchTitle);

                    echo '<ul class="pagination">';

                    $first = 1;

                    $prev  = $page - 1;

                    $next  = $page + 1;

                    $last  = $pages;

                    if ($page > 1) {

                        echo '<li><a href="Cate.php?page=' . $first . $link . '">首页</a></li>';

                        echo '<li><a href="Cate.php?page=' . $prev . $link . '">&laquo;</a></li>';

                    } else {

                        echo '<li class="disabled"><a>首页</a></li>';

                        echo '<li class="disabled"><a>&laquo;</a></li>';

                    }

                    $start = $page - 10 > 1 ? $page - 10 : 1;

                    $end   = $page + 10 < $pages ? $page + 10 : $pages;

                    for ($i = $start; $i < $page; $i++)

                        echo '<li><a href="Cate.php?page=' . $i . $link . '">' . $i . '</a></li>';

                    echo '<li class="disabled"><a>' . $page . '</a></li>';

                    for ($i = $page + 1; $i <= $end; $i++)

                        echo '<li><a href="Cate.php?page=' . $i . $link . '">' . $i . '</a></li>';

                    if ($page < $pages) {

                        echo '<li><a href="Cate.php?page=' . $next . $link . '">&raquo;</a></li>';

                        echo '<li><a href="Cate.php?page=' . $last . $link . '">尾页</a></li>';

                    } else {

                        echo '<li class="disabled"><a>&raquo;</a></li>';

                        echo '<li class="disabled"><a>尾页</a></li>';

                    }

                    echo '</ul>';

                    ?>

                </div>

            </div>

        </div>

    </div>

    <script>

        function setArticleStatus(id, st) {

            layer.load(2);

            $.post('ajax.php?act=change_article_st', {'aid': id,'st': st}).success(function (res) {

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

} else if ($_GET['mod'] == 'cateAdd' || $_GET['mod'] == 'cateUpdate') {

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



    if ($_GET['mod'] == 'cateUpdate') {

        $id = intval($_GET['id']);

       if (!empty($id)) {
            $contents = $DB->get('cate', '*', ['id' => $id]);
            if (empty($contents)){
	            $contents = [];
            }  
        }
    }

    ?>

    <div class="col-md-12 center-block" id="article" data-article-id="<?php echo $contents['id']; ?>" style="float: none;">

        <div class="block">

            <form method="post" class="clearfix" style="margin-bottom: 1rem;">

                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">cid</div>
                        <input type="text" class="form-control" id="cid" name="cid" value="<?php echo $contents['cid']; ?>">
                    </div>
                </div>

                 <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">tid</div>
                        <input type="text" class="form-control" id="tid" name="tid" value="<?php echo $contents['tid']; ?>">
                    </div>
                </div>

                 <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">seo标题</div>
                        <input type="text" class="form-control" id="seo_title" name="seo_title" value="<?php echo $contents['seo_title']; ?>">
                    </div>
                </div>

                 <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">seo关键词</div>
                        <input type="text" class="form-control" id="seo_keywords" name="seo_keywords" value="<?php echo $contents['seo_keywords']; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">seo描述</div>
                        <textarea class="form-control" id="seo_description" name="seo_description"

                                 style="min-width: 100%;" placeholder="seo 描述"><?php echo $contents['seo_description']; ?></textarea>

                    </div>
                </div>

                <button type="button" class="btn btn-primary pull-right" id="addcate"><?php echo $_GET['mod'] == 'cateAdd' ? '新增' : '修改'; ?></button>

            </form>

        </div>

    </div>

    <script>

        $(function ($) {

            $('#addcate').click(function () {

                var cid = $('#cid').val();
                var tid = $('#tid').val();
                var seo_title = $('#seo_title').val();
                var seo_keywords = $('#seo_keywords').val();
                var seo_description = $('#seo_description').val();

                var id = $('#article').attr('data-article-id');



                var requestData = {
					cid:cid,
					tid:tid,
					seo_title:seo_title,
					seo_keywords:seo_keywords,
					seo_description:seo_description
                };
                if (id) {

                    requestData['type'] = 'update';

                    requestData['id'] = id;

                } else {

                    requestData['type'] = 'add';

                }

                var loadFlag = layer.msg('正在读取数据，请稍候……', {icon: 16, shade: [0.01, '#fff'], shadeClose: false}, 60000);

                $.post('./Cate.php?act=save', requestData, function (data) {

                    layer.close(loadFlag);

                    layer.alert(data['msg']);

                    setTimeout(function () {

                        window.location.href = './Cate.php?mod=cateList';

                    }, 1500);

                }, 'json');



            });

        });

    </script>

<?php

} else if ($_GET['mod'] == 'cateDelete') {

    $id = intval($_GET['id']);

    if ($id <= 0)

        exit('<script>alert("ID无效");window.location.href = \'./Cate.php?mod=cateList\'</script>');

    if ($DB->delete('cate', ['id' => $id])->rowCount()) {

        exit('<script>alert("删除成功");window.location.href = \'./Cate.php?mod=cateList\'</script>');

    } else {

        exit('<script>alert("删除失败");window.location.href = \'./Cate.php?mod=cateList\'</script>');
    }



}

?>

<script src="//cdn.staticfile.org/layer/2.3/layer.js"></script>