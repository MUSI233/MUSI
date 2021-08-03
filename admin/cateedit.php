<?php
/**
 * 系统数据清理
 **/
require_once  "../includes/common.php";
$title = '修改分类';
include './head.php';

if ($islogin != 1) {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
};

if (empty($_GET['id'])){
	showmsg('错误的请求！', 1);
}
$id = $_GET['id'];

if(!empty($_POST)){

	if(empty($_POST['cid'])) showmsg('分类ID不能为空！',1);

	$id = trim($_POST['id']);
	$cid = trim($_POST['cid']);
	$tid = trim($_POST['tid'])?intval($_POST['tid']):0;

	//$sqli  = "SELECT `id` FROM `shua_cate` WHERE `cid` = " .$cid;
	//if(!$DB->get_column($sqli)) showmsg('此分类经存在！',1);

	$seo_title  = trim($_POST['seo_title']);
	$seo_keywords  = trim($_POST['seo_keywords']);
	$seo_description  = trim($_POST['seo_description']);
	
	$sql = "update shua_cate set cid='$cid',tid='$tid',seo_title='$seo_title',seo_keywords='$seo_keywords',seo_description='$seo_description' where id={$id}";
	if ($DB->query($sql) !== false) {
		showmsg('修改成功！', 1);
	} else {
		showmsg('修改失败！<br/>' . $DB->error(), 4);
	}
}

$re = $DB->get_row('select * from shua_cate where id='.$id);
?>
<div class="main pjaxmain">
	<div class="row">
		<style>
			td{overflow: hidden;text-overflow: ellipsis;white-space: nowrap;max-width:360px;}
		</style>
		<div class="col-sm-12 col-md-10 center-block" style="float: none;">
			<div class="block">
				<div class="">
					<form action="?id=<?php echo $id;?>" method="POST">
						<input type="hidden" name="id" value="<?php echo $re['id']; ?>">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;前台CID
								</span>
								<input type="text" class="form-control" name="cid" value="<?php echo $re['cid']; ?>" required="true">
							</div>
						</div>

						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;前台TID
								</span>
								<input type="text" class="form-control" name="tid" value="<?php echo $re['tid']; ?>">
							</div>
						</div>
						
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">
									&nbsp;&nbsp;&nbsp;SEO标题
								</span>
								<input type="text" class="form-control" name="seo_title" value="<?php echo $re['seo_title']; ?>" required="true">
							</div>
						</div>
						
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">
									SEO关键词
								</span>
								<input type="text" class="form-control" name="seo_keywords" value="<?php echo $re['seo_keywords']; ?>" required="">
							</div>
						</div>
						
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">
									&nbsp;&nbsp;&nbsp;&nbsp;SEO描述
								</span>
								<textarea class="form-control" name="seo_description" rows="4"><?php echo $re['seo_description']; ?></textarea>
							</div>
						</div>						
						<div class="form-group">
							<input type="submit" class="btn btn-primary btn-block" value="确认提交" />
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="//cdn.staticfile.org/layer/2.3/layer.js"></script>