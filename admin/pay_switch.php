<?php
include '../includes/common.php';
$title = '分类-支付禁用开关';
include './head.php';
if ($islogin != 1)
    exit("<script>window.location.href='./login.php';</script>");
?>

<div class="col-xs-12 col-sm-10 col-lg-8 center-block" style="float: none;" id="main">
    <div class="block">
        <div class="block-title">
            <h3 class="panel-title"><b>分类-支付禁用开关设置</b></h3>
        </div>
        <div class="">
            <div class="form-group">
                <div class="input-group">
                    <label class="input-group-addon" for="gift_open">是否开启 分类-支付禁用开关</label>
                    <select id="pay_switch_status" class="form-control">

                        <option value="0" <?php echo htmlspecialchars($conf['pay_switch']) == '0' ? 'selected' : ''; ?>>0_关闭</option>
                        <option value="1" <?php echo htmlspecialchars($conf['pay_switch']) == '1' ? 'selected' : ''; ?>>1_开启</option>
                    </select>
                </div>
            </div>
            <a class="btn btn-info btn-block" id="pay_switch" style="margin-bottom: 15px;">确认</a>
        </div>
    </div>
    <div class="block">
        <div class="block-title">
            <h3 class="panel-title"><b>开启 分类-支付禁用开关说明</b></h3>
        </div>
        <div class="">
            <span>开关开启后,原来在 添加/编辑商品里的 (商品可支付类型) 将会失效</span>
        </div>
    </div>
</div>
<script>

    $("#pay_switch").click(function () {
        ii = layer.load(1, {shade: 0.3});
        var pay_switch_status = $("#pay_switch_status").val();
        $.ajax({
            type: "get",
            url: "ajax.php?act=pay_switch",
            data: {
                pay_switch_status: pay_switch_status
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
