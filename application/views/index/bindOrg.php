<div>
<?php
include_once(APPPATH."views/common/bread.php");
?>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-danger" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            您尚未创建或加入任何商户
        </div>
    </div>
    <div class="col-lg-6">
        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption">
                    <span class="glyphicon glyphicon-star"></span>
                    创建商户
                </div>
            </div>
            <div class="portlet-body">
                <div class="note note-warning text-left">
                    <p>
                        如果您是商户老板，请在这里创建商户。
                    </p>

                </div>
                <button type="button" class="btn yellow-gold" onclick="lightbox({url:'<?=site_url("org/createOrg")?>'})">
                            创建 <span class="glyphicon glyphicon-star"></span>
                            </button>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption">
                    <span class="glyphicon glyphicon-ok-circle"></span>
                    加入商户
                </div>
            </div>
            <div class="portlet-body">
                <div class="note note-warning text-left">
                    <p>
                        如果您是商户员工，或者已经创建过商户，请询问 商户加入 密码，这个密码在店主的商户信息页面。
                    </p>

                </div>
                <form role="form" id="bindForm" action="<?=site_url("index/doLogin")?>" method="post">

                    <h3 class="form-title">请输入商户加入密码</h3>
                    <div class="form-group">
                        <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                        <label class="control-label visible-ie8 visible-ie9">店铺加入密码</label>
                        <div class="input-icon">
                            <span class="glyphicon glyphicon-ok-circle"></span>
                            <input class="form-control placeholder-no-fix" type="text" placeholder="店铺加入密码" id="enterCode" name="enterCode" required>
                        </div>
                    </div>

                    <div class="form-group">

                            <button type="button" class="btn green-meadow pull-right" onclick="reqBind()">
                            加入 <span class="glyphicon glyphicon-ok-circle"></span>
                            </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
var bindFormValidator = $("#bindForm").validate();
function reqBind(){
    if (bindFormValidator.form()==false) {
        return;
    };
    reqOperator('index','doBindOrg',$('#enterCode').val())
}
</script>
