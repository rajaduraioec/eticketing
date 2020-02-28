<?php


if (!defined('RAPPVERSION'))
    exit('No direct script access allowed');
/* 
 * 
 * @package	MMT Transport E-Ticketing
 * @Framework	Reactor Framework
 * @author      Increatech Dev Team
 * @copyright	Copyright (c) Since 2017, Increatech Business Solution Pvt Ltd, India (http://increatech.com/)
 * @link        https://increatech.com
 * @since       Version 1.0.0
 * @module      auth
 * @filesource  auth.views.password
 */

?>
<div class="modal-block modal-block-md modal-ajaxcontent-block">
    <div class="panel panel-color panel-info">
        <div class="panel-heading">
            <button type="button" class="close panel-action modal-dismiss pull-right" aria-hidden="true"><i class="fa fa-close"></i></button>
            <h3 class="panel-title"><?=$ictdata['idioms']['auth_cp_title'];?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <form id="modal-form" class="form-horizontal  mb-lg">
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-1"><?=$ictdata['idioms']['auth_cp_f1'];?></label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" name="opassword" placeholder="<?=$ictdata['idioms']['auth_cp_f1p'];?>" title="Old password required" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-2"><?=$ictdata['idioms']['auth_cp_f2'];?></label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" name="password" placeholder="<?=$ictdata['idioms']['auth_cp_f2p'];?>" pattern=".{8,20}" title="8 to 20 characters password required" required id="pass1" onkeyup="checkPass(); return false;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-3"><?=$ictdata['idioms']['auth_cp_f3'];?></label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" name="cpassword" placeholder="<?=$ictdata['idioms']['auth_cp_f3p'];?>" required  id="pass2" onkeyup="checkPass(); return false;">
                                    <span id="confirmMessage" class="confirmMessage"></span>
                            </div>
                        </div>
                    </form>
                        <div class="form-group mt-lg" align="center">
                            <button id='modal-submit' onclick="validateme('<?=$this->rview->url($ctrl_name.'/setpassword/');?>','modal-form')" class="btn btn-primary"><?=$ictdata['idioms']['submitbtn'];?></button>
                        </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-md-12 text-right">
                    <button class="btn btn-default modal-dismiss"><?=$ictdata['idioms']['x'];?></button>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(document).ready(function($) {
                    $("#modal-submit").prop("disabled",true);
            });
function checkPass()
{
    var pass1 = document.getElementById('pass1');
    var pass2 = document.getElementById('pass2');
    var message = document.getElementById('confirmMessage');
    var goodColor = "#66cc66";
    var badColor = "#ff6666";
    if(pass1.value === pass2.value){
//        pass2.style.backgroundColor = '';
        message.style.color = goodColor;
                                $("#modal-submit").prop("disabled",false);
        message.innerHTML = "Passwords Match!";
    }else{
//        pass2.style.backgroundColor = badColor;
        message.style.color = badColor;
                    $("#modal-submit").prop("disabled",true);
        message.innerHTML = "Passwords Not Match!";
    }
}  
        </script>
    </div>
</div>