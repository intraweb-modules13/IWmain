<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class IWmain_Controller_Cron extends Zikula_AbstractController {
function UserReports() {
    $time = $args['time'];
    $result = '0';
    $msg = '';
    //Checking Mailer
    $modid = ModUtil::getIdFromName('Mailer');
    $modinfo = ModUtil::getInfo($modid);
    $IWforums = ModUtil::getVar('IWmain','crAc_UR_IWforums');
    $IWmessages = ModUtil::getVar('IWmain','crAc_UR_IWmessages');
    $IWforms = ModUtil::getVar('IWmain','crAc_UR_IWforms');
    $IWnoteboard = ModUtil::getVar('IWmain','crAc_UR_IWnoteboard');
    if ($modinfo['state'] != 3) {
        $result = '-1';
        $msg .= '<div>'.__('The Mailer module is not active. The cron can not send emails to users.', $dom).'</div>';
    }elseif (!$IWforums && !$IWmessages && !$IWforms && !$IWnoteboard){
        $result = '-1';
        $msg .= '<div>'.__('There is no module connected to User Reports', $dom).'</div>';
    }
    if ($result != '-1') {
        $forumsNews = $IWforums? ModUtil::func('IWmain', 'cron', 'getForumNews', array('time' => $time)) : array();
        $messagesNews = $IWmessages? ModUtil::func('IWmain', 'cron', 'getMessagesNews', array('time' => $time)) : array();
        $formsNews = $IWforms? ModUtil::func('IWmain', 'cron', 'getFormsNews', array('time' => $time)) : array();
        $noteboardNews = $IWnoteboard? ModUtil::func('IWmain', 'cron', 'getNoteboardNews', array('time' => $time)) : array();
        
    }
}
}
