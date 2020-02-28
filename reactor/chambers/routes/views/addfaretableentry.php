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
<?php
$stages=explode("#~#", $info->stages);
$stagecount=$info->total_stages;
$tabletype=$info->fare_table_type;
$fare=array();
$fareinput=array()
?>
<?php
if($info->fare_table!=''||$info->fare_table!=NULL):
    $fares=  explode('#~#', $info->fare_table);
    if($tabletype==2):
        $farecount=0;
        $rowcount=0;
        for($j=0;$j<$stagecount;$j++){
        for($i=0;$i<$stagecount;$i++){
                if($rowcount<=$i){
                    $fareinput[$i][$rowcount]=$fares[$farecount];
                    $farecount++;
                }
            }
            $rowcount++;
        }
    endif;
endif;

?>


<div class="modal-block modal-block-md modal-ajaxcontent-block" style="max-width:95%; margin: 20px auto;">
    <div class="panel panel-color panel-info"  >
        <div class="panel-heading">
            <button type="button" class="close panel-action modal-dismiss pull-right" aria-hidden="true"><i class="fa fa-close"></i></button>
            <h3 class="panel-title"><?=$ictdata['idioms']['routes_addfare_title'];?></h3>
        </div>
        <div class="panel-body" style="height:500px; overflow:auto;">
            <div class="row">
                <div class="col-md-12">
                    <form id="modal-form" class="form-horizontal  mb-lg">
                        <?php
                                    if($tabletype==2){
                                        
                                        ?>
                                    <table class="datatable table table-striped table-bordered table-hover" id="datatable">
                                        <thead>
                                                <tr>

                                                        <th style='min-width:100px'></th>
                                                        <?php                                                        
                                                        foreach ($stages as $stage)
                                                            echo "<th style='min-width:100px'>$stage</th>";
                                                        ?>
                                                </tr>
                                        </thead>
                                        <tbody class="middle-align">
                                            <?php
                                            $rowcount=0;
                                            $columncount=0;
                                            foreach($stages as $stage){
                                                echo "<tr><td>$stage</td>";
                                                for($i=0;$i<$stagecount;$i++){
                                                        $fare=  (isset($fareinput[$rowcount][$i])?$fareinput[$rowcount][$i]:'');
                                                    if($i==$rowcount){
                                                        echo"<td><input type='text' class='form-control' name='fare[$rowcount][$i]' value='0' readonly required></td>";
                                                    }elseif($i>$rowcount){
                                                        
                                                        echo"<td><input type='text' class='form-control' disabled></td>";
                                                    }else{
                                                        echo"<td><input type='number' maxlength='7' step='0.5' min='0.5' class='form-control' name='fare[$rowcount][$i]' value='$fare'  required></td>";
                                                        
                                                    }
                                                    
                                                }
                                                    $rowcount++;
                                                echo "</tr>";
                                            }
                                            ?>
						</tbody>
                                    </table>
                                              <?php
                                              
                                    }else{
                                    ?>
                                    <table class="datatable table table-striped table-bordered table-hover" id="datatable">
                                        <thead>
                                                <tr>
                                                    <th><?=$ictdata['idioms']['routes_f10'];?></th>
                                                    <th><?=$ictdata['idioms']['routes_f12'];?></th>
                                                </tr>
                                        </thead>
                                        <tbody class="middle-align">
                                            <?php
                                            $rowcount=0;
                                            $columncount=0;
                                            foreach($stages as $stage){
                                                        $fare=(isset($fares[$rowcount])?$fares[$rowcount]:'');
                                                echo "<tr><td>$stage</td>";
                                                if($rowcount==0)
                                                echo"<td><input type='text' class='form-control' name='fare[$rowcount]' value='0' readonly required></td>";
                                                else
                                                echo"<td><input type='number' maxlength='7' step='0.5' min='0.5'  class='form-control' name='fare[$rowcount]' value='$fare'  required></td>";
                                                echo "</tr>";
                                                    $rowcount++;
                                            }
                                            ?>
						</tbody>
                                    </table>
                                              <?php
                                    }
                                    ?>

                        <div class="form-group mt-lg" align="center">
                            <button id='modal-submit' onclick="validateme('<?=$this->rview->url($ctrl_name.'/faretablesave/'.$id);?>','modal-form')" class="btn btn-primary"><?=$ictdata['idioms']['submitbtn'];?></button>
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