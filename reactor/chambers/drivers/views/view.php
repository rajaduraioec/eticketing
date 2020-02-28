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
 * @module      Drivers
 * @filesource  Drivers.views.view
 */
 
 ?>
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
        	<?php if($this->rauth->has_accesslevel($this->module_name,'create')):?>
                <div class="btn-group pull-right">
                    <a href="<?=$this->rview->url($ctrl_name.'/modeladdnew/');?>" class="simple-ajax-modal center btn btn-primary"  style="color: #FFFFFF">
                        <i class="fa fa-plus-circle"></i>  <?=$ictdata['idioms']['addbtn'];?>
                    </a>
                </div>
            <?php endif; ?>
            <h4 class="page-title"><?=$ictdata['idioms']['drivers_module_title'];?></h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="panel panel-border panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                <script type="text/javascript">
                    var dtinit=true;
                </script>
                    <table class="table table-striped table-bordered table-hover table-full-width" id="datatableajax" data-table-path="<?=$this->rview->url($ctrl_name.'/ajax_list');?>">
                        <thead>
                            <tr>
                                <th><?=$ictdata['idioms']['drivers_c1'];?></th>
                                <th><?=$ictdata['idioms']['drivers_c2'];?></th>
                                <th><?=$ictdata['idioms']['status'];?></th>
                                <?php if($this->rauth->has_accesslevel($this->module_name,'update')):?>
                                    <th><?=$ictdata['idioms']['#'];?></th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

