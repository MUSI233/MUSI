var handlerEmbed = function (captchaObj) {
    captchaObj.appendTo('#captcha');
    captchaObj.onReady(function () {
        $("#captcha_wait").hide();
    }).onSuccess(function () {
        var result = captchaObj.getValidate();
        if (!result) {
            return alert('请完成验证');
        }
        $("#captchaform").html('<input type="hidden" name="geetest_challenge" value="'+result.geetest_challenge+'" /><input type="hidden" name="geetest_validate" value="'+result.geetest_validate+'" /><input type="hidden" name="geetest_seccode" value="'+result.geetest_seccode+'" />');
    });
};
var handlerEmbed2 = function (token) {
    if (!token) {
        return alert('请完成验证');
    }
    $("#captchaform").html('<input type="hidden" name="token" value="'+token+'" />');
};

function load_captcha() {
    var captcha_type = $("input[name='captcha_type']").val();
    captcha_type = parseInt(captcha_type);
    if(captcha_type === 1){
        $.getScript("//static.geetest.com/static/tools/gt.js", function() {
            $.ajax({
                url: "/ajax.php?act=captcha&t=" + (new Date()).getTime(),
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
        });
    }else if(captcha_type === 2){
        var appid = $("input[name='appid']").val();
        $.getScript("//cdn.dingxiang-inc.com/ctu-group/captcha-ui/index.js", function() {
            var myCaptcha = _dx.Captcha(document.getElementById('captcha'), {
                appId: appid,
                type: 'basic',
                style: 'oneclick',
                width: "",
                success: handlerEmbed2
            })
            myCaptcha.on('ready', function () {
                $('#captcha_text').hide();
            })
        });
    }
}

$(document).ready(function () {
    load_captcha();
});