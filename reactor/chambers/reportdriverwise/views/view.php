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
 * @module      Reportdriverwise
 * @filesource  Reportdriverwise.views.view
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
                        <select class="form-control select2" required id="depot" >
                        <option value='all'>All Depots</option>
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
                        <select class="form-control select2" required id="driver" >
                        <option value='all'>All Driver's</option>
                            <?php
                            foreach($drivers as $driver)
                                echo '<option value="'.$driver['id_drivers'].'">'.$driver['driver_name'].'</option>';
                            ?>
                        </select>
                    </div>
                            <div class="form-group">
                    <input type="text" id="reportrange" class="form-control"/></div>
                    <button type="button" onclick="gendriverwisereport();" class="btn btn-info btn-single " id="rangebtn"><?=$ictdata['idioms']['submitbtn'];?></button>
                </form>
            </div>
            <h4 class="page-title"><?=$ictdata['idioms']['driver_report_title'];?></h4>
        </div>
    </div>
</div>
<div class="row">
    
    <div class="panel panel-border panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"></h3>
        </div>

        <div class="panel-body">
            <div class="row"  style="overflow:auto;">
        <script type="text/javascript">
            var rdtinit=true;
        </script>
        <div class="col-md-12" id="reportdata">
            <table class="table table-striped table-bordered table-hover table-full-width" id="datatable" data-table-path="<?=$this->rview->url($ctrl_name.'/ajax_list');?>">
                <thead>
                    <tr>
                        <th><?=$ictdata['idioms']['report_c34'];?></th>
                        <th><?=$ictdata['idioms']['report_c35'];?></th>
                        <th><?=$ictdata['idioms']['report_c36'];?></th>
                        <th><?=$ictdata['idioms']['report_c37'];?></th>
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

<script type="text/javascript">
    var baseurl='<?=$this->rview->url('reportdriverwise');?>';
    </script>
        <script src="<?=$this->rview->init('jquery.ict.report.init');?>"></script>

