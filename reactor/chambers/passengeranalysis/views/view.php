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
 * @module      Passengeranalysis
 * @filesource  Passengeranalysis.views.view
 */

?>
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="btn-group pull-right">
                <form class="form-inline" accept-charset="utf-8">
                    <?php
                    if($depot==NULL):
                        ?>
                        <div class="form-group">
                        <select class="form-control select2" required id="depot"   multiple="multiple">
                            <?php
                            foreach($depots as $depotrow)
                                echo '<option value="'.$depotrow['id_depots'].'">'.$depotrow['depot_name'].'</option>';
                            ?>
                        </select></div>
                    <?php
                        else:
                            echo "<input type='hidden' id='depot'value='$depot' required/>";
                    endif;
                    ?>
                            <div class="form-group">
                    <input type="text" id="reportrange" class="form-control"/></div>
                    <button type="button" onclick="analysis();" class="btn btn-info btn-single " id="rangebtn"><?=$ictdata['idioms']['submitbtn'];?></button>
                </form>
            </div>
            <h4 class="page-title"><?=$ictdata['idioms']['pa_module_title'];?></h4>
        </div>
    </div>
</div>
<div class="row">
    
        <script src="<?=$this->rview->plugin('morris.css','morris');?>"></script>
    <div class="panel panel-border panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"></h3>
        </div>

        <div class="panel-body"  style="height: 500px;">
            <div class="row"  >
        <script type="text/javascript">
            var rdtinit=true;
        </script>
        <div id="reportdata"  style="height: 320px;">
        </div>
    </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var baseurl='<?=$this->rview->url('passengeranalysis');?>';
</script>
<script src="<?=$this->rview->init('jquery.ict.analytics.init');?>"></script>
<script src="<?=$this->rview->plugin('morris.min.js','morris');?>"></script>
<script src="<?=$this->rview->plugin('raphael-min.js','raphael');?>"></script>

