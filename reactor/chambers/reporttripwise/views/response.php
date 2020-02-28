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
 * @module      Reporttripwise
 * @filesource  Reporttripwise.views.response
 */

?>
<script type="text/javascript">
    jQuery(document).ready(function($)
    {
            $("#datatable").dataTable({
                "bSort" : false,
                dom: "Bfrtip",
            buttons: [{
                extend: "copy",
                className: "btn-sm"
            }, {
                extend: "csv",
                className: "btn-sm"
            }, {
                extend: "excel",
                className: "btn-sm"
            }, {
                extend: "pdf",
                className: "btn-sm"
            }, {
                extend: "print",
                className: "btn-sm"
            }]
            });
    });
    </script>
<table class="table table-striped table-bordered table-hover table-full-width datatable" id="datatable" data-table-path="<?php // echo base_url($ctrl_name.'/ajax_list');?>">
    <thead>
        <tr>
            <th><?=$ictdata['idioms']['report_c1'];?></th>
            <?=($depot==NULL?'<th>'.$ictdata['idioms']['report_c2'].'</th>':'')?>
            <th><?=$ictdata['idioms']['report_c3'];?></th>
            <th><?=$ictdata['idioms']['report_c9'];?></th>
            <th><?=$ictdata['idioms']['report_c58'];?></th>
            <th><?=$ictdata['idioms']['report_c4'];?></th>
            <th><?=$ictdata['idioms']['report_c59'];?></th>
            <th><?=$ictdata['idioms']['report_c6'];?></th>
            <th><?=$ictdata['idioms']['report_c5'];?></th>
            <th><?=$ictdata['idioms']['report_c7'];?></th>
            <th><?=$ictdata['idioms']['report_c21'];?></th>
            <th><?=$ictdata['idioms']['report_c22'];?></th>
            <th><?=$ictdata['idioms']['report_c23'];?></th>
            <th><?=$ictdata['idioms']['report_c60'];?></th>
            <th><?=$ictdata['idioms']['report_c24'];?></th>
            <th><?=$ictdata['idioms']['report_c25'];?></th>
            <th><?=$ictdata['idioms']['report_c29'];?></th>
        </tr>
    </thead>
    
    <tbody>
        <?php
        foreach($results as $row):
        ?>
        <tr>
            <td><?=$row['uid'];?></td>
            <?=($depotck==NULL?"<td>".$row['depot']."</td>":'')?>
            <td><?=date('Y-m-d', strtotime($row['tripopening']));?></td>
            <td><?=$row['conductor'];?></td>
            <td><?=$row['ticketrange'];?></td>
            <td><?=$row['busno'];?></td>
            <td><?=$row['fleetno'];?></td>
            <td><?=$row['routeno'];?></td>
            <td><?=$row['wbn'];?></td>
            <td><?=$row['tripno'];?></td>
            <td><?=$row['seatcapacity'];?></td>
            <td><?=$row['tickets'];?></td>
            <td><?=$row['passes'];?></td>
            <td><?=$row['passamt'];?></td>
            <td><?=$row['luggages'];?></td>
            <td><?=$row['lugamt'];?></td>
            <td><?=$row['ticketamt']+$row['passamt']+$row['lugamt'];?></td>
        </tr>
        <?php
        endforeach;
        ?>
    </tbody>
</table>