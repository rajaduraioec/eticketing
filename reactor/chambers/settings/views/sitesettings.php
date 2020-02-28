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
 * @module      Settings
 * @filesource  Settings.views.sitesettings
 */

?>
<div class="modal-block modal-block-md modal-ajaxcontent-block">
    <div class="panel panel-color panel-info">
        <div class="panel-heading">
            <button type="button" class="close panel-action modal-dismiss pull-right" aria-hidden="true"><i class="fa fa-close"></i></button>
            <h3 class="panel-title"><?=$ictdata['idioms']['settings_module_title'];?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <form id="modal-form" class="form-horizontal  mb-lg">
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-1"><?=$ictdata['idioms']['settings_f1'];?></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="field-1" name="sitename" placeholder="<?=$ictdata['idioms']['settings_f1p'];?>" value="<?=$info->sitename;?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-2"><?=$ictdata['idioms']['settings_f2'];?></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="field-2" name="slogan" placeholder="<?=$ictdata['idioms']['settings_f2p'];?>" value="<?=$info->slogan;?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-3"><?=$ictdata['idioms']['settings_f3'];?></label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" id="field-3" name="email" placeholder="<?=$ictdata['idioms']['settings_f3p'];?>" value="<?=$info->email;?>" required>
                            </div>
                        </div>
                        <!-- <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-4"><?=$ictdata['idioms']['settings_f4'];?></label>
                            <div class="col-sm-10">
                                <select class="form-control" name='date_format'>
                                    <option value='dd-mm-yy' <?php if($info->date_format=='dd-mm-yy') echo 'selected';?>>dd-mm-yy</option>
                                    <option value='dd-mm-yyyy' <?php if($info->date_format=='dd-mm-yyyy') echo 'selected';?>>dd-mm-yyyy</option>
                                    <option value='mm-dd-yyyy' <?php if($info->date_format=='mm-dd-yyyy') echo 'selected';?>>mmd-dd-yyyy</option>
                                    <option value='yyyy-mm-dd' <?php if($info->date_format=='yyyy-mm-dd') echo 'selected';?>>yyyy-mm-dd</option>
                                    <option value='dd/mm/yy' <?php if($info->date_format=='dd/mm/yy') echo 'selected';?>>dd/mm/yy</option>
                                    <option value='dd/mm/yyyy' <?php if($info->date_format=='dd/mm/yyyy') echo 'selected';?>>dd/mm/yyyy</option>
                                    <option value='mm/dd/yyyy' <?php if($info->date_format=='mm/dd/yyyy') echo 'selected';?>>mm/dd/yyyy</option>
                                    <option value='yyyy/mm/dd' <?php if($info->date_format=='yyyy/mm/dd') echo 'selected';?>>yyyy/mm/dd</option>
                                </select>
                            </div>
                        </div> -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-5"><?=$ictdata['idioms']['settings_f5'];?></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="field-5" name="footer" placeholder="<?=$ictdata['idioms']['settings_f5p'];?>" value="<?=$info->footer;?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-6"><?=$ictdata['idioms']['settings_f6'];?></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="field-1" name="inspass" placeholder="<?=$ictdata['idioms']['settings_f6p'];?>" value="<?=$info->inspector_pass;?>" required>
                            </div>
                        </div>
                        <div class="form-group mt-lg" align="center">
                            <button id='modal-submit' onclick="validateme('<?=$this->rview->url($ctrl_name.'/savesite/');?>','modal-form')" class="btn btn-primary"><?=$ictdata['idioms']['submitbtn'];?></button>
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
    </div>
</div>