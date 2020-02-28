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
 * @module      Buses
 * @filesource  Buses.views.add
 */
 
 ?>
 
<div class="modal-block modal-block-md modal-ajaxcontent-block">
    <div class="panel panel-color panel-info">
        <div class="panel-heading">
            <button type="button" class="close panel-action modal-dismiss pull-right" aria-hidden="true"><i class="fa fa-close"></i></button>
            <h3 class="panel-title"><?=$ictdata['idioms']['buses_add_title'];?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <form id="modal-form" class="form-horizontal  mb-lg">
                        
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-1"><?=$ictdata['idioms']['buses_f1'];?></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="busno" name="bus-no" placeholder="<?=$ictdata['idioms']['buses_f1p'];?>" maxlength="25" pattern="[a-zA-Z0-9]+" title="Only 25 Character alphanumeric allowed" required>
                            <span id="availability"></span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-2"><?=$ictdata['idioms']['buses_f2'];?></label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" name="seatcapacity" placeholder="<?=$ictdata['idioms']['buses_f2p'];?>" pattern="[0-9]+" title="Enter Bus Seat Capacity in numeric allowed" required>
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
                    $("#modal-submit").prop("disabled",true);
                    $("#busno").blur(function () {
                            var value = $(this).val();
                            if (value === '') {
                    $("#availability").html("");
                    }else{
                        
            $.ajax({
                        url : "<?=$this->rview->url($ctrl_name.'/exists');?>/"+value ,
                        type: "GET",
                        dataType: "JSON",
                        success: function(response)
                        {
                            if(response.status)
                            {
                                $("#modal-submit").prop("disabled",false);
                                $("#availability").html(response.content);
                            }else{
                            	$("#modal-submit").prop("disabled",true);
                                $("#availability").html(response.content);
                            }
                        }
                    });   
                  } 
                    });
            });
        </script>
    </div>
</div>