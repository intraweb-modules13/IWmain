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
public function getForumNews($args) {
    $time = $args['time'];
    $result = array(2 => array ('IWforums' => 'html forum del 2'), 3 => array ('IWforums' => 'html forum del 3'));
    return $result;
}
public function getMessagesNews($args) {
    $time = $args['time'];
    $result = array(2 => array ('IWmessages' => 'html <b>messages</b> del 2'), 4 => array ('IWmessages' => 'html messages del 4'));
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
