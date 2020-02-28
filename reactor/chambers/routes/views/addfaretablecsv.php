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
 * @filesource  routes.views.addfaretable
 */

?>

<div class="modal-block modal-block-md modal-ajaxcontent-block" >
    
    <div class="panel panel-color panel-info"  >
        <div class="panel-heading">
            <button type="button" class="close panel-action modal-dismiss pull-right" aria-hidden="true"><i class="fa fa-close"></i></button>
            <h3 class="panel-title"><?=$ictdata['idioms']['routes_csvfare_title'];?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                      <div class="form-group">

                          <div class="col-sm-12" style="text-align: center;">
                        <a href="<?=$this->rview->url($ctrl_name.'/faretable_csvexport/'.$id);?>" target="_blank" class="btn btn-success btn-single btn-icon"><i class="fa fa-download"></i> <?=$ictdata['idioms']['dtbtn'];?></a> 
                    </div>
            </div>
        </br>
        <hr>
        
    <form  id="upload_file" class="form-horizontal">

        <div class="form-group">
            <label class="col-sm-4 control-label" for="field-1"><?=$ictdata['idioms']['routes_f13'];?></label>

                    <div class="col-sm-8">
                        <input type="file" class="form-control" id="field-1" name="faretable" placeholder="<?=$ictdata['idioms']['routes_f13p'];?>" accept=".csv" required>
                    </div>
            </div>
            <div class="form-group" align="center">
                <button id='submit-fare' type="button" onclick="fareUpload()" class="btn btn-primary btn-single "><?=$ictdata['idioms']['vubtn'];?></button>
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
    function fareUpload(){
        $("#submit-fare").prop("disabled",true);
        var data = new FormData($('#upload_file')[0]);
        $.ajax({
            type:"POST",
            url 			:"<?=$this->rview->url($ctrl_name.'/faretable_ajaximport/'.$id);?>",
            data:data,
            mimeType: "multipart/form-data",
            contentType: false,
            cache: false,
            processData: false,
            dataType: "JSON",
            success	: function (response)
            {
                if(response.status)
                {
                    $("div.modal-ajaxcontent-block").replaceWith(response.content);
                        reload_table();
                }else{
                    $("div.modal-ajaxcontent-block").replaceWith(response.content);
                }
            }
        });
        return;
    }
</script>
    </div>
</div>