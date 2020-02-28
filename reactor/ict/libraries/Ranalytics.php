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

class Ranalytics
{
    
    public function __construct()
    {
    }
 
    public function __get($var)
    {
        return get_instance()->$var;
    }
    
    public function gencollectionanalysis($date,$depots){
        $from=date('Y-m-d 00:00:00',  strtotime($date[0]));
        $to=date('Y-m-d 23:59:59',  strtotime($date[1]));
        $result=array();
        $count=0;
        $pto=new DateTime($to);
        $pto->add(new DateInterval('P1D'));
        $period   = new DatePeriod(
                    new DateTime($from),
                    new DateInterval('P1D'),$pto
                   );
        foreach ($period as $dt) {
            $datearray[]=$dt->format("d-m-Y");
        }
        $pcount=count($datearray);
        foreach($depots as $depot):
            $query[]=$this->rdb->qb('where',"id_depots",$depot);
            $depotinfo=$this->rdb->fetch('default','depots',$query,FALSE,TRUE);
            unset($query);
            $devices=$this->_getdevices($depot);
            $depotid=$depotinfo->id_depots;
            $depotname=$depotinfo->depot_name;
            $result[$depotid][0]['name']=$depotname;
            for($i=0;$i<$pcount;$i++):
                $datev=$datearray[$i];
                $result[$depotid][1][$datev]=0;
            endfor;
            foreach($devices as $device):
                $query[]=$this->rdb->qb('group_by',"DATE(wbclosedrectimestamp)",NULL,FALSE);
                if(date('Y-m-d')==date('Y-m-d',  strtotime($to))):
                    $tostate=1; 
                    $query[]=$this->rdb->qb('where',"(wbclosedrectimestamp BETWEEN '$from' AND '$to' OR wbclosedrectimestamp IS NULL)",NULL,FALSE);
                    else:
                    $tostate=0;
                    $query[]=$this->rdb->qb('where',"(wbclosedrectimestamp BETWEEN '$from' AND '$to')",NULL,FALSE);
                endif;
                $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
                $waybills=$this->rdb->fetch('data','waybilldetails',$query);
                unset($query);
                if(count($waybills)!=0):
                    foreach($waybills as $wb):
                        if($wb['is_wb_closed']==1):
                            $dv=date('d-m-Y',  strtotime($wb['wbclosedrectimestamp']));
                            $result[$depotid][1][$dv]+=$wb['wb_tickets_amount']+$wb['wb_passes_amount'];
                        endif;
                    endforeach;
                endif;
            endforeach;
            $count+=1;
        endforeach;
        return $result;
    }
    public function genpassengeranalysis($date,$depots){
        $from=date('Y-m-d 00:00:00',  strtotime($date[0]));
        $to=date('Y-m-d 23:59:59',  strtotime($date[1]));
        $result=array();
        $count=0;
        $pto=new DateTime($to);
        $pto->add(new DateInterval('P1D'));
        $period   = new DatePeriod(
                    new DateTime($from),
                    new DateInterval('P1D'),$pto
                   );
        foreach ($period as $dt) {
            $datearray[]=$dt->format("d-m-Y");
        }
        $pcount=count($datearray);
        foreach($depots as $depot):
            $query[]=$this->rdb->qb('where',"id_depots",$depot);
            $depotinfo=$this->rdb->fetch('default','depots',$query,FALSE,TRUE);
            unset($query);
            $devices=$this->_getdevices($depot);
            $depotid=$depotinfo->id_depots;
            $depotname=$depotinfo->depot_name;
            $result[$depotid][0]['name']=$depotname;
            for($i=0;$i<$pcount;$i++):
                $datev=$datearray[$i];
                $result[$depotid][1][$datev]=0;
            endfor;
            foreach($devices as $device):
                $query[]=$this->rdb->qb('group_by',"DATE(wbclosedrectimestamp)",NULL,FALSE);
                if(date('Y-m-d')==date('Y-m-d',  strtotime($to))):
                    $tostate=1; 
                    $query[]=$this->rdb->qb('where',"(wbclosedrectimestamp BETWEEN '$from' AND '$to' OR wbclosedrectimestamp IS NULL)",NULL,FALSE);
                    else:
                    $tostate=0;
                    $query[]=$this->rdb->qb('where',"(wbclosedrectimestamp BETWEEN '$from' AND '$to')",NULL,FALSE);
                endif;
                $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
                $waybills=$this->rdb->fetch('data','waybilldetails',$query);
                unset($query);
                if(count($waybills)!=0):
                    foreach($waybills as $wb):
                        if($wb['is_wb_closed']==1):
                            $dv=date('d-m-Y',  strtotime($wb['wbclosedrectimestamp']));
                            $result[$depotid][1][$dv]+=$wb['wb_total_tickets']+$wb['wb_total_passes'];
                        endif;
                    endforeach;
                endif;
            endforeach;
            $count+=1;
        endforeach;
        return $result;
    }
    
    public function gendepotwisereport($date,$depot){
        $from=date('Y-m-d 00:00:00',  strtotime($date[0]));
        $to=date('Y-m-d 23:59:59',  strtotime($date[1]));
        $reports=array();
        $devices=$this->_getdevices($depot);
        $count=0;
        $routeid='';
        $configid='';
        foreach($devices as $device):
            $query[]=$this->rdb->qb('where',"(wbopentimestamp BETWEEN '$from' AND '$to' OR wbclosedrectimestamp IS NULL)",NULL,FALSE);
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
                    $query[]=$this->rdb->qb('where',"tripno",1);
                    $query[]=$this->rdb->qb('select',"deviceroutes_id");
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $tripinfo=$this->rdb->fetch('data','tripdetails',$query,FALSE, TRUE);
                    unset($query);
                    if($routeid!=$tripinfo->deviceroutes_id):
                        $query[]=$this->rdb->qb('where',"id_deviceroutes",$tripinfo->deviceroutes_id);
                        $query[]=$this->rdb->qb('select',"route_no");
                        $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                        unset($query);
                    endif;
                    if($wb['is_wb_closed']==1):
                        $reports[$count]['busno']=$busno;
                        $reports[$count]['depot']=$device['depot_name'];
                        $reports[$count]['routeno']=$routeinfo->route_no;
                        $reports[$count]['wbopening']=$wb['wbopentimestamp'];
                        $reports[$count]['wbn']=$wb['way_bill_no'];
                        $reports[$count]['uid']=$device['uid'];
                        $reports[$count]['tickets']=$wb['wb_total_tickets'];
                        $reports[$count]['passes']=$wb['wb_total_passes'];
                        $reports[$count]['total']=$wb['wb_total_tickets']+$wb['wb_total_passes'];
                    else:
                        $tdetails=$this->_getwbinfobyid($wb['id_waybilldetails']);
                        $reports[$count]['busno']=$busno;
                        $reports[$count]['depot']=$device['depot_name'];
                        $reports[$count]['routeno']=$routeinfo->route_no;
                        $reports[$count]['wbopening']=$wb['wbopentimestamp'];
                        $reports[$count]['wbn']=$wb['way_bill_no'];
                        $reports[$count]['uid']=$device['uid'];
                        $reports[$count]['tickets']=$tdetails['tickets'];
                        $reports[$count]['passes']=$tdetails['passes'];
                        $reports[$count]['total']=$tdetails['tickets']+$tdetails['passes'];
                    endif;
                    $count+=1;
                endforeach;
            endif;
        endforeach;
        return $reports;
    }
    public function gendepotwisereportadminbydate($date){
        $from=date('Y-m-d 00:00:00',  strtotime($date[0]));
        $to=date('Y-m-d 23:59:59',  strtotime($date[1]));
        $reports=array();
        $devices=$this->_getdevices();
        $count=0;
        $routeid='';
        $configid='';
        foreach($devices as $device):
            $query[]=$this->rdb->qb('where',"(wbopentimestamp BETWEEN '$from' AND '$to' OR wbclosedrectimestamp IS NULL)",NULL,FALSE);
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
                    $query[]=$this->rdb->qb('where',"tripno",1);
                    $query[]=$this->rdb->qb('select',"deviceroutes_id");
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $tripinfo=$this->rdb->fetch('data','tripdetails',$query,FALSE, TRUE);
                    unset($query);
                    if($routeid!=$tripinfo->deviceroutes_id):
                        $query[]=$this->rdb->qb('where',"id_deviceroutes",$tripinfo->deviceroutes_id);
                        $query[]=$this->rdb->qb('select',"route_no");
                        $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                        unset($query);
                    endif;
                    if($wb['is_wb_closed']==1):
                        $reports[$count]['busno']=$busno;
                        $reports[$count]['depot']=$device['depot_name'];
                        $reports[$count]['routeno']=$routeinfo->route_no;
                        $reports[$count]['wbopening']=$wb['wbopentimestamp'];
                        $reports[$count]['wbn']=$wb['way_bill_no'];
                        $reports[$count]['uid']=$device['uid'];
                        $reports[$count]['tickets']=$wb['wb_total_tickets'];
                        $reports[$count]['passes']=$wb['wb_total_passes'];
                        $reports[$count]['total']=$wb['wb_total_tickets']+$wb['wb_total_passes'];
                    else:
                        $tdetails=$this->_getwbinfobyid($wb['id_waybilldetails']);
                        $reports[$count]['busno']=$busno;
                        $reports[$count]['depot']=$device['depot_name'];
                        $reports[$count]['routeno']=$routeinfo->route_no;
                        $reports[$count]['wbopening']=$wb['wbopentimestamp'];
                        $reports[$count]['wbn']=$wb['way_bill_no'];
                        $reports[$count]['uid']=$device['uid'];
                        $reports[$count]['tickets']=$tdetails['tickets'];
                        $reports[$count]['passes']=$tdetails['passes'];
                        $reports[$count]['total']=$tdetails['tickets']+$tdetails['passes'];
                    endif;
                    $count+=1;
                endforeach;
            endif;
        endforeach;
        return $reports;
    }
    
    public function gencollectionanalysisadminbydate($date){
        $from=date('Y-m-d 00:00:00',  strtotime($date[0]));
        $to=date('Y-m-d 23:59:59',  strtotime($date[1]));
        $result=array();
        $count=0;
        $pto=new DateTime($to);
        $pto->add(new DateInterval('P1D'));
        $period   = new DatePeriod(
                    new DateTime($from),
                    new DateInterval('P1D'),$pto
                   );
        foreach ($period as $dt) {
            $datearray[]=$dt->format("d-m-Y");
        }
        $pcount=count($datearray);
        foreach($depots as $depot):
            $query[]=$this->rdb->qb('where',"id_depots",$depot);
            $depotinfo=$this->rdb->fetch('default','depots',$query,FALSE,TRUE);
            unset($query);
            $devices=$this->_getdevices($depot);
            $depotid=$depotinfo->id_depots;
            $depotname=$depotinfo->depot_name;
            $result[$depotid][0]['name']=$depotname;
            for($i=0;$i<$pcount;$i++):
                $datev=$datearray[$i];
                $result[$depotid][1][$datev]=0;
            endfor;
            foreach($devices as $device):
                $query[]=$this->rdb->qb('group_by',"DATE(wbclosedrectimestamp)",NULL,FALSE);
                if(date('Y-m-d')==date('Y-m-d',  strtotime($to))):
                    $tostate=1; 
                    $query[]=$this->rdb->qb('where',"(wbclosedrectimestamp BETWEEN '$from' AND '$to' OR wbclosedrectimestamp IS NULL)",NULL,FALSE);
                    else:
                    $tostate=0;
                    $query[]=$this->rdb->qb('where',"(wbclosedrectimestamp BETWEEN '$from' AND '$to')",NULL,FALSE);
                endif;
                $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
                $waybills=$this->rdb->fetch('data','waybilldetails',$query);
                unset($query);
                if(count($waybills)!=0):
                    foreach($waybills as $wb):
                        if($wb['is_wb_closed']==1):
                            $dv=date('d-m-Y',  strtotime($wb['wbclosedrectimestamp']));
                            $result[$depotid][1][$dv]+=$wb['wb_tickets_amount']+$wb['wb_passes_amount'];
                        endif;
                    endforeach;
                endif;
            endforeach;
            $count+=1;
        endforeach;
        return $result;
    }
    public function genpassengeranalysisadminbydate($date){
        $from=date('Y-m-d 00:00:00',  strtotime($date[0]));
        $to=date('Y-m-d 23:59:59',  strtotime($date[1]));
        $result=array();
        $count=0;
        $pto=new DateTime($to);
        $pto->add(new DateInterval('P1D'));
        $period   = new DatePeriod(
                    new DateTime($from),
                    new DateInterval('P1D'),$pto
                   );
        foreach ($period as $dt) {
            $datearray[]=$dt->format("d-m-Y");
        }
        $pcount=count($datearray);
        foreach($depots as $depot):
            $query[]=$this->rdb->qb('where',"id_depots",$depot);
            $depotinfo=$this->rdb->fetch('default','depots',$query,FALSE,TRUE);
            unset($query);
            $devices=$this->_getdevices($depot);
            $depotid=$depotinfo->id_depots;
            $depotname=$depotinfo->depot_name;
            $result[$depotid][0]['name']=$depotname;
            for($i=0;$i<$pcount;$i++):
                $datev=$datearray[$i];
                $result[$depotid][1][$datev]=0;
            endfor;
            foreach($devices as $device):
                $query[]=$this->rdb->qb('group_by',"DATE(wbclosedrectimestamp)",NULL,FALSE);
                if(date('Y-m-d')==date('Y-m-d',  strtotime($to))):
                    $tostate=1; 
                    $query[]=$this->rdb->qb('where',"(wbclosedrectimestamp BETWEEN '$from' AND '$to' OR wbclosedrectimestamp IS NULL)",NULL,FALSE);
                    else:
                    $tostate=0;
                    $query[]=$this->rdb->qb('where',"(wbclosedrectimestamp BETWEEN '$from' AND '$to')",NULL,FALSE);
                endif;
                $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
                $waybills=$this->rdb->fetch('data','waybilldetails',$query);
                unset($query);
                if(count($waybills)!=0):
                    foreach($waybills as $wb):
                        if($wb['is_wb_closed']==1):
                            $dv=date('d-m-Y',  strtotime($wb['wbclosedrectimestamp']));
                            $result[$depotid][1][$dv]+=$wb['wb_total_tickets']+$wb['wb_total_passes'];
                        endif;
                    endforeach;
                endif;
            endforeach;
            $count+=1;
        endforeach;
        return $result;
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