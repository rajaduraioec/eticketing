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
 * @module      Reportdaywise
 * @filesource  Reportdaywise.views.response
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
<table class="table table-striped table-bordered table-hover table-full-width datatable" id="datatable" >
    <thead>
        <tr>
            <th><?=$ictdata['idioms']['report_c1'];?></th>
            <?=($depot==NULL?'<th>'.$ictdata['idioms']['report_c2'].'</th>':'')?>
            <th><?=$ictdata['idioms']['report_c3'];?></th>
            <th><?=$ictdata['idioms']['report_c4'];?></th>
            <th><?=$ictdata['idioms']['report_c9'];?></th>
            <!--<th><?=$ictdata['idioms']['report_c8'];?></th>-->
            <th><?=$ictdata['idioms']['report_c19'];?></th>
            <th><?=$ictdata['idioms']['report_c5'];?></th>
            <th><?=$ictdata['idioms']['report_c6'];?></th>
            <th><?=$ictdata['idioms']['report_c20'];?></th>
            <th><?=$ictdata['idioms']['report_c21'];?></th>
            <th><?=$ictdata['idioms']['report_c22'];?></th>
            <th><?=$ictdata['idioms']['report_c23'];?></th>
            <th><?=$ictdata['idioms']['report_c24'];?></th>
            <th><?=$ictdata['idioms']['report_c61'];?></th>
            <th><?=$ictdata['idioms']['report_c25'];?></th>
            <th><?=$ictdata['idioms']['report_c26'];?></th>
            <th><?=$ictdata['idioms']['report_c27'];?></th>
            <th><?=$ictdata['idioms']['report_c28'];?></th>
            <th><?=$ictdata['idioms']['report_c29'];?></th>
            <th><?=$ictdata['idioms']['report_c30'];?></th>
            <th><?=$ictdata['idioms']['report_c31'];?></th>
        </tr>
    </thead>
    
    <tbody>
        <?php
        foreach($results as $row):
        ?>
        <tr>
            <td><?=$row['uid'];?></td>
            <?=($depotck==NULL?"<td>".$row['depot']."</td>":'')?>
            <td><?=date('Y-m-d',  strtotime($row['wbopening']));?></td>
            <td><?=$row['busno'];?></td>
            <td><?=$row['conductor'];?></td>
            <!--<td><?=$row['driver'];?></td>-->
            <td><?=$row['ticketrange'];?></td>
            <td><?=$row['wbn'];?></td>
            <td><?=$row['routeno'];?></td>
            <td><?=$row['trips'];?></td>
            <td><?=$row['seatcapacity']*$row['trips'];?></td>
            <td><?=$row['tickets'];?></td>
            <td><?=$row['passes'];?></td>
            <td><?=$row['luggages'];?></td>
            <td><?=$row['ticketamt'];?></td>
            <td><?=$row['lugamt'];?></td>
            <td><?=$row['cashamt'];?></td>
            <td><?=$row['cardamt'];?></td>
            <td><?=$row['expensesamt'];?></td>
            <td><?=$row['cashamt']-$row['expensesamt'];?></td>
            <td><?=$row['cashamt'];?></td>
            <td><?=$row['handovercash'];?></td>
        </tr>
        <?php
        endforeach;
        ?>
    </tbody>
</table>