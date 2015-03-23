<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class IWmain_Controller_Cron extends Zikula_AbstractController {
public function UserReports($args) {
    $dateTimeTo = $args['dateTimeTo'];
    $dateTimeFrom = $args['dateTimeFrom'];
    $cronURfreq = $this->getVar('cronURfreq');
    if (($dateTimeTo - $dateTimeFrom) < ($cronURfreq*60*60)) {
        $msg = '<div>'.__('User reports enabled, but executed too recently').'</div>';
        $exit = -3;
        return array('cronResponse' => $msg, 'exit' => $exit);
    }
    $everybodySubscribed = $this->getVar('everybodySubscribed');
    $msg = '<h3>'.__('User reports').'</h3>';
    //Checking Mailer
    $modid = ModUtil::getIdFromName('Mailer');
    $modinfo = ModUtil::getInfo($modid);
    $IWforums = ModUtil::getVar('IWmain','crAc_UR_IWforums');
    $IWmessages = ModUtil::getVar('IWmain','crAc_UR_IWmessages');
    $IWforms = ModUtil::getVar('IWmain','crAc_UR_IWforms');
    $IWnoteboard = ModUtil::getVar('IWmain','crAc_UR_IWnoteboard');
    if ($modinfo['state'] != 3) {
        $exit = '-1';
        $msg .= '<div>'.__('The Mailer module is not active. The cron can not send emails to users.').'</div>';
    }elseif (!$IWforums && !$IWmessages && !$IWforms && !$IWnoteboard){
        $exit = '0';
        $msg .= '<div>'.__('There is no module connected to User Reports').'</div>';
    }else {
        $msg .= '<div>'.__('Modules connected:').' ';
        $msg .= $IWforums ? '- IWforums ' : '';
        $msg .= $IWmessages ? '- IWmessages ' : '';
        $msg .= $IWforms ? '- IWforms ' : '';
        $msg .= $IWnoteboard ? '- IWnoteboard ' : '';
        $msg .= '</div><br>';
        //Getting News from modules
        $forumsNews = $IWforums ? ModUtil::apiFunc('IWmain', 'cron', 'getForumsNews', array('dateTimeTo' => $dateTimeTo, 'dateTimeFrom' => $dateTimeFrom)) : array();
        $messagesNews = $IWmessages ? ModUtil::apiFunc('IWmain', 'cron', 'getMessagesNews', array('dateTimeTo' => $dateTimeTo, 'dateTimeFrom' => $dateTimeFrom)) : array();
        $formsNews = $IWforms ? ModUtil::apiFunc('IWmain', 'cron', 'getFormsNews', array('dateTimeTo' => $dateTimeTo, 'dateTimeFrom' => $dateTimeFrom)) : array();
        $noteboardNews = $IWnoteboard ? ModUtil::apiFunc('IWmain', 'cron', 'getNoteboardNews', array('dateTimeTo' => $dateTimeTo, 'dateTimeFrom' => $dateTimeFrom)) : array();
        //News construction
        $forumsNews = array_combine(array_map(function($a) {return '_' . $a;}, array_keys($forumsNews)), $forumsNews);
        $messagesNews = array_combine(array_map(function($a) {return '_' . $a;}, array_keys($messagesNews)), $messagesNews);
        $formsNews = array_combine(array_map(function($a) {return '_' . $a;}, array_keys($formsNews)), $formsNews);
        $noteboardNews = array_combine(array_map(function($a) {return '_' . $a;}, array_keys($noteboardNews)), $noteboardNews);
        $news = array_merge_recursive($forumsNews , $messagesNews , $formsNews , $noteboardNews);
        $news = array_combine(array_map(function($a) {return substr($a, 1);}, array_keys($news)), $news);
        //Case no news
        if (empty($news)) {
            $exit = '1';
            $msg .= '<div>'.__('No news').'</div>';
            return array('cronResponse' => $msg, 'exit' => $exit);
        }
        $subject = $this->getVar('cronSubjectText');
        $HeaderText = $this->getVar('cronHeaderText');
        $FooterText = $this->getVar('cronFooterText');
        $IWforumsHd = $this->getVar('crAc_UR_IWforums_hd');
        $IWmessagesHd = $this->getVar('crAc_UR_IWmessages_hd');
        $IWformsHd = $this->getVar('crAc_UR_IWforms_hd');
        $IWnoteboardHd = $this->getVar('crAc_UR_IWnoteboard_hd');
        $uSub = 0;
        $uEmail = 0;
        $uOk = 0;
        foreach ($news as $userId => $userNews) {
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
                    $view->assign('cronHeaderText', $cronHeaderText)
                            ->assign('FooterText',$FooterText)
                            ->assign('HeaderText',$HeaderText)
                            ->assign('IWforums',$IWforums)
                            ->assign('IWmessages',$IWmessages)
                            ->assign('IWforms',$IWforms)
                            ->assign('IWnoteboard',$IWnoteboard)
                            ->assign('IWforumsHd',$IWforumsHd)
                            ->assign('IWmessagesHd',$IWmessagesHd)
                            ->assign('IWformsHd',$IWformsHd)
                            ->assign('IWnoteboardHd',$IWnoteboardHd)
                            ->assign('userNews',$userNews);
                    $newsText = $view->fetch('IWmain_cron_mail.tpl');
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
        //Checking sending
        $exit = ($uEmail == $uOk) ? 1 : -1;
    }
    return array('cronResponse' => $msg, 'exit' => $exit);
}
}
