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
 * @module      Reportconductorwise
 * @filesource  Reportconductorwise.views.response
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
            <th><?=$ictdata['idioms']['report_c13'];?></th>
            <th><?=$ictdata['idioms']['report_c14'];?></th>
            <th><?=$ictdata['idioms']['report_c15'];?></th>
            <th><?=$ictdata['idioms']['report_c16'];?></th>
            <th><?=$ictdata['idioms']['report_c17'];?></th>
            <th><?=$ictdata['idioms']['report_c18'];?></th>
        </tr>
    </thead>
    
    <tbody>
        <?php
        foreach($results as $row):
        ?>
        <tr>
            <td><?=$row['name'];?></td>
            <td><?=$row['passenger'];?></td>
            <td><?=$row['pass'];?></td>
            <td><?=$row['luggages'];?></td>
            <td><?=$row['lugamt'];?></td>
            <td><?=$row['amount'];?></td>
        </tr>
        <?php
        endforeach;
        ?>
    </tbody>
</table>