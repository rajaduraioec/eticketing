<?php

/* 
 * 
 * @package	MMT Transport E-Ticketing
 * @Framework	Reactor Framework
 * @author         Increatech Dev Team
 * @copyright	Copyright (c) Since 2017, Increatech Business Solution Pvt Ltd, India (http://increatech.com/)
 * @link           https://increatech.com
 * @since          Version 1.0.0
 * @module      API
 * @filesource  api.controllers
 */


defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';
class Api extends REST_Controller {
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->helper('string');
    }
    /*
     * Sync Time
     * This API will provide response to the Device about the real time of server in IST
     */
    function synctime_get(){
        date_default_timezone_set('Africa/Accra');
        $time=date("Y-m-d  H:i:s");
        $response = [
            ['time' => $time]];
        if ($response)
        {
            $this->response($response, 200); // 200 being the HTTP response code
        }
    }
    function syncapp_get(){
        if (!$this->get('appid')||!$this->get('seq')||!$this->get('model'))
        {
            $this->response(['merror' => '101'], 200);
        }else
        {
            $url='http://appcdn.clancor.biz/api/app/appid/'.$this->get('appid').'/seq/'.$this->get('seq').'/model/'.$this->get('model');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $content=curl_exec($ch);
            curl_close ($ch);
            $response=  json_decode($content);
            $this->response($response, 200);
        }
    }
    function status_get(){
        if (!$this->get('uid'))
        {
            $this->response(['merror' => '101'], 200);
        }else
        {
            $query[]=$this->rdb->qb('where','uid',$this->get('uid'));
            $device=$this->rdb->fetch('default','devices',$query);
            unset($query);
            if(count($device)==1){
                $deviceinfo=$device[0];
                $this->response(['mstatus' => $deviceinfo['active']], 200);
            }else{
                $this->response(['merror' => '100'], 200);
            }
        }
    }
    /*
     * Master Data
     */
    function config_get(){
        if (!$this->get('uid'))
        {
            $this->response(['merror' => '101'], 200);
        }else
        {
            $query[]=$this->rdb->qb('where','uid',$this->get('uid'));
            $device=$this->rdb->fetch('default','devices',$query);
            unset($query);
            if(count($device)==1){
            $query[]=$this->rdb->qb('where','devices_id',$device[0]['id_devices']);
            $query[]=$this->rdb->qb('order_by','createdon','DESC');
            $query[]=$this->rdb->qb('limit','1');
            $configdetails=$this->rdb->fetch('config','deviceconfig',$query);
            unset($query);
            if(count($configdetails)!=0){
                $response=array();
                $config=$configdetails[0];
                $response['configid'] = $config['configid'];
                $query[]=$this->rdb->qb('where','depots_id',$config['depots_id']);
                $details=$this->rdb->fetch('default','headerfooters',$query,FALSE,TRUE);
                unset($query);
                $response['header'] = array(
                    'header1'=>$details->h1,
                    'header2'=>$details->h2,
                    'footer'=>$details->f
                );
                $response['luggage'] = $config['luggage_status'];
                unset($details);
                $query[]=$this->rdb->qb('where','depots_id',$config['depots_id']);
                $details=$this->rdb->fetch('default','deviceadmins',$query,FALSE,TRUE);
                unset($query);
                $response['admin']=array(
                'name'=>$details->admin_name,
                'uname'=>$details->admin_id,
                'pass'=>$details->admin_pass);
                unset($details);
                $query[]=$this->rdb->qb('where','id_deviceconductors',$config['conductors_id']);
                $details=$this->rdb->fetch('config','deviceconductors',$query,FALSE,TRUE);
                unset($query);
                $response['conductor']=array(
                'name'=>$details->conductor_name,
                'uname'=>$details->conductor_id,
                'pass'=>$details->conductor_pass);
                unset($details);
                $query[]=$this->rdb->qb('where','id_devicedrivers',$config['drivers_id']);
                $details=$this->rdb->fetch('config','devicedrivers',$query,FALSE,TRUE);
                unset($query);
                $response['driver']=array(
                'id'=>$details->driver_id,
                'name'=>$details->driver_name);
                unset($details);
                $query[]=$this->rdb->qb('where','id_site_settings','1');
                $details=$this->rdb->fetch('default','site_settings',$query,FALSE,TRUE);
                $response['inspectorpass']=$details->inspector_pass;
                unset($details);
                unset($query);
                $query[]=$this->rdb->qb('where','active',1);
                $details=$this->rdb->fetch('default','expenses',$query);
                foreach($details as $row){
                    $response['expense'][]=array(
                            'id'=>$row['id_expenses'],
                            'name'=>$row['expense_name']);
                }
                unset($query);
                unset($details);
                $query[]=$this->rdb->qb('where','active',1);
                $details=$this->rdb->fetch('default','passes',$query);
                foreach($details as $row){
                    $response['pass'][]=array(
                            'id'=>$row['id_passes'],
                            'name'=>$row['pass_name'],
                            'perc'=>$row['pass_perc']
                            );
                }
                unset($query);
                unset($details);
                $query[]=$this->rdb->qb('where','id_buses',$config['buses_id']);
                $details=$this->rdb->fetch('default','buses',$query,FALSE,TRUE);
                $response['bus'][]=$details->bus_no;
                unset($query);
                unset($details);
                $query[]=$this->rdb->qb('join','deviceconfigroutes','deviceconfigroutes.deviceroutes_id=deviceroutes.id_deviceroutes');
                $query[]=$this->rdb->qb('where','deviceconfigroutes.deviceconfig_id',$config['id_deviceconfig']);
                $details=$this->rdb->fetch('config','deviceroutes',$query);
                foreach($details as $row){
                    $response['route'][]=array(
                            'routeno'=>$row['route_no'],
                            'name'=>$row['route_name'],
                            'bustype'=>$row['service_type'],
                            'tabletype'=>$row['fare_table_type'],
                            'totalstage'=>$row['total_stages']
                            );
                }
                unset($query);
                unset($details);
                    $this->response($response, 200); // 200 being the HTTP response code
            }else{
            $this->response(['error' => 'Device Not Configured'], 200);
            }
            }else{
                
            $this->response(['error' => 'Device Not Regestered'], 200);
            }
        }
    }
    
    /*
     * Stage details of particulat route of device
     * @params
     * uid->machineid
     * count->No of records should give response
     * packets->Packet details requested
     * routeno->route id number of routes
     */
    function routestages_post(){
        if (!$this->post('configid')||!$this->post('count')||!$this->post('packet')||!$this->post('routeno'))
        {
            $this->response(['merror' => '101'], 200);
        }else
        {
            $reqcount=$this->post('count');
            $reqpacket=$this->post('packet');
            $query[]=$this->rdb->qb('where','configid',$this->post('configid'));
            $configdetails=$this->rdb->fetch('config','deviceconfig',$query);
            unset($query);
            if(count($configdetails)!=0){
                $config=$configdetails[0];
                $query[]=$this->rdb->qb('join','deviceconfigroutes','deviceconfigroutes.deviceroutes_id=deviceroutes.id_deviceroutes');
                $query[]=$this->rdb->qb('where','deviceconfigroutes.deviceconfig_id',$config['id_deviceconfig']);
                $query[]=$this->rdb->qb('where','deviceroutes.route_no',$this->post('routeno'));
                $query[]=$this->rdb->qb('select','stages');
                $routeinfo=$this->rdb->fetch('config','deviceroutes',$query);
                if(count($routeinfo)!=1){
                    $this->response(['merror' => '104'], 200);
                }else{
                    $results=  explode('#~#', $routeinfo[0]['stages']);
                    $resultcount=count($results);
                    if($resultcount<=$reqcount){
                        $totalpackets=1;
                    }else{
                        $totalpackets=ceil($resultcount/$reqcount);
                    }
                    if(!($reqpacket<=$totalpackets)){
                        $this->response(['merror' => '104'], 200);
                    }else{
                        if($totalpackets==1){
                            $recordcount=$resultcount;
                            $rowinitial=0;
                            $rowend=$recordcount;
                        }elseif($reqpacket==$totalpackets){
                            $recordcount=$resultcount-$reqcount*($totalpackets-1);
                            $rowinitial=$reqcount*($reqpacket-1);
                            $rowend=$resultcount;
                        }else{
                            $recordcount=$reqcount;
                            $rowinitial=$reqcount*($reqpacket-1);
                            $rowend=$rowinitial+$reqcount;
                        }
                        $packet="$reqpacket".'/'."$totalpackets";

                        $response=array();
                        $response['count']=$recordcount;
                        $response['packet']=$packet;
                        $results=  array_splice($results, $rowinitial,$rowend);
                        for($i=0;$i<$recordcount;$i++){
                                $response['stages'][]=$results[$i];
                        }
                        if ($response)
                        {
                            $this->response($response, 200); // 200 being the HTTP response code
                        }
                    }
                }
            }else{
                $this->response(['merror' => '104'], 200);
            }
        }
    }
    
    /*
     * Fare table details of particulat route of device
     * @params
     * configid->configuration id
     * count->No of records should give response
     * packets->Packet details requested
     * routeno->routeno of route requested
     */
    function routefare_post(){
        if (!$this->post('configid')||!$this->post('count')||!$this->post('packet')||!$this->post('routeno'))
        {
            $this->response(['merror' => '101'], 200);
        }else
        {
            $reqcount=$this->post('count');
            $reqpacket=$this->post('packet');
            $query[]=$this->rdb->qb('where','configid',$this->post('configid'));
            $configdetails=$this->rdb->fetch('config','deviceconfig',$query);
            unset($query);
            if(count($configdetails)!=0){
                $config=$configdetails[0];
                $query[]=$this->rdb->qb('join','deviceconfigroutes','deviceconfigroutes.deviceroutes_id=deviceroutes.id_deviceroutes');
                $query[]=$this->rdb->qb('where','deviceconfigroutes.deviceconfig_id',$config['id_deviceconfig']);
                $query[]=$this->rdb->qb('where','deviceroutes.route_no',$this->post('routeno'));
                $query[]=$this->rdb->qb('select','fare_table');
                $routeinfo=$this->rdb->fetch('config','deviceroutes',$query);
                if(count($routeinfo)==1){
                    $results=  explode('#~#', $routeinfo[0]['fare_table']);
                    $resultcount=count($results);
                    if($resultcount<=$reqcount){
                        $totalpackets=1;
                    }else{
                        $totalpackets=ceil($resultcount/$reqcount);
                    }
                    if(!($reqpacket<=$totalpackets)){
                        $this->response(['merror' => '104'], 200);
                    }else{
                        if($totalpackets==1){
                            $recordcount=$resultcount;
                            $rowinitial=0;
                                    $rowend=$recordcount;
                        }elseif($reqpacket==$totalpackets){
                            $recordcount=$resultcount-$reqcount*($totalpackets-1);
                            $rowinitial=$reqcount*($reqpacket-1);
                                    $rowend=$resultcount;
                        }else{
                            $recordcount=$reqcount;
                            $rowinitial=$reqcount*($reqpacket-1);
                                    $rowend=$rowinitial+$reqcount;
                        }
                        $packet="$reqpacket".'/'."$totalpackets";

                        $response=array();
                        $response['count']=$recordcount;
                        $response['packet']=$packet;
                                $results=  array_splice($results, $rowinitial,$rowend);
                        for($i=0;$i<$recordcount;$i++){
                                $response['fare'][]=floatval($results[$i]);
                        }
                        $this->response($response, 200); // 200 being the HTTP response code
                    }
                }else{
                    $this->response(['merror' => '203'], 200);
                }
            }else{
            $this->response(['merror' => '104'], 200);
            }
        }
    }
/*
 * End of Master API
 */
    
/*
 * Multiple Ticket API
 */
    function tickets_post(){
        if (0==count($this->post('count'))||0==count($this->post('configid'))||0==count($this->post('wbn'))||0==count($this->post('tripno'))||
                0==count($this->post('tktno'))||0==count($this->post('timestamp'))||0==count($this->post('sn'))||
                0==count($this->post('dn'))||0==count($this->post('tkttype'))||0==count($this->post('qty'))||
                0==count($this->post('passtype'))||0==count($this->post('passrefid'))||0==count($this->post('rate'))||
                0==count($this->post('amount'))||0==count($this->post('paytype'))||0==count($this->post('carduid'))||
                0==count($this->post('istripo'))||0==count($this->post('routeno'))||0==count($this->post('stagetype'))||
                0==count($this->post('lat'))||0==count($this->post('lng'))||0==count($this->post('utctimestamp'))
                )
        {
            $this->response(['merror' => '101'], 200);
        }else{
            //Initiatlize
            $configdetails=array();
            $config=array();
            $waybillinfo=array();
            $wbdetails=array();
            $tripinfo=array();
            $count=$this->post('count');
            $lat=$this->post('lat');
            $lng=$this->post('lng');
            $utctimestamp=$this->post('utctimestamp');
            $rtimestamp=date('Y-m-d H:i:s');
            //Arrays
            $configid=$this->post('configid');
            $wbn=$this->post('wbn');
            $tripno=$this->post('tripno');
            $tkn=$this->post('tktno');
            $timestamp=$this->post('timestamp');
            $source_stage=$this->post('sn');
            $destination_stage=$this->post('dn');
            $tkttype=$this->post('tkttype');
            $qty=$this->post('qty');
            $passtype=$this->post('passtype');
            $passrefid=$this->post('passrefid');
            $rate=$this->post('rate');
            $amount=$this->post('amount');
            $paytype=$this->post('paytype');
            $carduid=$this->post('carduid');
            $istrip_open=$this->post('istripo');
            $routeno=$this->post('routeno');
            $stagetype=$this->post('stagetype');
            if(count($configid)==$count&&count($wbn)==$count&&count($tripno)==$count&&count($tkn)==$count&&count($timestamp)==$count
                    &&count($source_stage)==$count&&count($destination_stage)==$count&&count($tkttype)==$count&&count($qty)==$count
                    &&count($passtype)==$count&&count($passrefid)==$count&&count($rate)==$count&&count($amount)==$count
                    &&count($paytype)==$count&&count($carduid)==$count
                    &&count($istrip_open)==$count&&count($routeno)==$count&&count($stagetype)==$count){//Check data is received completely
                for($i=0;$i<$count;$i++){
                    if(count($configdetails)!=1){
                        $query[]=$this->rdb->qb('where','configid',$configid[$i]);
                        $configdetails=$this->rdb->fetch('config','deviceconfig',$query);
                        unset($query);
                    }elseif($config['configid']!=$configid[$i]){
                        $query[]=$this->rdb->qb('where','configid',$configid[$i]);
                        $configdetails=$this->rdb->fetch('config','deviceconfig',$query);
                        unset($query);
                    }
                    if(count($configdetails)!=1){
                        $response[]= ['tkn' => $tkn[$i],'merror' => 201];
                    }else{
                        $config=$configdetails[0];
                        if(!isset($waybillinfo['id'])){
                            $query[]=$this->rdb->qb('where','way_bill_no',$wbn[$i]);
                            $query[]=$this->rdb->qb('where','wb_deviceconfig_id',$config['id_deviceconfig']);
                            $wbdetails=$this->rdb->fetch('data','waybilldetails',$query);
                            unset($query);
                        }elseif($waybillinfo['no']!=$wbn[$i]){
                            $query[]=$this->rdb->qb('where','way_bill_no',$wbn[$i]);
                            $query[]=$this->rdb->qb('where','wb_deviceconfig_id',$config['id_deviceconfig']);
                            $wbdetails=$this->rdb->fetch('data','waybilldetails',$query);
                            unset($query);
                        }
                        if(count($wbdetails)=='0'&&$tripno[$i]=='1'&&$istrip_open[$i]==1){//Create WB and TRIP Details
                            $data['wb_deviceconfig_id']=$config['id_deviceconfig'];
                            $data['wb_devices_id']=$config['devices_id'];
                            $data['wb_depots_id']=$config['depots_id'];
                            $data['way_bill_no']=$wbn[$i];
                            $data['wbopenticket']=$tkn[$i];
                            $data['wbopentimestamp']=$timestamp[$i];
                            $waybillinfo['id']=$this->rdb->create('data','waybilldetails',$data);
                            $waybillinfo['no']=$wbn[$i];
                            $data['id_waybilldetails']=$waybillinfo['id'];
                            $wbdetails[]=$data;
                            unset($data);
                            $waybillinfo['no']=$wbn[$i];
                            $query[]=$this->rdb->qb('join','deviceconfigroutes','deviceconfigroutes.deviceroutes_id=deviceroutes.id_deviceroutes');
                            $query[]=$this->rdb->qb('where','deviceconfigroutes.deviceconfig_id',$config['id_deviceconfig']);
                            $query[]=$this->rdb->qb('where','deviceroutes.route_no',$routeno[$i]);
                            $query[]=$this->rdb->qb('select','id_deviceroutes');
                            $routeinfo=$this->rdb->fetch('config','deviceroutes',$query);
                            unset($query);
                            $data['waybilldetails_id']=$waybillinfo['id'];
                            $data['deviceroutes_id']=$routeinfo[0]['id_deviceroutes'];
                            $data['stage_type']=$stagetype[$i];
                            $data['first_ticket_no']=$tkn[$i];
                            $data['tripno']=$tripno[$i];
                            $data['opentimestamp']=$timestamp[$i];
                            $tripinfo['id']=$this->rdb->create('data','tripdetails',$data);
                            $tripinfo['no']=$tripno[$i];
                            unset($data);
                        }elseif(count($wbdetails)=='0'&&$tripno[$i]!='1'){
                            $response[]= ['tkn' => $tkn[$i],'merror' => 202];
                            continue;
                        }elseif(count($wbdetails)=='1'&&$istrip_open[$i]==1){//Create TRIP Details
                            $waybillinfo['id']=$wbdetails[0]['id_waybilldetails'];
                            $waybillinfo['no']=$wbdetails[0]['way_bill_no'];
                            $query[]=$this->rdb->qb('where','waybilldetails_id',$waybillinfo['id']);
                            $query[]=$this->rdb->qb('where','tripno',$tripno[$i]);
                            $query[]=$this->rdb->qb('select','id_tripdetails');
                            $tripdetails=$this->rdb->fetch('data','tripdetails',$query);
                            unset($query);
                            if(count($tripdetails)==0){
                                $query[]=$this->rdb->qb('join','deviceconfigroutes','deviceconfigroutes.deviceroutes_id=deviceroutes.id_deviceroutes');
                                $query[]=$this->rdb->qb('where','deviceconfigroutes.deviceconfig_id',$config['id_deviceconfig']);
                                $query[]=$this->rdb->qb('where','deviceroutes.route_no',$routeno[$i]);
                                $query[]=$this->rdb->qb('select','id_deviceroutes');
                                $routeinfo=$this->rdb->fetch('config','deviceroutes',$query);
                                unset($query);
                                $data['waybilldetails_id']=$waybillinfo['id'];
                                $data['deviceroutes_id']=$routeinfo[0]['id_deviceroutes'];
                                $data['stage_type']=$stagetype[$i];
                                $data['first_ticket_no']=$tkn[$i];
                                $data['tripno']=$tripno[$i];
                                $data['opentimestamp']=$timestamp[$i];
                                $tripinfo['id']=$this->rdb->create('data','tripdetails',$data);
                                $tripinfo['no']=$tripno[$i];
                                unset($data);
                            }else{
                                $tripinfo['id']=$tripdetails[0]['id_tripdetails'];
                                $tripinfo['no']=$tripno[$i];
                            }
                        }elseif(count($wbdetails)=='1'&&$istrip_open[$i]!=1){//Fetch TRIP ID
                            $waybilldetailid=$wbdetails[0]['id_waybilldetails'];
                            if(!isset($tripinfo['id'])){
                                $query[]=$this->rdb->qb('where','waybilldetails_id',$waybilldetailid);
                                $query[]=$this->rdb->qb('where','tripno',$tripno[$i]);
                                $query[]=$this->rdb->qb('select','id_tripdetails');
                                $tripdetails=$this->rdb->fetch('data','tripdetails',$query);
                                unset($query);
                                if(count($tripdetails)==0){
                                    $response[]= ['tkn' => $tkn[$i],'merror' => 202];
                                    continue;
                                }else{
                                    $tripinfo['id']=$tripdetails[0]['id_tripdetails'];
                                    $tripinfo['no']=$tripno[$i];
                                }
                            }elseif($tripinfo['no']!=$tripno[$i]){
                                $query[]=$this->rdb->qb('where','waybilldetails_id',$waybilldetailid);
                                $query[]=$this->rdb->qb('where','tripno',$tripno[$i]);
                                $query[]=$this->rdb->qb('select','id_tripdetails');
                                $tripdetails=$this->rdb->fetch('data','tripdetails',$query);
                                unset($query);                            
                                $tripinfo['id']=$tripdetails[0]['id_tripdetails'];
                                $tripinfo['no']=$tripno[$i];
                            }
                        }
                        
                        $query[]=$this->rdb->qb('where','tripdetails_id',$tripinfo['id']);
                        $query[]=$this->rdb->qb('where','ticket_no',$tkn[$i]);
                        $query[]=$this->rdb->qb('select','id_ticketdetails');
                        $ticketdetails=$this->rdb->fetch('data','ticketdetails',$query);
                        unset($query);
                        if(count($ticketdetails)!=0){
                            $response[]= ['tkn' => $tkn[$i],'txid' => $ticketdetails[0]['id_ticketdetails']];
                        }else{
                            $data['tripdetails_id']=$tripinfo['id'];
                            $data['ticket_no']=$tkn[$i];
                            $data['ticket_timestamp']=$timestamp[$i];
                            $data['source_stage_no']=$source_stage[$i];
                            $data['destination_stage_no']=$destination_stage[$i];
                            $data['ticket_type']=$tkttype[$i];
                            $data['qty']=$qty[$i];
                            $data['pass_type']=$passtype[$i];
                            $data['pass_ref_id']=$passrefid[$i];
                            $data['rate']=$rate[$i];
                            $data['amount']=$amount[$i];
                            $data['paytype']=$paytype[$i];
                            $data['carduid']=$carduid[$i];
                            $data['lat']=$lat;
                            $data['lng']=$lng;
                            $data['geo_timestamp']=$utctimestamp;
                            $data['rec_timestamp']=$rtimestamp;
                            $response[]= ['tkn' => $tkn[$i],'txid' => $this->rdb->create('data','ticketdetails',$data)];
                            unset($data);
                        }
                    }
                }
                if($response)
                {
                    $this->response($response, 200); // 200 being the HTTP response code
                }
                else
                {
                    $this->response(['merror' => '102'], 200);
                }
            }else{
                $this->response(['merror' => '101'], 200);
            }
        } 
    }
    /*
     * Trip Close
     */
    function tripclose_post(){
        if (0==count($this->post('configid'))||0==count($this->post('wbn'))||0==count($this->post('tripno'))||
                0==count($this->post('lasttktno'))||0==count($this->post('timestamp'))||0==count($this->post('tickets'))||
                0==count($this->post('pass'))||0==count($this->post('luggage'))||0==count($this->post('ticketsamt'))||
                0==count($this->post('passamt'))||0==count($this->post('luggageamt'))||0==count($this->post('expenseamt'))||
                0==count($this->post('cashamt'))||0==count($this->post('cardamt'))||
                0==count($this->post('odometer'))||0==count($this->post('fleetno'))||0==count($this->post('accident'))||
                0==count($this->post('breakdown'))
                )
        {
            $this->response(['merror' => '101'], 200);
        }else{
            //Initiatlize
            $configid=$this->post('configid');
            $wbn=$this->post('wbn');
            $tripno=$this->post('tripno');
            $lasttkn=$this->post('lasttktno');
            $timestamp=$this->post('timestamp');
            $tkts=$this->post('tickets');
            $pass=$this->post('pass');
            $luggage=$this->post('luggage');
            $tktsamt=$this->post('ticketsamt');
            $passamt=$this->post('passamt');
            $luggageamt=$this->post('luggageamt');
            $expenseamt=$this->post('expenseamt');
            $cardamt=$this->post('cardamt');
            $cashamt=$this->post('cashamt');
            $odometer=$this->post('odometer');
            $fleetno=$this->post('fleetno');
            $accident=$this->post('accident');
            $breakdown=$this->post('breakdown');
            $query[]=$this->rdb->qb('where','configid',$configid);
            $configdetails=$this->rdb->fetch('config','deviceconfig',$query);
            unset($query);
            if(count($configdetails)!=1){
                $this->response(['merror' => '201'], 200);
            }else{
                $config=$configdetails[0];
                if($lasttkn==0){
                if(0==count($this->post('routeno'))||0==count($this->post('stagetype'))){
                        $this->response(['merror' => '203'], 200);
                    }else{
                        $routeno=$this->post('routeno');
                        $stagetype=$this->post('stagetype');
                        $query[]=$this->rdb->qb('where','way_bill_no',$wbn);
                        $query[]=$this->rdb->qb('where','wb_deviceconfig_id',$config['id_deviceconfig']);
                        $wbdetails=$this->rdb->fetch('data','waybilldetails',$query);
                        unset($query);
                        $query[]=$this->rdb->qb('join','deviceconfigroutes','deviceconfigroutes.deviceroutes_id=deviceroutes.id_deviceroutes');
                        $query[]=$this->rdb->qb('where','deviceconfigroutes.deviceconfig_id',$config['id_deviceconfig']);
                        $query[]=$this->rdb->qb('where','deviceroutes.route_no',$routeno);
                        $query[]=$this->rdb->qb('select','id_deviceroutes');
                        $routeinfo=$this->rdb->fetch('config','deviceroutes',$query);
                        unset($query);
                        if(count($routeinfo)==1){
                            $routeinfo=$routeinfo[0];
                            if(count($wbdetails)!=0){
                                $wbnid=$wbdetails[0]['id_waybilldetails'];
                            }else{
                                $data['wb_deviceconfig_id']=$config['id_deviceconfig'];
                                $data['wb_devices_id']=$config['devices_id'];
                                $data['wb_depots_id']=$config['depots_id'];
                                $data['way_bill_no']=$wbn;
                                $data['wbopentimestamp']=$timestamp;
                                $wbnid=$this->rdb->create('data','waybilldetails',$data);
                                unset($data);
                            }
                            $data['waybilldetails_id']=$wbnid;
                            $data['tripno']=$tripno;
                            $data['deviceroutes_id']=$routeinfo['id_deviceroutes'];
                            $data['stage_type']=$stagetype;
                            $data['first_ticket_no']=0;
                            $data['last_ticket_no']=0;
                            $data['opentimestamp']=$timestamp;
                            $data['is_trip_closed']=1;
                            $data['total_tickets']=$tkts;
                            $data['total_passes']=$pass;
                            $data['total_luggages']=$luggage;
                            $data['tickets_amount']=$tktsamt;
                            $data['passes_amount']=$passamt;
                            $data['luggages_amount']=$luggageamt;
                            $data['expenses_amount']=$expenseamt;
                            $data['card_amount']=$cardamt;
                            $data['cash_amount']=$cashamt;
                            $data['odometer']=$odometer;
                            $data['fleet_no']=$fleetno;
                            $data['is_accident']=$accident;
                            $data['is_breakdown']=$breakdown;
                            $data['closedrectimestamp']=$timestamp;
                            $tripid=$this->rdb->create('data','tripdetails',$data);
                            unset($data);
                        }else{
                            $this->response(['merror' => '203'], 200);
                        }
                    }
                }else{//Just close trip
                    $query[]=$this->rdb->qb('where','way_bill_no',$wbn);
                    $query[]=$this->rdb->qb('where','wb_deviceconfig_id',$config['id_deviceconfig']);
                    $wbdetails=$this->rdb->fetch('data','waybilldetails',$query);
                    unset($query);
                    if(count($wbdetails)!=0){
                        $wbdetails=$wbdetails[0];
                        $query[]=$this->rdb->qb('where','waybilldetails_id',$wbdetails['id_waybilldetails']);
                        $query[]=$this->rdb->qb('where','tripno',$tripno);
                        $query[]=$this->rdb->qb('select','id_tripdetails,is_trip_closed');
                        $tripdetails=$this->rdb->fetch('data','tripdetails',$query);
                        unset($query);
                        if(count($tripdetails)!=0){
                            $tripid=$tripdetails[0]['id_tripdetails'];
                            if($tripdetails[0]['is_trip_closed']==0){
                                $data['last_ticket_no']=$lasttkn;
                                $data['is_trip_closed']=1;
                                $data['total_tickets']=$tkts;
                                $data['total_passes']=$pass;
                                $data['total_luggages']=$luggage;
                                $data['tickets_amount']=$tktsamt;
                                $data['passes_amount']=$passamt;
                                $data['luggages_amount']=$luggageamt;
                                $data['expenses_amount']=$expenseamt;
                                $data['card_amount']=$cardamt;
                                $data['cash_amount']=$cashamt;
                                $data['odometer']=$odometer;
                                $data['fleet_no']=$fleetno;
                                $data['is_accident']=$accident;
                                $data['is_breakdown']=$breakdown;
                                $data['closedrectimestamp']=$timestamp;
                                $condition[]=$this->rdb->qb('where','id_tripdetails',$tripid);
                                $this->rdb->update('data','tripdetails', $data,$condition);
                                unset($data);
                                unset($condition);
                            }
                        }else{
                            $this->response(['merror' => '202'], 200);
                            exit();
                        }
                    }else{
                        $this->response(['merror' => '204'], 200);
                        exit();
                    }

                }
                $response[]= ['triptxid' => $tripid];

                $this->response($response, 200); // 200 being the HTTP response code
            }
        }
    }    
/*
 * Waybill Close API
 */
    function wbclose_post(){
        if (0==count($this->post('configid'))||0==count($this->post('wbn'))||0==count($this->post('trips'))||
                0==count($this->post('timestamp'))||0==count($this->post('tickets'))||0==count($this->post('pass'))||
                0==count($this->post('luggage'))||0==count($this->post('ticketsamt'))||0==count($this->post('passamt'))||
                0==count($this->post('luggageamt'))||0==count($this->post('expenseamt'))||
                0==count($this->post('handovercash'))||0==count($this->post('odometer'))||0==count($this->post('lastticket'))||
                0==count($this->post('count'))||
                0==count($this->post('cashamt'))||0==count($this->post('cardamt')))
        {
            $this->response(['merror' => '101'], 200);
        }else{
            //Initialize
            $response=array();
            $etripno='';
            $configid=$this->post('configid');
            $wbn=$this->post('wbn');
            $trips=$this->post('trips');
            $timestamp=$this->post('timestamp');
            $tkts=$this->post('tickets');
            $pass=$this->post('pass');
            $luggage=$this->post('luggage');
            $tktsamt=$this->post('ticketsamt');
            $passamt=$this->post('passamt');
            $luggageamt=$this->post('luggageamt');
            $expenseamt=$this->post('expenseamt');
            $cardamt=$this->post('cardamt');
            $cashamt=$this->post('cashamt');
            $handovercash=$this->post('handovercash');
            $odometer=$this->post('odometer');
            $lastticket=$this->post('lastticket');
            $count=$this->post('count');
            $expenses=$this->post('expenses');
            if(count($expenses)==$count||$count==0){
                $query[]=$this->rdb->qb('where','configid',$configid);
                $configdetails=$this->rdb->fetch('config','deviceconfig',$query);
                unset($query);
                if(count($configdetails)!=1){
                    $this->response(['merror' => '201'], 200);
                }else{
                    $config=$configdetails[0];
                    $query[]=$this->rdb->qb('where','way_bill_no',$wbn);
                    $query[]=$this->rdb->qb('where','wb_deviceconfig_id',$config['id_deviceconfig']);
                    $wbdetails=$this->rdb->fetch('data','waybilldetails',$query);
                    unset($query);
                    if(count($wbdetails)!=0){
                        $wbdetails=$wbdetails[0];
                        $data['is_wb_closed']=1;
                        $data['total_trips']=$trips;
                        $data['wbcloseticket']=$lastticket;
                        $data['wbodometer']=$odometer;
                        $data['wbhandovercash']=$handovercash;
                        $data['wb_total_tickets']=$tkts;
                        $data['wb_total_passes']=$pass;
                        $data['wb_total_luggages']=$luggage;
                        $data['wb_tickets_amount']=$tktsamt;
                        $data['wb_passes_amount']=$passamt;
                        $data['wb_luggages_amount']=$luggageamt;
                        $data['wb_expenses_amount']=$expenseamt;
                        $data['wb_card_amount']=$cardamt;
                        $data['wb_cash_amount']=$cashamt;
                        $data['wbclosedrectimestamp']=$timestamp;
                        $condition[]=$this->rdb->qb('where','way_bill_no',$wbn);
                        $condition[]=$this->rdb->qb('where','wb_deviceconfig_id',$config['id_deviceconfig']);
                        $this->rdb->update('data','waybilldetails', $data,$condition);
                        unset($data);
                        unset($condition);
                        $response[]= ['wbtxid' => $wbdetails['id_waybilldetails']];
                        if($count!=0):
                            for($i=0;$i<$count;$i++){ 
                                $expense=  explode('~', $expenses[$i]);//timestamp~tripid~expenseid~amount~fuelid~remarks
                                $query[]=$this->rdb->qb('where','e_waybilldetails_id',$wbdetails['id_waybilldetails']);
                                $query[]=$this->rdb->qb('where','e_timestamp',$expense[0]);
                                $query[]=$this->rdb->qb('where','e_tripno',$expense[1]);
                                $query[]=$this->rdb->qb('where','expenses_id',$expense[2]);
                                $expensedetails=$this->rdb->fetch('data','expensedetails',$query);
                                unset($query);
                                if(count($expensedetails)==0){
                                    $data['e_waybilldetails_id']=$wbdetails['id_waybilldetails'];
                                    $data['e_timestamp']=$expense[0];
                                    $data['e_tripno']=$expense[1];
                                    $data['expenses_id']=$expense[2];
                                    $data['amount']=$expense[3];
                                    if($expense[2]==1)
                                        $data['fuel_ltr']=$expense[4];
                                    $expenseid=$this->rdb->create('data','expensedetails',$data);
                                    unset($data);
                                }else{
                                    $expenseid=$expensedetails[0]['id_expensedetails'];
                                }
                                $response[]= ['expenseid' => $expense[2],'txid' => $expenseid];
                            }
                        endif;
                        $this->response($response, 200);
                    }else{
                        $this->response(['merror' => '204'], 200);
                        exit();
                    }
                }
            }else{
                $this->response(['merror' => '101'], 200);
            }
        } 
    }
    /*
     * Inspector Report
     */
    function inspection_post(){
        if (0==count($this->post('configid'))||0==count($this->post('wbn'))||0==count($this->post('tripno'))||
                0==count($this->post('timestamp'))||0==count($this->post('insidno'))||0==count($this->post('remarks')))
        {
            $this->response(['merror' => '101'], 200);
        }else{
            //Initialize
            $configid=$this->post('configid');
            $wbn=$this->post('wbn');
            $tripno=$this->post('tripno');
            $timestamp=$this->post('timestamp');
            $insidno=$this->post('insidno');
            $remarks=$this->post('remarks');
            $query[]=$this->rdb->qb('where','configid',$configid);
            $configdetails=$this->rdb->fetch('config','deviceconfig',$query);
            unset($query);
            if(count($configdetails)!=1){
                $this->response(['merror' => '201'], 200);
            }else{
                $config=$configdetails[0];
                $query[]=$this->rdb->qb('where','way_bill_no',$wbn);
                $query[]=$this->rdb->qb('where','wb_deviceconfig_id',$config['id_deviceconfig']);
                $wbdetails=$this->rdb->fetch('data','waybilldetails',$query);
                unset($query);
                if(count($wbdetails)!=0){
                    $wbdetails=$wbdetails[0];
                    $query[]=$this->rdb->qb('where','waybilldetails_id',$wbdetails['id_waybilldetails']);
                    $query[]=$this->rdb->qb('where','tripno',$tripno);
                    $query[]=$this->rdb->qb('select','id_tripdetails,is_trip_closed');
                    $tripdetails=$this->rdb->fetch('data','tripdetails',$query);
                    unset($query);
                    if(count($tripdetails)!=0){
                        $tripid=$tripdetails[0]['id_tripdetails'];
                        $query[]=$this->rdb->qb('where','inspector_id',$insidno);
                        $query[]=$this->rdb->qb('select','id_inspectors');
                        $inspectordetails=$this->rdb->fetch('default','inspectors',$query);
                        unset($query);
                        if(count($inspectordetails)!=0){
                            $inspectorid=$inspectordetails[0]['id_inspectors'];
                        }else{
                            $inspectorid=NULL;
                        }
                        $query[]=$this->rdb->qb('where','inspector_id',$insidno);
                        $query[]=$this->rdb->qb('select','id_inspectors');
                        $inspectordetails=$this->rdb->fetch('default','inspectors',$query);
                        unset($query);
                        
                        $data['i_tripdetails_id']=$tripid;
                        $data['i_timestamp']=$timestamp;
                        $data['ins_id_no']=$insidno;
                        $data['inspectors_id']=$inspectorid;
                        $data['	remarks']=$remarks;
                        $txid=$this->rdb->create('data','inspectiondetails', $data);
                        unset($data);
                        $this->response(['txid' => $txid,'insid'=>$insidno], 200);
                    }else{
                        $this->response(['merror' => '202'], 200);
                        exit();
                    }
                }else{
                    $this->response(['merror' => '204'], 200);
                    exit();
                }
            }
        } 
    }
    

    /*
     * Write log function
     */
    function write_log($message, $logfile='') {
        
    // Filename of log to use when none is given to write_log
//    define("DEFAULT_LOG",$_SERVER["DOCUMENT_ROOT"]."clancor/logs/default.log");
    define("DEFAULT_LOG",$_SERVER["DOCUMENT_ROOT"]."/logs/default.log");
  // Determine log file
  if($logfile == '') {
    // checking if the constant for the log file is defined
    if (defined("DEFAULT_LOG") == TRUE) {
        $logfile = DEFAULT_LOG;
     }
    // the constant is not defined and there is no log file given as input
    else {
        error_log('No log file defined!',0);
        return array('status' => false, 'message' => 'No log file defined!');
    }
  }
 
  // Get time of request
  if( ($time = $_SERVER['REQUEST_TIME']) == '') {
    $time = time();
  }
 
  // Get IP address
  if( ($remote_addr = $_SERVER['REMOTE_ADDR']) == '') {
    $remote_addr = "REMOTE_ADDR_UNKNOWN";
  }
 
  // Get requested script
  if( ($request_uri = $_SERVER['REQUEST_URI']) == '') {
    $request_uri = "REQUEST_URI_UNKNOWN";
  }
 
  // Format the date and time
  $date = date("Y-m-d H:i:s", $time);
 
  // Append to the log file
  if($fd = @fopen($logfile, "a")) {
    $result = fputcsv($fd, array($date, $remote_addr, $request_uri,$message));
    fclose($fd);
 
    if($result > 0)
      return array('status' => true);  
    else
      return array('status' => false, 'message' => 'Unable to write to '.$logfile.'!');
  }
  else {
    return array('status' => false, 'message' => 'Unable to open log '.$logfile.'!');
  }
}
    
}
