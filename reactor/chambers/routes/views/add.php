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
 * @filesource  Routes.views.add
 */

?>
<div class="modal-block modal-block-md modal-ajaxcontent-block">
    <div class="panel panel-color panel-info">
        <div class="panel-heading">
            <button type="button" class="close panel-action modal-dismiss pull-right" aria-hidden="true"><i class="fa fa-close"></i></button>
            <h3 class="panel-title"><?=$ictdata['idioms']['routes_add_title'];?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <form id="modal-form" class="form-horizontal  mb-lg">
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-1"><?=$ictdata['idioms']['routes_f1'];?></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="route-name" placeholder="<?=$ictdata['idioms']['routes_f1p'];?>" required maxlength="30" pattern="[a-zA-Z0-9\s]+" title="Only 30 Character alphanumeric and space allowed">
                            <span id="availability"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-2"><?=$ictdata['idioms']['routes_f2'];?></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="routeid" name="routeid" placeholder="<?=$ictdata['idioms']['routes_f2p'];?>" required maxlength="15" pattern="[a-zA-Z0-9]+" title="Unique Route No upto 15 Character alphanumeric is allowed">
                            <span id="availability"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-3"><?=$ictdata['idioms']['routes_f3'];?></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="service_type" placeholder="<?=$ictdata['idioms']['routes_f3p'];?>" required maxlength="15" pattern="[a-zA-Z0-9]+" title="Only 15 Character alphanumeric is allowed">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="field-4"><?=$ictdata['idioms']['routes_f4'];?></label>
                            <div class="col-sm-10">
                                <label class="radio-inline">
                                    <input type="radio" value="1" name="f_table_type" checked required>
                                    Linear
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" value="2" name="f_table_type" required>
                                    Matrix  
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                                <label class="col-sm-2 control-label" for="field-5"><?=$ictdata['idioms']['routes_f5'];?></label>

                                <div class="col-sm-10">
                                    <input type="number" class="form-control" id="field-5" name="route_target" placeholder="<?=$ictdata['idioms']['routes_f5p'];?>" required>
                                </div>
                        </div>
                        <div class="form-group">
                                <label class="col-sm-2 control-label" for="field-6"><?=$ictdata['idioms']['routes_f6'];?></label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control" id="field-6" name="load_factor" placeholder="<?=$ictdata['idioms']['routes_f6p'];?>" required>
                                </div>
                        </div>
                        <div class="form-group">
                                <label class="col-sm-2 control-label" for="field-7"><?=$ictdata['idioms']['routes_f7'];?></label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control" id="field-7" name="distance" placeholder="<?=$ictdata['idioms']['routes_f7p'];?>" required>
                                </div>
                        </div>
                        <div class="form-group">
                                <label class="col-sm-2 control-label" for="field-8"><?=$ictdata['idioms']['routes_f8'];?></label>

                                <div class="col-sm-10">
                                    <input type="number" class="form-control" id="field-8" name="total_stages" placeholder="<?=$ictdata['idioms']['routes_f8p'];?>" required>
                                </div>
                        </div>
                        <div class="form-group">
                                <label class="col-sm-2 control-label" for="field-9"><?=$ictdata['idioms']['routes_f9'];?></label>
                                <div class="col-sm-10 my-form"></div>
                        </div>
                        <div class="my-form">
                            <div class="col-sm-12 movethis">
                                <div class="form-group col-sm-12 text-box" id="text-box">
                                    <label class="col-sm-2 control-label" for="box1"><?=$ictdata['idioms']['routes_f10'];?> <span class="box-number">1</span></label>

                                    <div class="col-sm-8">
                                        <input type="text" name="stages[]" maxlength="15" id="ebox1"  class="form-control" pattern="[a-zA-Z0-9.\-\s]+" placeholder="<?=$ictdata['idioms']['routes_f10p'];?>" required/>

                                    </div>
                                    <a href="#" class="col-sm-2 add-box">Add</a>
                                </div>
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
            $("#routeid").blur(function () {
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
    // Starts
    
    
    $('.my-form .add-box').click(function(){
        var n = $('.text-box').length + 1;
        if( 200 < n ) {
            alert('Maximum 200 Stages only allowed');
            return false;
        }
		var box_html = $('<div class="form-group col-sm-12 text-box" id="text-box"><label class="col-sm-2 control-label" for="box' + n + '"><?=$ictdata['idioms']['routes_f10'];?> <span class="box-number">' + n + '</span></label><div class="col-sm-8"><input type="text" name="stages[]" maxlength="15" maxlength="15" value="" id="ebox' + n + '"  class="form-control"   pattern="[a-zA-Z0-9.\-\s]+" placeholder="<?=$ictdata['idioms']['routes_f10p'];?>" required/></div><a href="#" class="col-sm-2 remove-box">Remove</a></div>');
        box_html.hide();
        $('.my-form .text-box:last').after(box_html);
        box_html.fadeIn('slow');
        return false;
    });
    $('.my-form').on('click', '.remove-box', function(){
        $(this).parent().css( 'background-color', '#FF6C6C' );
        $(this).parent().fadeOut("slow", function() {
            $(this).remove();
            $('.box-number').each(function(index){
                $(this).text( index + 1 );
            });
        });
        return false;
    });
	
	$('.my-form').on('click', '.text-box:last', function(){
			 var n = $('.text-box').length + 1;
        if( 200 < n ) {
            alert('Maximum 200 Stages only allowed');
            return false;
		}
		var box_html = $('<div class="form-group col-sm-12 text-box" id="text-box"><label class="col-sm-2 control-label" for="box' + n + '"><?=$ictdata['idioms']['routes_f10'];?> <span class="box-number">' + n + '</span></label><div class="col-sm-8"><input type="text" name="stages[]" maxlength="15" value="" id="ebox' + n + '"  class="form-control"   pattern="[a-zA-Z0-9.\-\s]+" placeholder="<?=$ictdata['idioms']['routes_f10p'];?>" required/></div><a href="#" class="col-sm-2 remove-box">Remove</a></div>');
        box_html.hide();
        $('.my-form .text-box:last').after(box_html);
        box_html.fadeIn('slow');
        return false;
    });
    //Ends
    });
    
</script>
    </div>
</div>
