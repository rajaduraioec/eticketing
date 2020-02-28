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
 * @module      Reportinspector
 * @filesource  Reportinspector.views.response
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
            <th><?=$ictdata['idioms']['report_c39'];?></th>
            <th><?=$ictdata['idioms']['report_c40'];?></th>
            <th><?=$ictdata['idioms']['report_c4'];?></th>
            <th><?=$ictdata['idioms']['report_c6'];?></th>
            <th><?=$ictdata['idioms']['report_c5'];?></th>
            <th><?=$ictdata['idioms']['report_c41'];?></th>
        </tr>
    </thead>
    
    <tbody>
        <?php
        foreach($results as $row):
        ?>
        <tr>
            <td><?=$row['uid'];?></td>
            <?=($depotck==NULL?"<td>".$row['depot']."</td>":'')?>
            <td><?=date('Y-m-d H:i:s',  strtotime($row['date']));?></td>
            <td><?=$row['insid'];?></td>
            <td><?=$row['busno'];?></td>
            <td><?=$row['routeno'];?></td>
            <td><?=$row['wbn'];?></td>
            <td><?=$row['remarks'];?></td>
        </tr>
        <?php
        endforeach;
        ?>
    </tbody>
</table>