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
 * @module      Config
 * @filesource  Config.views.config
 */
 
 ?>
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title"><?=$ictdata['idioms']['ct_module_title'];?></h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="panel panel-border panel-primary">
        <div class="panel-heading"></div><div class="panel-body">
            <div class="row">
            	<form id="config-form" class="form-horizontal">
            		<div class="panel-body">
            			<div class="row">
        	    			<div class="col-sm-6">
                                            <?php
                                            if(isset($depotsid)):
                                                echo '<input type="hidden" name="depot-id" value="'.$depotsid.'">';
                                            else:
                                                
                                            ?>
		                        <div class="form-group">
		                            <label class="col-sm-4 control-label" for="field-1" style="text-align:left"><?=$ictdata['idioms']['ct_f1'];?></label>
		                            <div class="col-sm-8">
		                                <select class="form-control select2" name="depot-id" id="depot" required data-placeholder="Select Depot ...">
                                                    <option></option>
			                                <?php
			                                foreach($depots as $depot)
			                                    echo '<option value="'.$depot['id_depots'].'">'.$depot['depot_name'].'</option>';
			                                ?>
                                                </select>
		                            </div>
		                        </div>
                                            <?php
                                            endif;
                                            ?>
		                        <div class="form-group">
		                            <label class="col-sm-4 control-label" for="field-2" style="text-align:left"><?=$ictdata['idioms']['ct_f2'];?></label>
		                            <div class="col-sm-8">
		                                <select class="form-control select2" name="device-id" id="device" required  data-placeholder="Select Device ...">
                                                    <option></option>
			                                <?php
			                                foreach($devices as $device)
			                                    echo '<option value="'.$device['id_devices'].'">'.$device['uid'].'</option>';
			                                ?>
		                                </select>
		                            </div>
		                        </div>
		                        <div class="form-group">
		                            <label class="col-sm-4 control-label" for="field-3" style="text-align:left"><?=$ictdata['idioms']['ct_f3'];?></label>
		                            <div class="col-sm-8">
		                                <select class="form-control select2" name="driver-id" id="driver" required>
			                        <option></option>
			                            </select>
		                            </div>
		                        </div>
		                        <div class="form-group">
		                            <label class="col-sm-4 control-label" for="field-4" style="text-align:left"><?=$ictdata['idioms']['ct_f4'];?></label>
		                            <div class="col-sm-8">
		                                <select class="form-control select2" name="conductor-id" id="conductor" required>
			                        <option></option>
			                            </select>
		                            </div>
		                        </div>
		                        <div class="form-group">
		                            <label class="col-sm-4 control-label" for="field-5" style="text-align:left"><?=$ictdata['idioms']['ct_f5'];?></label>
		                            <div class="col-sm-8">
		                                <select class="form-control select2" name="bus-id" id="bus" required>
			                        <option></option>
			                            </select>
		                            </div>
		                        </div>
		                        <div class="form-group">
		                            <label class="col-sm-4 control-label" for="field-6" style="text-align:left"><?=$ictdata['idioms']['ct_f6'];?></label>
		                            <div class="col-sm-8">
		                            	<input type="checkbox" name="luggage_status" checked data-plugin="switchery" data-color="#039cfd"  data-size="small"/>
		                            </div>
		                        </div>
	                        </div>
    	                    <div class="col-sm-6">
		                        <div class="form-group">
		                            <label class="col-sm-4 control-label" for="field-7" style="text-align:left"><?=$ictdata['idioms']['ct_f7'];?></label>
		                            <div class="col-sm-8">
		                                <select  class="select2 form-control select2-multiple" multiple="multiple" multiple data-placeholder="Choose ..." name="route-id[]" id="route" required>
		                                    <option></option>
		                                </select>
		                            </div>
		                        </div>
		                        <div class="form-group">
		                            <label class="col-sm-4 control-label" for="field-8" style="text-align:left"><?=$ictdata['idioms']['ct_f8'];?></label>
		                            <div class="col-sm-8">
                                                <input type="number" class="form-control" id="odometer" name="odometer" placeholder="<?=$ictdata['idioms']['ct_f8p'];?>">
		                            </div>
		                        </div>
		                        <div class="form-group">
		                            <label class="col-sm-4 control-label" for="field-9" style="text-align:left"><?=$ictdata['idioms']['ct_f9'];?></label>
		                            <div class="col-sm-8">
                                                <input type="number" class="form-control" id="shiftno" name="shiftno" placeholder="<?=$ictdata['idioms']['ct_f9p'];?>">
		                            </div>
		                        </div>
		                        <div class="form-group">
		                            <label class="col-sm-4 control-label" for="field-10" style="text-align:left"><?=$ictdata['idioms']['ct_f10'];?></label>
		                            <div class="col-sm-8">
                                                <input type="number" class="form-control" id="dutyno" name="dutyno" placeholder="<?=$ictdata['idioms']['ct_f10p'];?>">
		                            </div>
		                        </div>
	                        </div>
	    				</div>
        			</div>
        			<div class="row">
		            	<div class="form-group mt-lg" align="center">
		                    <button id='config-submit' onclick="configvalidateme('<?=$this->rview->url($ctrl_name.'/configure/');?>','config-form')" class="btn btn-primary"><?=$ictdata['idioms']['configbtn'];?></button>
		                </div>
		            </div>
        		</form>	
    		</div>
		</div>
	</div>
</div>
<script type="text/javascript">
    var baseurl='<?=$this->rview->url();?>';
</script>
<script src="<?=$this->rview->init('jquery.ict.configtool.init');?>"></script>