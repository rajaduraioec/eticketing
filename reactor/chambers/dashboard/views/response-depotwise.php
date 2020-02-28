<?php

if (!defined('RAPPVERSION'))
    exit('No direct script access allowed');
/* 
 * 
 * @package	MMT Transport E-Ticketing
 * @Framework	Reactor Framework
 * @author         Increatech Dev Team
 * @copyright	Copyright (c) Since 2017, Increatech Business Solution Pvt Ltd, India (http://increatech.com/)
 * @link           https://increatech.com
 * @since          Version 1.0.0
 * @module      Dashboard
 * @filesource  dashboard.views.response-depotwise
 */

?>
<script type="text/javascript">
    jQuery(document).ready(function($)
    {
        $("#ddlivedatatable").dataTable({
            "bFilter": false,
            "bSort" : false,
            "bLengthChange": false,
            "pageLength": 5
        });
        $("#ddlatedatatable").dataTable({
            "bFilter": false,
            "bSort" : false,
            "bLengthChange": false,
            "pageLength": 5
        });
    });
</script>

<div class="modal-block modal-block-md modal-ajaxcontent-block" style="max-width:95%; margin: 20px auto;">
    <div class="panel panel-info"  >
        <div class="panel-heading">
            <button type="button" class="close panel-action modal-dismiss pull-right" aria-hidden="true"><i class="fa fa-close"></i></button>
            <h3 class="panel-title" style="color: #FFFFFF">Depot Status ( <?=$data['depot']->depot_name;?> )</h3>
        </div>
        <div class="panel-body" style="height:500px; overflow:auto;">
            
<div class="row">
<div class="col-md-6">
    <div class="panel panel-border panel-primary">
        <div class="panel-heading">
            <div class="btn-group pull-right">
                Updated on <?=date('Y-m-d H:i:s');?>
            </div>
            <h3 class="panel-title"><?=$ictdata['idioms']['dashboard_live_title'];?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered table-hover table-full-width table-colored table-primary" id="ddlivedatatable">
                        <thead>
                            <tr>
                                <th><?=$ictdata['idioms']['dashboard_depot_c1'];?></th>
                                <th><?=$ictdata['idioms']['dashboard_depot_c2'];?></th>
                                <th><?=$ictdata['idioms']['dashboard_depot_c3'];?></th>
                                <th><?=$ictdata['idioms']['dashboard_depot_c4'];?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $pass=0;
                        $collection=0;
                        foreach($data['live'] as $row):
                        ?>
                        <tr>
                            <td><?=$row['busno'];?></td>
                            <td><?=$row['routeno'];?></td>
                            <td style="text-align:right;"><?=$row['passengers'];?></td>
                            <td style="text-align:right;"><?=number_format ( $row['collections'] , 2 , "." , "," );?></td>
                        </tr>
                        <?php
                        $pass+=$row['passengers'];
                        $collection+=$row['collections'];
                        endforeach;
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">Total</td>
                                <td><?=$pass;?></td>
                                <td><?=number_format ( $collection , 2 , "." , "," );?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="panel panel-border panel-success">
        <div class="panel-heading">
            <div class="btn-group pull-right">
                Updated on <?=date('Y-m-d H:i:s');?>
            </div>
            <h3 class="panel-title"><?=$ictdata['idioms']['dashboard_last_title'];?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <script type="text/javascript">
                    var rdtinit=true;
                </script>
                <div class="col-md-12" id="lastdata">
                    <table class="table table-striped table-bordered table-hover table-full-width table-colored table-success" id="ddlatedatatable">
                        <thead>
                            <tr>
                                <th><?=$ictdata['idioms']['dashboard_depot_c1'];?></th>
                                <th><?=$ictdata['idioms']['dashboard_depot_c2'];?></th>
                                <th><?=$ictdata['idioms']['dashboard_depot_c3'];?></th>
                                <th><?=$ictdata['idioms']['dashboard_depot_c4'];?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $pass=0;
                        $collection=0;
                        foreach($data['last'] as $row):
                        ?>
                        <tr>
                            <td><?=$row['busno'];?></td>
                            <td><?=$row['routeno'];?></td>
                            <td style="text-align:right;"><?=$row['passengers'];?></td>
                            <td style="text-align:right;"><?=number_format ( $row['collections'] , 2 , "." , "," );?></td>
                        </tr>
                        <?php
                        $pass+=$row['passengers'];
                        $collection+=$row['collections'];
                        endforeach;
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">Total</td>
                                <td style="text-align:right;"><?=$pass;?></td>
                                <td style="text-align:right;"><?=number_format ( $collection , 2 , "." , "," );?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-md-12 text-right">
                    <button class="btn btn-default modal-dismiss">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>