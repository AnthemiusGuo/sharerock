<!-- BEGIN LOGIN FORM -->
<form id="loginForm" class="login-form" action="<?=site_url("aindex/doLogin")?>" method="post">
    <h3 class="form-title">登录</h3>
    <div class="form-group">
        <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
        <label class="control-label visible-ie8 visible-ie9">登录名</label>
        <div class="input-icon">
            <span class="glyphicon glyphicon-edit"></span>
            <input class="form-control placeholder-no-fix" type="text" placeholder="登录名" id="loginName" name="loginName">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label visible-ie8 visible-ie9">密码</label>
        <div class="input-icon">
            <span class="glyphicon glyphicon-edit"></span>
            <input class="form-control placeholder-no-fix" type="password" placeholder="密码" id="uPassword" name="uPassword">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-6">
            <div class="checkbox">
                <label>
                    <input type="checkbox" id="uRememberMe">记住我
                </label>
            </div>
        </div>
        <div class="col-sm-6">
            <button type="button" class="btn green-haze pull-right" onclick="req_login()">
            登录 <span class="glyphicon glyphicon-ok"></span>
            </button>
        </div>
    </div>

    <div class="clearfix"></div>

    <hr/>
    <div class="create-account">
        <h4>还没有帐号？</h4>
        <p>
            请其他有权限账户登录创建您的帐号，本后台不可自助注册帐号
        </p>
    </div>
    <hr/>
</form>
<!-- END LOGIN FORM -->
<script>
var validator = $("#loginForm").validate();
function req_login(){
    var loginName = $("#loginName").val();
    var uPassword = $("#uPassword").val();
    var uRememberMe = $("#uRememberMe").prop('checked');
    $("#loginForm .form-group").removeClass('has-error');
    if (validator.form()==false) {
        return;
    };
    ajax_post({m:'index',a:'doLogin',data:{loginName:loginName,uPassword:uPassword,uRememberMe:uRememberMe},callback:function(json){
            if (json.rstno>0){
                window.location.href=json.data.goto_url;
            } else {
                var showErr = {};
                showErr[json.data.err.id] = json.data.err.msg ;
                validator.showErrors(showErr);
            }
        }
  });
}
</script>
