var $_GET = (function(){
    var url = window.document.location.href.toString();
    var u = url.split("?");
    if(typeof(u[1]) == "string"){
        u = u[1].split("&");
        var get = {};
        for(var i in u){
            var j = u[i].split("=");
            get[j[0]] = j[1];
        }
        return get;
    } else {
        return {};
    }
})();
function getcount() {
    $.ajax({
        type : "GET",
        url : "ajax.php?act=getcount",
        dataType : 'json',
        async: true,
        success : function(data) {
            $('#count_yxts').html(data.yxts);
            $('#count_orders').html(data.orders);
            $('#count_orders1').html(data.orders1);
            $('#count_orders2').html(data.orders2);
            $('#count_orders_all').html(data.orders);
            $('#count_orders_today').html(data.orders2);
            $('#count_money').html(data.money);
            $('#count_money1').html(data.money1);
            $('#count_site').html(data.site);
            if(data.gift != null){
                $.each(data.gift, function(k, v) {
                    $('#pst_1').append('<li class="py-2 align-middle"><strong class="text-truncate d-inline-block align-middle" style="word-break:break-all;max-width:3.5rem;">'+k+'</strong> 获得&nbsp;'+v+'</li>');
                });
                $('.giftlist').show();
                $('.giftlist ul').css('height',(35*$('#pst_1 li').length)+'px');
                scollgift();
            }
        }
    });
}
var pwdlayer;
function changepwd(id,skey) {
    pwdlayer = layer.open({
        type: 1,
        title: '修改密码',
        skin: 'layui-layer-rim',
        content: '<div class="input-group p-2 focused text-left"><div class="input-group my-2"><div class="input-group-prepend" style="margin:0;"><div class="input-group-text wxd-bor-rad0">新密码</div></div><input type="text" id="pwd" value="" class="form-control wxd-bor-rad0 pl-2" placeholder="请填写新的密码" required/></div><input type="submit" id="save" onclick="saveOrderPwd('+id+',\''+skey+'\')" class="btn btn-primary btn-block" value="提交保存"></div>'
    });
}
function saveOrderPwd(id,skey) {
    var pwd=$("#pwd").val();
    if(pwd==''){layer.alert('请确保每项不能为空！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    var ii = layer.load(2, {shade:[0.1,'#fff']});
    $.ajax({
        type : "POST",
        url : "ajax.php?act=changepwd",
        data : {id:id,pwd:pwd,skey:skey},
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                layer.msg('保存成功！');
                layer.close(pwdlayer);
            }else{
                layer.alert(data.msg,{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
            }
        }
    });
}
function scollgift(){
    setInterval(function() {
        var frist_li_idx = $("#pst_1 li:first");
        var c_li = frist_li_idx.clone();
        frist_li_idx.animate({
            "marginTop": "-35px",
            "opacity": "hide"
        }, 600, function() {
            $(this).remove();
            $("#pst_1").append(c_li);
        });
    }, 2000);
}
function getPoint(type) {
    if($('#tid option:selected').val()==undefined || $('#tid option:selected').val()=="0"){
        $('#inputsname').html("");
        $('#need').val('');
        $('#alert_frame').hide();
        return false;
    }
    $('#display_need').show();
    if($('#searchkw').val() == ''){
        history.replaceState({}, null, './?cid='+$('#cid').val()+'&tid='+$('#tid option:selected').val());
    }else{
        history.replaceState({}, null, './');
    }
    var multi = $('#tid option:selected').attr('multi');
    var count = $('#tid option:selected').attr('count');
    var price = $('#tid option:selected').attr('price');
    var shopimg = $('#tid option:selected').attr('shopimg');
    var close = $('#tid option:selected').attr('close');
    if(multi==1 && count>0){
        $('#need').val('￥'+price +"元 ➠ "+count+"个");
    }else{
        $('#need').val('￥'+price +"元");
    }
    if(close == 1){
        $('#submit_buy').val('当前商品已停止下单');
        layer.alert('当前商品维护中，停止下单！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
    }else if(price == 0){
        $('#submit_buy').val('立即免费领取');
    }else{
        $('#submit_buy').val('立即购买');
    }
    if(multi == 1){
        $('#display_num').show();
    }else{
        $('#display_num').hide();
    }
    var desc = $('#tid option:selected').attr('desc');
    if(desc!='' && alert!='null'){
        $('#alert_frame').show();
        $('#alert_frame').html(unescape(desc));
    }else{
        $('#alert_frame').hide();
    }
    $('#inputsname').html("");
    var inputname = $('#tid option:selected').attr('inputname');
    if(inputname!=''){
        $('#inputsname').append('<div class="input-group mb-2 focused text-left"><div class="input-group-text wxd-bor-radr0 wxd-bor-rad0" id="inputname">'+inputname+'</div><input type="text" name="inputvalue" id="inputvalue" value="'+($_GET['qq']?$_GET['qq']:'')+'" class="form-control pl-2" required onblur="checkInput()"/></div>');
    }else{
        $('#inputsname').append('<div class="input-group mb-2 focused text-left"><div class="input-group-text wxd-bor-radr0 wxd-bor-rad0" id="inputname">下单ＱＱ</div><input type="text" name="inputvalue" id="inputvalue" value="'+($_GET['qq']?$_GET['qq']:'')+'" class="form-control pl-2" required onblur="checkInput()"/></div>');
    }
    var inputsname = $('#tid option:selected').attr('inputsname');
    if(inputsname!=''){
        $.each(inputsname.split('|'), function(i, value) {
            if(value.indexOf('{')>0 && value.indexOf('}')>0){
                var addstr = '';
                var selectname = value.split('{')[0];
                var selectstr = value.split('{')[1].split('}')[0];
                $.each(selectstr.split(','), function(i, v) {
                    if(v.indexOf(':')>0){
                        i = v.split(':')[0];
                        v = v.split(':')[1];
                    }else{
                        i = v;
                    }
                    addstr += '<option value="'+i+'">'+v+'</option>';
                });
                $('#inputsname').append('<div class="input-group mb-2 focused text-left"><div class="input-group-text wxd-bor-radr0 wxd-bor-rad0" id="inputname'+(i+2)+'">'+selectname+'</div><select name="inputvalue'+(i+2)+'" id="inputvalue'+(i+2)+'" class="form-control pl-2">'+addstr+'</select></div>');
            }else{
                if(value=='说说ID'||value=='说说ＩＤ'){
                    var addstr='<div class="input-group-append onclick" onclick="get_shuoshuo(\'inputvalue'+(i+2)+'\',$(\'#inputvalue\').val())"><span class="btn btn-primary wxd-bor-rad0">获取</span></div>';
                }else if(value=='日志ID'||value=='日志ＩＤ'){
                    var addstr='<div class="input-group-append onclick" onclick="get_rizhi(\'inputvalue'+(i+2)+'\',$(\'#inputvalue\').val())"><span class="btn btn-primary wxd-bor-rad0">获取</span></div>';
                }else if(value=='作品ID'||value=='作品ＩＤ'||value=='快手作品ID'){
                    var addstr='<div class="input-group-append onclick" onclick="get_kuaishou(\'inputvalue'+(i+2)+'\',$(\'#inputvalue\').val())"><span class="btn btn-primary wxd-bor-rad0">获取</span></div>';
                }else if(value=='抖音评论ID'){
                    var addstr='<div class="input-group-append onclick" onclick="getCommentList(\'inputvalue'+(i+2)+'\',$(\'#inputvalue\').val())"><span class="btn btn-primary wxd-bor-rad0">获取</span></div>';
                }else if(value=='商品重量'){
                    var addstr='<div class="input-group-append"><span class="input-group-text">公斤</span></div>';
                }else if(value=='图片链接'){
                    var addstr='<div class="input-group-append" onclick="TuTips()"><span class="btn btn-primary"><i class="fas fa-upload"></i></span></div>';
                }else{
                    var addstr='';
                }
                $('#inputsname').append('<div class="input-group mb-2 focused text-left"><div class="input-group-text wxd-bor-radr0 wxd-bor-rad0" id="inputname'+(i+2)+'">'+value+'</div><input type="text" name="inputvalue'+(i+2)+'" id="inputvalue'+(i+2)+'" value="" class="form-control pl-2" required/>'+addstr+'</div>');
            }
        });
    }
    if($("#inputname").html() == '快手ID'||$("#inputname").html() == '快手ＩＤ'||$("#inputname").html() == '快手用户ID'){
        $('#inputvalue').attr("placeholder", "在此输入快手作品链接 可自动获取");
        if($("#inputname2").html() == '作品ID'||$("#inputname2").html() == '作品ＩＤ'||$("#inputname2").html() == '快手作品ID'){
            $('#inputvalue2').attr("placeholder", "填写作品链接后点击→");
            $('#inputvalue2').attr("data-toggle", "tooltip");
            $('#inputvalue2').attr("data-placement", "top");
            $('#inputvalue2').attr("title", "在快手ID框填写作品连接");
            $("#inputvalue2").attr("readonly", "true");
        }
    }else if($("#inputname2").html() == '说说ID'||$("#inputname2").html() == '说说ＩＤ'){
        $('#inputvalue2').attr("placeholder", "填写QQ号码后点击→");
        $('#inputvalue2').attr("disabled", true);
    }else if($("#inputname").html() == '歌曲ID'||$("#inputname").html() == '歌曲ＩＤ'){
        $('#inputvalue').attr("placeholder", "在此输入歌曲的分享链接 可自动获取");
    }else if($("#inputname").html() == '火山ID'||$("#inputname").html() == '火山作品ID'||$("#inputname").html() == '火山视频ID'||$("#inputname").html() == '火山ＩＤ'){
        $('#inputvalue').attr("placeholder", "在此输入火山视频的链接 可自动获取");
    }else if($("#inputname").html() == '抖音ID'||$("#inputname").html() == '抖音作品ID'||$("#inputname").html() == '抖音视频ID'||$("#inputname").html() == '抖音ＩＤ'){
        $('#inputvalue').attr("placeholder", "在此输入抖音的分享链接 可自动获取");
        if($("#inputname2").html() == '抖音评论ID'||$("#inputname2").html() == '抖音评论ＩＤ'){
            $('#inputvalue2').attr("placeholder", "填写作品链接后点击→");
            $('#inputvalue2').attr("data-toggle", "tooltip");
            $('#inputvalue2').attr("data-placement", "top");
            $('#inputvalue2').attr("title", "在抖音ID框填写作品连接");
            $("#inputvalue2").attr("readonly", "true");
        }
    }else if($("#inputname").html() == 'QQ号码' || $("#inputname").html() == 'ＱＱ号码'){
        $('#inputvalue').attr("placeholder", "请认真填写正确的QQ号码");
        if($("#inputname2").html() == 'QQ密码' || $("#inputname2").html() == 'ＱＱ密码'){
            $('#inputvalue2').attr("placeholder", "请认真填写正确的QQ密码");
        }
    }else if($("#inputname").html() == '微视ID'||$("#inputname").html() == '微视作品ID'||$("#inputname").html() == '微视ＩＤ'){
        $('#inputvalue').attr("placeholder", "在此输入微视的作品链接 可自动获取");
    }else if($("#inputname").html() == '评论内容'||$("#inputname").html() == '备注信息'){
        $('#inputvalue').attr("placeholder", "在此输入评论的内容 多个请用#隔开");
    }else if($("#inputname").html() == '小H书ID'||$("#inputname").html() == '小H书ＩＤ'){
        $('#inputvalue').attr("placeholder", "在此输入小H书的分享链接 可自动获取");
    }else if($("#inputname").html() == '微视主页ID'){
        $('#inputvalue').attr("placeholder", "在此输入微视的主页链接 可自动获取");
    }else if($("#inputname").html() == '头条ID'||$("#inputname").html() == '头条ＩＤ'){
        $('#inputvalue').attr("placeholder", "在此输入今日头条的链接 可自动获取");
    }else if($("#inputname").html() == '美拍ID'||$("#inputname").html() == '美拍ＩＤ'||$("#inputname").html() == '美拍作品ID'||$("#inputname").html() == '美拍视频ID'){
        $('#inputvalue').attr("placeholder", "在此输入美拍视频链接 可自动获取");
    }else if($("#inputname").html() == 'Ｂ站ＩＤ'||$("#inputname").html() == 'B站ID'||$("#inputname").html() == 'Ｂ站视频ID'||$("#inputname").html() == 'Ｂ站专栏ID'){
        $('#inputvalue').attr("placeholder", "在此输入Ｂ站对应链接 可自动获取");
    }else if($("#inputname").html() == '最右ＩＤ'||$("#inputname").html() == '最右ID'){
        $('#inputvalue').attr("placeholder", "在此输入最右帖子链接 可自动获取");
    }else if($("#inputname").html() == '美图ＩＤ'||$("#inputname").html() == '美图作品ID'){
        $('#inputvalue').attr("placeholder", "在此输入美图对应链接 可自动获取");
    }else if($("#inputname").html() == '淘宝链接'||$("#inputname").html() == '淘口令'){
        $('#inputvalue').attr("placeholder", "在此输入淘宝分享链接 可自动获取");
    }else if($("#inputname").html() == '砍价链接'||$("#inputname").html() == '拆红包链接'){
        $('#inputvalue').attr("placeholder", "在此输入链接将自动缩短");
    }else if($("#inputname3").html() == '图片链接'){
        $('#inputvalue3').attr("placeholder", "点击右侧按钮上传图片");
        $("#inputvalue3").attr("readonly", "true");
    }else{
        $('#inputvalue').removeAttr("placeholder");
        $('#inputvalue2').removeAttr("placeholder");
    }
    if($('#tid option:selected').attr('isfaka')==1){
        $('#inputvalue').attr("placeholder", "用于接收卡密以及查询订单使用");
        $('#display_left').show();
        $.ajax({
            type : "POST",
            url : "ajax.php?act=getleftcount",
            data : {tid:$('#tid option:selected').val()},
            dataType : 'json',
            success : function(data) {
                $('#leftcount').val(data.count)
            }
        });
        if($.cookie('email'))$('#inputvalue').val($.cookie('email'));
    }else{
        $('#display_left').hide();
    }
    var alert = $('#tid option:selected').attr('alert');
    if(alert!='' && alert!='null'){
        var ii=layer.alert(''+unescape(alert)+'',{
            btn:['好的'],
            title:'商品提示',
            skin: 'layui-layer-molv layui-layer-wxd',
            closeBtn:false
        },function(){
            layer.close(ii);
        });
    }

    //input 事件
    function changeEvent() {
        var tempDom = $(this);

        var inputValue = tempDom.val();

        var attrName = tempDom.parent().find('div.input-group-addon').text();

        if (!attrName) {
            attrName = tempDom.parent().find('div.input-group-text').text();
        }

        if (attrName.indexOf('链接') !== -1) {

            if (inputValue !== '' && inputValue.indexOf('http') >= 0) {
                var urlReg = /((http|https):\/\/)+(\w+\.)+(\w+)[\w\/\.\-(?<=\?&)]*/g;
                inputValue.replace(urlReg, function (match, param, offset, string) {
                    tempDom.val(match);
                })
            }
        }

    }

    $('#inputsname input[type="text"]').focus(changeEvent).blur(changeEvent);
}
function get_shuoshuo(id,uin,km,page){
    km = km || 0;
    page = page || 1;
    if(uin==''){
        layer.alert('请先填写QQ号！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
    }
    var ii = layer.load(2, {shade:[0.1,'#fff']});
    $.ajax({
        type : "GET",
        url : "ajax.php?act=getshuoshuo&uin="+uin+"&page="+page+"&hashsalt="+hashsalt,
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                var addstr='';
                $.each(data.data, function(i, item){
                    addstr+='<option value="'+item.tid+'">'+item.content+'</option>';
                });
                var nextpage = page+1;
                var lastpage = page>1?page-1:1;
                if($('#show_shuoshuo').length > 0){
                    if(km==1){
                        $('#show_shuoshuo').html('<div class="input-group mb-2 focused text-left"><div class="input-group-prepend onclick" title="上一页" onclick="get_shuoshuo(\''+id+'\',$(\'#km_inputvalue\').val(),'+km+','+lastpage+')"><span class="btn btn-dark wxd-bor-rad0"><i class="fa fa-chevron-left"></i></span></div><select id="shuoid" class="form-control px-3" onchange="set_shuoshuo(\''+id+'\');">'+addstr+'</select><div class="input-group-append onclick" title="下一页" onclick="get_shuoshuo(\''+id+'\',$(\'#km_inputvalue\').val(),'+km+','+nextpage+')"><span class="btn btn-dark wxd-bor-rad0"><i class="fa fa-chevron-right"></i></span></div></div>');
                    }else{
                        $('#show_shuoshuo').html('<div class="input-group mb-2 focused text-left"><div class="input-group-prepend onclick" title="上一页" onclick="get_shuoshuo(\''+id+'\',$(\'#inputvalue\').val(),'+km+','+lastpage+')"><span class="btn btn-dark wxd-bor-rad0"><i class="fa fa-chevron-left"></i></span></div><select id="shuoid" class="form-control px-3" onchange="set_shuoshuo(\''+id+'\');">'+addstr+'</select><div class="input-group-append onclick" title="下一页" onclick="get_shuoshuo(\''+id+'\',$(\'#inputvalue\').val(),'+km+','+nextpage+')"><span class="btn btn-dark wxd-bor-rad0"><i class="fa fa-chevron-right"></i></span></div></div>');
                    }
                }else{
                    if(km==1){
                        $('#km_inputsname').append('<div class="input-group mb-2 focused text-left" id="show_shuoshuo"><div class="input-group-prepend onclick" title="上一页" onclick="get_shuoshuo(\''+id+'\',$(\'#km_inputvalue\').val(),'+km+','+lastpage+')"><span class="btn btn-dark wxd-bor-rad0"><i class="fa fa-chevron-left"></i></span></div><select id="shuoid" class="form-control px-3" onchange="set_shuoshuo(\''+id+'\');">'+addstr+'</select><div class="input-group-append onclick" title="下一页" onclick="get_shuoshuo(\''+id+'\',$(\'#km_inputvalue\').val(),'+km+','+nextpage+')"><span class="btn btn-dark wxd-bor-rad0"><i class="fa fa-chevron-right"></i></span></div></div>');
                    }else{
                        $('#inputsname').append('<div class="input-group mb-2 focused text-left" id="show_shuoshuo"><div class="input-group-prepend onclick" title="上一页" onclick="get_shuoshuo(\''+id+'\',$(\'#inputvalue\').val(),'+km+','+lastpage+')"><span class="btn btn-dark wxd-bor-rad0"><i class="fa fa-chevron-left"></i></span></div><select id="shuoid" class="form-control px-3" onchange="set_shuoshuo(\''+id+'\');">'+addstr+'</select><div class="input-group-append onclick" title="下一页" onclick="get_shuoshuo(\''+id+'\',$(\'#inputvalue\').val(),'+km+','+nextpage+')"><span class="btn btn-dark wxd-bor-rad0"><i class="fa fa-chevron-right"></i></span></div></div></div>');
                    }
                }
                set_shuoshuo(id);
            }else{
                layer.alert(data.msg,{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
            }
        }
    });
}
function set_shuoshuo(id){
    var shuoid = $('#shuoid').val();
    $('#'+id).val(shuoid);
}
function get_rizhi(id,uin,km,page){
    km = km || 0;
    page = page || 1;
    if(uin==''){
        layer.alert('请先填写QQ号！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
    }
    var ii = layer.load(2, {shade:[0.1,'#fff']});
    $.ajax({
        type : "GET",
        url : "ajax.php?act=getrizhi&uin="+uin+"&page="+page+"&hashsalt="+hashsalt,
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                var addstr='';
                $.each(data.data, function(i, item){
                    addstr+='<option value="'+item.blogId+'">'+item.title+'</option>';
                });
                var nextpage = page+1;
                var lastpage = page>1?page-1:1;
                if($('#show_rizhi').length > 0){
                    $('#show_rizhi').html('<div class="input-group"><div class="input-group-prepend onclick" onclick="get_rizhi(\''+id+'\',$(\'#inputvalue\').val(),'+km+','+lastpage+')"><span class="btn btn-dark wxd-bor-rad0"><i class="fa fa-chevron-left"></i></span></div><select id="blogid" class="form-control px-3" onchange="set_rizhi(\''+id+'\');">'+addstr+'</select><div class="input-group-append onclick" onclick="get_rizhi(\''+id+'\',$(\'#inputvalue\').val(),'+km+','+nextpage+')"><span class="btn btn-dark wxd-bor-rad0"><i class="fa fa-chevron-right"></i></span></div></div>');
                }else{
                    if(km==1){
                        $('#km_inputsname').append('<div class="input-group mb-2 focused text-left" id="show_rizhi"><div class="input-group-prepend onclick" onclick="get_rizhi(\''+id+'\',$(\'#km_inputvalue\').val(),'+km+','+lastpage+')"><span class="btn btn-dark wxd-bor-rad0"><i class="fa fa-chevron-left"></i></span></div><select id="blogid" class="form-control px-3" onchange="set_rizhi(\''+id+'\');">'+addstr+'</select><div class="input-group-append onclick" onclick="get_rizhi(\''+id+'\',$(\'#km_inputvalue\').val(),'+km+','+nextpage+')"><span class="btn btn-dark wxd-bor-rad0"><i class="fa fa-chevron-right"></i></span></div></div>');
                    }else{
                        $('#inputsname').append('<div class="input-group mb-2 focused text-left" id="show_rizhi"><div class="input-group-prepend onclick" onclick="get_rizhi(\''+id+'\',$(\'#inputvalue\').val(),'+km+','+lastpage+')"><span class="btn btn-dark wxd-bor-rad0"><i class="fa fa-chevron-left"></i></span></div><select id="blogid" class="form-control px-3" onchange="set_rizhi(\''+id+'\');">'+addstr+'</select><div class="input-group-append onclick" onclick="get_rizhi(\''+id+'\',$(\'#inputvalue\').val(),'+km+','+nextpage+')"><span class="btn btn-dark wxd-bor-rad0"><i class="fa fa-chevron-right"></i></span></div></div>');
                    }
                }
                set_rizhi(id);
            }else{
                layer.alert(data.msg,{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
            }
        }
    });
}
function set_rizhi(id){
    var blogid = $('#blogid').val();
    $('#'+id).val(blogid);
}
function fillOrder(id,skey){
    if(!confirm('是否确定补交订单？',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'}))return;
    $.ajax({
        type : "POST",
        url : "ajax.php?act=fill",
        data : {orderid:id,skey:skey},
        dataType : 'json',
        success : function(data) {
            layer.alert(data.msg,{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
            $("#submit_query").click();
        }
    });
}
function getsongid(){
    var songurl=$("#inputvalue").val();
    if(songurl==''){layer.alert('请确保每项不能为空！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    if(songurl.indexOf('.qq.com')<0){layer.alert('请输入正确的歌曲的分享链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    try{
        var songid = songurl.split('s=')[1].split('&')[0];
    }catch(e){
        layer.alert('请输入正确的歌曲的分享链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
    }
    $('#inputvalue').val(songid);
}
function getkuaishouid(){
    var kuauishouurl=$("#inputvalue").val();
    if(kuauishouurl==''){layer.alert('请确保每项不能为空！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    if (kuauishouurl.indexOf('/s/') > 0 || kuauishouurl.indexOf('kuaishou.com') > 0) {
        var ii = layer.load(2, {shade:[0.1,'#fff']});
        $.ajax({
            type : "POST",
            url : "ajax.php?act=getkuaishou",
            data : {url:kuauishouurl},
            dataType : 'json',
            success : function(data) {
                layer.close(ii);
                if(data.code == 0){
                    if(data.authorid.indexOf('user/')>0){
                        var authorid = data.authorid.split('user/')[1].split('?')[0];
                        $('#inputvalue').val(authorid);
                    }else{
                        $('#inputvalue').val(data.authorid);
                    }
                    if($('#inputvalue2').length>0)$('#inputvalue2').val(data.videoid);
                }else{
                    layer.alert(data.msg,{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
                }
            }
        });
    }else{
        if(kuauishouurl.indexOf('gifshow.com')<0 && kuauishouurl.indexOf('kuaishou.com')<0 && kuauishouurl.indexOf('kwai.com')<0 && kuauishouurl.indexOf('etoote.com')<0 && kuauishouurl.indexOf('kspkg.com')<0 && kuauishouurl.indexOf('yxixy.com')<0){layer.alert('请输入正确的快手作品链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
        try{
            if(kuauishouurl.indexOf('userId=')>0){
                var authorid = kuauishouurl.split('userId=')[1].split('&')[0];
            }else{
                var authorid = kuauishouurl.split('photo/')[1].split('/')[0];
            }
            if(kuauishouurl.indexOf('photoId=')>0){
                var videoid = kuauishouurl.split('photoId=')[1].split('&')[0];
            }else{
                var videoid = kuauishouurl.split('photo/')[1].split('/')[1].split('?')[0];
            }
        }catch(e){
            layer.alert('请输入正确的快手作品链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
        }
        $('#inputvalue').val(authorid);
        if($('#inputvalue2').length>0)$('#inputvalue2').val(videoid);
    }
    return true;
}
function get_kuaishou(id,ksid){
    if(ksid==''){
        ksid = $('#inputvalue2').val();
        if(ksid==''){
            layer.alert('请先填写快手作品链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
        }
    }
    var zpid = $('#'+id).val();
    if(ksid.indexOf('http')>=0){
        var kuauishouurl = ksid;
    }else if(zpid.indexOf('http')>=0){
        var kuauishouurl = zpid;
    }else if(zpid==''){
        layer.alert('请先填写快手作品链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
    }else{
        return true;
    }
    if(kuauishouurl.indexOf('gifshow.com')<0 && kuauishouurl.indexOf('kuaishou.com')<0 && kuauishouurl.indexOf('kwai.com')<0 && kuauishouurl.indexOf('etoote.com')<0 && kuauishouurl.indexOf('kspkg.com')<0 && kuauishouurl.indexOf('yxixy.com')<0){layer.alert('请输入正确的快手作品链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    if (kuauishouurl.indexOf('/s/') > 0 || kuauishouurl.indexOf('kuaishou.com') > 0) {
        var ii = layer.load(2, {shade:[0.1,'#fff']});
        $.ajax({
            type : "POST",
            url : "ajax.php?act=getkuaishou",
            data : {url:kuauishouurl},
            dataType : 'json',
            success : function(data) {
                layer.close(ii);
                if(data.code == 0){
                    $('#inputvalue').val(data.authorid);
                    $('#inputvalue2').val(data.videoid);
                }else{
                    layer.alert(data.msg,{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
                }
            }
        });
    }else{
        try{
            if(kuauishouurl.indexOf('userId=')>0){
                var authorid = kuauishouurl.split('userId=')[1].split('&')[0];
            }else{
                var authorid = kuauishouurl.split('photo/')[1].split('/')[0];
            }
            if(kuauishouurl.indexOf('photoId=')>0){
                var videoid = kuauishouurl.split('photoId=')[1].split('&')[0];
            }else{
                var videoid = kuauishouurl.split('photo/')[1].split('/')[1].split('?')[0];
            }
        }catch(e){
            layer.alert('请输入正确的快手作品链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
        }
        $('#inputvalue').val(authorid);
        $('#inputvalue2').val(videoid);
    }
}
function gethuoshanid(){
    var songurl=$("#inputvalue").val();
    if(songurl==''){layer.alert('请确保每项不能为空！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    if(songurl.indexOf('.huoshan.com')<0){layer.alert('请输入正确的链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    if(songurl.indexOf('/s/')>0){
        var ii = layer.load(2, {shade:[0.1,'#fff']});
        $.ajax({
            type : "POST",
            url : "ajax.php?act=gethuoshan",
            data : {url:songurl},
            dataType : 'json',
            success : function(data) {
                layer.close(ii);
                if(data.code == 0){
                    $('#inputvalue').val(data.itemid);
                }else{
                    layer.alert(data.msg,{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
                }
            }
        });
    }else{
        try{
            if(songurl.indexOf('video/')>0){
                var songid = songurl.split('video/')[1].split('/')[0];
            }else if(songurl.indexOf('item/')>0){
                var songid = songurl.split('item/')[1].split('/')[0];
            }else if(songurl.indexOf('room/')>0){
                var songid = songurl.split('room/')[1].split('/')[0];
            }else{
                var songid = songurl.split('user/')[1].split('/')[0];
            }
        }catch(e){
            layer.alert('请输入正确的链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
        }
        $('#inputvalue').val(songid);
    }
}
function getdouyinid(){
    var songurl=$("#inputvalue").val();
    if(songurl==''){layer.alert('请确保每项不能为空！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    if(songurl.indexOf('.douyin.com')<0 && songurl.indexOf('.iesdouyin.com')<0){layer.alert('请输入正确的链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    if(songurl.indexOf('/v.douyin.com/')>0){
        var ii = layer.load(2, {shade:[0.1,'#fff']});
        $.ajax({
            type : "POST",
            url : "ajax.php?act=getdouyin",
            data : {url:songurl},
            dataType : 'json',
            success : function(data) {
                layer.close(ii);
                if(data.code == 0){
                    $('#inputvalue').val(data.songid);
                }else{
                    layer.alert(data.msg,{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
                }
            }
        });
    }else{
        try{
            if(songurl.indexOf('video/')>0){
                var songid = songurl.split('video/')[1].split('/')[0];
            }else if(songurl.indexOf('music/')>0){
                var songid = songurl.split('music/')[1].split('/')[0];
            }else{
                var songid = songurl.split('user/')[1].split('/')[0];
            }
        }catch(e){
            layer.alert('请输入正确的链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
        }
        $('#inputvalue').val(songid);
    }
}
function gettoutiaoid(){
    var songurl=$("#inputvalue").val();
    if(songurl==''){layer.alert('请确保每项不能为空！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    if(songurl.indexOf('.toutiao.com')<0){layer.alert('请输入正确的链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    try{
        if(songurl.indexOf('user/')>0){
            var songid = songurl.split('user/')[1].split('/')[0];
        }else if(songurl.indexOf('group/')>0){
            var songid = songurl.split('group/')[1].split('/')[0];
        }else{
            var songid = songurl.split('profile/')[1].split('/')[0];
        }
    }catch(e){
        layer.alert('请输入正确的链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
    }
    $('#inputvalue').val(songid);
}
function getweishiid(){
    var songurl=$("#inputvalue").val();
    if(songurl==''){layer.alert('请确保每项不能为空！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    if(songurl.indexOf('.qq.com')<0){layer.alert('请输入正确的链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    try{
        if(songurl.indexOf('feed/')>0){
            var songid = songurl.split('feed/')[1].split('/')[0];
        }else if(songurl.indexOf('personal/')>0){
            var songid = songurl.split('personal/')[1].split('/')[0];
        }else{
            var songid = songurl.split('id=')[1].split('&')[0];
        }
    }catch(e){
        layer.alert('请输入正确的链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
    }
    $('#inputvalue').val(songid);
}

function getxiaohongshuid(){
    var songurl=$("#inputvalue").val();
    if(songurl==''){layer.alert('请确保每项不能为空！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    if(songurl.indexOf('/t.cn/')>0 || songurl.indexOf('/xhsurl.com/')>0 || songurl.indexOf('/w.url.cn/')>0){
        var ii = layer.load(2, {shade:[0.1,'#fff']});
        $.ajax({
            type : "POST",
            url : "ajax.php?act=gethongshu",
            data : {url:songurl},
            dataType : 'json',
            success : function(data) {
                layer.close(ii);
                if(data.code == 0){
                    $('#inputvalue').val(data.songid);
                }else{
                    layer.alert(data.msg,{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
                }
            }
        });
    }else{
        try{
            if(songurl.indexOf('.xiaohongshu.com')<0){layer.alert('请输入正确的链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
            if(songurl.indexOf('profile/')>0){
                var songid = songurl.split('profile/')[1].split('?')[0];
            }else if(songurl.indexOf('item/')>0){
                var songid = songurl.split('item/')[1].split('?')[0];
            }else if(songurl.indexOf('vendor/')>0){
                var songid = songurl.split('vendor/')[1].split('?')[0];
            }else if(songurl.indexOf('?xhsshare')>0){
                var songid = songurl.split('?xhsshare')[1].split('?')[0];
            }
        }catch(e){
            layer.alert('请输入正确的链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
        }
        $('#inputvalue').val(songid);
    }
}
function getbilibiliid(){
    var songurl=$("#inputvalue").val();
    if(songurl==''){layer.alert('请确保每项不能为空！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    if(songurl.indexOf('.bilibili.com')<0){layer.alert('请输入正确的视频链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    try{
        var bilisz = /[^0-9.]/g;
        if(songurl.indexOf('video/')>0){
            var songid = songurl.split('video/')[1].split('?')[0].replace(bilisz,'');
        }else if(songurl.indexOf('read/')>0){
            var songid = songurl.split('read/')[1].split('?')[0].replace(bilisz,'');
        }else{
            var songid = songurl.split('com/')[1].split('?')[0].replace(bilisz,'');
        }
    }catch(e){
        layer.alert('请输入正确的视频链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
    }
    $('#inputvalue').val(songid);
}
function getzuiyouid(){
    var songurl=$("#inputvalue").val();
    if(songurl==''){layer.alert('请确保每项不能为空！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    if(songurl.indexOf('.izuiyou.com')<0){layer.alert('请输入正确的帖子链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    try{
        var songid = songurl.split('detail/')[1].split('?')[0];
    }catch(e){
        layer.alert('请输入正确的帖子链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
    }
    $('#inputvalue').val(songid);
}
function getmeituid(){
    var songurl=$("#inputvalue").val();
    if(songurl==''){layer.alert('请确保每项不能为空！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    if(songurl.indexOf('.meitu.com')<0){layer.alert('请输入正确的美图秀秀的分享链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    try{
        if(songurl.indexOf('feed_id=')>0){
            var songid = songurl.split('feed_id=')[1].split('&')[0];
        }else if(songurl.indexOf('uid=')>0){
            var songid = songurl.split('uid=')[1].split('&')[0];
        }
    }catch(e){
        layer.alert('请输入正确的美图秀秀的分享链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
    }
    $('#inputvalue').val(songid);
}
function gettaobaoid(){
    var songurl=$("#inputvalue").val();
    if(songurl==''){layer.alert('请确保每项不能为空！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    if(songurl.indexOf('.tb.cn')<0){layer.alert('请输入正确的淘宝分享链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    try{
        //var tburl = /(https?|http):\/\/[-A-Za-z0-9+&@#/%?=~_|!:,.;]+[-A-Za-z0-9+&@#/%=~_|]/g;
        var tburl = /￥(.+?)￥/g;
        var songid = songurl.match(tburl);
    }catch(e){
        layer.alert('请输入正确的淘宝分享链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
    }
    $('#inputvalue').val(songid);
}
function getmeipaiid(){
    var songurl=$("#inputvalue").val();
    if(songurl==''){layer.alert('请确保每项不能为空！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    if(songurl.indexOf('.meipai.com')<0){layer.alert('请输入正确的视频链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    try{
        var songid = songurl.split('media/')[1].split('?')[0];
    }catch(e){
        layer.alert('请输入正确的视频链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
    }
    $('#inputvalue').val(songid);
}
function getzpurl(){
    var songurl=$("#inputvalue").val();
    if(songurl==''){layer.alert('请确保每项不能为空！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    try{
        var reg = /[a-zA-z]+:\/\/[^\s]*/;
        var songid = songurl.match(reg);
    }catch(e){
        layer.alert('请输入正确的链接！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
    }
    $('#inputvalue').val(songid);
}
function getkanjiaid(){
    var songurl=$("#inputvalue").val();
    if(songurl==''){layer.alert('请确保每项不能为空！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
    var songid = songurl.split('//')[1].split('/')[0];
    if(songid != "dwz.cn"){
        var load = layer.load();
        $.ajax({
            type : "POST",
            url : 'ajax.php?act=dwz',
            data : {"url":songurl},
            dataType : 'json',
            success : function(data) {
                layer.close(load);
                if(data.code == 1){
                    $('#inputvalue').val(data.data);
                }else{
                    layer.alert(data.msg,{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
                    $("#inputvalue").val("")
                    return false;
                }
            }
        });
    }
}
function getCommentList(id,aweme_id,km,page){
    km = km || 0;
    page = page || 1;
    if(aweme_id==''){
        layer.alert('请先填写抖音作品ID！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
    }
    if(aweme_id.length != 19){
        layer.alert('抖音作品ID填写错误',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;
    }
    var ii = layer.load(2, {shade:[0.1,'#fff']});
    $.ajax({
        type : "GET",
        url : "https://api.douyin.qlike.cn/api.php?act=GetCommentList&aweme_id="+aweme_id+"&page="+page+"&hashsalt="+hashsalt,
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.total != 0){
                var addstr='';
                $.each(data.comments, function(i, item){
                    addstr+='<option value="'+item.cid+'">[昵称 => '+item.user.nickname+'][内容 => '+item.text+'][赞数量=>'+item.digg_count+']</option>';
                });
                var nextpage = page+1;
                var lastpage = page>1?page-1:1;
                if($('#show_shuoshuo').length > 0){
                    $('#show_shuoshuo').html('<div class="input-group mb-2 focused text-left"><div class="input-group-prepend onclick" title="上一页" onclick="getCommentList(\''+id+'\',$(\'#inputvalue\').val(),'+km+','+lastpage+')"><span class="btn btn-dark wxd-bor-rad0"><i class="fa fa-chevron-left"></i></span></div><select id="shuoid" class="form-control px-3" onchange="set_shuoshuo(\''+id+'\');">'+addstr+'</select><div class="input-group-append onclick" title="下一页" onclick="getCommentList(\''+id+'\',$(\'#inputvalue\').val(),'+km+','+nextpage+')"><span class="btn btn-dark wxd-bor-rad0"><i class="fa fa-chevron-right"></i></span></div></div>');
                }else{
                    if(km==1){
                        $('#km_inputsname').append('<div class="input-group mb-2 focused text-left" id="show_shuoshuo"><div class="input-group-prepend onclick" title="上一页" onclick="getCommentList(\''+id+'\',$(\'#km_inputvalue\').val(),'+km+','+lastpage+')"><span class="btn btn-dark wxd-bor-rad0"><i class="fa fa-chevron-left"></i></span></div><select id="shuoid" class="form-control px-3" onchange="set_shuoshuo(\''+id+'\');">'+addstr+'</select><div class="input-group-append onclick" title="下一页" onclick="getCommentList(\''+id+'\',$(\'#km_inputvalue\').val(),'+km+','+nextpage+')"><span class="btn btn-dark wxd-bor-rad0"><i class="fa fa-chevron-right"></i></span></div></div>');
                    }else{
                        $('#inputsname').append('<div class="input-group mb-2 focused text-left" id="show_shuoshuo"><div class="input-group-prepend onclick" title="上一页" onclick="getCommentList(\''+id+'\',$(\'#inputvalue\').val(),'+km+','+lastpage+')"><span class="btn btn-dark wxd-bor-rad0"><i class="fa fa-chevron-left"></i></span></div><select id="shuoid" class="form-control px-3" onchange="set_shuoshuo(\''+id+'\');">'+addstr+'</select><div class="input-group-append onclick" title="下一页" onclick="getCommentList(\''+id+'\',$(\'#inputvalue\').val(),'+km+','+nextpage+')"><span class="btn btn-dark wxd-bor-rad0"><i class="fa fa-chevron-right"></i></span></div></div>');
                    }
                }
                set_shuoshuo(id);
            }else{
                layer.alert('您的作品好像没人评论',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
            }
        },
        error: function(a) {
            layer.close(ii);
            layer.alert('网络错误，请稍后重试',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
        }
    });
}
function queryOrder(type,content,page){
    $('#submit_query').val('Loading');
    $('#result2').hide();
    $('#list').html('');
    $.ajax({
        type : "POST",
        url : "ajax.php?act=query",
        data : {type:type, qq:content, page:page},
        dataType : 'json',
        success : function(data) {
            if(data.code == 0){
                var status;
                $.each(data.data, function(i, item){
                    if(item.status==1)
                        status='<span class="text-success"><i class="fas fa-circle"></i> 已完成</span>';
                    else if(item.status==2)
                        status='<span class="text-warning"><i class="fas fa-circle"></i> 处理中</span>';
                    else if(item.status==3/* && item.result!=null*/)
                        status='<span class="text-danger"><i class="fas fa-circle"></i> 异常</span>';
                        //else if(item.status==3)
                    //    status='<span class="badge badge-danger">异常</span>&nbsp;<button type="submit" class="btn btn-primary btn-sm" onclick="fillOrder('+item.id+',\''+item.skey+'\')">补交</button>';
                    else if(item.status==4)
                        status='<span class="text-secondary"><i class="fas fa-circle"></i> 已退款</span>';
                    else
                        status='<span class="text-info"><i class="fas fa-circle"></i> 待处理</span>';
                    $('#list').append('<tr orderid='+item.id+'>'+
                        '<td class="text-center" style="white-space: nowrap;">'+status+'</td>'+
                        '<td class="text-center" style="white-space: nowrap;"><span class="text-truncate d-inline-block d-lg-inline" style="width:8rem;">'+item.name+'</span></td>'+
                        '<td class="text-center d-none d-md-block" style="white-space: nowrap;"><span class="text-truncate d-inline-block d-lg-inline" style="width:8rem;">'+item.input+'</span></td>'+
                        '<td class="text-center" style="white-space: nowrap;">'+item.value+'</td>'+
                        '<td class="text-center d-none d-lg-block" style="white-space: nowrap;">'+item.addtime+'</td>'+
                        '<td class="text-center" style="white-space: nowrap;"><a href="javascript:void(0);" onclick="showOrder('+item.id+',\''+item.skey+'\')" title="查看订单详细" class="badge badge-success mr-1">详情</a><a href="javascript:void(0);" class="badge badge-primary" id="copy-btn" data-text="复制成功，若有疑问请发送至客服处理！" data-clipboard-text="【'+item.name+'】订单编号：'+item.id+'丨下单信息：'+item.input+'">复制订单</a></td>'+
                        '</tr>');
                    if(item.result!=null){
                        if(item.status==3){
                            $('#list').append('<tr class="text-left"><td colspan=6><span class="text-break text-danger"><i class="fas fa-arrow-up"></i> 异常原因：'+item.result+'</span></td></tr>');
                        }
                    }
                });
                if(data.data == ''){
                    $('#list').append('<tr class="text-center"><td colspan=6><i class="fas fa-exclamation-circle"></i> 暂无订单！</td></tr>');
                }
                var addstr = '';
                if(data.islast==true) addstr += '<button class="btn btn-primary btn-sm float-left" onclick="queryOrder(\''+data.type+'\',\''+data.content+'\','+(data.page-1)+')">上一页</button>';
                if(data.isnext==true) addstr += '<button class="btn btn-primary btn-sm float-right" onclick="queryOrder(\''+data.type+'\',\''+data.content+'\','+(data.page+1)+')">下一页</button>';
                $('#list').append('<tr><td colspan=6>'+addstr+'</td></tr>');
                $("#result2").slideDown();
                if($_GET['buyok']){
                    showOrder(data.data[0].id,data.data[0].skey)
                }
            }else{
                layer.alert(data.msg,{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
            }
            $('#submit_query').val('立即查询');
        }
    });
}
function showOrder(id,skey){
    var ii = layer.load(2, {shade:[0.1,'#fff']});
    var status = ['<span class="text-info"><i class="fas fa-circle"></i> 待处理</span>','<span class="text-success"><i class="fas fa-circle"></i> 已完成</span>','<span class="text-warning"><i class="fas fa-circle"></i> 处理中</span>','<span class="text-danger"><i class="fas fa-circle"></i> 异常</span>','<span class="text-secondary"><i class="fas fa-circle"></i> 已退款</span>'];
    $.ajax({
        type : "POST",
        url : "ajax.php?act=order",
        data : {id:id,skey:skey},
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code === 0){
                var item = '<table class="table table-condensed table-hover table-sm wxd-table-now">';
                item += '<tr><td colspan="6" style="text-align:center"><b>订单基本信息</b></td></tr><tr><td class="bg-info text-white">编号</td><td colspan="5">'+id+'</td></tr><tr><td class="bg-info text-white">名称</td><td colspan="5"><span class="text-truncate d-inline-block" style="width:18rem;">'+data.name+'</span></td></tr><!--tr><td class="bg-info text-white">金额</td><td colspan="5">'+data.money+'元</td></tr--><tr><td class="bg-info text-white">时间</td><td colspan="5">'+data.date+'</td></tr><tr><td class="bg-info text-white">信息</td><td colspan="5">'+data.inputs+'</td><tr><td class="bg-info text-white">状态</td><td colspan="5">'+status[data.status]+'</td></tr>';
                if(data.complain){
                    item += '<tr><td class="bg-info text-white">操作</td><td><a href="./user/workorder.php?my=add&orderid='+id+'&skey='+skey+'" target="_blank" onclick="return checklogin('+data.islogin+')" class="btn btn-sm btn-dark">投诉订单</a></td></tr>';
                    if (data.kf_info && parseInt(data.kf_info.show_order_kf) === 1) {
                        if (parseInt(data.kf_info.show_order_kf_type) === 0) {
                            item += '&nbsp;<a href="http://wpa.qq.com/msgrd?v=3&uin=' + data.kf_info.show_order_kf_qq + '&site=qq&menu=yes" target="_blank" class="btn btn-xs btn-default" style="color: red;">联系客服</a></td></tr>';
                        } else if (parseInt(data.kf_info.show_order_kf_type) === 1) {
                            item += '&nbsp;<a href="' + data.kf_info.show_order_kf_href + '" target="_blank" class="btn btn-xs btn-default" style="color: red;">联系客服</a></td></tr>';
                        }
                    }
                }
                if(data.list && data.list.order_state){
                    var statuss = data.list.order_state=='已退单' || data.list.order_state=='退单中' || data.list.order_state=='已退款'?'异常':data.list.order_state;
                    item += '<tr><td colspan="6" style="text-align:center"><b>订单实时状态</b></td><tr><td class="bg-warning text-white">数量</td><td>'+data.list.num+'</td><td class="bg-warning text-white">时间</td><td colspan="3">'+data.list.add_time+'</td></tr><tr><td class="bg-warning text-white">初始</td><td>'+data.list.start_num+'</td><td class="bg-warning text-white">当前</td><td>'+data.list.now_num+'</td><td class="bg-warning text-white">状态</td><td><font color=blue>'+statuss+'</font></td>';
                }else if(data.kminfo){
                    item += '<tr><td colspan="6" style="text-align:center"><b>以下是你的卡密信息</b></td><tr><td colspan="6">'+data.kminfo+'</td></tr>';
                }else if(data.result){
                    item += '<tr><td colspan="6" style="text-align:center"><b>处理结果</b></td><tr><td colspan="6">'+data.result+'</td></tr>';
                }
                if(data.desc){
                    item += '<tr><td colspan="6" style="text-align:center"><b>商品简介</b></td><tr><td colspan="6">'+data.desc+'</td></tr>';
                }
                item += '</table>';
                layer.open({
                    type: 1,
                    title: '订单详细信息',
                    skin: 'layui-layer-rim',
                    content: item
                });
            }else{
                layer.alert(data.msg,{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
            }
        }
    });
}
var handlerEmbed = function (captchaObj) {
    captchaObj.appendTo('#captcha');
    captchaObj.onReady(function () {
        $("#captcha_wait").hide();
    }).onSuccess(function () {
        var result = captchaObj.getValidate();
        if (!result) {
            return alert('请完成验证');
        }
        var ii = layer.load(2, {shade:[0.1,'#fff']});
        $.ajax({
            type : "POST",
            url : "ajax.php?act=pay",
            data : {tid:$("#tid").val(),inputvalue:$("#inputvalue").val(),inputvalue2:$("#inputvalue2").val(),inputvalue3:$("#inputvalue3").val(),inputvalue4:$("#inputvalue4").val(),inputvalue5:$("#inputvalue5").val(),num:$("#num").val(),hashsalt:hashsalt,geetest_challenge:result.geetest_challenge,geetest_validate:result.geetest_validate,geetest_seccode:result.geetest_seccode},
            dataType : 'json',
            success : function(data) {
                layer.close(ii);
                if(data.code >= 0){
                    $('#alert_frame').hide();
                    alert('领取成功！');
                    window.location.href='?buyok=1';
                }else{
                    layer.alert(data.msg,{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
                    captchaObj.reset();
                }
            }
        });
    });
};
function toTool(cid,tid){
    history.replaceState({}, null, './?cid='+cid+'&tid='+tid);
    $("#recommend").modal('hide');
    $_GET['tid']=tid;
    $_GET["cid"]=cid;
    $("#cid").val(cid);
    $("#cid").change();
    $("#goodType").hide('normal');
    $("#goodTypeContent").show('normal');
}
function dopay(type,orderid){
    if(type == 'rmb'){
        var ii = layer.msg('正在提交订单请稍候...', {icon: 16,shade: 0.5,time: 15000});
        $.ajax({
            type : "POST",
            url : "ajax.php?act=payrmb",
            data : {orderid: orderid},
            dataType : 'json',
            success : function(data) {
                layer.close(ii);
                if(data.code == 1){
                    alert(data.msg,{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
                    window.location.href='?buyok=1';
                }else if(data.code == -2){
                    alert(data.msg,{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
                    window.location.href='?buyok=1';
                }else if(data.code == -3){
                    var confirmobj = layer.confirm('你的余额不足，请充值！', {
                        btn: ['立即充值','取消'],
                        title: '小提示',
                        skin: 'layui-layer-molv layui-layer-wxd'
                    }, function(){
                        window.location.href='./user/#chongzhi';
                    }, function(){
                        layer.close(confirmobj);
                    });
                }else if(data.code == -4){
                    var confirmobj = layer.confirm('你还未登录，是否现在登录？', {
                        btn: ['登录','注册','取消'],
                        title: '小提示',
                        skin: 'layui-layer-molv layui-layer-wxd'
                    }, function(){
                        window.location.href='./user/login.php';
                    }, function(){
                        window.location.href='./user/reg.php';
                    }, function(){
                        layer.close(confirmobj);
                    });
                }else{
                    layer.alert(data.msg,{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
                }
            }
        });
    }else{
        window.location.href='other/submit.php?type='+type+'&orderid='+orderid;
    }
}
function checkInput() {
    const input_name_dom = $("#inputname");
    if (input_name_dom.html() == 'KFID' || input_name_dom.html() == 'KFＩＤ' || input_name_dom.html() == 'KF用户ID') {
        if ($("#inputvalue").val() != '' && $("#inputvalue").val().indexOf('http') >= 0) {
            getkuaishouid();
        }
    } else if (input_name_dom.html() == '歌曲ID' || input_name_dom.html() == '歌曲ＩＤ') {
        if ($("#inputvalue").val().indexOf("s=") == -1) {
            if ($("#inputvalue").val().length != 12 && $("#inputvalue").val().length != 16) {
                layer.alert('歌曲ID是一串12位或16位的字符!<br>输入K歌作品链接即可！');
                return false;
            }
        } else if ($("#inputvalue").val() != '') {
            getsongid();
        }
    } else if ($("#inputname").html() == '火山ID' || $("#inputname").html() == '火山作品ID' || $("#inputname").html() == '火山视频ID' || $("#inputname").html() == '火山ＩＤ') {
        if ($("#inputvalue").val() != '' && $("#inputvalue").val().indexOf('http') >= 0) {
            gethuoshanid();
        }
    } else if ($("#inputname").html() == '音乐ID' || $("#inputname").html() == '音乐作品ID' || $("#inputname").html() == '音乐视频ID' || $("#inputname").html() == '音乐ＩＤ' || $("#inputname").html() == '音乐主页ID') {
        if ($("#inputvalue").val() != '' && $("#inputvalue").val().indexOf('http') >= 0) {
            getdouyinid();
        }
    } else if ($("#inputname").html() == '微视ID' || $("#inputname").html() == '微视作品ID' || $("#inputname").html() == '微视ＩＤ' || $("#inputname").html() == '微视主页ID') {
        if ($("#inputvalue").val() != '' && $("#inputvalue").val().indexOf('http') >= 0) {
            getweishiid();
        }
    } else if ($("#inputname").html() == '头条ID' || $("#inputname").html() == '头条ＩＤ') {
        if ($("#inputvalue").val() != '' && $("#inputvalue").val().indexOf('http') >= 0) {
            gettoutiaoid();
        }
    } else if ($("#inputname").html() == '小红书ID' || $("#inputname").html() == '小红书作品ID' || $("#inputname").html() == '皮皮虾ID' || $("#inputname").html() == '皮皮虾作品ID') {
        if ($("#inputvalue").val() != '' && $("#inputvalue").val().indexOf('http') >= 0) {
            getxiaohongshuid();
        }
    } else if ($("#inputname").html() == '美拍ID' || $("#inputname").html() == '美拍ＩＤ' || $("#inputname").html() == '美拍作品ID' || $("#inputname").html() == '美拍视频ID') {
        if ($("#inputvalue").val() != '' && $("#inputvalue").val().indexOf('http') >= 0) {
            getmeipaiid();
        }
    } else if ($("#inputname").html() == '哔哩哔哩视频ID' || $("#inputname").html() == '哔哩哔哩ID' || $("#inputname").html() == '哔哩视频ID') {
        if ($("#inputvalue").val() != '' && $("#inputvalue").val().indexOf('http') >= 0) {
            getbilibiliid();
        }
    } else if ($("#inputname").html() == '最右帖子ID') {
        if ($("#inputvalue").val() != '' && $("#inputvalue").val().indexOf('http') >= 0) {
            getzuiyouid();
        }
    } else if ($("#inputname").html() == '全民视频ID' || $("#inputname").html() == '全民小视频ID') {
        if ($("#inputvalue").val() != '' && $("#inputvalue").val().indexOf('http') >= 0) {
            getquanminid();
        }
    } else if ($("#inputname").html() == '美图作品ID' || $("#inputname").html() == '美图视频ID') {
        if ($("#inputvalue").val() != '' && $("#inputvalue").val().indexOf('http') >= 0) {
            getmeituid();
        }
    } else if ($("#inputname").html() == '绿洲作品ID' || $("#inputname").html() == '绿洲视频ID') {
        if ($("#inputvalue").val() != '' && $("#inputvalue").val().indexOf('http') >= 0) {
            getoasisid();
        }
    } else if ($("#inputname").html() == '绿洲用户ID') {
        if ($("#inputvalue").val() != '' && $("#inputvalue").val().indexOf('http') >= 0) {
            getoasiUid();
        }
    }
}

function getquanminid() {
    var songurl = $("#inputvalue").val();
    if (songurl == '') {
        layer.alert('请确保每项不能为空！');
        return false;
    }
    if (songurl.indexOf('hao222.com') < 0) {
        layer.alert('请输入正确的视频链接！');
        return false;
    }
    try {
        var songid = songurl.split('vid=')[1].split('&')[0];
        layer.msg('ID获取成功！下单即可');
    } catch (e) {
        layer.alert('请输入正确的视频链接！');
        return false;
    }
    $('#inputvalue').val(songid);
}

function getoasisid() {
    var songurl = $("#inputvalue").val();
    if (songurl == '') {
        layer.alert('请确保每项不能为空！');
        return false;
    }
    if (songurl.indexOf('weibo.cn') < 0 && songurl.indexOf('weibo.com') < 0) {
        layer.alert('请输入正确的视频链接！');
        return false;
    }
    try {
        var songid = songurl.split('sid=')[1].split('&')[0];
        layer.msg('ID获取成功！下单即可');
    } catch (e) {
        layer.alert('请输入正确的视频链接！');
        return false;
    }
    $('#inputvalue').val(songid);
}

function getoasiUid() {
    var songurl = $("#inputvalue").val();
    if (songurl == '') {
        layer.alert('请确保每项不能为空！');
        return false;
    }
    if (songurl.indexOf('weibo.cn') < 0 && songurl.indexOf('weibo.com') < 0) {
        layer.alert('请输入正确的绿洲用户分享页链接！');
        return false;
    }
    try {
        var songid = songurl.split('uid=')[1].split('&')[0];
        layer.msg('ID获取成功！下单即可');
    } catch (e) {
        layer.alert('请输入正确的绿洲用户分享页链接！');
        return false;
    }
    $('#inputvalue').val(songid);
}

function checklogin(islogin){
    if(islogin==1){
        return true;
    }else{
        var confirmobj = layer.confirm('为方便反馈处理结果，投诉订单前请先登录网站！', {
            btn: ['登录','注册','取消'],
            title: '小提示',
            skin: 'layui-layer-molv layui-layer-wxd'
        }, function(){
            window.location.href='./user/login.php';
        }, function(){
            window.location.href='./user/reg.php';
        }, function(){
            layer.close(confirmobj);
        });
        return false;
    }
}
var audio_init = {
    changeClass: function (target,id) {
        var className = $(target).attr('class');
        var ids = document.getElementById(id);
        (className == 'on')
            ? $(target).removeClass('on').addClass('off')
            : $(target).removeClass('off').addClass('on');
        (className == 'on')
            ? ids.pause()
            : ids.play();
    },
    play:function(){
        document.getElementById('media').play();
    }
};
$(document).ready(function(){
    $(document).on('click','.goodTypeChange',function(){
        var id = $(this).data('id');
        var img = $(this).data('img');
        history.replaceState({}, null, './?cid='+id);
        $("#searchkw").val('');
        $("#cid").val(id);
        $("#cid").change();
        $("#goodType").hide('normal');
        $("#goodTypeContent").show('normal');
    });
    $(document).on('click','.nav-tabs,.backType',function(){
        history.replaceState({}, null, './');
        $("#searchkw").val('');
        $("#goodType").show('normal');
        $("#goodTypeContent").hide('normal');
    });
    $(document).on('click','#showSearchBar',function(){
        $("#display_selectclass").slideToggle();
        $("#display_searchBar").slideToggle();
    });
    $(document).on('click','#closeSearchBar',function(){
        $("#display_searchBar").slideToggle();
        $("#display_selectclass").slideToggle();
    });
    $(document).on('click','#doSearch',function(){
        var kw = $("#searchkw").val();
        var search = $("#cid").attr('type') == 'hidden'?true:false;
        if(kw==''){layer.alert("搜索内容不能为空！",{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return;}
        var ii = layer.load(2, {shade:[0.1,'#fff']});
        if(search == true){
            $("#goodType").hide('normal');
            $("#goodTypeContent").show('normal');
        };
        $("#tid").empty();
        $("#tid").append('<option value="0">请选择商品</option>');
        $.ajax({
            type : "POST",
            url : "ajax.php?act=gettool",
            data : {kw:kw},
            dataType : 'json',
            success : function(data) {
                layer.close(ii);
                if(data.code == 0){
                    var num = 0;
                    $.each(data.data, function (i, res) {
                        $("#tid").append('<option value="'+res.tid+'" cid="'+res.cid+'" price="'+res.price+'" desc="'+escape(res.desc)+'" alert="'+escape(res.alert)+'" inputname="'+res.input+'" inputsname="'+res.inputs+'" multi="'+res.multi+'" isfaka="'+res.isfaka+'" count="'+res.value+'" close="'+res.close+'">'+res.name+'</option>');
                        num++;
                    });
                    $("#tid").val(0);
                    getPoint();
                    if(num==0 && cid!=0)$("#tid").html('<option value="0">没有搜索到相关商品</option>');
                }else{
                    layer.alert(data.msg,{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
                }
            },
            error:function(data){
                layer.msg('服务器错误');
                return false;
            }
        });
    });
    $(document).on('change', '#cid', function() {
        var cid = $(this).val();
        if(cid>0)history.replaceState({}, null, './?cid='+cid);
        var ii = layer.load(2, {shade:[0.1,'#fff']});
        $("#tid").empty();
        $("#tid").append('<option value="0">请选择商品</option>');
        $.ajax({
            type : "GET",
            url : "ajax.php?act=gettool&cid="+cid+"&info=1",
            dataType : 'json',
            success : function(data) {
                layer.close(ii);
                $("#tid").empty();
                $("#tid").append('<option value="0">请选择商品</option>');
                if(data.code == 0){
                    if(data.info!=null)$("#goodTypeContent").find('img[data-name="thumb"]').attr('src', data.info.shopimg ? data.info.shopimg : '/assets/img/Product/default.png');
                    var num = 0;
                    $.each(data.data, function (i, res) {
                        $("#tid").append('<option value="'+res.tid+'" cid="'+res.cid+'" price="'+res.price+'" desc="'+escape(res.desc)+'" alert="'+escape(res.alert)+'" inputname="'+res.input+'" inputsname="'+res.inputs+'" multi="'+res.multi+'" isfaka="'+res.isfaka+'" count="'+res.value+'" close="'+res.close+'">'+res.name+'</option>');
                        num++;
                    });
                    if($_GET["tid"] && $_GET["cid"]==cid){
                        var tid = parseInt($_GET["tid"]);
                        $("#tid").val(tid);
                    }else{
                        $("#tid").val(0);
                    }
                    getPoint(1);
                    if(num==0 && cid!=0)$("#tid").html('<option value="0">该分类下没有商品</option>');
                }else{
                    layer.alert(data.msg,{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
                }
            },
            error:function(data){
                layer.msg('服务器错误');
                return false;
            }
        });
    });
    $(document).on('click','#submit_buy',function(){
        var tid=$("#tid").val();
        if(tid==0){layer.alert('请选择商品！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
        var inputvalue = $("#inputvalue").val();
        if(inputvalue=='' || tid==''){layer.alert('请确保每项不能为空！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
        if($("#inputvalue2").val()=='' || $("#inputvalue3").val()=='' || $("#inputvalue4").val()=='' || $("#inputvalue5").val()==''){layer.alert('请确保每项不能为空！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
        if(($('#inputname').html()=='下单ＱＱ' || $('#inputname').html()=='ＱＱ账号' || $("#inputname").html() == 'QQ账号') && (inputvalue.length<5 || inputvalue.length>11 || isNaN(inputvalue))){layer.alert('请输入正确的QQ号！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
        var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
        if($('#inputname').html()=='你的邮箱' && !reg.test(inputvalue)){layer.alert('邮箱格式不正确！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
        reg=/^[1][0-9]{10}$/;
        if($('#inputname').html()=='手机号码' && !reg.test(inputvalue)){layer.alert('手机号码格式不正确！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
        if($("#inputname2").html() == '说说ID'||$("#inputname2").html() == '说说ＩＤ'){
            if($("#inputvalue2").val().length != 24){layer.alert('说说必须是原创说说！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
        }
        checkInput();
        if($("#inputname2").html() == '作品ID'||$("#inputname2").html() == '作品ＩＤ'){
            if($("#inputvalue2").val()!='' && $("#inputvalue2").val().indexOf('http')>=0){
                $("#inputvalue").val($("#inputvalue2").val());
                get_kuaishou('inputvalue2',$('#inputvalue').val());
            }
        }
        if($("#inputname").html() == '抖音作品ID'||$("#inputname").html() == '火山作品ID'||$("#inputname").html() == '火山直播ID'){
            if($("#inputvalue").val().length != 19){layer.alert('您输入的作品ID有误！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
        }
        if($("#inputname2").html() == '抖音评论ID'){
            if($("#inputvalue2").val().length != 16){layer.alert('您输入的评论ID有误！请点击自动获取手动选择评论！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
        }
        $('#pay_frame').hide();
        var ii = layer.load(2, {shade:[0.1,'#fff']});
        $.ajax({
            type : "POST",
            url : "ajax.php?act=pay",
            data : {tid:tid,inputvalue:$("#inputvalue").val(),inputvalue2:$("#inputvalue2").val(),inputvalue3:$("#inputvalue3").val(),inputvalue4:$("#inputvalue4").val(),inputvalue5:$("#inputvalue5").val(),num:$("#num").val(),hashsalt:hashsalt},
            dataType : 'json',
            success : function(data) {
                layer.close(ii);
                if(data.code == 0){
                    if($('#inputname').html()=='你的邮箱'){
                        $.cookie('email', inputvalue);
                    }
                    var paymsg = '';
                    if(data.pay_wxpay>0){
                        paymsg+='<button class="btn btn-success btn-block" onclick="dopay(\'wxpay\',\''+data.trade_no+'\')" style="margin-top:10px;"><i class="fab fa-weixin"></i>&nbsp;微信支付</button>';
                    }
                    if(data.pay_alipay>0){
                        paymsg+='<button class="btn btn-primary btn-block" onclick="dopay(\'alipay\',\''+data.trade_no+'\')" style="margin-top:10px;"><i class="fab fa-alipay"></i>&nbsp;支付宝[推荐]</button>';
                    }
                    if(data.pay_qqpay>0){
                        paymsg+='<button class="btn btn-info btn-block" onclick="dopay(\'qqpay\',\''+data.trade_no+'\')" style="margin-top:10px;"><i class="fab fa-qq"></i>&nbsp;ＱＱ钱包</button>';
                    }
                    if(data.pay_tenpay>0){
                        paymsg+='<button class="btn btn-default btn-block" onclick="dopay(\'tenpay\',\''+data.trade_no+'\')" style="margin-top:10px;"><img width="20" src="assets/icon/tenpay.ico" class="logo">财付通</button>';
                    }
                    if (data.pay_rmb>0) {
                        paymsg+='<button class="btn btn-dark btn-block" onclick="dopay(\'rmb\',\''+data.trade_no+'\')">余额支付（剩'+data.user_rmb+'元）</button>';
                    }
                    layer.alert('<center><h2>￥ '+data.need+'</h2><hr>'+paymsg+'<hr><a class="btn btn-light btn-block" onclick="window.location.reload()">取消订单</a></center>',{
                        btn:[],
                        title:'提交订单成功',
                        skin: 'layui-layer-molv layui-layer-wxd',
                        closeBtn: false
                    });
                }else if(data.code == 1){
                    $('#alert_frame').hide();
                    if($('#inputname').html()=='你的邮箱'){
                        $.cookie('email', inputvalue);
                    }
                    alert('领取成功！');
                    window.location.href='?buyok=1';
                }else if(data.code == 2){
                    $.getScript("//static.geetest.com/static/tools/gt.js");
                    layer.open({
                        type: 1,
                        title: '完成验证',
                        skin: 'layui-layer-rim',
                        area: ['320px', '100px'],
                        content: '<div id="captcha"><div id="captcha_text">正在加载验证码</div><div id="captcha_wait"><div class="loading"><div class="loading-dot"></div><div class="loading-dot"></div><div class="loading-dot"></div><div class="loading-dot"></div></div></div></div>'
                    });
                    $.ajax({
                        url: "ajax.php?act=captcha&t=" + (new Date()).getTime(),
                        type: "get",
                        dataType: "json",
                        success: function (data) {
                            $('#captcha_text').hide();
                            $('#captcha_wait').show();
                            initGeetest({
                                gt: data.gt,
                                challenge: data.challenge,
                                new_captcha: data.new_captcha,
                                product: "popup",
                                width: "100%",
                                offline: !data.success
                            }, handlerEmbed);
                        }
                    });
                }else{
                    layer.alert(data.msg,{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
                }
            }
        });
    });
    $("#submit_checkkm").click(function(){
        var km=$("#km").val();
        if(km==''){layer.alert('请确保卡密不能为空！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
        $('#submit_km').val('Loading');
        $('#km_show_frame').hide();
        $.ajax({
            type : "POST",
            url : "ajax.php?act=checkkm",
            data : {km:km},
            dataType : 'json',
            success : function(data) {
                if(data.code == 0){
                    if(data.close == 1){
                        layer.alert('当前商品维护中，停止下单！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
                        $('#submit_checkkm').val('检查卡密');
                        return false;
                    }
                    $('#submit_checkkm').hide();
                    $('#km').attr("disabled",true);
                    $('#km_tid').val(data.tid);
                    $('#km_name').val(data.name);
                    if(data.desc!=''){
                        $('#km_alert_frame').show();
                        $('#km_alert_frame').html(data.desc);
                    }else{
                        $('#km_alert_frame').hide();
                    }
                    $('#km_inputsname').html("");
                    var inputname = data.inputname;
                    if(inputname!=''){
                        $('#km_inputsname').append('<div class="input-group mb-2 focused text-left"><div class="input-group-append" id="km_inputname"><span class="input-group-text wxd-bor-radr0 wxd-bor-rad0">'+inputname+'</span></div><input type="text" name="inputvalue" id="km_inputvalue" value="'+($_GET['qq']?$_GET['qq']:'')+'" class="form-control pl-3" required/></div>');
                    }else{
                        $('#km_inputsname').append('<div class="input-group mb-2 focused text-left"><div class="input-group-append" id="km_inputname"><span class="input-group-text">下单ＱＱ</span></div><input type="text" name="inputvalue" id="km_inputvalue" value="'+($_GET['qq']?$_GET['qq']:'')+'" class="form-control pl-3" required/></div>');
                    }
                    var inputsname = data.inputsname;
                    if(inputsname!=''){
                        $.each(inputsname.split('|'), function(i, value) {
                            if(value=='说说ID'||value=='说说ＩＤ')
                                var addstr='<div class="input-group-append onclick" onclick="get_shuoshuo(\'km_inputvalue'+(i+2)+'\',$(\'#km_inputvalue\').val(),1)"><span class="btn btn-primary wxd-bor-rad0">获取</span></div>';
                            else if(value=='日志ID'||value=='日志ＩＤ')
                                var addstr='<div class="input-group-append onclick" onclick="get_rizhi(\'km_inputvalue'+(i+2)+'\',$(\'#km_inputvalue\').val(),1)"><span class="btn btn-primary wxd-bor-rad0">获取</span></div>';
                            else
                                var addstr='';
                            $('#km_inputsname').append('<div class="input-group mb-2 focused text-left"><div class="input-group-prepend" id="km_inputname"><span class="input-group-text">'+value+'</span></div><input type="text" name="inputvalue'+(i+2)+'" id="km_inputvalue'+(i+2)+'" value="" class="form-control pl-3" required/>'+addstr+'</div>');
                        });
                    }

                    $("#km_show_frame").slideDown();
                    if(data.alert!='' && data.alert!='null'){
                        var ii=layer.alert(data.alert,{
                            btn:['好的'],
                            title:'商品提示',
                            skin: 'layui-layer-molv layui-layer-wxd',
                            closeBtn:false
                        },function(){
                            layer.close(ii);
                        });
                    }
                }else{
                    layer.alert(data.msg,{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
                }
                $('#submit_checkkm').val('检查卡密');
            }
        });
    });
    $("#submit_card").click(function(){
        var km=$("#km").val();
        var inputvalue=$("#km_inputvalue").val();
        if(inputvalue=='' || km==''){layer.alert('请确保每项不能为空！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
        if($("#km_inputvalue2").val()=='' || $("#km_inputvalue3").val()=='' || $("#km_inputvalue4").val()=='' || $("#km_inputvalue5").val()==''){layer.alert('请确保每项不能为空！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
        if($('#km_inputname').html()=='下单ＱＱ' && (inputvalue.length<5 || inputvalue.length>11)){layer.alert('请输入正确的QQ号！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
        if($("#km_inputname2").html() == '说说ID'||$("#km_inputname2").html() == '说说ＩＤ'){
            if($("#km_inputvalue2").val().length != 24){layer.alert('说说必须是原创说说！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
        }
        $('#submit_card').val('Loading');
        $('#result1').hide();
        $.ajax({
            type : "POST",
            url : "ajax.php?act=card",
            data : {km:km,inputvalue:inputvalue,inputvalue2:$("#km_inputvalue2").val(),inputvalue3:$("#km_inputvalue3").val(),inputvalue4:$("#km_inputvalue4").val(),inputvalue5:$("#km_inputvalue5").val()},
            dataType : 'json',
            success : function(data) {
                if(data.code == 0){
                    alert(data.msg,{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
                    window.location.href='?buyok=1';
                }else{
                    layer.alert(data.msg,{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
                }
                $('#submit_card').val('立即购买');
            }
        });
    });
    $(document).on('click','#submit_query',function () {
        if($("input[name=qq]").length > 0){
            $("input[name=qq]").attr("id","qq3");
        }
        var qq=$("#qq3").val(),
            type=$("input[name=queryType]:checked").val();
        queryOrder(type,qq,1);
    });
    $(document).on('click','#submit_lqq',function () {
        var qq=$("#qq4").val();
        if(qq==''){layer.alert('QQ号不能为空！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
        if(qq.length<5 || qq.length>11){layer.alert('请输入正确的QQ号！',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});return false;}
        $('#result3').hide();
        if($.cookie('lqq') && $.cookie('lqq').indexOf(qq)>=0){
            $('#result3').html('<div class="alert alert-success"><img src="assets/img/ico_success.png">&nbsp;该QQ已经提交过，请勿重复提交！</div>');
            $("#result3").slideDown();
            return false;
        }
        $('#submit_lqq').val('Loading');
        $.ajax({
            type : "POST",
            url : "ajax.php?act=lqq",
            data : {qq:qq,salt:hashsalt},
            dataType : 'json',
            success : function(data) {
                if($.cookie('lqq')){
                    $.cookie('lqq', $.cookie('lqq')+'-'+qq);
                }else{
                    $.cookie('lqq', qq);
                }
                $('#result3').html('<div class="alert alert-success"><img src="assets/img/ico_success.png">&nbsp;QQ已提交 正在为您排队,可能需要一段时间 请稍后查看圈圈增长情况</div>');
                $("#result3").slideDown();
                $('#submit_lqq').val('立即提交');
            }
        });
    });

    var i = $("#num").val();
    $(document).on('click','#num_add',function () {
        if ($("#need").val() == ''){
            layer.alert('请先选择商品',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
            return false;
        }
        var multi = $('#tid option:selected').attr('multi');
        var count = $('#tid option:selected').attr('count');
        if (multi == 0){
            layer.alert('该商品不支持选择数量',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
            return false;
        }
        i++;
        var price = $('#tid option:selected').attr('price');
        $("#num").val(i);
        price = price * i;
        count = count * i;
        if(count>0)$('#need').val('￥'+price.toFixed(2) +"元 ➠ "+count+"个");
        else $('#need').val('￥'+price.toFixed(2) +"元");
    });
    $(document).on('click','#num_min',function () {
        if($("#num").val()<=1){
            layer.msg('最低下单一份哦！');
            return false;
        }
        if ($("#need").val() == ''){
            layer.alert('请先选择商品',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
            return false;
        }
        var multi = $('#tid option:selected').attr('multi');
        var count = $('#tid option:selected').attr('count');
        if (multi == 0){
            layer.alert('该商品不支持选择数量',{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
            return false;
        }
        i--;
        var price = $('#tid option:selected').attr('price');
        $("#num").val(i);
        price = price * i;
        count = count * i;
        if(count>0)$('#need').val('￥'+price.toFixed(2) +"元 ➠ "+count+"个");
        else $('#need').val('￥'+price.toFixed(2) +"元");
        if (i <= 0) {
            $("#num").val(1);
            i = 1;
            if(count>0)$('#need').val('￥'+$('#tid option:selected').attr('price') +"元 ➠ "+count+"个");
            else $('#need').val('￥'+$('#tid option:selected').attr('price') +"元");
        }
    });
    $(document).on('blur','#num',function () {
        var price = $('#tid option:selected').attr('price');
        var count = $('#tid option:selected').attr('count');
        if($("#num").val()<1){
            $("#num").val("1")
        }
        price = price * $("#num").val();
        count = count * $("#num").val();
        if(count>0)$('#need').val('￥'+price.toFixed(2) +"元 ➠ "+count+"个");
        else $('#need').val('￥'+price.toFixed(2) +"元");
    });

    var gogo;
    $(document).on("click","#start",function(){
        $("#gift").css("display",'block');
        ii=layer.load(1,{shade:0.3});
        $.ajax({
            type:"GET",
            url:"ajax.php?act=gift_start",
            dataType:"json",
            success:function(choujiang){
                layer.close(ii);
                if(choujiang.code == 0){
                    $("#start").css("display",'none');
                    $("#stop").css("display",'block');
                    var obj = eval(choujiang.data);
                    var len = obj.length;
                    gogo = setInterval(function(){
                        var num = Math.floor(Math.random()*len);
                        var id = obj[num]['tid'];
                        var v = obj[num]['name'];
                        $("#roll").html(v);
                    },100);
                }else{
                    layer.alert(choujiang.msg,{title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
                }
            }
        });
    });
    $(document).on("click","#stop",function(){
        ii=layer.load(1,{shade:0.3});
        clearInterval(gogo);
        $("#roll").html('正在抽奖中..');
        var rand = Math.random(1);
        $.ajax({
            type:"GET",
            url:"ajax.php?act=gift_start&action=ok&r=" + rand,
            dataType:"json",
            success:function(msg){
                layer.close(ii);
                if(msg.code==0){
                    $.ajax({
                        type:"POST",
                        url:"ajax.php?act=gift_stop&r=" + rand,
                        data:{hashsalt:hashsalt,token:msg.token},
                        dataType:"json",
                        success:function(data){
                            if(data.code == 0){
                                $("#roll").html('恭喜您抽到奖品：'+data.name);
                                $("#start").css("display",'block');
                                $("#stop").css("display",'none');
                                layer.alert('恭喜您抽到奖品：'+data.name+'，请填写中奖信息', {
                                    skin: 'layui-layer-molv layui-layer-wxd'
                                    ,closeBtn: 0
                                }, function(){
                                    window.location.href='?gift=1&cid='+data.cid+'&tid='+data.tid;
                                });
                            }else{
                                layer.alert(data.msg,{icon:2,shade:0.3,title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
                                $("#roll").html('点击下方按钮开始抽奖');
                                $("#start").css("display",'block');
                                $("#stop").css("display",'none');
                                $("#gift").css("display",'none');
                            }
                        }
                    });
                }else{
                    layer.alert(msg.msg,{icon:2,shade:0.3,title: '小提示',skin: 'layui-layer-molv layui-layer-wxd'});
                    $("#start").css("display",'block');
                    $("#stop").css("display",'none');
                    $("#gift").css("display",'none');
                }
            }
        });
    });


    if(homepage == true){
        getcount();
    }
    if($_GET['buyok']){
        var orderid = $_GET['orderid'];
        $("#tab-query").tab('show');
        $("#submit_query").click();
        isModal=false;
    }else if($_GET['chadan']){
        $("#tab-query").tab('show');
        isModal=false;
    }
    if($_GET['gift']){
        isModal=false;
    }
    if($_GET['cid']){
        var cid = parseInt($_GET['cid']);
        $("#cid").val(cid);
    }
    $("#cid").change();

    if($.cookie('sec_defend_time'))$.removeCookie('sec_defend_time', { path: '/' });
    if( !$.cookie('op') && isModal==true){
        $('#myModal').modal({
            keyboard: true
        });
        var cookietime = new Date();
        cookietime.setTime(cookietime.getTime() + (60*60*1000));
        $.cookie('op', false, { expires: cookietime });
    }
    var visits = $.cookie("counter")
    if(!visits)
    {
        visits=1;
    }
    else
    {
        visits=parseInt(visits)+1;
    }
    $('#counter').html(visits);
    $.cookie("counter", visits, 24*60*60*30);

    if($('#audio-play').is(':visible')){
        audio_init.play();
    }

});

$(document).on('change','#getQueryType',function () {
    var hqType = $("option:selected").val();
    switch(hqType) {
        case 'kuaishou':
            $("input[name=qq]").attr("placeholder","填写快手链接将自动获取ID");
            $("input[name=qq]").attr("id","inputvalue");
            $(document).on('change','#inputvalue',function () {
                getkuaishouid();
            });
            break;
        case 'douyin':
            $("input[name=qq]").attr("placeholder","填写抖音分享链接将自动获取ID");
            $("input[name=qq]").attr("id","inputvalue");
            $(document).on('change','#inputvalue',function () {
                getdouyinid();
            });
            break;
        case 'xiaohs':
            $("input[name=qq]").attr("placeholder","填写小H书分享链接将自动获取ID");
            $("input[name=qq]").attr("id","inputvalue");
            $(document).on('change','#inputvalue',function () {
                getxiaohongshuid();
            });
            break;
        case 'weishi':
            $("input[name=qq]").attr("placeholder","填写微视分享链接将自动获取ID");
            $("input[name=qq]").attr("id","inputvalue");
            $(document).on('change','#inputvalue',function () {
                getweishiid();
            });
            break;
        case 'kanjia':
            $("input[name=qq]").attr("placeholder","填写拼多多/京东砍价分享链接将自动获取ID");
            $("input[name=qq]").attr("id","inputvalue");
            $(document).on('change','#inputvalue',function () {
                getkanjiaid();
            });
            break;
        case 'huoshan':
            $("input[name=qq]").attr("placeholder","填写小火山分享链接将自动获取ID");
            $("input[name=qq]").attr("id","inputvalue");
            $(document).on('change','#inputvalue',function () {
                gethuoshanid();
            });
            break;
        case 'quanmin':
            $("input[name=qq]").attr("placeholder","填写歌曲分享链接将自动获取ID");
            $("input[name=qq]").attr("id","inputvalue");
            $(document).on('change','#inputvalue',function () {
                getsongid();
            });
            break;
        case 'bilibili':
            $("input[name=qq]").attr("placeholder","填写哔哩哔哩分享链接将自动获取ID");
            $("input[name=qq]").attr("id","inputvalue");
            $(document).on('change','#inputvalue',function () {
                getbilibiliid();
            });
            break;
        default:
            $("input[name=qq]").attr("placeholder","输入查询的内容（留空则显示最新订单）");
            $("input[name=qq]").attr("id","qq3");
    }
});
