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
 * @module      Devices
 * @filesource  devices.views.edit
 */
?>
<div class="modal-block modal-block-md modal-ajaxcontent-block">
    <div class="panel panel-color panel-info">
        <div class="panel-heading">
            <button type="button" class="close panel-action modal-dismiss pull-right" aria-hidden="true"><i class="fa fa-close"></i></button>
            <h3 class="panel-title"><?=$ictdata['idioms']['devices_edit_title'];?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <form id="modal-form" class="form-horizontal  mb-lg">
                        <div class="form-group">
                        <label class="col-sm-2 control-label" for="field-1"><?=$ictdata['idioms']['devices_f1'];?></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="uid" name="device-id" value="<?=$info->uid;?>" placeholder="<?=$ictdata['idioms']['devices_f1p'];?>" disabled>
                            <span id="availability"></span>
                            </div>
                        </div>
                        <div class="form-group">
                        <label class="col-sm-2 control-label" for="field-2"><?=$ictdata['idioms']['devices_f2'];?></label>
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
                            <label class="col-sm-2 control-label" for="field-3"><?=$ictdata['idioms']['status'];?></label>
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