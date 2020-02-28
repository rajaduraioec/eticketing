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
 * @module      Reportdepotwise
 * @filesource  Reportdepotwise.views.response
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
            <th><?=$ictdata['idioms']['report_c5'];?></th>
            <th><?=$ictdata['idioms']['report_c6'];?></th>
            <th><?=$ictdata['idioms']['report_c32'];?></th>
            <th><?=$ictdata['idioms']['report_c15'];?></th>
            <th><?=$ictdata['idioms']['report_c14'];?></th>
            <th><?=$ictdata['idioms']['report_c33'];?></th>
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
            <td><?=$row['wbn'];?></td>
            <td><?=$row['routeno'];?></td>
            <td><?=$row['tickets'];?></td>
            <td><?=$row['passes'];?></td>
            <td><?=$row['total'];?></td>
            <td><?=$row['luggages'];?></td>
        </tr>
        <?php
        endforeach;
        ?>
    </tbody>
</table>