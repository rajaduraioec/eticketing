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
 * @filesource  users.views.edit
 */

?>
<div class="modal-block modal-block-md modal-ajaxcontent-block">
    <div class="panel panel-color panel-info">
        <div class="panel-heading">
            <button type="button" class="close panel-action modal-dismiss pull-right" aria-hidden="true"><i class="fa fa-close"></i></button>
            <h3 class="panel-title"><?=$ictdata['idioms']['users_edit_title'];?></h3>
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
                                foreach($depots as $depot):
                                    if($info->depots_id==$depot['id_depots'])
                                    echo '<option value="'.$depot['id_depots'].'" selected>'.$depot['depot_name'].'</option>';
                                    else
                                    echo '<option value="'.$depot['id_depots'].'">'.$depot['depot_name'].'</option>';
                                    
                                endforeach;
                                ?>
                            </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-2"><?=$ictdata['idioms']['users_f2'];?></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="username" name="user-name" value="<?=$info->username;?>" placeholder="<?=$ictdata['idioms']['users_f2p'];?>" disabled>
                            <span id="availability"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-3"><?=$ictdata['idioms']['users_f3'];?></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="first-name" placeholder="<?=$ictdata['idioms']['users_f3p'];?>" value="<?=$info->first_name;?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-4"><?=$ictdata['idioms']['users_f4'];?></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="last-name" placeholder="<?=$ictdata['idioms']['users_f4p'];?>" value="<?=$info->last_name;?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-5"><?=$ictdata['idioms']['users_f5'];?></label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" name="email" placeholder="<?=$ictdata['idioms']['users_f5p'];?>" value="<?=$info->email;?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-6"><?=$ictdata['idioms']['status'];?></label>
                            <div class="col-sm-10">
                                <?php
                                if($info->active==1)
                                    echo '<input type="checkbox" name="active" checked data-plugin="switchery" data-color="#039cfd"  data-size="small"/>';
                                else
                                    echo '<input type="checkbox" name="active" data-plugin="switchery" data-color="#039cfd"  data-size="small"/>';
                                ?>
                            </div>
                        </div>
                        <div class="form-group mt-lg" align="center">
                            <button id='modal-submit' onclick="validateme('<?=$this->rview->url($ctrl_name.'/save/'.$id);?>','modal-form')" class="btn btn-primary"><?=$ictdata['idioms']['submitbtn'];?></button>
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
                $('[data-plugin="switchery"]').each(function (idx, obj) {
                    new Switchery($(this)[0], $(this).data());
                });
            });
        </script>
    </div>
</div>
