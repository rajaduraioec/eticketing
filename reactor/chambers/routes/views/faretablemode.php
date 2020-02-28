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
 * @module      Routes
 * @filesource  Routes.views.addfaretable
 */

?>

<div class="modal-block modal-block-md modal-ajaxcontent-block" >
    <div class="panel panel-color panel-info"  >
        <div class="panel-heading">
            <button type="button" class="close panel-action modal-dismiss pull-right" aria-hidden="true"><i class="fa fa-close"></i></button>
            <h3 class="panel-title"><?=$ictdata['idioms']['routes_fare_title'];?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <form id="modal-form" class="form-horizontal  mb-lg">
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="field-1"><?=$ictdata['idioms']['routes_f1'];?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="route-name" placeholder="<?=$ictdata['idioms']['routes_f1p'];?>" value="<?=$info->route_name;?>" required maxlength="30" pattern="[a-zA-Z0-9\s]+" title="Only 30 Character alphanumeric and space allowed" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="field-2"><?=$ictdata['idioms']['routes_f2'];?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="routeid" name="routeid" placeholder="<?=$ictdata['idioms']['routes_f2p'];?>" value="<?=$info->route_no;?>" required maxlength="15" pattern="[a-zA-Z0-9]+" title="Unique Route ID upto 15 Character alphanumeric is allowed" disabled>
                            
                            </div>
                        </div>
                        <div class="form-group">
                                <label class="col-sm-4 control-label" for="field-1"><?=$ictdata['idioms']['routes_f11'];?></label>

                                <div class="col-sm-8">
                                    <label class="radio-inline">
                                        <input type="radio" value="1" name="f_table_mode" checked required>
                                            CSV File upload
                                    </label>
                                    <?php
                                    if($info->total_stages<=10):?>
                                    <label class="radio-inline">
                                        <input type="radio" value="2" name="f_table_mode" required>
                                            Direct Entry
                                    </label>
                                    <?php else:
                                        echo '<span>Direct Entry Disabled for more than 10 stages</span>';
                                    endif;
                                    ?>
                                </div>
                        </div>
                        
                        <div class="form-group mt-lg" align="center">
                            <button id='modal-submit' onclick="validateme('<?=$this->rview->url($ctrl_name.'/faretable/'.$id);?>','modal-form')" class="btn btn-primary"><?=$ictdata['idioms']['proceedbtn'];?></button>
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
