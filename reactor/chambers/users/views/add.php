<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/* 
 * 
 * @package	MMT Transport E-Ticketing
 * @Framework	Reactor Framework
 * @author      Increatech Dev Team
 * @copyright	Copyright (c) Since 2017, Increatech Business Solution Pvt Ltd, India (http://increatech.com/)
 * @link        https://increatech.com
 * @since       Version 1.0.0
 * @module      Users
 * @filesource  users.views.add
 */

?>
<div class="modal-block modal-block-md modal-ajaxcontent-block">
    <div class="panel panel-color panel-info">
        <div class="panel-heading">
            <button type="button" class="close panel-action modal-dismiss pull-right" aria-hidden="true"><i class="fa fa-close"></i></button>
            <h3 class="panel-title"><?=$ictdata['idioms']['users_add_title'];?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <form id="modal-form" class="form-horizontal  mb-lg">
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-1"><?=$ictdata['idioms']['users_f1'];?></label>
                            <div class="col-sm-10">
                                
                                <select class="form-control select2" required name="depots_id">
                                <option></option>
                                <?php
                                foreach($depots as $depot)
                                    echo '<option value="'.$depot['id_depots'].'">'.$depot['depot_name'].'</option>';
                                ?>
                            </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-2"><?=$ictdata['idioms']['users_f2'];?></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="username" name="user-name" placeholder="<?=$ictdata['idioms']['users_f2p'];?>" required>
                            <span id="availability"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-3"><?=$ictdata['idioms']['users_f3'];?></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="first-name" placeholder="<?=$ictdata['idioms']['users_f3p'];?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-4"><?=$ictdata['idioms']['users_f4'];?></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="last-name" placeholder="<?=$ictdata['idioms']['users_f4p'];?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-5"><?=$ictdata['idioms']['users_f5'];?></label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" name="email" placeholder="<?=$ictdata['idioms']['users_f5p'];?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-6"><?=$ictdata['idioms']['users_f6'];?></label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" name="password" placeholder="<?=$ictdata['idioms']['users_f6p'];?>" pattern=".{8,20}" title="8 to 20 characters password required" required id="pass1" onkeyup="checkPass(); return false;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-7"><?=$ictdata['idioms']['users_f7'];?></label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" name="cpassword" placeholder="<?=$ictdata['idioms']['users_f7p'];?>" required  id="pass2" onkeyup="checkPass(); return false;">
                                    <span id="confirmMessage" class="confirmMessage"></span>
                            </div>
                        </div>
                        <div class="form-group mt-lg" align="center">
                            <button id='modal-submit' onclick="validateme('<?=$this->rview->url($ctrl_name.'/create/');?>','modal-form')" class="btn btn-primary"><?=$ictdata['idioms']['submitbtn'];?></button>
                        </div>
                    </form>
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
                    $(".select2").select2({
                    dropdownParent: $(".select2").parent()
                });
                    $("#modal-submit").prop("disabled",true);
                    $("#username").blur(function () {
                            var value = $(this).val();
                            if (value === '') {
                    $("#availability").html("");
                    }else{
                        
            $.ajax({
                        url : "<?=$this->rview->url($ctrl_name.'/exists/');?>"+value ,
                        type: "GET",
                        dataType: "JSON",
                        success: function(response)
                        {
                            if(response.status)
                            {
                                $("#modal-submit").prop("disabled",false);
                                $("#availability").html(response.content);
                            }else{
                                $("#availability").html(response.content);
                            }
                        }
                    });   
                  } 
                    });
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
        message.innerHTML = "Passwords Match!";
    }else{
//        pass2.style.backgroundColor = badColor;
        message.style.color = badColor;
        message.innerHTML = "Passwords Not Match!";
    }
}  
        </script>
    </div>
</div>