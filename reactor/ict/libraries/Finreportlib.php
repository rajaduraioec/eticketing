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

class Finreportlib
{
    public function __construct()
    {
    }
    public function __get($var)
    {
        return get_instance()->$var;
    }
    public function gendaywisereport($date,$depot){
        $from=date('Y-m-d 00:00:00',  strtotime($date[0]));
        $to=date('Y-m-d 23:59:59',  strtotime($date[1]));
        $reports=array();
        $devices=$this->_getdevices($depot);
        $count=0;
        $routeid='';
        $configid='';
        $conductor='';
        $driver='';
        foreach($devices as $device):
            $query[]=$this->rdb->qb('where',"(wbopentimestamp BETWEEN '$from' AND '$to' OR wbclosedrectimestamp IS NULL)",NULL,FALSE);
            $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
            $waybills=$this->rdb->fetch('data','waybilldetails',$query);
            unset($query);
            if(count($waybills)!=0):
                foreach($waybills as $wb):
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id,conductors_id,drivers_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $businfo=$this->getbusinfo($configinfo->buses_id);
                        $busno=$businfo->bus_no;
                        $seatcapacity=$businfo->seat_capacity;
                        $conductor=$this->getcondunctorinfo($configinfo->conductors_id)->conductor_name;
                        $driver=$this->getdriverinfo($configinfo->drivers_id)->driver_name;
                    endif;
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
                if($wb['is_wb_closed']==1):
                    $reports[$count]['busno']=$busno;
                    $reports[$count]['seatcapacity']=$seatcapacity;
                    $reports[$count]['handovercash']=$wb['wbhandovercash'];
                    $reports[$count]['depot']=$device['depot_name'];
                    $reports[$count]['conductor']=$conductor;
                    $reports[$count]['driver']=$driver;
                    $reports[$count]['ticketrange']=$wb['wbopenticket'].' - '.$wb['wbcloseticket'];
                    $reports[$count]['routeno']=$routeinfo->route_name;
                    $reports[$count]['wbopening']=$wb['wbopentimestamp'];
                    $reports[$count]['wbn']=$wb['way_bill_no'];
                    $reports[$count]['wbclosed']=1;
                    $reports[$count]['uid']=$device['uid'];
                    $reports[$count]['trips']=$wb['total_trips'];
                    $reports[$count]['tickets']=$wb['wb_total_tickets'];
                    $reports[$count]['passes']=$wb['wb_total_passes'];
                    $reports[$count]['luggages']=$wb['wb_total_luggages'];
                    $reports[$count]['ticketamt']=$wb['wb_tickets_amount'];
                    $reports[$count]['passamt']=$wb['wb_passes_amount'];
                    $reports[$count]['lugamt']=$wb['wb_luggages_amount'];
                    $reports[$count]['expensesamt']=$wb['wb_expenses_amount'];
                    $reports[$count]['cardamt']=$wb['wb_card_amount'];
                    $reports[$count]['cashamt']=$wb['wb_cash_amount'];
                else:
                    $tdetails=$this->_getwbinfobyid($wb['id_waybilldetails']);
                    $reports[$count]['busno']=$busno;
                    $reports[$count]['seatcapacity']=$seatcapacity;
                    $reports[$count]['handovercash']=$wb['wbhandovercash'];
                    $reports[$count]['depot']=$device['depot_name'];
                    $reports[$count]['conductor']=$conductor;
                    $reports[$count]['driver']=$driver;
                    $reports[$count]['ticketrange']=$wb['wbopenticket'].' - '.$tdetails['lastticket'];
                    $reports[$count]['routeno']=$routeinfo->route_name;
                    $reports[$count]['wbopening']=$wb['wbopentimestamp'];
                    $reports[$count]['wbn']=$wb['way_bill_no'];
                    $reports[$count]['wbclosed']=0;
                    $reports[$count]['uid']=$device['uid'];
                    $reports[$count]['trips']=$tdetails['trips'];
                    $reports[$count]['tickets']=$tdetails['tickets'];
                    $reports[$count]['passes']=$tdetails['passes'];
                    $reports[$count]['luggages']=$tdetails['luggages'];
                    $reports[$count]['ticketamt']=$tdetails['ticketsamt'];
                    $reports[$count]['passamt']=$tdetails['passesamt'];
                    $reports[$count]['lugamt']=$tdetails['lugamt'];
                    $reports[$count]['expensesamt']=$tdetails['expenses'];
                    $reports[$count]['cardamt']=$tdetails['card'];
                    $reports[$count]['cashamt']=$tdetails['cash'];
                endif;
                $count+=1;
                endforeach;
            endif;
        endforeach;
        return $reports;
    }
    public function gentripwisereport($date,$depot){
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
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $trips=$this->rdb->fetch('data','tripdetails',$query);
                    unset($query);
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id,conductors_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no,seat_capacity");
                        $businfo=$this->rdb->fetch('default','buses',$query,FALSE, TRUE);
                        $busno=$businfo->bus_no;
                        $seatcapacity=$businfo->seat_capacity;
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_deviceconductors",$configinfo->conductors_id);
                        $conductorinfo=$this->rdb->fetch('config','deviceconductors',$query,FALSE, TRUE);
                        $conductor=$conductorinfo->conductor_name;
                        unset($query);
                    endif;
                    foreach($trips as $trip):
                        if($routeid!=$trip['deviceroutes_id']):
                            $query[]=$this->rdb->qb('where',"id_deviceroutes",$trip['deviceroutes_id']);
                            $query[]=$this->rdb->qb('select',"route_name");
                            $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                            unset($query);
                            $routeid=$trip['deviceroutes_id'];
                        endif;
                        if($trip['is_trip_closed']==1):
                            $reports[$count]['busno']=$busno;
                            $reports[$count]['seatcapacity']=$seatcapacity;
                            $reports[$count]['depot']=$device['depot_name'];
                            $reports[$count]['conductor']=$conductor;
                            $reports[$count]['routeno']=$routeinfo->route_name;
                            $reports[$count]['ticketrange']=$trip['first_ticket_no'].' - '.$trip['last_ticket_no'];
                            $reports[$count]['tripopening']=$trip['opentimestamp'];
                            $reports[$count]['fleetno']=$trip['fleet_no'];
                            $reports[$count]['wbn']=$wb['way_bill_no'];
                            $reports[$count]['tripclosed']=$trip['is_trip_closed'];
                            $reports[$count]['uid']=$device['uid'];
                            $reports[$count]['tripno']=$trip['tripno'];
                            $reports[$count]['tickets']=$trip['total_tickets'];
                            $reports[$count]['passes']=$trip['total_passes'];
                            $reports[$count]['luggages']=$trip['total_luggages'];
                            $reports[$count]['ticketamt']=$trip['tickets_amount'];
                            $reports[$count]['passamt']=$trip['passes_amount'];
                            $reports[$count]['lugamt']=$trip['luggages_amount'];
                            $reports[$count]['card']=$trip['card_amount'];
                            $reports[$count]['cash']=$trip['cash_amount'];
                        else:
                            $tdetails=$this->_gettripticketinfobyid($trip['id_tripdetails']);
                            $reports[$count]['busno']=$busno;
                            $reports[$count]['seatcapacity']=$seatcapacity;
                            $reports[$count]['depot']=$device['depot_name'];
                            $reports[$count]['conductor']=$conductor;
                            $reports[$count]['routeno']=$routeinfo->route_name;
                            $reports[$count]['ticketrange']=$trip['first_ticket_no'].' - '.$tdetails['lastticket'];
                            $reports[$count]['tripopening']=$trip['opentimestamp'];
                            $reports[$count]['fleetno']=$trip['fleet_no'];
                            $reports[$count]['wbn']=$wb['way_bill_no'];
                            $reports[$count]['tripclosed']=$trip['is_trip_closed'];
                            $reports[$count]['uid']=$device['uid'];
                            $reports[$count]['tripno']=$trip['tripno'];
                            $reports[$count]['tickets']=$tdetails['tickets'];
                            $reports[$count]['passes']=$tdetails['passes'];
                            $reports[$count]['luggages']=$tdetails['luggages'];
                            $reports[$count]['ticketamt']=$tdetails['ticketsamt'];
                            $reports[$count]['passamt']=$tdetails['passesamt'];
                            $reports[$count]['lugamt']=$tdetails['lugamt'];
                            $reports[$count]['cashamt']=$tdetails['cash'];
                            $reports[$count]['cardamt']=$tdetails['card'];
                        endif;
                        $count+=1;
                    endforeach;
                endforeach;
            endif;
            
        endforeach;
        return $reports;
    }
    public function getbusinfo($bid){
        $query[]=$this->rdb->qb('where',"id_buses",$bid);
        $query[]=$this->rdb->qb('select',"bus_no,seat_capacity");
        return $this->rdb->fetch('default','buses',$query,FALSE, TRUE);

    }
    public function getconductorinfo($cid){
        $query[]=$this->rdb->qb('where',"id_deviceconductors",$cid);
        return  $this->rdb->fetch('config','deviceconductors',$query,FALSE, TRUE);
    }
    public function getdriverinfo($did){                        
        $query[]=$this->rdb->qb('where',"id_devicedrivers",$did);
        return $this->rdb->fetch('config','devicedrivers',$query,FALSE, TRUE);
    }
    public function genexpensewisereport($date,$depot){
        $from=date('Y-m-d 00:00:00',  strtotime($date[0]));
        $to=date('Y-m-d 23:59:59',  strtotime($date[1]));
        $reports=array();
        $devices=$this->_getdevices($depot);
        $count=0;
        $configid='';
        $conductor='';
        $query[]=$this->rdb->qb('order_by',"id_expenses",'asc');
        $exp=$this->rdb->fetch('default','expenses',$query);
        unset($query);
        foreach($exp as $edetails):
            $expmaster[$edetails['id_expenses']]=$edetails['expense_name'];
        endforeach;
        foreach($devices as $device):
            $query[]=$this->rdb->qb('where',"wbopentimestamp BETWEEN '$from' AND '$to'",NULL,FALSE);
            $query[]=$this->rdb->qb('where',"is_wb_closed",1);
            $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
            $waybills=$this->rdb->fetch('data','waybilldetails',$query);
            unset($query);
            if(count($waybills)!=0):
                foreach($waybills as $wb):
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $trips=$this->rdb->fetch('data','tripdetails',$query);
                    unset($query);
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id,conductors_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_deviceconductors",$configinfo->conductors_id);
                        $conductorinfo=$this->rdb->fetch('config','deviceconductors',$query,FALSE, TRUE);
                        $conductor=$conductorinfo->conductor_name;
                        unset($query);
                    endif;
                    $query[]=$this->rdb->qb('where',"e_waybilldetails_id",$wb['id_waybilldetails']);
                    $expenses=$this->rdb->fetch('data','expensedetails',$query);
                    unset($query);
                    
                    if(count($expenses)>0):
                        foreach($expenses as $expense):
                            $reports[$count]['busno']=$busno;
                            $reports[$count]['depot']=$device['depot_name'];
                            $reports[$count]['wbn']=$wb['way_bill_no'];
                            $reports[$count]['conductor']=$conductor;
                            $reports[$count]['uid']=$device['uid'];
                            $reports[$count]['date']=$expense['e_timestamp'];
                            $reports[$count]['expense']=$expmaster[$expense['expenses_id']];
                            $reports[$count]['amount']=$expense['amount'];
                        $count+=1;

                        endforeach;
                    endif;
                endforeach;
            endif;
        endforeach;
        return $reports;
    }
    public function genstagewisereport($date,$depot){
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
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $trips=$this->rdb->fetch('data','tripdetails',$query);
                    unset($query);
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id,conductors_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_deviceconductors",$configinfo->conductors_id);
                        $conductorinfo=$this->rdb->fetch('config','deviceconductors',$query,FALSE, TRUE);
                        $conductor=$conductorinfo->conductor_name;
                        unset($query);
                    endif;
                    foreach($trips as $trip):
                        if($routeid!=$trip['deviceroutes_id']):
                            $query[]=$this->rdb->qb('where',"id_deviceroutes",$trip['deviceroutes_id']);
                            $query[]=$this->rdb->qb('select',"route_name,stages");
                            $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                            unset($query);
                            $routeid=$trip['deviceroutes_id'];
                            $oroutestages=explode('#~#',$routeinfo->stages);
                        endif;
                        $stages=$oroutestages;
                        $query[]=$this->rdb->qb('order_by',"source_stage_no",'asc');
                        $query[]=$this->rdb->qb('order_by',"destination_stage_no",'asc');
                        $query[]=$this->rdb->qb('group_by',"source_stage_no");
                        $query[]=$this->rdb->qb('group_by',"destination_stage_no");
                        $query[]=$this->rdb->qb('select',"sum(amount) as collection,source_stage_no,destination_stage_no");
                        $query[]=$this->rdb->qb('where',"tripdetails_id",$trip['id_tripdetails']);
                        $sdetails=$this->rdb->fetch('data','ticketdetails',$query);
                        unset($query);
                        $tdate=date('Y-m-d',  strtotime($trip['opentimestamp']));
                        foreach($sdetails as $sdetail):
                            $query[]=$this->rdb->qb('where',"tripdetails_id",$trip['id_tripdetails']);
                            $query[]=$this->rdb->qb('group_by',"ticket_type");
                            $query[]=$this->rdb->qb('where','source_stage_no',$sdetail['source_stage_no']);
                            $query[]=$this->rdb->qb('where','destination_stage_no',$sdetail['destination_stage_no']);
                            $query[]=$this->rdb->qb('select',"count(id_ticketdetails) as tickets,ticket_type");
                            $recorddetails=$this->rdb->fetch('data','ticketdetails',$query);
                            unset($query);
                            $sno=intval($sdetail['source_stage_no'])-1;
                            $dno=intval($sdetail['destination_stage_no'])-1;
                            $reports[$count]['uid']=$device['uid'];
                            $reports[$count]['busno']=$busno;
                            $reports[$count]['conductor']=$conductor;
                            $reports[$count]['depot']=$device['depot_name'];
                            $reports[$count]['date']=$tdate;
                            $reports[$count]['wbn']=$wb['way_bill_no'];
                            $reports[$count]['routeno']=$routeinfo->route_name;
                            $reports[$count]['tripno']=$trip['tripno'];
                            $reports[$count]['up']=$stages[$sno];
                            $reports[$count]['down']=$stages[$dno];
                            $reports[$count]['tickets']=0;
                            $reports[$count]['pass']=0;
                            $reports[$count]['luggages']=0;
                            $reports[$count]['collection']=$sdetail['collection'];
                            foreach($recorddetails as $recorddetail):
                                if($recorddetail['ticket_type']==1)
                                    $reports[$count]['tickets']=$recorddetail['tickets'];
                                else if($recorddetail['ticket_type']==2)
                                    $reports[$count]['pass']=$recorddetail['tickets'];
                                else if($recorddetail['ticket_type']==3)
                                    $reports[$count]['luggages']=$recorddetail['tickets'];
                            endforeach;
                            $count+=1;
                        endforeach;
                    endforeach;
                endforeach;
            endif;
            
        endforeach;
        return $reports;
    }
    public function genticketwisereport($date,$depot){
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
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $trips=$this->rdb->fetch('data','tripdetails',$query);
                    unset($query);
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id,conductors_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_deviceconductors",$configinfo->conductors_id);
                        $conductorinfo=$this->rdb->fetch('config','deviceconductors',$query,FALSE, TRUE);
                        $conductor=$conductorinfo->conductor_name;
                        unset($query);
                    endif;
                    foreach($trips as $trip):
                        if($routeid!=$trip['deviceroutes_id']):
                            $query[]=$this->rdb->qb('where',"id_deviceroutes",$trip['deviceroutes_id']);
                            $query[]=$this->rdb->qb('select',"route_name,stages");
                            $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                            unset($query);
                            $routeid=$trip['deviceroutes_id'];
                            $stages=explode('#~#',$routeinfo->stages);
                        endif;
                        $query[]=$this->rdb->qb('order_by',"ticket_no",'asc');
                        $query[]=$this->rdb->qb('where',"tripdetails_id",$trip['id_tripdetails']);
                        $tktdetails=$this->rdb->fetch('data','ticketdetails',$query);
                        unset($query);
                        foreach($tktdetails as $tktdetail):
                                $sno=intval($tktdetail['source_stage_no'])-1;
                                $dno=intval($tktdetail['destination_stage_no'])-1;
                                $reports[$count]['uid']=$device['uid'];
                                $reports[$count]['conductor']=$conductor;
                                $reports[$count]['depot']=$device['depot_name'];
                                $reports[$count]['date']=$tktdetail['ticket_timestamp'];
                                $reports[$count]['tktno']=$tktdetail['ticket_no'];
                                $reports[$count]['busno']=$busno;
                                $reports[$count]['wbn']=$wb['way_bill_no'];
                                $reports[$count]['routeno']=$routeinfo->route_name;
                                $reports[$count]['tripno']=$trip['tripno'];
                                $reports[$count]['up']=$stages[$sno];
                                $reports[$count]['down']=$stages[$dno];
                                $reports[$count]['paytype']=($tktdetail['paytype']==0?'Cash':'Card');
                                if($tktdetail['ticket_type']==1)://Passenger Ticket
                                    $reports[$count]['tickets']=$tktdetail['qty'];
                                    $reports[$count]['pass']=0;
                                    $reports[$count]['luggage']=0;
                                elseif($tktdetail['ticket_type']==2):
                                    $reports[$count]['tickets']=0;
                                    $reports[$count]['pass']=$tktdetail['qty'];
                                    $reports[$count]['luggage']=0;
                                elseif($tktdetail['ticket_type']==3):
                                    $reports[$count]['tickets']=0;
                                    $reports[$count]['pass']=0;
                                    $reports[$count]['luggage']=$tktdetail['qty'];
                                endif;
                                $reports[$count]['amt']=$tktdetail['amount'];
                                $count+=1;
                        endforeach;
                    endforeach;
                endforeach;
            endif;
            
        endforeach;
        return $reports;
    }
    public function gencardbasedreport($date,$depot){
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
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $trips=$this->rdb->fetch('data','tripdetails',$query);
                    unset($query);
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id,conductors_id,drivers_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_deviceconductors",$configinfo->conductors_id);
                        $conductorinfo=$this->rdb->fetch('config','deviceconductors',$query,FALSE, TRUE);
                        $conductor=$conductorinfo->conductor_name;
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_devicedrivers",$configinfo->drivers_id);
                        $driverinfo=$this->rdb->fetch('config','devicedrivers',$query,FALSE, TRUE);
                        $driver=$driverinfo->driver_name;
                        unset($query);
                    endif;
                    foreach($trips as $trip):
                        if($routeid!=$trip['deviceroutes_id']):
                            $query[]=$this->rdb->qb('where',"id_deviceroutes",$trip['deviceroutes_id']);
                            $query[]=$this->rdb->qb('select',"route_name,stages");
                            $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                            unset($query);
                            $routeid=$trip['deviceroutes_id'];
                            $stages=explode('#~#',$routeinfo->stages);
                        endif;
                        $query[]=$this->rdb->qb('order_by',"ticket_no",'asc');
                        $query[]=$this->rdb->qb('where',"ticket_type !=",3);
                        $query[]=$this->rdb->qb('where',"paytype",'1');
                        $query[]=$this->rdb->qb('where',"tripdetails_id",$trip['id_tripdetails']);
                        $tktdetails=$this->rdb->fetch('data','ticketdetails',$query);
                        unset($query);
                        foreach($tktdetails as $tktdetail):
                            if($tktdetail['ticket_type']!=3):
                                //order->date, bus no, wbn, route, trip no, tkt no, upstage, down stage, payment mode, ticket, pass, amt
                                $sno=intval($tktdetail['source_stage_no'])-1;
                                $dno=intval($tktdetail['destination_stage_no'])-1;
                                $reports[$count]['uid']=$device['uid'];
                                $reports[$count]['depot']=$device['depot_name'];
                                $reports[$count]['conductor']=$conductor;
                                $reports[$count]['driver']=$driver;
                                $reports[$count]['date']=$tktdetail['ticket_timestamp'];
                                $reports[$count]['tktno']=$tktdetail['ticket_no'];
                                $reports[$count]['busno']=$busno;
                                $reports[$count]['wbn']=$wb['way_bill_no'];
                                $reports[$count]['routeno']=$routeinfo->route_name;
                                $reports[$count]['tripno']=$trip['tripno'];
                                $reports[$count]['up']=$stages[$sno];
                                $reports[$count]['down']=$stages[$dno];
                                $reports[$count]['card']=$tktdetail['carduid'];
                                $reports[$count]['amt']=$tktdetail['amount'];
                                $count+=1;
                            endif;
                        endforeach;
                    endforeach;
                endforeach;
            endif;
            
        endforeach;
        return $reports;
    }
    public function gendaywisereportadmin($date){
        $from=date('Y-m-d 00:00:00',  strtotime($date[0]));
        $to=date('Y-m-d 23:59:59',  strtotime($date[1]));
        $reports=array();
        $devices=$this->_getdevices();
        $count=0;
        $routeid='';
        $configid='';
        $conductor='';
        $driver='';
        foreach($devices as $device):
            $query[]=$this->rdb->qb('where',"(wbopentimestamp BETWEEN '$from' AND '$to' OR wbclosedrectimestamp IS NULL)",NULL,FALSE);
            $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
            $waybills=$this->rdb->fetch('data','waybilldetails',$query);
            unset($query);
            if(count($waybills)!=0):
                foreach($waybills as $wb):
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id,conductors_id,drivers_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no,seat_capacity");
                        $businfo=$this->rdb->fetch('default','buses',$query,FALSE, TRUE);
                        $busno=$businfo->bus_no;
                        $seatcapacity=$businfo->seat_capacity;
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_deviceconductors",$configinfo->conductors_id);
                        $conductorinfo=$this->rdb->fetch('config','deviceconductors',$query,FALSE, TRUE);
                        $conductor=$conductorinfo->conductor_name;
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_devicedrivers",$configinfo->drivers_id);
                        $driverinfo=$this->rdb->fetch('config','devicedrivers',$query,FALSE, TRUE);
                        $driver=$driverinfo->driver_name;
                        unset($query);
                    endif;
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
                if($wb['is_wb_closed']==1):
                    $reports[$count]['busno']=$busno;
                    $reports[$count]['seatcapacity']=$seatcapacity;
                    $reports[$count]['handovercash']=$wb['wbhandovercash'];
                    $reports[$count]['depot']=$device['depot_name'];
                    $reports[$count]['conductor']=$conductor;
                    $reports[$count]['driver']=$driver;
                    $reports[$count]['ticketrange']=$wb['wbopenticket'].' - '.$wb['wbcloseticket'];
                    $reports[$count]['routeno']=$routeinfo->route_name;
                    $reports[$count]['wbopening']=$wb['wbopentimestamp'];
                    $reports[$count]['wbn']=$wb['way_bill_no'];
                    $reports[$count]['wbclosed']=1;
                    $reports[$count]['uid']=$device['uid'];
                    $reports[$count]['trips']=$wb['total_trips'];
                    $reports[$count]['tickets']=$wb['wb_total_tickets'];
                    $reports[$count]['passes']=$wb['wb_total_passes'];
                    $reports[$count]['luggages']=$wb['wb_total_luggages'];
                    $reports[$count]['ticketamt']=$wb['wb_tickets_amount'];
                    $reports[$count]['passamt']=$wb['wb_passes_amount'];
                    $reports[$count]['lugamt']=$wb['wb_luggages_amount'];
                    $reports[$count]['expensesamt']=$wb['wb_expenses_amount'];
                    $reports[$count]['cardamt']=$wb['wb_card_amount'];
                    $reports[$count]['cashamt']=$wb['wb_cash_amount'];
                else:
                    $tdetails=$this->_getwbinfobyid($wb['id_waybilldetails']);
                    $reports[$count]['busno']=$busno;
                    $reports[$count]['seatcapacity']=$seatcapacity;
                    $reports[$count]['handovercash']=$wb['wbhandovercash'];
                    $reports[$count]['depot']=$device['depot_name'];
                    $reports[$count]['conductor']=$conductor;
                    $reports[$count]['driver']=$driver;
                    $reports[$count]['ticketrange']=$wb['wbopenticket'].' - '.$tdetails['lastticket'];
                    $reports[$count]['routeno']=$routeinfo->route_name;
                    $reports[$count]['wbopening']=$wb['wbopentimestamp'];
                    $reports[$count]['wbn']=$wb['way_bill_no'];
                    $reports[$count]['wbclosed']=0;
                    $reports[$count]['uid']=$device['uid'];
                    $reports[$count]['trips']=$tdetails['trips'];
                    $reports[$count]['tickets']=$tdetails['tickets'];
                    $reports[$count]['passes']=$tdetails['passes'];
                    $reports[$count]['luggages']=$tdetails['luggages'];
                    $reports[$count]['ticketamt']=$tdetails['ticketsamt'];
                    $reports[$count]['passamt']=$tdetails['passesamt'];
                    $reports[$count]['lugamt']=$tdetails['lugamt'];
                    $reports[$count]['expensesamt']=$tdetails['expenses'];
                    $reports[$count]['cardamt']=$tdetails['card'];
                    $reports[$count]['cashamt']=$tdetails['cash'];
                endif;
                $count+=1;
                endforeach;
            endif;
        endforeach;
        return $reports;
    }
    public function gentripwisereportadmin($date){
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
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $trips=$this->rdb->fetch('data','tripdetails',$query);
                    unset($query);
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id,conductors_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no,seat_capacity");
                        $businfo=$this->rdb->fetch('default','buses',$query,FALSE, TRUE);
                        $busno=$businfo->bus_no;
                        $seatcapacity=$businfo->seat_capacity;
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_deviceconductors",$configinfo->conductors_id);
                        $conductorinfo=$this->rdb->fetch('config','deviceconductors',$query,FALSE, TRUE);
                        $conductor=$conductorinfo->conductor_name;
                        unset($query);
                    endif;
                    foreach($trips as $trip):
                        if($routeid!=$trip['deviceroutes_id']):
                            $query[]=$this->rdb->qb('where',"id_deviceroutes",$trip['deviceroutes_id']);
                            $query[]=$this->rdb->qb('select',"route_name");
                            $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                            unset($query);
                            $routeid=$trip['deviceroutes_id'];
                        endif;
                        if($trip['is_trip_closed']==1):
                            $reports[$count]['busno']=$busno;
                            $reports[$count]['seatcapacity']=$seatcapacity;
                            $reports[$count]['depot']=$device['depot_name'];
                            $reports[$count]['conductor']=$conductor;
                            $reports[$count]['routeno']=$routeinfo->route_name;
                            $reports[$count]['ticketrange']=$trip['first_ticket_no'].' - '.$trip['last_ticket_no'];
                            $reports[$count]['tripopening']=$trip['opentimestamp'];
                            $reports[$count]['fleetno']=$trip['fleet_no'];
                            $reports[$count]['wbn']=$wb['way_bill_no'];
                            $reports[$count]['tripclosed']=$trip['is_trip_closed'];
                            $reports[$count]['uid']=$device['uid'];
                            $reports[$count]['tripno']=$trip['tripno'];
                            $reports[$count]['tickets']=$trip['total_tickets'];
                            $reports[$count]['passes']=$trip['total_passes'];
                            $reports[$count]['luggages']=$trip['total_luggages'];
                            $reports[$count]['ticketamt']=$trip['tickets_amount'];
                            $reports[$count]['passamt']=$trip['passes_amount'];
                            $reports[$count]['lugamt']=$trip['luggages_amount'];
                            $reports[$count]['card']=$trip['card_amount'];
                            $reports[$count]['cash']=$trip['cash_amount'];
                        else:
                            $tdetails=$this->_gettripticketinfobyid($trip['id_tripdetails']);
                            $reports[$count]['busno']=$busno;
                            $reports[$count]['seatcapacity']=$seatcapacity;
                            $reports[$count]['depot']=$device['depot_name'];
                            $reports[$count]['conductor']=$conductor;
                            $reports[$count]['routeno']=$routeinfo->route_name;
                            $reports[$count]['ticketrange']=$trip['first_ticket_no'].' - '.$tdetails['lastticket'];
                            $reports[$count]['tripopening']=$trip['opentimestamp'];
                            $reports[$count]['fleetno']=$trip['fleet_no'];
                            $reports[$count]['wbn']=$wb['way_bill_no'];
                            $reports[$count]['tripclosed']=$trip['is_trip_closed'];
                            $reports[$count]['uid']=$device['uid'];
                            $reports[$count]['tripno']=$trip['tripno'];
                            $reports[$count]['tickets']=$tdetails['tickets'];
                            $reports[$count]['passes']=$tdetails['passes'];
                            $reports[$count]['luggages']=$tdetails['luggages'];
                            $reports[$count]['ticketamt']=$tdetails['ticketsamt'];
                            $reports[$count]['passamt']=$tdetails['passesamt'];
                            $reports[$count]['lugamt']=$tdetails['lugamt'];
                            $reports[$count]['cashamt']=$tdetails['cash'];
                            $reports[$count]['cardamt']=$tdetails['card'];
                        endif;
                        $count+=1;
                    endforeach;
                endforeach;
            endif;
            
        endforeach;
        return $reports;
    }
    public function genexpensewisereportadmin($date){
        $from=date('Y-m-d 00:00:00',  strtotime($date[0]));
        $to=date('Y-m-d 23:59:59',  strtotime($date[1]));
        $reports=array();
        $devices=$this->_getdevices();
        $count=0;
        $configid='';
        $conductor='';
        $query[]=$this->rdb->qb('order_by',"id_expenses",'asc');
        $exp=$this->rdb->fetch('default','expenses',$query);
        unset($query);
        foreach($exp as $edetails):
            $expmaster[$edetails['id_expenses']]=$edetails['expense_name'];
        endforeach;
        foreach($devices as $device):
            $query[]=$this->rdb->qb('where',"wbopentimestamp BETWEEN '$from' AND '$to'",NULL,FALSE);
            $query[]=$this->rdb->qb('where',"is_wb_closed",1);
            $query[]=$this->rdb->qb('where',"wb_devices_id",$device['id_devices']);
            $waybills=$this->rdb->fetch('data','waybilldetails',$query);
            unset($query);
            if(count($waybills)!=0):
                foreach($waybills as $wb):
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $trips=$this->rdb->fetch('data','tripdetails',$query);
                    unset($query);
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id,conductors_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_deviceconductors",$configinfo->conductors_id);
                        $conductorinfo=$this->rdb->fetch('config','deviceconductors',$query,FALSE, TRUE);
                        $conductor=$conductorinfo->conductor_name;
                        unset($query);
                    endif;
                    $query[]=$this->rdb->qb('where',"e_waybilldetails_id",$wb['id_waybilldetails']);
                    $expenses=$this->rdb->fetch('data','expensedetails',$query);
                    unset($query);
                    
                    if(count($expenses)>0):
                        foreach($expenses as $expense):
                            $reports[$count]['busno']=$busno;
                            $reports[$count]['depot']=$device['depot_name'];
                            $reports[$count]['wbn']=$wb['way_bill_no'];
                            $reports[$count]['conductor']=$conductor;
                            $reports[$count]['uid']=$device['uid'];
                            $reports[$count]['date']=$expense['e_timestamp'];
                            $reports[$count]['expense']=$expmaster[$expense['expenses_id']];
                            $reports[$count]['amount']=$expense['amount'];
                        $count+=1;

                        endforeach;
                    endif;
                endforeach;
            endif;
        endforeach;
        return $reports;
    }
    public function genstagewisereportadmin($date){
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
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $trips=$this->rdb->fetch('data','tripdetails',$query);
                    unset($query);
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id,conductors_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_deviceconductors",$configinfo->conductors_id);
                        $conductorinfo=$this->rdb->fetch('config','deviceconductors',$query,FALSE, TRUE);
                        $conductor=$conductorinfo->conductor_name;
                        unset($query);
                    endif;
                    foreach($trips as $trip):
                        if($routeid!=$trip['deviceroutes_id']):
                            $query[]=$this->rdb->qb('where',"id_deviceroutes",$trip['deviceroutes_id']);
                            $query[]=$this->rdb->qb('select',"route_name,stages");
                            $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                            unset($query);
                            $routeid=$trip['deviceroutes_id'];
                            $oroutestages=explode('#~#',$routeinfo->stages);
                        endif;
                        $stages=$oroutestages;
                        $query[]=$this->rdb->qb('order_by',"source_stage_no",'asc');
                        $query[]=$this->rdb->qb('order_by',"destination_stage_no",'asc');
                        $query[]=$this->rdb->qb('group_by',"source_stage_no");
                        $query[]=$this->rdb->qb('group_by',"destination_stage_no");
                        $query[]=$this->rdb->qb('select',"sum(amount) as collection,source_stage_no,destination_stage_no");
                        $query[]=$this->rdb->qb('where',"tripdetails_id",$trip['id_tripdetails']);
                        $sdetails=$this->rdb->fetch('data','ticketdetails',$query);
                        unset($query);
                        $tdate=date('Y-m-d',  strtotime($trip['opentimestamp']));
                        foreach($sdetails as $sdetail):
                            $query[]=$this->rdb->qb('where',"tripdetails_id",$trip['id_tripdetails']);
                            $query[]=$this->rdb->qb('group_by',"ticket_type");
                            $query[]=$this->rdb->qb('where','source_stage_no',$sdetail['source_stage_no']);
                            $query[]=$this->rdb->qb('where','destination_stage_no',$sdetail['destination_stage_no']);
                            $query[]=$this->rdb->qb('select',"count(id_ticketdetails) as tickets,ticket_type");
                            $recorddetails=$this->rdb->fetch('data','ticketdetails',$query);
                            unset($query);
                            $sno=intval($sdetail['source_stage_no'])-1;
                            $dno=intval($sdetail['destination_stage_no'])-1;
                            $reports[$count]['uid']=$device['uid'];
                            $reports[$count]['busno']=$busno;
                            $reports[$count]['conductor']=$conductor;
                            $reports[$count]['depot']=$device['depot_name'];
                            $reports[$count]['date']=$tdate;
                            $reports[$count]['wbn']=$wb['way_bill_no'];
                            $reports[$count]['routeno']=$routeinfo->route_name;
                            $reports[$count]['tripno']=$trip['tripno'];
                            $reports[$count]['up']=$stages[$sno];
                            $reports[$count]['down']=$stages[$dno];
                            $reports[$count]['tickets']=0;
                            $reports[$count]['pass']=0;
                            $reports[$count]['luggages']=0;
                            $reports[$count]['collection']=$sdetail['collection'];
                            foreach($recorddetails as $recorddetail):
                                if($recorddetail['ticket_type']==1)
                                    $reports[$count]['tickets']=$recorddetail['tickets'];
                                else if($recorddetail['ticket_type']==2)
                                    $reports[$count]['pass']=$recorddetail['tickets'];
                                else if($recorddetail['ticket_type']==3)
                                    $reports[$count]['luggages']=$recorddetail['tickets'];
                            endforeach;
                            $count+=1;
                        endforeach;
                    endforeach;
                endforeach;
            endif;
            
        endforeach;
        return $reports;
    }
    public function genticketwisereportadmin($date){
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
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $trips=$this->rdb->fetch('data','tripdetails',$query);
                    unset($query);
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id,conductors_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_deviceconductors",$configinfo->conductors_id);
                        $conductorinfo=$this->rdb->fetch('config','deviceconductors',$query,FALSE, TRUE);
                        $conductor=$conductorinfo->conductor_name;
                        unset($query);
                    endif;
                    foreach($trips as $trip):
                        if($routeid!=$trip['deviceroutes_id']):
                            $query[]=$this->rdb->qb('where',"id_deviceroutes",$trip['deviceroutes_id']);
                            $query[]=$this->rdb->qb('select',"route_name,stages");
                            $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                            unset($query);
                            $routeid=$trip['deviceroutes_id'];
                            $stages=explode('#~#',$routeinfo->stages);
                        endif;
                        $query[]=$this->rdb->qb('order_by',"ticket_no",'asc');
                        $query[]=$this->rdb->qb('where',"tripdetails_id",$trip['id_tripdetails']);
                        $tktdetails=$this->rdb->fetch('data','ticketdetails',$query);
                        unset($query);
                        foreach($tktdetails as $tktdetail):
                                $sno=intval($tktdetail['source_stage_no'])-1;
                                $dno=intval($tktdetail['destination_stage_no'])-1;
                                $reports[$count]['uid']=$device['uid'];
                                $reports[$count]['conductor']=$conductor;
                                $reports[$count]['depot']=$device['depot_name'];
                                $reports[$count]['date']=$tktdetail['ticket_timestamp'];
                                $reports[$count]['tktno']=$tktdetail['ticket_no'];
                                $reports[$count]['busno']=$busno;
                                $reports[$count]['wbn']=$wb['way_bill_no'];
                                $reports[$count]['routeno']=$routeinfo->route_name;
                                $reports[$count]['tripno']=$trip['tripno'];
                                $reports[$count]['up']=$stages[$sno];
                                $reports[$count]['down']=$stages[$dno];
                                $reports[$count]['paytype']=($tktdetail['paytype']==0?'Cash':'Card');
                                if($tktdetail['ticket_type']==1)://Passenger Ticket
                                    $reports[$count]['tickets']=$tktdetail['qty'];
                                    $reports[$count]['pass']=0;
                                    $reports[$count]['luggage']=0;
                                elseif($tktdetail['ticket_type']==2):
                                    $reports[$count]['tickets']=0;
                                    $reports[$count]['pass']=$tktdetail['qty'];
                                    $reports[$count]['luggage']=0;
                                elseif($tktdetail['ticket_type']==3):
                                    $reports[$count]['tickets']=0;
                                    $reports[$count]['pass']=0;
                                    $reports[$count]['luggage']=$tktdetail['qty'];
                                endif;
                                $reports[$count]['amt']=$tktdetail['amount'];
                                $count+=1;
                        endforeach;
                    endforeach;
                endforeach;
            endif;
            
        endforeach;
        return $reports;
    }
    public function gencardbasedreportadmin($date){
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
                    $query[]=$this->rdb->qb('where',"waybilldetails_id",$wb['id_waybilldetails']);
                    $trips=$this->rdb->fetch('data','tripdetails',$query);
                    unset($query);
                    if($configid!=$wb['wb_deviceconfig_id']):
                        $query[]=$this->rdb->qb('where',"id_deviceconfig",$wb['wb_deviceconfig_id']);
                        $query[]=$this->rdb->qb('select',"buses_id,conductors_id,drivers_id");
                        $configinfo=$this->rdb->fetch('config','deviceconfig',$query,FALSE, TRUE);
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_buses",$configinfo->buses_id);
                        $query[]=$this->rdb->qb('select',"bus_no");
                        $busno=$this->rdb->fetch('default','buses',$query,FALSE, TRUE)->bus_no;
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_deviceconductors",$configinfo->conductors_id);
                        $conductorinfo=$this->rdb->fetch('config','deviceconductors',$query,FALSE, TRUE);
                        $conductor=$conductorinfo->conductor_name;
                        unset($query);
                        $query[]=$this->rdb->qb('where',"id_devicedrivers",$configinfo->drivers_id);
                        $driverinfo=$this->rdb->fetch('config','devicedrivers',$query,FALSE, TRUE);
                        $driver=$driverinfo->driver_name;
                        unset($query);
                    endif;
                    foreach($trips as $trip):
                        if($routeid!=$trip['deviceroutes_id']):
                            $query[]=$this->rdb->qb('where',"id_deviceroutes",$trip['deviceroutes_id']);
                            $query[]=$this->rdb->qb('select',"route_name,stages");
                            $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                            unset($query);
                            $routeid=$trip['deviceroutes_id'];
                            $stages=explode('#~#',$routeinfo->stages);
                        endif;
                        $query[]=$this->rdb->qb('order_by',"ticket_no",'asc');
                        $query[]=$this->rdb->qb('where',"ticket_type !=",3);
                        $query[]=$this->rdb->qb('where',"paytype",'1');
                        $query[]=$this->rdb->qb('where',"tripdetails_id",$trip['id_tripdetails']);
                        $tktdetails=$this->rdb->fetch('data','ticketdetails',$query);
                        unset($query);
                        foreach($tktdetails as $tktdetail):
                            if($tktdetail['ticket_type']!=3):
                                //order->date, bus no, wbn, route, trip no, tkt no, upstage, down stage, payment mode, ticket, pass, amt
                                $sno=intval($tktdetail['source_stage_no'])-1;
                                $dno=intval($tktdetail['destination_stage_no'])-1;
                                $reports[$count]['uid']=$device['uid'];
                                $reports[$count]['depot']=$device['depot_name'];
                                $reports[$count]['conductor']=$conductor;
                                $reports[$count]['driver']=$driver;
                                $reports[$count]['date']=$tktdetail['ticket_timestamp'];
                                $reports[$count]['tktno']=$tktdetail['ticket_no'];
                                $reports[$count]['busno']=$busno;
                                $reports[$count]['wbn']=$wb['way_bill_no'];
                                $reports[$count]['routeno']=$routeinfo->route_name;
                                $reports[$count]['tripno']=$trip['tripno'];
                                $reports[$count]['up']=$stages[$sno];
                                $reports[$count]['down']=$stages[$dno];
                                $reports[$count]['card']=$tktdetail['carduid'];
                                $reports[$count]['amt']=$tktdetail['amount'];
                                $count+=1;
                            endif;
                        endforeach;
                    endforeach;
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
            $response['luggages']=0;
            $response['lugamt']=0;
            $response['expenses']=0;
            $response['card']=0;
            $response['cash']=0;
            $routecheck=0;
            foreach($tripdetails as $trip):
                if($routecheck==1):
                    $query[]=$this->rdb->qb('where',"id_deviceroutes",$trip['deviceroutes_id']);
                    $query[]=$this->rdb->qb('select',"route_name");
                    $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                    unset($query);
                    $response['routeno']=$routeinfo->route_name;
                    $routecheck=1;
                endif;
                if($trip['is_trip_closed']==1):
                    $response['tickets']+=$trip['total_tickets'];
                    $response['passes']+=$trip['total_passes'];
                    $response['luggages']+=$trip['total_luggages'];
                    $response['ticketsamt']+=$trip['tickets_amount'];
                    $response['passesamt']+=$trip['passes_amount'];
                    $response['lugamt']+=$trip['luggages_amount'];
                    $response['expenses']+=$trip['expenses_amount'];
                    $response['card']+=$trip['card_amount'];
                    $response['cash']+=$trip['cash_amount'];
                    $response['lastticket']=$trip['last_ticket_no'];
                else:
                    $tdetails=$this->_gettripticketinfobyid($trip['id_tripdetails']);
                    $response['tickets']+=$tdetails['tickets'];
                    $response['passes']+=$tdetails['passes'];
                    $response['luggages']+=$tdetails['luggages'];
                    $response['ticketsamt']+=$tdetails['ticketsamt'];
                    $response['passesamt']+=$tdetails['passesamt'];
                    $response['lugamt']+=$tdetails['lugamt'];
                    $response['card']+=$tdetails['card'];
                    $response['cash']+=$tdetails['cash'];
                    $response['lastticket']=$tdetails['lastticket'];
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
            $response['luggages']=0;
            $response['lugamt']=0;
            $response['expenses']=0;
            $response['card']=0;
            $response['cash']=0;
            $routecheck=0;
            foreach($tripdetails as $trip):
                if($routecheck==1):
                    $query[]=$this->rdb->qb('where',"id_deviceroutes",$trip['deviceroutes_id']);
                    $query[]=$this->rdb->qb('select',"route_name");
                    $routeinfo=$this->rdb->fetch('config','deviceroutes',$query,FALSE, TRUE);
                    unset($query);
                    $response['routeno']=$routeinfo->route_name;
                    $routecheck=1;
                endif;
                if($trip['is_trip_closed']==1):
                    $response['tickets']+=$trip['total_tickets'];
                    $response['passes']+=$trip['total_passes'];
                    $response['luggages']+=$trip['total_luggages'];
                    $response['ticketsamt']+=$trip['tickets_amount'];
                    $response['passesamt']+=$trip['passes_amount'];
                    $response['lugamt']+=$trip['luggages_amount'];
                    $response['expenses']+=$trip['expenses_amount'];
                    $response['card']+=$trip['card_amount'];
                    $response['cash']+=$trip['cash_amount'];
                    $response['lastticket']=$trip['last_ticket_no'];
                else:
                    $tdetails=$this->_gettripticketinfobyid($trip['id_tripdetails']);
                    $response['tickets']+=$tdetails['tickets'];
                    $response['passes']+=$tdetails['passes'];
                    $response['luggages']+=$tdetails['luggages'];
                    $response['ticketsamt']+=$tdetails['ticketsamt'];
                    $response['passesamt']+=$tdetails['passesamt'];
                    $response['lugamt']+=$tdetails['lugamt'];
                    $response['card']+=$tdetails['card'];
                    $response['cash']+=$tdetails['cash'];
                    $response['lastticket']=$tdetails['lastticket'];
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
            $query[]=$this->rdb->qb('select','max(ticket_no) as lastticket');
            $query[]=$this->rdb->qb('where','tripdetails_id',$tid);
            $query[]=$this->rdb->qb('where','ticket_type !=',3);
            $lastticket=$this->rdb->fetch('data','ticketdetails',$query,FALSE,TRUE)->lastticket;
            unset($query);
            $response['tickets']=$tickets->tickets;
            $response['ticketsamt']=$tickets->amount;
            $response['passes']=$pass->tickets;
            $response['passesamt']=$pass->amount;
            $response['luggages']=$lug->tickets;
            $response['lugamt']=$lug->amount;
            $response['card']=$card;
            $response['lastticket']=$lastticket;
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