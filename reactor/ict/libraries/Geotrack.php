<?php

/* 
 * 
 * @package	MMT Transport E-Ticketing
 * @Framework	Reactor Framework
 * @author         Increatech Dev Team
 * @copyright	Copyright (c) Since 2017, Increatech Business Solution Pvt Ltd, India (http://increatech.com/)
 * @link           https://increatech.com
 * @since          Version 1.0.0
 * @module      Tracking
 * @filesource
 */



defined('RAPPVERSION') or exit('No direct script access allowed');

/* 
 * Copyright 2017 Increatech Business Solution Pvt Ltd, India.
 * All Rights Reserved
 * www.increatech.com
 * info@increatech.com
 */

class Geotrack
{
    
    public function __construct()
    {
    }
 
    public function __get($var)
    {
        return get_instance()->$var;
    }
    public function getdata($depot,$bus){
        $from=date('Y-m-d H:i:s');
        $to=date('Y-m-d 23:59:59');
        $reports=array();
        $devices=$this->_getdevices($depot);
        $count=0;
        $routeid='';
        $configid='';
        foreach($devices as $device):
            $query[]=$this->rdb->qb('where','is_wb_closed',0);
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
                        if($bus==NULL || $bus==$configinfo->buses_id):
                            $query[]=$this->rdb->qb('limit',"1");
                            $query[]=$this->rdb->qb('order_by',"tripno",'DESC');
                            $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                            $tripinfo=$this->rdb->fetch('data','tripdetails',$query,FALSE, TRUE);
                            unset($query);
                            if($routeid!=$tripinfo->deviceroutes_id):
                                $query[]=$this->rdb->qb('where',"id_deviceroutes",$tripinfo->deviceroutes_id);
                                $query[]=$this->rdb->qb('select',"route_no,stages");
                                $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                                unset($query);
                                $routeid=$tripinfo->deviceroutes_id;
                                $stages=explode('#~#',$routeinfo->stages);
                            endif;
                            $query[]=$this->rdb->qb('where',"(ticket_timestamp BETWEEN '$from' AND '$to')",NULL,FALSE);
                            $query[]=$this->rdb->qb('where',"tripdetails_id",$tripinfo->id_tripdetails);
                            $query[]=$this->rdb->qb('limit',"1");
                            $query[]=$this->rdb->qb('order_by',"ticket_timestamp",'desc');
                            $lasttkts=$this->rdb->fetch('data','ticketdetails',$query);
                            unset($query);
                            if(count($lasttkts)!=0):
                                $lasttkt=$lasttkts[0];
                                $query[]=$this->rdb->qb('where',"(ticket_timestamp BETWEEN '$from' AND '$to')",NULL,FALSE);
                                $query[]=$this->rdb->qb('limit',"1");
                                $query[]=$this->rdb->qb('order_by',"geo_timestamp",'desc');
                                $query[]=$this->rdb->qb('where',"tripdetails_id",$tripinfo->id_tripdetails);
                                $query[]=$this->rdb->qb('select',"lat,lng,geo_timestamp");
                                $geoinfo=$this->rdb->fetch('data','ticketdetails',$query,FALSE, TRUE);
                                unset($query);
                                if($geoinfo->lat!='0.000000'&&$geoinfo->lng!='0.000000'):
                                    $stageno=intval($lasttkt['source_stage_no'])-1;
                                    if($tripinfo->is_trip_closed==1):
                                        $reports[$count]['busno']=$busno;
                                        $reports[$count]['depot']=$device['depot_name'];
                                        $reports[$count]['routeno']=$routeinfo->route_no;
                                        $reports[$count]['stage']=$stages[$stageno];
                                        $reports[$count]['lat']=$geoinfo->lat;
                                        $reports[$count]['lng']=$geoinfo->lng;
                                        $reports[$count]['timestamp']=date('Y-m-d H:i:s',strtotime("$geoinfo->geo_timestamp UTC"));
                                        $reports[$count]['passengers']=$tripinfo->total_tickets+$tripinfo->total_passes;
                                        $reports[$count]['collection']=$tripinfo->tickets_amount+$tripinfo->passes_amount;
                                    else:
                                        $tdetails=$this->_gettripticketinfobyid($tripinfo->id_tripdetails);
                                        $reports[$count]['busno']=$busno;
                                        $reports[$count]['depot']=$device['depot_name'];
                                        $reports[$count]['routeno']=$routeinfo->route_no;
                                        $reports[$count]['stage']=$stages[$stageno];
                                        $reports[$count]['lat']=$geoinfo->lat;
                                        $reports[$count]['lng']=$geoinfo->lng;
                                        $reports[$count]['timestamp']=date('Y-m-d H:i:s',strtotime("$geoinfo->geo_timestamp UTC"));
                                        $reports[$count]['passengers']=$tdetails['tickets']+$tdetails['passes'];
                                        $reports[$count]['collection']=$tdetails['ticketsamt']+$tdetails['passesamt'];
                                    endif;
                                    $count+=1;
                                endif;
                            endif;
                        endif;
                endforeach;
            endif;
        endforeach;
        return $reports;
    }
    public function getdataadminbybus($bus){
        $from=date('Y-m-d H:i:s');
        $to=date('Y-m-d 23:59:59');
        $reports=array();
        $devices=$this->_getdevices();
        $count=0;
        $routeid='';
        $configid='';
        foreach($devices as $device):
            $query[]=$this->rdb->qb('where','is_wb_closed',0);
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
                        if($bus==NULL || $bus==$configinfo->buses_id):
                            $query[]=$this->rdb->qb('limit',"1");
                            $query[]=$this->rdb->qb('order_by',"tripno",'DESC');
                            $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                            $tripinfo=$this->rdb->fetch('data','tripdetails',$query,FALSE, TRUE);
                            unset($query);
                            if($routeid!=$tripinfo->deviceroutes_id):
                                $query[]=$this->rdb->qb('where',"id_deviceroutes",$tripinfo->deviceroutes_id);
                                $query[]=$this->rdb->qb('select',"route_no,stages");
                                $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                                unset($query);
                                $routeid=$tripinfo->deviceroutes_id;
                                $stages=explode('#~#',$routeinfo->stages);
                            endif;
                            $query[]=$this->rdb->qb('where',"(ticket_timestamp BETWEEN '$from' AND '$to')",NULL,FALSE);
                            $query[]=$this->rdb->qb('where',"tripdetails_id",$tripinfo->id_tripdetails);
                            $query[]=$this->rdb->qb('limit',"1");
                            $query[]=$this->rdb->qb('order_by',"ticket_timestamp",'desc');
                            $lasttkts=$this->rdb->fetch('data','ticketdetails',$query);
                            unset($query);
                            if(count($lasttkts)!=0):
                                $lasttkt=$lasttkts[0];
                                $query[]=$this->rdb->qb('where',"(ticket_timestamp BETWEEN '$from' AND '$to')",NULL,FALSE);
                                $query[]=$this->rdb->qb('limit',"1");
                                $query[]=$this->rdb->qb('order_by',"geo_timestamp",'desc');
                                $query[]=$this->rdb->qb('where',"tripdetails_id",$tripinfo->id_tripdetails);
                                $query[]=$this->rdb->qb('select',"lat,lng,geo_timestamp");
                                $geoinfo=$this->rdb->fetch('data','ticketdetails',$query,FALSE, TRUE);
                                unset($query);
                                if($geoinfo->lat!='0.000000'&&$geoinfo->lng!='0.000000'):
                                    $stageno=intval($lasttkt['source_stage_no'])-1;
                                    if($tripinfo->is_trip_closed==1):
                                        $reports[$count]['busno']=$busno;
                                        $reports[$count]['depot']=$device['depot_name'];
                                        $reports[$count]['routeno']=$routeinfo->route_no;
                                        $reports[$count]['stage']=$stages[$stageno];
                                        $reports[$count]['lat']=$geoinfo->lat;
                                        $reports[$count]['lng']=$geoinfo->lng;
                                        $reports[$count]['timestamp']=date('Y-m-d H:i:s',strtotime("$geoinfo->geo_timestamp UTC"));
                                        $reports[$count]['passengers']=$tripinfo->total_tickets+$tripinfo->total_passes;
                                        $reports[$count]['collection']=$tripinfo->tickets_amount+$tripinfo->passes_amount;
                                    else:
                                        $tdetails=$this->_gettripticketinfobyid($tripinfo->id_tripdetails);
                                        $reports[$count]['busno']=$busno;
                                        $reports[$count]['depot']=$device['depot_name'];
                                        $reports[$count]['routeno']=$routeinfo->route_no;
                                        $reports[$count]['stage']=$stages[$stageno];
                                        $reports[$count]['lat']=$geoinfo->lat;
                                        $reports[$count]['lng']=$geoinfo->lng;
                                        $reports[$count]['timestamp']=date('Y-m-d H:i:s',strtotime("$geoinfo->geo_timestamp UTC"));
                                        $reports[$count]['passengers']=$tdetails['tickets']+$tdetails['passes'];
                                        $reports[$count]['collection']=$tdetails['ticketsamt']+$tdetails['passesamt'];
                                    endif;
                                    $count+=1;
                                endif;
                            endif;
                        endif;
                endforeach;
            endif;
        endforeach;
        return $reports;
    }
    public function getdataadminbydepot($depot){
        $from=date('Y-m-d H:i:s');
        $to=date('Y-m-d 23:59:59');
        $reports=array();
        $devices=$this->_getdevices($depot);
        $count=0;
        $bus=NULL;
        $routeid='';
        $configid='';
        foreach($devices as $device):
            $query[]=$this->rdb->qb('where','is_wb_closed',0);
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
                        if($bus==NULL || $bus==$configinfo->buses_id):
                            $query[]=$this->rdb->qb('limit',"1");
                            $query[]=$this->rdb->qb('order_by',"tripno",'DESC');
                            $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                            $tripinfo=$this->rdb->fetch('data','tripdetails',$query,FALSE, TRUE);
                            unset($query);
                            if($routeid!=$tripinfo->deviceroutes_id):
                                $query[]=$this->rdb->qb('where',"id_deviceroutes",$tripinfo->deviceroutes_id);
                                $query[]=$this->rdb->qb('select',"route_no,stages");
                                $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                                unset($query);
                                $routeid=$tripinfo->deviceroutes_id;
                                $stages=explode('#~#',$routeinfo->stages);
                            endif;
                            $query[]=$this->rdb->qb('where',"(ticket_timestamp BETWEEN '$from' AND '$to')",NULL,FALSE);
                            $query[]=$this->rdb->qb('where',"tripdetails_id",$tripinfo->id_tripdetails);
                            $query[]=$this->rdb->qb('limit',"1");
                            $query[]=$this->rdb->qb('order_by',"ticket_timestamp",'desc');
                            $lasttkts=$this->rdb->fetch('data','ticketdetails',$query);
                            unset($query);
                            if(count($lasttkts)!=0):
                                $lasttkt=$lasttkts[0];
                                $query[]=$this->rdb->qb('where',"(ticket_timestamp BETWEEN '$from' AND '$to')",NULL,FALSE);
                                $query[]=$this->rdb->qb('limit',"1");
                                $query[]=$this->rdb->qb('order_by',"geo_timestamp",'desc');
                                $query[]=$this->rdb->qb('where',"tripdetails_id",$tripinfo->id_tripdetails);
                                $query[]=$this->rdb->qb('select',"lat,lng,geo_timestamp");
                                $geoinfo=$this->rdb->fetch('data','ticketdetails',$query,FALSE, TRUE);
                                unset($query);
                                if($geoinfo->lat!='0.000000'&&$geoinfo->lng!='0.000000'):
                                    $stageno=intval($lasttkt['source_stage_no'])-1;
                                    if($tripinfo->is_trip_closed==1):
                                        $reports[$count]['busno']=$busno;
                                        $reports[$count]['depot']=$device['depot_name'];
                                        $reports[$count]['routeno']=$routeinfo->route_no;
                                        $reports[$count]['stage']=$stages[$stageno];
                                        $reports[$count]['lat']=$geoinfo->lat;
                                        $reports[$count]['lng']=$geoinfo->lng;
                                        $reports[$count]['timestamp']=date('Y-m-d H:i:s',strtotime("$geoinfo->geo_timestamp UTC"));
                                        $reports[$count]['passengers']=$tripinfo->total_tickets+$tripinfo->total_passes;
                                        $reports[$count]['collection']=$tripinfo->tickets_amount+$tripinfo->passes_amount;
                                    else:
                                        $tdetails=$this->_gettripticketinfobyid($tripinfo->id_tripdetails);
                                        $reports[$count]['busno']=$busno;
                                        $reports[$count]['depot']=$device['depot_name'];
                                        $reports[$count]['routeno']=$routeinfo->route_no;
                                        $reports[$count]['stage']=$stages[$stageno];
                                        $reports[$count]['lat']=$geoinfo->lat;
                                        $reports[$count]['lng']=$geoinfo->lng;
                                        $reports[$count]['timestamp']=date('Y-m-d H:i:s',strtotime("$geoinfo->geo_timestamp UTC"));
                                        $reports[$count]['passengers']=$tdetails['tickets']+$tdetails['passes'];
                                        $reports[$count]['collection']=$tdetails['ticketsamt']+$tdetails['passesamt'];
                                    endif;
                                    $count+=1;
                                endif;
                            endif;
                        endif;
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
                if($routecheck==1):
                    $query[]=$this->rdb->qb('where',"id_deviceroutes",$trip['deviceroutes_id']);
                    $query[]=$this->rdb->qb('select',"route_no");
                    $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                    unset($query);
                    $response['routeno']=$routeinfo->route_no;
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
            foreach($tripdetails as $trip):
                if($trip['is_trip_closed']==1):
                    $response['tickets']+=$trip['total_tickets'];
                    $response['passes']+=$trip['total_passes'];
                    $response['ticketsamt']+=$trip['tickets_amount'];
                    $response['passesamt']+=$trip['passes_amount'];
                    $response['expenses']+=$trip['expenses_amount'];
                    $response['card']+=$trip['card_amount'];
                    $response['cash']+=$trip['cash_amount'];
                else:
                    $tdetails=$this->_gettripticketinfobyid($trip['id_tripdetails']);
                    $response['tickets']+=$tdetails['tickets'];
                    $response['passes']+=$tdetails['passes'];
                    $response['ticketsamt']+=$tdetails['ticketsamt'];
                    $response['passesamt']+=$tdetails['passesamt'];
                    $response['card']+=$tdetails['card'];
                    $response['cash']+=$tdetails['cash'];
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
            $response['tickets']=$tickets->tickets;
            $response['ticketsamt']=$tickets->amount;
            $response['passes']=$pass->tickets;
            $response['passesamt']=$pass->amount;
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