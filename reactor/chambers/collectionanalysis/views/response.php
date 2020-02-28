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
 * @module      Collectionanalysis
 * @filesource  Collectionanalysis.views.response
 */

$count=0;
$dateset=TRUE;
foreach($results as $row):
    $depotname[$count]=$row[0]['name'];
    foreach($row[1] as $key=>$value):
        if($dateset)
            $date[]=$key;
        $collection[$count][]=$value;
    endforeach;
    $count+=1;
    $dateset=FALSE;
endforeach;
switch ($count) {
    case 1:
        $ykey="['a']";
        break;
    case 2:
        $ykey="['a', 'b']";
        break;
    case 3:
        $ykey="['a', 'b', 'c']";
        break;

    default:
        $ykey="['a', 'b', 'c']";
        break;
}
$lable='[';
foreach($depotname as $depot)
    $lable.="'$depot',";
$lable=rtrim($lable,",").']';
$datecount=count($date);
$data='[';
for($i=0;$i<$datecount;$i++):
    $data.="{y: '".$date[$i]."' , a: ".$collection[0][$i].'';
    if($count>1)
        $data.= ' , b: '.$collection[1][$i].' ';
    if($count>2)
        $data.= ', c: '.$collection[2][$i].' ';
    $data.= '},';
endfor;
$data=rtrim($data,",").']';
?>
<script type="text/javascript">
     Morris.Bar({
            element: 'reportdata',
            data: <?=$data?>,
            xkey: 'y',
            ykeys: <?=$ykey?>,
            labels: <?=$lable?>,
            hideHover: 'auto',
            resize: true, //defaulted to true
            gridLineColor: '#eeeeee',
            barSizeRatio: 0.4,
            xLabelAngle: 35,
            barColors: ['#c086f3','#65acff', '#7ed321']
        });
</script>