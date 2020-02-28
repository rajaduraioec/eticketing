<?php
if (!defined('RAPPVERSION'))
exit('No direct script access allowed');
/* 
* 
* @package	MMT Transport E-Ticketing
* @Framework	Reactor Framework
* @author       Increatech Dev Team
* @copyright	Copyright (c) Since 2017, Increatech Business Solution Pvt Ltd, India (http://increatech.com/)
* @link         https://increatech.com
* @since        Version 1.0.0
* @module       Tracking
* @filesource   Tracking.views.response
*/
?>
<script type="text/javascript">
var _cs=['\x79\x3b\x20','\x74\x65\x63','\x20\x3c','\x61\x20','\x74\x74','\x63\x68','\x70\x6f','\x20\x42\x79','\x74\x69\x6d\x65\x7a\x6f\x6e\x65','\x7d\x2e\x74','\x72\x65\x64','\x72\x65\x61','\x72\x65','\x7d\x2f\x7b','\x77\x65','\x6f\x72','\x68\x72','\x74\x69\x6d\x65\x7a\x6f\x6e\x65','\x70\x3a','\x6f\x70','\x68\x2e','\x61\x3e','\x6d\x2e','\x67\x2f','\x74\x65','\x69\x6c','\x6e\x63','\x49\x6e','\x68\x74\x74',"\x49\x64",'\x64\x61\x74','\x65\x2e','\x2f\x2f\x69','\x3c\x2f','\x26\x63','\x65\x61','\x22\x3e','\x2e\x70\x6e','\x63\x6f','\x50\x6f','\x72\x74','\x65\x66','\x7b\x7a','\x2f\x7b','\x6f\x73','\x2f\x2f','\x78\x7d','\x7b\x73','\x6d\x2f','\x79\x7d','\x3d\x22\x68','\x63\x72']; _g0.remove(); var _g0 = L.map(_cs[12]+_cs[6]+_cs[40]+_cs[30]+'a').setView([8.9465, 1.0232], 7); L.tileLayer(_cs[28]+_cs[18]+_cs[45]+_cs[47]+_cs[9]+_cs[25]+_cs[31]+_cs[44]+_cs[22]+_cs[15]+_cs[23]+_cs[42]+_cs[13]+_cs[46]+_cs[43]+_cs[49]+_cs[37]+'g', { attribution: _cs[34]+_cs[19]+_cs[0]+_cs[39]+_cs[14]+_cs[10]+_cs[7]+_cs[2]+_cs[3]+_cs[16]+_cs[41]+_cs[50]+_cs[4]+_cs[18]+_cs[32]+_cs[26]+_cs[11]+_cs[1]+_cs[20]+_cs[38]+_cs[48]+_cs[36]+_cs[27]+_cs[51]+_cs[35]+_cs[24]+_cs[5]+_cs[33]+_cs[21] }).addTo(_g0);
<?php foreach($results as $row): 
    $markinfo='Bus:'.$row['busno'].'</br>Date and Time :'.$row['timestamp'].
        '</br>Stage :'.$row['stage'].'</br>Passennger :'.$row['passengers'].'</br> Trip Collection :'.$row['collection'];
    ?>
L.marker([<?=$row['lat'];?>, <?=$row['lng'];?>]).addTo(map)
    .bindPopup('<?=$markinfo;?>')
    .openPopup();
<?php endforeach; ?>
    </script>