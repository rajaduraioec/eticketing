<?php

/* 
 * 
 * @package	MMT Transport E-Ticketing
 * @Framework	Reactor Framework
 * @author         Increatech Dev Team
 * @copyright	Copyright (c) Since 2017, Increatech Business Solution Pvt Ltd, India (http://increatech.com/)
 * @link           https://increatech.com
 * @since          Version 1.0.0
 * @module      Reportstagewise
 * @filesource
 */



defined('RAPPVERSION') or exit('No direct script access allowed');

/* 
 * Copyright 2017 Increatech Business Solution Pvt Ltd, India.
 * All Rights Reserved
 * www.increatech.com
 * info@increatech.com
 */

class Dashlib
{
    
    public function __construct()
    {
    }
 
    public function __get($var)
    {
        return get_instance()->$var;
    }
    public function genlivestatusbydepot($depot){//Depot null to fetch all device details
        $datefrom=date('Y-m-d 00:00:00');
        $dateto=date('Y-m-d 23:59:59');
        $reports=array();
        $devices=$this->_getdevices($depot);
        $count=0;
        $routeid='';
        $configid='';
        foreach($devices as $device):
            $query[]=$this->rdb->qb('order_by',"wbopentimestamp",'desc');
            $query[]=$this->rdb->qb('where',"(is_wb_closed='0') OR (wbclosedrectimestamp BETWEEN '$datefrom' AND '$dateto')",NULL,FALSE);
            $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
            $waybills=$this->rdb->fetch('data','waybilldetails',$query);
            unset($query);
            if(count($waybills)!=0):
                foreach($waybills as $wb):
                    $wbpassengers=0;
                    $wbcollections=0;
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                    endif;
                    //Trips of waybill
                    $query[]=$this->rdb->qb('order_by',"tripno",'asc');
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $trips=$this->rdb->fetch('data','tripdetails',$query);
                    unset($query);
                    $ticketstodaycheck=0;
                    foreach($trips as $trip):
                        $tid=$trip['id_tripdetails'];
                        $tripno=$trip['tripno'];
                        if($tripno==1)
                            $firsttripdevicerouteid=$trip['deviceroutes_id'];
                        $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
                        $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
                        $query[]=$this->rdb->qb('where',"ticket_timestamp BETWEEN '$datefrom' AND '$dateto'",NULL,FALSE);
                        $query[]=$this->rdb->qb('where','ticket_type!=',3);//passengers
                        $tickets=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE);
                        unser($query);
                        $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
                        $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
                        $query[]=$this->rdb->qb('where',"ticket_timestamp BETWEEN '$datefrom' AND '$dateto'",NULL,FALSE);
                        $query[]=$this->rdb->qb('where','ticket_type',3);//luggages
                        $luggage=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE);
                        unser($query);
                        $triptkt=$tickets->tickets+$luggage->tickets;
                        if($triptkt!=0):
                            $ticketstodaycheck=1;
                            $wbpassengers+=$tickets->tickets;
                            $wbcollections+=$tickets->amount+$luggage->amount;
                        endif;
                    endforeach;
                    if($ticketstodaycheck==1):
                        if($routeid!=$firsttripdevicerouteid):
                            $query[]=$this->rdb->qb('where',"id_deviceroutes",$firsttripdevicerouteid);
                            $query[]=$this->rdb->qb('select',"route_name");
                            $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                            unset($query);
                        endif;
                        $reports[$count]['busno']=$busno;
                        $reports[$count]['routeno']=$routeinfo->route_name;
                        $reports[$count]['passengers']=$wbpassengers;
                        $reports[$count]['collections']=$wbcollections;
                        $count+=1;
                    endif;
                endforeach;
            endif;
        endforeach;
        return $reports;
    }
    public function genrlivedstatusbydepot($depot){

        $datefrom=date('Y-m-d 00:00:00');
        $dateto=date('Y-m-d 23:59:59');
        $reports=array();
        $devices=$this->_getdevices($depot);
        $count=0;
        $routeid='';
        $configid='';
        foreach($devices as $device):
            $query[]=$this->rdb->qb('order_by',"wbopentimestamp",'desc');
            $query[]=$this->rdb->qb('where',"(is_wb_closed='0') OR (wbclosedrectimestamp BETWEEN '$datefrom' AND '$dateto')",NULL,FALSE);
            $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
            $waybills=$this->rdb->fetch('data','waybilldetails',$query);
            unset($query);
            if(count($waybills)!=0):
                foreach($waybills as $wb):
                    $wbpassengers=0;
                    $wbcollections=0;
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                    endif;
                    $query[]=$this->rdb->qb('order_by',"tripno",'asc');
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $trips=$this->rdb->fetch('data','tripdetails',$query);
                    unset($query);
                    $ticketstodaycheck=0;
                    foreach($trips as $trip):
                        $tid=$trip['id_tripdetails'];
                        $tripno=$trip['tripno'];
                        if($tripno==1)
                            $firsttripdevicerouteid=$trip['deviceroutes_id'];
                        $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
                        $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
                        $query[]=$this->rdb->qb('where',"ticket_timestamp BETWEEN '$datefrom' AND '$dateto'",NULL,FALSE);
                        $query[]=$this->rdb->qb('where','ticket_type!=',3);//passengers
                        $tickets=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE);
                        unser($query);
                        $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
                        $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
                        $query[]=$this->rdb->qb('where',"ticket_timestamp BETWEEN '$datefrom' AND '$dateto'",NULL,FALSE);
                        $query[]=$this->rdb->qb('where','ticket_type',3);//luggages
                        $luggage=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE);
                        unser($query);
                        $triptkt=$tickets->tickets+$luggage->tickets;
                        if($triptkt!=0):
                            $ticketstodaycheck=1;
                            $wbpassengers+=$tickets->tickets;
                            $wbcollections+=$tickets->amount+$luggage->amount;
                        endif;
                    endforeach;
                    if($ticketstodaycheck==1):
                        if($routeid!=$firsttripdevicerouteid):
                            $query[]=$this->rdb->qb('where',"id_deviceroutes",$firsttripdevicerouteid);
                            $query[]=$this->rdb->qb('select',"route_name");
                            $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                            unset($query);
                        endif;
                        $reports[$count]['busno']=$busno;
                        $reports[$count]['routeno']=$routeinfo->route_name;
                        $reports[$count]['passengers']=$wbpassengers;
                        $reports[$count]['collections']=$wbcollections;
                        $count+=1;
                    endif;
                endforeach;
            endif;
        endforeach;
        return $reports;
    }
    public function genrlivedstatusadmin($depot){

        $datefrom=date('Y-m-d 00:00:00');
        $dateto=date('Y-m-d 23:59:59');
        $reports=array();
        $devices=$this->_getdevices($depot);
        $count=0;
        $routeid='';
        $configid='';
        foreach($devices as $device):
            $query[]=$this->rdb->qb('order_by',"wbopentimestamp",'desc');
            $query[]=$this->rdb->qb('where',"(is_wb_closed='0') OR (wbclosedrectimestamp BETWEEN '$datefrom' AND '$dateto')",NULL,FALSE);
            $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
            $waybills=$this->rdb->fetch('data','waybilldetails',$query);
            unset($query);
            if(count($waybills)!=0):
                foreach($waybills as $wb):
                    $wbpassengers=0;
                    $wbcollections=0;
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                    endif;
                    $query[]=$this->rdb->qb('order_by',"tripno",'asc');
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $trips=$this->rdb->fetch('data','tripdetails',$query);
                    unset($query);
                    $ticketstodaycheck=0;
                    foreach($trips as $trip):
                        $tid=$trip['id_tripdetails'];
                        $tripno=$trip['tripno'];
                        if($tripno==1)
                            $firsttripdevicerouteid=$trip['deviceroutes_id'];
                        $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
                        $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
                        $query[]=$this->rdb->qb('where',"ticket_timestamp BETWEEN '$datefrom' AND '$dateto'",NULL,FALSE);
                        $query[]=$this->rdb->qb('where','ticket_type!=',3);//passengers
                        $tickets=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE);
                        unser($query);
                        $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
                        $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
                        $query[]=$this->rdb->qb('where',"ticket_timestamp BETWEEN '$datefrom' AND '$dateto'",NULL,FALSE);
                        $query[]=$this->rdb->qb('where','ticket_type',3);//luggages
                        $luggage=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE);
                        unser($query);
                        $triptkt=$tickets->tickets+$luggage->tickets;
                        if($triptkt!=0):
                            $ticketstodaycheck=1;
                            $wbpassengers+=$tickets->tickets;
                            $wbcollections+=$tickets->amount+$luggage->amount;
                        endif;
                    endforeach;
                    if($ticketstodaycheck==1):
                        if($routeid!=$firsttripdevicerouteid):
                            $query[]=$this->rdb->qb('where',"id_deviceroutes",$firsttripdevicerouteid);
                            $query[]=$this->rdb->qb('select',"route_name");
                            $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                            unset($query);
                        endif;
                        $reports[$count]['busno']=$busno;
                        $reports[$count]['routeno']=$routeinfo->route_name;
                        $reports[$count]['passengers']=$wbpassengers;
                        $reports[$count]['collections']=$wbcollections;
                        $count+=1;
                    endif;
                endforeach;
            endif;
        endforeach;
        return $reports;
    }
    public function genlivestatus(){
        $reports=array();
        $query[]=$this->rdb->qb('order_by',"depot_name",'asc');
        $depots=$this->rdb->fetch('default','depots',$query);
        unset($query);
        $count=0;
        foreach ($depots as $depot):
            $devices=$this->_getdevices($depot['id_depots']);
            $amount=0;
            $passengers=0;
            $waybillscount=0;
            foreach($devices as $device):
                $query[]=$this->rdb->qb('order_by',"wbopentimestamp",'desc');
                $query[]=$this->rdb->qb('where',"is_wb_closed",'0');
                $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
                $waybills=$this->rdb->fetch('data','waybilldetails',$query);
                unset($query);
                if(count($waybills)!=0):
                    foreach($waybills as $wb):
                    $waybillscount+=1;
                    if($wb['is_wb_closed']==1):
                        $passengers+=$wb['wb_total_tickets']+$wb['wb_total_passes'];
                        $amount+=$wb['wb_tickets_amount']+$wb['wb_passes_amount'];
                    else:
                        $tdetails=$this->_getwbdbinfobyid($wb['id_waybilldetails']);
                        $passengers+=$tdetails['tickets']+$tdetails['passes'];
                        $amount+=$tdetails['ticketsamt']+$tdetails['passesamt'];
                    endif;
                    endforeach;
                endif;
            endforeach;
            $reports[$count]['depot']=$depot['depot_name'];
            $reports[$count]['depotid']=$depot['id_depots'];
            $reports[$count]['waybills']=$waybillscount;
            $reports[$count]['amount']=$amount;
            $reports[$count]['passengers']=$passengers;
            $count+=1;
        endforeach;
        return $reports;
    }
    public function genrlivestatusadmin(){
        $datefrom=date('Y-m-d 00:00:00');
        $dateto=date('Y-m-d 23:59:59');
        $reports=array();
        $devices=$this->_getdevices($depot);
        $count=0;
        $routeid='';
        $configid='';
        foreach($devices as $device):
            $query[]=$this->rdb->qb('order_by',"wbopentimestamp",'desc');
            $query[]=$this->rdb->qb('where',"(is_wb_closed='0') OR (wbclosedrectimestamp BETWEEN '$datefrom' AND '$dateto')",NULL,FALSE);
            $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
            $waybills=$this->rdb->fetch('data','waybilldetails',$query);
            unset($query);
            if(count($waybills)!=0):
                foreach($waybills as $wb):
                    $wbpassengers=0;
                    $wbcollections=0;
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                    endif;
                    $query[]=$this->rdb->qb('order_by',"tripno",'asc');
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $trips=$this->rdb->fetch('data','tripdetails',$query);
                    unset($query);
                    $ticketstodaycheck=0;
                    foreach($trips as $trip):
                        $tid=$trip['id_tripdetails'];
                        $tripno=$trip['tripno'];
                        if($tripno==1)
                            $firsttripdevicerouteid=$trip['deviceroutes_id'];
                        $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
                        $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
                        $query[]=$this->rdb->qb('where',"ticket_timestamp BETWEEN '$datefrom' AND '$dateto'",NULL,FALSE);
                        $query[]=$this->rdb->qb('where','ticket_type!=',3);//passengers
                        $tickets=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE);
                        unser($query);
                        $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
                        $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
                        $query[]=$this->rdb->qb('where',"ticket_timestamp BETWEEN '$datefrom' AND '$dateto'",NULL,FALSE);
                        $query[]=$this->rdb->qb('where','ticket_type',3);//luggages
                        $luggage=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE);
                        unser($query);
                        $triptkt=$tickets->tickets+$luggage->tickets;
                        if($triptkt!=0):
                            $ticketstodaycheck=1;
                            $wbpassengers+=$tickets->tickets;
                            $wbcollections+=$tickets->amount+$luggage->amount;
                        endif;
                    endforeach;
                    if($ticketstodaycheck==1):
                        if($routeid!=$firsttripdevicerouteid):
                            $query[]=$this->rdb->qb('where',"id_deviceroutes",$firsttripdevicerouteid);
                            $query[]=$this->rdb->qb('select',"route_name");
                            $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                            unset($query);
                        endif;
                        $reports[$count]['busno']=$busno;
                        $reports[$count]['routeno']=$routeinfo->route_name;
                        $reports[$count]['passengers']=$wbpassengers;
                        $reports[$count]['collections']=$wbcollections;
                        $count+=1;
                    endif;
                endforeach;
            endif;
        endforeach;
        return $reports;
    }

    public function genrlivestatus(){

        $datefrom=date('Y-m-d 00:00:00');
        $dateto=date('Y-m-d 23:59:59');
        $reports=array();
        $devices=$this->_getdevices();
        $count=0;
        $routeid='';
        $configid='';
        foreach($devices as $device):
            $query[]=$this->rdb->qb('order_by',"wbopentimestamp",'desc');
            $query[]=$this->rdb->qb('where',"(is_wb_closed='0') OR (wbclosedrectimestamp BETWEEN '$datefrom' AND '$dateto')",NULL,FALSE);
            $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
            $waybills=$this->rdb->fetch('data','waybilldetails',$query);
            unset($query);
            if(count($waybills)!=0):
                foreach($waybills as $wb):
                    $wbpassengers=0;
                    $wbcollections=0;
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                    endif;
                    $query[]=$this->rdb->qb('order_by',"tripno",'asc');
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $trips=$this->rdb->fetch('data','tripdetails',$query);
                    unset($query);
                    $ticketstodaycheck=0;
                    foreach($trips as $trip):
                        $tid=$trip['id_tripdetails'];
                        $tripno=$trip['tripno'];
                        if($tripno==1)
                            $firsttripdevicerouteid=$trip['deviceroutes_id'];
                        $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
                        $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
                        $query[]=$this->rdb->qb('where',"ticket_timestamp BETWEEN '$datefrom' AND '$dateto'",NULL,FALSE);
                        $query[]=$this->rdb->qb('where','ticket_type!=',3);//passengers
                        $tickets=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE);
                        unser($query);
                        $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
                        $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
                        $query[]=$this->rdb->qb('where',"ticket_timestamp BETWEEN '$datefrom' AND '$dateto'",NULL,FALSE);
                        $query[]=$this->rdb->qb('where','ticket_type',3);//luggages
                        $luggage=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE);
                        unser($query);
                        $triptkt=$tickets->tickets+$luggage->tickets;
                        if($triptkt!=0):
                            $ticketstodaycheck=1;
                            $wbpassengers+=$tickets->tickets;
                            $wbcollections+=$tickets->amount+$luggage->amount;
                        endif;
                    endforeach;
                    if($ticketstodaycheck==1):
                        if($routeid!=$firsttripdevicerouteid):
                            $query[]=$this->rdb->qb('where',"id_deviceroutes",$firsttripdevicerouteid);
                            $query[]=$this->rdb->qb('select',"route_name");
                            $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                            unset($query);
                        endif;
                        $reports[$count]['busno']=$busno;
                        $reports[$count]['routeno']=$routeinfo->route_name;
                        $reports[$count]['passengers']=$wbpassengers;
                        $reports[$count]['collections']=$wbcollections;
                        $count+=1;
                    endif;
                endforeach;
            endif;
        endforeach;
        return $reports;
    }
    public function genlaststatus(){
        $reports=array();
        $query[]=$this->rdb->qb('order_by',"depot_name",'asc');
        $depots=$this->rdb->fetch('default','depots',$query);
        unset($query);
        $count=0;
        foreach ($depots as $depot):
            $devices=$this->_getdevices($depot['id_depots']);
            $amount=0;
            $passengers=0;
            $waybillscount=0;
            foreach($devices as $device):
                $query[]=$this->rdb->qb('limit',"1");
                $query[]=$this->rdb->qb('order_by',"wbopentimestamp",'desc');
                $query[]=$this->rdb->qb('where',"is_wb_closed",'1');
                $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
                $waybills=$this->rdb->fetch('data','waybilldetails',$query);
                unset($query);
                if(count($waybills)!=0):
                    foreach($waybills as $wb):
                    $waybillscount+=1;
                    if($wb['is_wb_closed']==1):
                        $passengers+=$wb['wb_total_tickets']+$wb['wb_total_passes'];
                        $amount+=$wb['wb_tickets_amount']+$wb['wb_passes_amount'];
                    else:
                        $tdetails=$this->_getwbdbinfobyid($wb['id_waybilldetails']);
                        $passengers+=$tdetails['tickets']+$tdetails['passes'];
                        $amount+=$tdetails['ticketsamt']+$tdetails['passesamt'];
                    endif;
                    endforeach;
                endif;
            endforeach;
            $reports[$count]['depot']=$depot['depot_name'];
            $reports[$count]['depotid']=$depot['id_depots'];
            $reports[$count]['waybills']=$waybillscount;
            $reports[$count]['amount']=$amount;
            $reports[$count]['passengers']=$passengers;
            $count+=1;
        endforeach;
        return $reports;
    }
    public function genrlastdaystatusalldepot(){

        $reports=array();
        $devices=$this->_getdevices();
        $count=0;
        $routeid='';
        $configid='';
        foreach($devices as $device):
            $query[]=$this->rdb->qb('limit',"1");
            $query[]=$this->rdb->qb('order_by',"wbopentimestamp",'desc');
            $query[]=$this->rdb->qb('where',"is_wb_closed",'1');
            $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
            $waybills=$this->rdb->fetch('data','waybilldetails',$query);
            unset($query);
            if(count($waybills)!=0):
                foreach($waybills as $wb):
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                    endif;
                if($wb['is_wb_closed']==1):
                    $query[]=$this->rdb->qb('where',"tripno",1);
                    $query[]=$this->rdb->qb('select',"deviceroutes_id");
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $tripinfo=$this->rdb->fetch('data','tripdetails',$query,FALSE, TRUE);
                    unset($query);
                    if($routeid!=$tripinfo->deviceroutes_id):
                        $query[]=$this->rdb->qb('where',"id_deviceroutes",$tripinfo->deviceroutes_id);
                        $query[]=$this->rdb->qb('select',"route_name");
                        $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                        unset($query);
                    endif;
                    $reports[$count]['busno']=$busno;
                    $reports[$count]['routeno']=$routeinfo->route_name;
                    $reports[$count]['passengers']=$wb['wb_total_tickets']+$wb['wb_total_passes'];
                    $reports[$count]['collections']=$wb['wb_tickets_amount']+$wb['wb_passes_amount'];
                else:
                    $tdetails=$this->_getwbinfobyid($wb['id_waybilldetails']);
                    $reports[$count]['busno']=$busno;
                    $reports[$count]['routeno']=$tdetails['routename'];
                    $reports[$count]['passengers']=$tdetails['tickets']+$tdetails['passes'];
                    $reports[$count]['collections']=$tdetails['ticketsamt']+$tdetails['passesamt'];
                endif;
                $count+=1;
                endforeach;
            endif;
        endforeach;
        return $reports;
    }
    public function genrlastdaystatusbydepot($depot){

        $reports=array();
        $devices=$this->_getdevices($depot);
        $count=0;
        $routeid='';
        $configid='';
        foreach($devices as $device):
            $query[]=$this->rdb->qb('limit',"1");
            $query[]=$this->rdb->qb('order_by',"wbopentimestamp",'desc');
            $query[]=$this->rdb->qb('where',"is_wb_closed",'1');
            $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
            $waybills=$this->rdb->fetch('data','waybilldetails',$query);
            unset($query);
            if(count($waybills)!=0):
                foreach($waybills as $wb):
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                    endif;
                if($wb['is_wb_closed']==1):
                    $query[]=$this->rdb->qb('where',"tripno",1);
                    $query[]=$this->rdb->qb('select',"deviceroutes_id");
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $tripinfo=$this->rdb->fetch('data','tripdetails',$query,FALSE, TRUE);
                    unset($query);
                    if($routeid!=$tripinfo->deviceroutes_id):
                        $query[]=$this->rdb->qb('where',"id_deviceroutes",$tripinfo->deviceroutes_id);
                        $query[]=$this->rdb->qb('select',"route_name");
                        $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                        unset($query);
                    endif;
                    $reports[$count]['busno']=$busno;
                    $reports[$count]['routeno']=$routeinfo->route_name;
                    $reports[$count]['passengers']=$wb['wb_total_tickets']+$wb['wb_total_passes'];
                    $reports[$count]['collections']=$wb['wb_tickets_amount']+$wb['wb_passes_amount'];
                else:
                    $tdetails=$this->_getwbinfobyid($wb['id_waybilldetails']);
                    $reports[$count]['busno']=$busno;
                    $reports[$count]['routeno']=$tdetails['routename'];
                    $reports[$count]['passengers']=$tdetails['tickets']+$tdetails['passes'];
                    $reports[$count]['collections']=$tdetails['ticketsamt']+$tdetails['passesamt'];
                endif;
                $count+=1;
                endforeach;
            endif;
        endforeach;
        return $reports;
    }
    public function genlaststatusbydepot($depot){
        $reports=array();
        $devices=$this->_getdevices($depot);
        $count=0;
        $routeid='';
        $configid='';
        foreach($devices as $device):
            $query[]=$this->rdb->qb('limit',"1");
            $query[]=$this->rdb->qb('order_by',"wbopentimestamp",'desc');
            $query[]=$this->rdb->qb('where',"is_wb_closed",'1');
            $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
            $waybills=$this->rdb->fetch('data','waybilldetails',$query);
            unset($query);
            if(count($waybills)!=0):
                foreach($waybills as $wb):
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                    endif;
                if($wb['is_wb_closed']==1):
                    $query[]=$this->rdb->qb('where',"tripno",1);
                    $query[]=$this->rdb->qb('select',"deviceroutes_id");
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $tripinfo=$this->rdb->fetch('data','tripdetails',$query,FALSE, TRUE);
                    unset($query);
                    if($routeid!=$tripinfo->deviceroutes_id):
                        $query[]=$this->rdb->qb('where',"id_deviceroutes",$tripinfo->deviceroutes_id);
                        $query[]=$this->rdb->qb('select',"route_name");
                        $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                        unset($query);
                    endif;
                    $reports[$count]['busno']=$busno;
                    $reports[$count]['routeno']=$routeinfo->route_name;
                    $reports[$count]['passengers']=$wb['wb_total_tickets']+$wb['wb_total_passes'];
                    $reports[$count]['collections']=$wb['wb_tickets_amount']+$wb['wb_passes_amount'];
                else:
                    $tdetails=$this->_getwbinfobyid($wb['id_waybilldetails']);
                    $reports[$count]['busno']=$busno;
                    $reports[$count]['routeno']=$tdetails['routename'];
                    $reports[$count]['passengers']=$tdetails['tickets']+$tdetails['passes'];
                    $reports[$count]['collections']=$tdetails['ticketsamt']+$tdetails['passesamt'];
                endif;
                $count+=1;
                endforeach;
            endif;
        endforeach;
        return $reports;
    }public function genadminlaststatus(){
        $reports=array();
        $devices=$this->_getdevices();
        $count=0;
        $routeid='';
        $configid='';
        foreach($devices as $device):
            $query[]=$this->rdb->qb('limit',"1");
            $query[]=$this->rdb->qb('order_by',"wbopentimestamp",'desc');
            $query[]=$this->rdb->qb('where',"is_wb_closed",'1');
            $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
            $waybills=$this->rdb->fetch('data','waybilldetails',$query);
            unset($query);
            if(count($waybills)!=0):
                foreach($waybills as $wb):
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                    endif;
                if($wb['is_wb_closed']==1):
                    $query[]=$this->rdb->qb('where',"tripno",1);
                    $query[]=$this->rdb->qb('select',"deviceroutes_id");
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $tripinfo=$this->rdb->fetch('data','tripdetails',$query,FALSE, TRUE);
                    unset($query);
                    if($routeid!=$tripinfo->deviceroutes_id):
                        $query[]=$this->rdb->qb('where',"id_deviceroutes",$tripinfo->deviceroutes_id);
                        $query[]=$this->rdb->qb('select',"route_name");
                        $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                        unset($query);
                    endif;
                    $reports[$count]['busno']=$busno;
                    $reports[$count]['routeno']=$routeinfo->route_name;
                    $reports[$count]['passengers']=$wb['wb_total_tickets']+$wb['wb_total_passes'];
                    $reports[$count]['collections']=$wb['wb_tickets_amount']+$wb['wb_passes_amount'];
                else:
                    $tdetails=$this->_getwbinfobyid($wb['id_waybilldetails']);
                    $reports[$count]['busno']=$busno;
                    $reports[$count]['routeno']=$tdetails['routename'];
                    $reports[$count]['passengers']=$tdetails['tickets']+$tdetails['passes'];
                    $reports[$count]['collections']=$tdetails['ticketsamt']+$tdetails['passesamt'];
                endif;
                $count+=1;
                endforeach;
            endif;
        endforeach;
        return $reports;
    }
    public function genadminlaststatusbydepot($depot){
        $reports=array();
        $devices=$this->_getdevices($depot);
        $count=0;
        $routeid='';
        $configid='';
        foreach($devices as $device):
            $query[]=$this->rdb->qb('limit',"1");
            $query[]=$this->rdb->qb('order_by',"wbopentimestamp",'desc');
            $query[]=$this->rdb->qb('where',"is_wb_closed",'1');
            $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
            $waybills=$this->rdb->fetch('data','waybilldetails',$query);
            unset($query);
            if(count($waybills)!=0):
                foreach($waybills as $wb):
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                    endif;
                if($wb['is_wb_closed']==1):
                    $query[]=$this->rdb->qb('where',"tripno",1);
                    $query[]=$this->rdb->qb('select',"deviceroutes_id");
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $tripinfo=$this->rdb->fetch('data','tripdetails',$query,FALSE, TRUE);
                    unset($query);
                    if($routeid!=$tripinfo->deviceroutes_id):
                        $query[]=$this->rdb->qb('where',"id_deviceroutes",$tripinfo->deviceroutes_id);
                        $query[]=$this->rdb->qb('select',"route_name");
                        $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                        unset($query);
                    endif;
                    $reports[$count]['busno']=$busno;
                    $reports[$count]['routeno']=$routeinfo->route_name;
                    $reports[$count]['passengers']=$wb['wb_total_tickets']+$wb['wb_total_passes'];
                    $reports[$count]['collections']=$wb['wb_tickets_amount']+$wb['wb_passes_amount'];
                else:
                    $tdetails=$this->_getwbinfobyid($wb['id_waybilldetails']);
                    $reports[$count]['busno']=$busno;
                    $reports[$count]['routeno']=$tdetails['routename'];
                    $reports[$count]['passengers']=$tdetails['tickets']+$tdetails['passes'];
                    $reports[$count]['collections']=$tdetails['ticketsamt']+$tdetails['passesamt'];
                endif;
                $count+=1;
                endforeach;
            endif;
        endforeach;
        return $reports;
    }
    /*
     * Internal Functions
     */
    function _getwbinfobyid($wbid=''){
        if($wbid!=''):
            $response=array();
            $query[]=$this->rdb->qb('where','waybilldetails_id',$wbid);
            $tripdetails=$this->rdb->fetch('data','tripdetails',$query);
            unset($query);
            $response['trips']=count($tripdetails);
            $response['tickets']=0;
            $response['routeno']='';
            $response['passes']=0;
            $response['ticketsamt']=0;
            $response['passesamt']=0;
            $response['lugamt']=0;
            $response['expenses']=0;
            $response['card']=0;
            $response['cash']=0;
            $routecheck=0;
            foreach($tripdetails as $trip):
                if($routecheck!=1):
                    $query[]=$this->rdb->qb('where',"id_deviceroutes",$trip['deviceroutes_id']);
                    $query[]=$this->rdb->qb('select',"route_name");
                    $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                    unset($query);
                    $response['routename']=$routeinfo->route_name;
                    $routecheck=1;
                endif;
                if($trip['is_trip_closed']==1):
                    $response['tickets']+=$trip['total_tickets'];
                    $response['passes']+=$trip['total_passes'];
                    $response['ticketsamt']+=$trip['tickets_amount'];
                    $response['passesamt']+=$trip['passes_amount'];
                    $response['lugamt']+=$trip['luggages_amount'];
                    $response['expenses']+=$trip['expenses_amount'];
                    $response['card']+=$trip['card_amount'];
                    $response['cash']+=$trip['cash_amount'];
                else:
                    $tdetails=$this->_gettripticketinfobyid($trip['id_tripdetails']);
                    $response['tickets']+=$tdetails['tickets'];
                    $response['passes']+=$tdetails['passes'];
                    $response['ticketsamt']+=$tdetails['ticketsamt'];
                    $response['passesamt']+=$tdetails['passesamt'];
                    $response['lugamt']+=$tdetails['lugamt'];
                    $response['card']+=$tdetails['card'];
                    $response['cash']+=$tdetails['cash'];
                endif;
            endforeach;
            return $response;
        endif;
        return;
    }
    function _getwbinfobyrid($wbid=''){
        if($wbid!=''):
            $response=array();
            $query[]=$this->rdb->qb('where','waybilldetails_id',$wbid);
            $tripdetails=$this->rdb->fetch('data','tripdetails',$query);
            unset($query);
            $response['trips']=count($tripdetails);
            $response['tickets']=0;
            $response['routeno']='';
            $response['passes']=0;
            $response['ticketsamt']=0;
            $response['passesamt']=0;
            $response['lugamt']=0;
            $response['expenses']=0;
            $response['card']=0;
            $response['cash']=0;
            $routecheck=0;
            foreach($tripdetails as $trip):
                if($routecheck!=1):
                    $query[]=$this->rdb->qb('where',"id_deviceroutes",$trip['deviceroutes_id']);
                    $query[]=$this->rdb->qb('select',"route_name");
                    $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                    unset($query);
                    $response['routename']=$routeinfo->route_name;
                    $routecheck=1;
                endif;
                if($trip['is_trip_closed']==1):
                    $response['tickets']+=$trip['total_tickets'];
                    $response['passes']+=$trip['total_passes'];
                    $response['ticketsamt']+=$trip['tickets_amount'];
                    $response['passesamt']+=$trip['passes_amount'];
                    $response['lugamt']+=$trip['luggages_amount'];
                    $response['expenses']+=$trip['expenses_amount'];
                    $response['card']+=$trip['card_amount'];
                    $response['cash']+=$trip['cash_amount'];
                else:
                    $tdetails=$this->_gettripticketinfobyid($trip['id_tripdetails']);
                    $response['tickets']+=$tdetails['tickets'];
                    $response['passes']+=$tdetails['passes'];
                    $response['ticketsamt']+=$tdetails['ticketsamt'];
                    $response['passesamt']+=$tdetails['passesamt'];
                    $response['lugamt']+=$tdetails['lugamt'];
                    $response['card']+=$tdetails['card'];
                    $response['cash']+=$tdetails['cash'];
                endif;
            endforeach;
            return $response;
        endif;
        return;
    }
    function _getwbdbinfobyid($wbid=''){
        if($wbid!=''):
            $response=array();
            $query[]=$this->rdb->qb('where','waybilldetails_id',$wbid);
            $tripdetails=$this->rdb->fetch('data','tripdetails',$query);
            unset($query);
            $response['trips']=count($tripdetails);
            $response['tickets']=0;
            $response['passes']=0;
            $response['ticketsamt']=0;
            $response['passesamt']=0;
            $response['expenses']=0;
            $response['card']=0;
            $response['cash']=0;
            $response['lugamt']=0;
            foreach($tripdetails as $trip):
                if($trip['is_trip_closed']==1):
                    $response['tickets']+=$trip['total_tickets'];
                    $response['passes']+=$trip['total_passes'];
                    $response['ticketsamt']+=$trip['tickets_amount'];
                    $response['passesamt']+=$trip['passes_amount'];
                    $response['expenses']+=$trip['expenses_amount'];
                    $response['card']+=$trip['card_amount'];
                    $response['cash']+=$trip['cash_amount'];
                    $response['lugamt']+=$trip['luggages_amount'];
                else:
                    $tdetails=$this->_gettripticketinfobyid($trip['id_tripdetails']);
                    $response['tickets']+=$tdetails['tickets'];
                    $response['passes']+=$tdetails['passes'];
                    $response['ticketsamt']+=$tdetails['ticketsamt'];
                    $response['passesamt']+=$tdetails['passesamt'];
                    $response['card']+=$tdetails['card'];
                    $response['cash']+=$tdetails['cash'];
                    $response['lugamt']+=$tdetails['lugamt'];
                endif;
            endforeach;
            return $response;
        endif;
        return;
    }
    function _getwbdbinfobyrid($wbid=''){
        if($wbid!=''):
            $response=array();
            $query[]=$this->rdb->qb('where','waybilldetails_id',$wbid);
            $tripdetails=$this->rdb->fetch('data','tripdetails',$query);
            unset($query);
            $response['trips']=count($tripdetails);
            $response['tickets']=0;
            $response['passes']=0;
            $response['ticketsamt']=0;
            $response['passesamt']=0;
            $response['expenses']=0;
            $response['card']=0;
            $response['cash']=0;
            $response['lugamt']=0;
            foreach($tripdetails as $trip):
                if($trip['is_trip_closed']==1):
                    $response['tickets']+=$trip['total_tickets'];
                    $response['passes']+=$trip['total_passes'];
                    $response['ticketsamt']+=$trip['tickets_amount'];
                    $response['passesamt']+=$trip['passes_amount'];
                    $response['expenses']+=$trip['expenses_amount'];
                    $response['card']+=$trip['card_amount'];
                    $response['cash']+=$trip['cash_amount'];
                    $response['lugamt']+=$trip['luggages_amount'];
                else:
                    $tdetails=$this->_gettripticketinfobyid($trip['id_tripdetails']);
                    $response['tickets']+=$tdetails['tickets'];
                    $response['passes']+=$tdetails['passes'];
                    $response['ticketsamt']+=$tdetails['ticketsamt'];
                    $response['passesamt']+=$tdetails['passesamt'];
                    $response['card']+=$tdetails['card'];
                    $response['cash']+=$tdetails['cash'];
                    $response['lugamt']+=$tdetails['lugamt'];
                endif;
            endforeach;
            return $response;
        endif;
        return;
    }
    function _gettripticketinfobyid($tid=''){
        if($tid!=''):
            $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
            $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
            $query[]=$this->rdb->qb('where','ticket_type',1);
            $tickets=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE);
            unset($query);
            $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
            $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
            $query[]=$this->rdb->qb('where','ticket_type',2);
            $pass=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE);
            unset($query);
            $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
            $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
            $query[]=$this->rdb->qb('where','ticket_type',3);
            $lug=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE);
            unset($query);
            $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
            $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
            $query[]=$this->rdb->qb('where','ticket_type',1);
            $query[]=$this->rdb->qb('where','paytype',1);
            $card=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE)->amount;
            unset($query);
            $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
            $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
            $query[]=$this->rdb->qb('where','ticket_type',1);
            $query[]=$this->rdb->qb('where','paytype',0);
            $cash=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE)->amount;
            unset($query);
            $response['tickets']=$tickets->tickets;
            $response['ticketsamt']=$tickets->amount;
            $response['passes']=$pass->tickets;
            $response['passesamt']=$pass->amount;
            $response['lug']=$lug->tickets;
            $response['lugamt']=$lug->amount;
            $response['card']=$card;
            $response['cash']=$cash;
            return $response;
        endif;
        return;
    }
    function _getstagewiseinfobytripid($tid=''){
        if($tid!=''):
            $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
            $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
            $query[]=$this->rdb->qb('where','ticket_type',1);
            $tickets=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE);
            unset($query);
            $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
            $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
            $query[]=$this->rdb->qb('where','ticket_type',2);
            $pass=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE);
            unset($query);
            $query[]=$this->rdb->qb('select','sum(amount) as amount, count(ticket_no) as tickets');
            $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
            $query[]=$this->rdb->qb('where','ticket_type',3);
            $lug=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE);
            unset($query);
            $response['tickets']=$tickets->tickets;
            $response['ticketsamt']=$tickets->amount;
            $response['passes']=$pass->tickets;
            $response['passesamt']=$pass->amount;
            $response['lug']=$lug->tickets;
            $response['lugamt']=$lugamt->amount;
            return $response;
        endif;
        return;
    }
    public function _getdevices($depot=NULL){
        if($depot!=NULL)
            $query[]=$this->rdb->qb('where','depots_id',$depot);
        $query[]=$this->rdb->qb('select','id_devices,uid,depot_name');
        $query[]=$this->rdb->qb('join','depots','depots.id_depots=devices.depots_id');
        $return=$this->rdb->fetch('default','devices',$query);
        unset($query);
        return $return;
    }
}