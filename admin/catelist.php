<?php
/**
 * 分类SEO
 **/
include("../includes/common.php");
$title = '分类SEO';
include './head.php';
if ($islogin != 1) {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
};
?>

<div class="col-sm-12 col-md-10 center-block" style="float: none;width:100%">
	<?php
	$my = isset($_GET['my']) ? $_GET['my'] : null;
if ($my == 'del') {
		if($my == 'del'){
			echo '<div class="block">
					<div class="block-title w h"><h3 class="panel-title">删除文章</h3></div>
					<div class=" box">';
			$id  = $_GET['id'];
			$sql = $DB->query("DELETE FROM shua_cate WHERE id=$id");
			if ($sql) {
				echo '删除成功！';
			} else {
				echo '删除失败！';
			}
			echo '<hr/><a href="./catelist.php">>>返回分类列表</a></div></div>';
		}
	}
	else
	{
		$where = '';
		if ($my == 'searcha') {
			$keys  = $_POST['cid'];
			$where = "WHERE cid like '%" . $keys . "%'";
		}
		$pagesize = 300;
		$pages    = ceil($numrows / $pagesize);//计算总页面数
		$page     = isset($_GET['page']) ? intval($_GET['page']) : 1;
	    $page     = ($page == 0) ? 1 : $page;
		$offset   = $pagesize * ($page - 1);
	   $sql      = "SELECT * FROM shua_cate $where order by id desc limit $offset,$pagesize";
		$rs       = $DB->query($sql);
	   $numrows  = $DB->count("SELECT count(*) from shua_cate");
	?>
	<div class="block">
		<form action="?my=search" method="POST" class="form-inline">
			<a href="./cateadd.php" class="btn btn-primary">添加</a>
		</form>
		<form action="?my=searcha" method="POST" class="form-inline" style="margin-top:10px;">
			<div class="form-group">
				<label>cid：</label>
			</div>
			<div class="form-group">
				<input type="text" class="form-control" name="cid" placeholder="请输入关键词" style="width:150px;" required="true">
			</div>
			<button type="submit" class="btn btn-success">搜索</button>
		</form>
		<style type="text/css">
			table thead tr th,table tbody tr td{text-align:center;}
		</style>
		<script type="text/javascript">
			$('.table-btn').click(function(){
				$('#tableform').attr('action',$(this).data('action'));
			});
				
			
		</script>
		<div class="table-responsive">
			<table class="table table-striped">
				<thead>
				<tr>
					<th width=90>前台CID</th>
					<th width=90>前台TID</th>
					<th width=200>SEO标题</th>
					<th width=200>SEO关键词</th>
					<th>SEO描述</th>
					<th width=110>操作</th>
				</tr>
				</thead>
				<tbody>
				<?php
					while ($res = $DB->fetch($rs)) {
						echo '<tr><td>' . $res['cid'] . '</td><td>'.$res['tid'].'</td><td>' . $res['seo_title'] . '</td><td>' .$res['seo_keywords']. '</td><td>' . $res['seo_description']. '</td><td><a href="./cateedit.php?id=' . $res['id'] . '" class="btn btn-xs btn-danger">查看</a><a href="./catelist.php?my=del&id=' . $res['id'] . '" class="btn btn-xs btn-danger" style="margin:0 10px;" onclick="return confirm(\'你确实要删除此文章吗？\');">删除</a></td></tr>';
					}
				?>
				</tbody>
			</table>
		</div>
		</form>
		<?php  }?>
	</div>
</div>