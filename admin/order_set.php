<?php
include '../includes/common.php';
$title = '订单管理设置';
include './head.php';
if ($islogin != 1)
    exit("<script>window.location.href='./login.php';</script>");
?>

<div class="col-xs-12 col-sm-10 col-lg-8 center-block" style="float: none;" id="main">
    <div class="block">
        <div class="block-title">
            <h3 class="panel-title"><b>订单管理设置</b></h3>
        </div>
        <div class="">
            <div class="form-group">
                <div class="input-group">
                    <label class="input-group-addon" for="gift_open">是否开启随机订单号</label>
                    <select id="order_status" class="form-control">

                        <option value="0" <?php echo htmlspecialchars($conf['order_set']) == '0' ? 'selected' : ''; ?>>0_关闭</option>
                        <option value="1" <?php echo htmlspecialchars($conf['order_set']) == '1' ? 'selected' : ''; ?>>1_开启</option>
                    </select>
                </div>
            </div>
            <a class="btn btn-info btn-block" id="order_set" style="margin-bottom: 15px;">确认</a>
        </div>
    </div>
    <div class="block">
        <div class="block-title">
            <h3 class="panel-title"><b>开启随机订单号说明</b></h3>
        </div>
        <div class="">
            <span>开启后前后台订单号显示一个字符串,不再是纯数字订单号 ;</span>
        </div>
    </div>
</div>
<script>

    $("#order_set").click(function () {
        ii = layer.load(1, {shade: 0.3});
        var order_status = $("#order_status").val();
        $.ajax({
            type: "get",
            url: "ajax.php?act=order_set",
            data: {
                order_status: order_status
            },
            dataType: "json",
            success: function (order_set) {
                layer.close(ii);
                if (order_set.code === 0) {
                    layer.msg('保存成功', {icon: 1, time: 1000, shade: 0.3});
                    location.reload();
                } else {
                    layer.alert(order_set.msg);
                }
            }
        });
    });

</script>
