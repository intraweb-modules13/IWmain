<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cron
 *
 * @author jguille2
 */
class IWmain_Api_Cron extends Zikula_AbstractApi {
public function getForumsNews($args) {
    $result = array();
    $dateTimeFrom = $args['dateTimeFrom'];
    $dateTimeTo = $args['dateTimeTo'];
    //Checking IWforums module
    $modinfo = ModUtil::getInfo(ModUtil::getIdFromName('IWforums'));
    if ($modinfo['state'] != 3) return $result;
    if ($modinfo['version'] >= '3.1.0') {
        $result = ModUtil::apiFunc('IWforums', 'user', 'getAllUnreadedMessages', array('dateTimeFrom' => $dateTimeFrom, 'dateTimeTo' => $dateTimeTo));
    } else {
        $messages = array();
        
        if (!is_null($dateTimeFrom)) {
            $pntable = DBUtil::getTables();
            $f = $pntable['IWforums_definition_column'];
            $t = $pntable['IWforums_temes_column'];
            $m = $pntable['IWforums_msg_column'];
            
            // Get all the messages posted after $dateTimeFrom in subscribibles forums
            $sql  = "SELECT F.$f[fid] AS fid,  M.$m[ftid] AS ftid, M.$m[fmid] AS fmid, M.$m[titol] AS msgTitle, M.$m[usuari] AS user, M.$m[data] AS date, M.$m[llegit] AS readers, T.$t[titol] AS topic, T.$t[order], ";
            $sql .= "F.$f[nom_forum] AS forum, F.subscriptionMode, F.subscribers, F.noSubscribers, F.$f[grup] AS grup, F.$f[mod] AS moderators ";
            $sql .= "FROM `IWforums_msg` AS M, `IWforums_temes` AS T, `IWforums_definition` AS F ";        
            $sql .= "WHERE M.$m[ftid] = T.$t[ftid] AND T.$t[fid] = F.$f[fid] AND F.$f[actiu] = 1 AND M.$m[data] >= ".$dateTimeFrom." AND M.$m[data] < ".$dateTimeTo." AND F.subscriptionMode > 0 ";
            $sql .= "ORDER BY F.$f[fid], T.$t[order], M.$m[data]";
            $query = DBUtil::executeSQL($sql);
            $messages = DBUtil::marshallObjects($query);

            foreach ($messages as $key => $message) {
                // Extract forum moderators
                $moderators = explode('$$', substr($message['moderators'], 0, strlen($message['moderators']) -1));
                unset($moderators[0]);
                //Extract message readers
                $readers = explode('$$', substr($message['readers'], 0, strlen($message['readers']) -1));
                unset($readers[0]);
                // Extract grups 
                $auxGroups = explode('$$', substr($message['grup'], 0, strlen($message['grup']) -1));
                unset($auxGroups[0]);
                $groups = array();
                foreach ($auxGroups as $ag){
                    $g = explode ('|', $ag);
                    $groups[] = $g[0];
                }
                // Construct a unique list with the users that have read access to a forum 
                $members = array();
                foreach ($groups as $group){
                    // Get group members
                    $users = UserUtil::getUsersForGroup($group);
                    foreach ($users as $user){
                        // Avoid duplicated users
                        if (!in_array($user, $members)) $members[$user] = $user;
                    }
                }
                // Add moderators
                foreach ($moderators as $moderator){
                    if (!in_array($moderator, $members)) $members[$moderator] = $moderator;
                }
                // Remove readers
                foreach ($readers as $reader) {
                    if (in_array($reader, $members)) unset($members[$reader]);                        
                }
                $messages[$key]['receivers'] = $members;
            }    
            
            // At this point, every message has a list of receivers
            // Let's construct an array with the associated information to send
            $information = array();
            foreach ($messages as $message){
                if (isset($message['receivers'])) {
                    foreach($message['receivers'] as $receiver){
                        $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
                        $information[$receiver][$message['fid']]['nom_forum'] = $message['forum'];
                        $information[$receiver][$message['fid']]['subscriptionMode'] = $message['subscriptionMode'];
                        $information[$receiver][$message['fid']]['fid'] = $message['fid'];
                        $information[$receiver][$message['fid']]['topics'][$message['ftid']]['titol'] = $message['topic'];
                        $information[$receiver][$message['fid']]['topics'][$message['ftid']]['messages'][$message['fmid']]['title'] = $message['msgTitle'];
                        $information[$receiver][$message['fid']]['topics'][$message['ftid']]['messages'][$message['fmid']]['author'] = ModUtil::func('IWmain', 'user', 'getUserInfo', array('sv' => $sv, 'info' => 'ncc', 'uid' => $message['user']));
                        $information[$receiver][$message['fid']]['topics'][$message['ftid']]['messages'][$message['fmid']]['date'] = strtolower(DateUtil::getDatetime($message['date'], 'datetimelong', true));
                    }
                }
            }
            foreach ($information as $key => $userReport){
                $view = Zikula_View::getInstance($this->name, false);  
                $view->assign('info', $userReport);
                $result[$key]['IWforums'] = $view->fetch('reports/IWforums_user_report.tpl');
            }
        }
    }
    return $result;
}
public function getMessagesNews($args) {
    $result = array();
    $dateTimeFrom = $args['dateTimeFrom'];
    $dateTimeTo = $args['dateTimeTo'];
    //Checking IWmessages module
    $modinfo = ModUtil::getInfo(ModUtil::getIdFromName('IWmessages'));
    if ($modinfo['state'] != 3) return $result;
    
    if (!is_null($dateTimeFrom)) {
            $sql  = "SELECT iw_msg_id AS msg_id, iw_subject AS subject, UNIX_TIMESTAMP(iw_msg_time) AS msg_time, iw_to_userid AS to_userid, iw_from_userid AS from_userid, iw_read_msg AS read_msg";
            $sql .= " FROM IWmessages";
            $sql .= " WHERE iw_read_msg = 0 AND UNIX_TIMESTAMP(iw_msg_time) >=".$dateTimeFrom." AND UNIX_TIMESTAMP(iw_msg_time) <".$dateTimeTo;
            $query = DBUtil::executeSQL($sql);
            $messages = DBUtil::marshallObjects($query);
            $mes2 = array();
            foreach ($messages as $message) {
                $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
                $fromUserInfo = ModUtil::func($this->name, 'user', 'getUserInfo', array('sv' => $sv, 'uid' => $message['from_userid'],'info'=> array('l','n','c1')));
                $message['from_userName'] = ($fromUserInfo['n'] != '') ? $fromUserInfo['n']." ".$fromUserInfo['c1'] : $fromUserInfo['l'];
                $message['msg_time_tx'] = date("d-m-Y / H:i",$message['msg_time']);
                $mes2[$message['to_userid']][] = $message;
            }
            foreach ($mes2 as $key => $me2) {
                $view = Zikula_View::getInstance($this->name, false);  
                $view->assign('messages', $me2);
                $result[$key]['IWmessages'] = $view->fetch('reports/IWmessages_user_report.tpl');
            } 
    }
    
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
