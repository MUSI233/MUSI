<?php
/**
 * 系统数据清理
 **/
require_once  "../includes/common.php";
$title = '添加分类';
include './head.php';

if ($islogin != 1) {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
};

if(!empty($_POST)){
	if(empty($_POST['cid'])) showmsg('分类ID不能为空！',1);

	$cid = trim($_POST['cid']);
	$tid = trim($_POST['tid'])?intval($_POST['tid']):0;
	//$sqli  = "SELECT `id` FROM `shua_cate` WHERE `cid` = '" . $cid . "' LIMIT 1";
	//if($DB->get_column($sqli)) showmsg('分类ID已经存在！',1);

	$seo_title  = trim($_POST['seo_title']);
	$seo_keywords  = trim($_POST['seo_keywords']);
	$seo_description  = trim($_POST['seo_description']);
	$sql = "insert into `shua_cate`(`cid`,`tid`,`seo_title`,`seo_keywords`,`seo_description`) values(".$cid.",".$tid.",'" . $seo_title."','".$seo_keywords."','".$seo_description."')";

	if ($DB->query($sql)) {
		showmsg('添加成功！', 1);
	} else {
		showmsg('添加失败！<br/>' . $DB->error(), 4);
	}
}
?>
<div class="main pjaxmain"  style="width:98%">
	<div class="row">
		<style>
			td{overflow: hidden;text-overflow: ellipsis;white-space: nowrap;max-width:360px;}
		</style>
		<div class="col-sm-12 col-md-10 center-block" style="float: none;">
			<div class="block">
				<div class="">
					<form action="./cateadd.php?my=add" method="POST">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;前台CID</span>
								<input type="text" class="form-control" name="cid" placeholder="请输入前台CID" required="true">
							</div>
						</div>

						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;前台TID</span>
								<input type="text" class="form-control" name="tid" placeholder="请输入前台TID">
							</div>
						</div>
						
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">&nbsp;&nbsp;&nbsp;SEO标题</span>
								<input type="text" class="form-control" name="seo_title" placeholder="请输入SEO标题" required="">
							</div>
						</div>

						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">SEO关键词</span>
								<input type="text" class="form-control" name="seo_keywords" placeholder="请输入SEO关键词"  required="">
							</div>
						</div>
						
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">&nbsp;&nbsp;&nbsp;&nbsp;SEO描述</span>
								<textarea class="form-control" name="seo_description" rows="4" placeholder="请输入SEO描述"></textarea>
							</div>
						</div>
						<div class="form-group">
							<input type="submit" class="btn btn-primary btn-block" value="提交" />
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="//cdn.staticfile.org/layer/2.3/layer.js"></script>