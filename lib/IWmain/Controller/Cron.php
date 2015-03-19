<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class IWmain_Controller_Cron extends Zikula_AbstractController {
public function UserReports($args) {
    $time = $args['time'];
    $everybodySubscribed = $this->getVar('everybodySubscribed');
    $result = '0';
    $msg = '<h3>'.__('User reports').'</h3>';
    //Checking Mailer
    $modid = ModUtil::getIdFromName('Mailer');
    $modinfo = ModUtil::getInfo($modid);
    $IWforums = ModUtil::getVar('IWmain','crAc_UR_IWforums');
    $IWmessages = ModUtil::getVar('IWmain','crAc_UR_IWmessages');
    $IWforms = ModUtil::getVar('IWmain','crAc_UR_IWforms');
    $IWnoteboard = ModUtil::getVar('IWmain','crAc_UR_IWnoteboard');
    if ($modinfo['state'] != 3) {
        $result = '-1';
        $msg .= '<div>'.__('The Mailer module is not active. The cron can not send emails to users.').'</div>';
    }elseif (!$IWforums && !$IWmessages && !$IWforms && !$IWnoteboard){
        $result = '-1';
        $msg .= '<div>'.__('There is no module connected to User Reports').'</div>';
    }else {
        $msg .= '<div>'.__('Modules connected:').' ';
        $msg .= $IWforums ? '- IWforums ' : '';
        $msg .= $IWmessages ? '- IWmessages ' : '';
        $msg .= $IWforms ? '- IWforms ' : '';
        $msg .= $IWnoteboard ? '- IWnoteboard ' : '';
        $msg .= '</div><br>';
        //Getting News from modules
        $forumsNews = $IWforums ? ModUtil::func('IWmain', 'cron', 'getForumNews', array('time' => $time)) : array();
        $messagesNews = $IWmessages ? ModUtil::func('IWmain', 'cron', 'getMessagesNews', array('time' => $time)) : array();
        $formsNews = $IWforms ? ModUtil::func('IWmain', 'cron', 'getFormsNews', array('time' => $time)) : array();
        $noteboardNews = $IWnoteboard ? ModUtil::func('IWmain', 'cron', 'getNoteboardNews', array('time' => $time)) : array();
        //News construction
        $forumsNews = array_combine(array_map(function($a) {return '_' . $a;}, array_keys($forumsNews)), $forumsNews);
        $messagesNews = array_combine(array_map(function($a) {return '_' . $a;}, array_keys($messagesNews)), $messagesNews);
        $formsNews = array_combine(array_map(function($a) {return '_' . $a;}, array_keys($formsNews)), $formsNews);
        $noteboardNews = array_combine(array_map(function($a) {return '_' . $a;}, array_keys($noteboardNews)), $noteboardNews);
        $news = array_merge_recursive($forumsNews , $messagesNews , $formsNews , $noteboardNews);
        $news = array_combine(array_map(function($a) {return substr($a, 1);}, array_keys($news)), $news);
        
        //Case no news
        if (empty($news)) {
            $result = '0';
            $msg .= '<div>'.__('No news').'</div>';
            return $msg;
        }
        /*return $msg;
        echo "<pre>";
        print_r($news);
        echo "</pre>";
        exit();*/
        $subject = $this->getVar('cronSubjectText');
        $cronHeaderText = $this->getVar('cronHeaderText');
        $cronFooterText = $this->getVar('cronFooterText');
        $uSub = 0;
        $uEmail = 0;
        $uOk = 0;
        foreach ($news as $userId => $userNews) {
            /*echo $userId."<br>";
            echo "<pre>";
        print_r($userNews);
        echo "</pre>";
        exit();*/
            //get subscriber info
            $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
            $subscribeNews = ModUtil::apiFunc('IWmain', 'user', 'userVarExists', array('name' => 'subscribeNews',
                    'module' => 'IWmain_cron',
                    'uid' => $userId,
                    'sv' => $sv));
            //get user mail
            $userInfo = UserUtil::getVars($userId);
            $userMail = $userInfo['email'];
            if ($everybodySubscribed || $subscribeNews) {
                $uSub++;
                if ($userMail != '') {
                    $uEmail++;
                    $view = Zikula_View::getInstance($this->name, false);  
                    $view->assign('cronHeaderText', $cronHeaderText);
                    $newsText = $view->fetch('nom_plantilla.tpl');
                    $sendResult = ModUtil::apiFunc('Mailer', 'user', 'sendmessage', array('toname' => $userMail,
                                'toaddress' => $userMail,
                                'subject' => $subject,
                                'body' => $newsText,
                                'html' => 1));
                    if ($sendResult) $uOk++;
                }
            }
        }
        $msg .= '<ul><li>'.count($news).' '.__('users with news.').'</li>';
        $msg .= '<li>'.$uSub.' '.__('of them subscribed.').'</li>';
        $msg .= '<li>'.$uEmail.' '.__('subscribers with email.').'</li>';
        $msg .= '<li>'.$uOk.' '.__('emails sended.').'</li></ul>';

    }
    return $msg;
}
public function getForumNews($args) {
    $time = $args['time'];
    $result = array(2 => array ('forum' => 'html forum del 2'), 3 => array ('forum' => 'html forum del 3'));
    return $result;
}
public function getMessagesNews($args) {
    $time = $args['time'];
    $result = array(3 => array ('messages' => 'html messages del 3'), 4 => array ('forum' => 'html messages del 4'));
    return $result;
}
public function getFormsNews($args) {
    $result = array();
    return $result;
}
public function getNoteboardNews($args) {
    $result = array();
    return $result;
}
}
