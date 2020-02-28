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
 * @module      Tracking
 * @filesource  Tracking.views.view
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
                        <select class="form-control mselect2" required id="depot" >
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
                        <select class="form-control select2" required id="buses" >
                        <option value='all'>All Buses</option>
                            <?php
                            foreach($buses as $bus)
                                echo '<option value="'.$bus['id_buses'].'">'.$bus['bus_no'].'</option>';
                            ?>
                        </select>
                    </div>
                    <button type="button" onclick="track();" class="btn btn-info btn-single " id="rangebtn"><?=$ictdata['idioms']['submitbtn'];?></button>
                </form>
            </div>
            <h4 class="page-title"><?=$ictdata['idioms']['track_module_title'];?></h4>
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
                <?='<link rel="stylesheet" href="https://unpkg.com/leaflet@1.2.0/dist/leaflet.css" />';?>
                <?='<script src="https://unpkg.com/leaflet@1.2.0/dist/leaflet.js"></script>';?>
        <div class="col-md-12" id="reportdata" style="height: 500px">
        </div>
    </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var _cs=['\x68\x74','\x7d\x2e\x70','\x68\x72','\x74\x65','\x3e\x49','\x70\x3a\x2f','\x72\x65\x61','\x74\x69','\x61\x20','\x2f\x7b','\x50\x6f\x77','\x6c\x65\x2e','\x7a\x7d','\x20\x3c','\x79\x3b\x20','\x73\x7d\x2e','\x68\x3c','\x64\x61\x74','\x6d\x2f\x22','\x65\x63','\x2f\x7b\x79','\x3d\x22','\x61\x62\x73','\x2e\x6f\x72','\x6f\x73\x6d','\x2f\x61','\x63\x68','\x62\x36\x34','\x42\x79','\x6e\x63\x72','\x72\x65\x70','\x74\x70','\x65\x61\x74','\x2e\x63\x6f','\x6e\x67','\x65\x64\x20','\x67\x2f\x7b','\x65\x66','\x68\x74\x74','\x65\x72','\x69\x6e\x63','\x78\x7d','\x6d\x61\x74\x68','\x26\x63','\x3a\x2f\x2f','\x6f\x72\x74','\x6f\x70']; var _g0 = L.map(_cs[30]+_cs[45]+_cs[17]+'a').setView([8.9465, 1.0232], 7); L.tileLayer(_cs[38]+_cs[5]+_cs[9]+_cs[15]+_cs[7]+_cs[11]+_cs[24]+_cs[23]+_cs[36]+_cs[12]+_cs[9]+_cs[41]+_cs[20]+_cs[1]+_cs[34], { attribution: _cs[43]+_cs[46]+_cs[14]+_cs[10]+_cs[39]+_cs[35]+_cs[28]+_cs[13]+_cs[8]+_cs[2]+_cs[37]+_cs[21]+_cs[0]+_cs[31]+_cs[44]+_cs[40]+_cs[6]+_cs[3]+_cs[26]+_cs[33]+_cs[18]+_cs[4]+_cs[29]+_cs[32]+_cs[19]+_cs[16]+_cs[25]+'>' }).addTo(_g0);
    var baseurl='<?=$this->rview->url('tracking');?>';
</script>
<script src="<?=$this->rview->init('jquery.ict.tracking.init');?>"></script>

