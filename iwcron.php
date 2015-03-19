<?php

//1. Init zikula engine
$time = time();
include 'lib/bootstrap.php';
$core->init();
ModUtil::load('IWmain', 'admin');
$langcode = ModUtil::getVar('ZConfig', 'language_i18n');
ZLanguage::setLocale($langcode);
ZLanguage::bindCoreDomain();
$dom = ZLanguage::getModuleDomain('IWmain');

//2. Checking cron password
$passwordActive = ModUtil::getVar('IWmain','cronPasswordActive');
if ($passwordActive) {
    $passwordString = ModUtil::getVar('IWmain','cronPasswordString');
    $passwordSended = FormUtil::getPassedValue('password',null,'GET');
    if ($passwordString !== $passwordSended) {
        print __("You can't execute iwcron", $dom);
        cronShutdown();

    }
}

//3. Cron actions
$sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
$lastCronSuccessfull = ModUtil::func('IWmain', 'user', 'userGetVar', array('uid' => -100,
            'name' => 'lastCronSuccessfull',
            'module' => 'IWmain_cron',
            'sv' => $sv));
$dateTimeFrom = (int)$lastCronSuccessfull;
$cronResponse = '<h2>'.__('Cron Actions', $dom).'</h2>';
$exit = -2;
$crAc_UserReports = ModUtil::getVar('IWmain','crAc_UserReports');
if ($crAc_UserReports) $userReports = ModUtil::func('IWmain', 'cron', 'userReports', array('dateTimeFrom' => $dateTimeFrom, 'dateTimeTo' => $time));
$cronResponse .= isset($userReports['cronResponse']) ? $userReports['cronResponse'] : '';
$exit = isset($userReports['exit']) ? $userReports['exit'] : $exit;

//4. Cron times
$executeTime = date('M, d Y - H.i', $time);
//Cron successfull time
if ($exit == 1 || $exit == 0) {
    $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
    ModUtil::func('IWmain', 'user', 'userSetVar', array('uid' => -100,
        'name' => 'lastCronSuccessfull',
        'module' => 'IWmain_cron',
        'lifetime' => 1000 * 24 * 60 * 60,
        'sv' => $sv,
        'value' => $time));
    $lastCronSuccessfull = $time;
} else {
    $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
    $lastCronSuccessfull = ModUtil::func('IWmain', 'user', 'userGetVar', array('uid' => -100,
            'name' => 'lastCronSuccessfull',
            'module' => 'IWmain_cron',
            'sv' => $sv));
}
$lastCronSuccessfullTime = date('M, d Y - H.i', $lastCronSuccessfull);
if ($lastCronSuccessfullTime == '')
    $lastCronSuccessfullTime = __('Never', $dom);
//last cron time
$sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
ModUtil::func('IWmain', 'user', 'userSetVar', array('uid' => -100,
    'name' => 'lastCron',
    'module' => 'IWmain_cron',
    'lifetime' => 1000 * 24 * 60 * 60,
    'sv' => $sv,
    'value' => $time));


//5. Global cron response
$cronResponse .= '<h2>'.__('Cron Response', $dom).'</h2>';
$cronResponse .= '<div>' . __('Cron execution time', $dom) . ': ' . $executeTime . '</div><br><br>';
$cronResponse .= '<div>' . __('Cron results', $dom) . ': ';
if ($exit == 1) {
    $cronResponse .= '<span style="color: green;">' . __('It has executed correctly', $dom) . '</span></div>';
} elseif ($exit == 0) {
    $cronResponse .= '<span style="color: orange;">' . __('User reports running without actions configured. Success time updated.', $dom) . '</span></div>';
} elseif ($exit == -1) {
    $cronResponse .= '<span style="color: red;">' . __('It has not worked. Some error ocurred.', $dom) . '</span></div>';
} elseif ($exit == -3) {
    $cronResponse .= '<span style="color: red;">' . __('It has not worked. Waiting for minimum time between reports.', $dom) . '</span></div>';
} elseif ($exit == -2) {
    $cronResponse .= '<span">' . __('User Reports disabled', $dom) . '</span></div>';
}
$cronResponse .= '<div>' . __('Last User Reports success execution', $dom) . ': ' . $lastCronSuccessfullTime . '</div>';
//saving cronResponse
$sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
ModUtil::func('IWmain', 'user', 'userSetVar', array('uid' => -100,
    'name' => 'cronResponse',
    'module' => 'IWmain_cron',
    'lifetime' => 1000 * 24 * 60 * 60,
    'sv' => $sv,
    'value' => $cronResponse));

//6. Ending
if (isset($_REQUEST['return']) && $_REQUEST['return'] == 1) {
    return System::redirect(ModUtil::url('IWmain', 'admin', 'main2'));
} else {
    print $cronResponse;
}
cronShutdown();

function userNews() {

    $dom = ZLanguage::getModuleDomain('IWmain');
    //get the users mails
    $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
    $usersMails = ModUtil::func('IWmain', 'user', 'getAllUsersInfo', array('sv' => $sv,
                'info' => 'e'));
    $subject = ModUtil::getVar('IWmain','cronSubjectText');
    $ok = 0;
    $ko = 0;
    foreach ($usersMails as $key => $value) {
        if ($value != '') {
            //check if user is subscribed to news
            $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
            $subscribed = ModUtil::func('IWmain', 'user', 'userGetVar', array('uid' => $key,
                        'name' => 'subscribeNews',
                        'module' => 'IWmain_cron',
                        'sv' => $sv));
            if ($subscribed) {
                //get user last send mail
                $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
                $userLastMail = ModUtil::func('IWmain', 'user', 'userGetVar', array('uid' => $key,
                            'name' => 'lastUserSendMail',
                            'module' => 'IWmain_cron',
                            'sv' => $sv));
                //calc user news
                $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
                ModUtil::func('IWmain', 'user', 'news', array('uid' => $key,
                    'sv' => $sv));
                $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
                $newsValue = ModUtil::func('IWmain', 'user', 'userGetVar', array('uid' => $key,
                            'name' => 'news',
                            'module' => 'IWmain_block_news',
                            'sv' => $sv,
                            'nult' => true));
                // Now find out if there are any news in the message or if it's just HTML comments
		 $comments = array('<!---ta--->', '<!---/ta--->',
		 '<!---ag--->', '<!---/ag--->',
		 '<!---me--->', '<!---/me--->',
 		'<!---fo--->', '<!---/fo--->',
 		'<!---fu--->', '<!---/fu--->',
 		'<!---ch--->', '<!---/ch--->',
 		'<!---fr--->', '<!---/fr--->');

 		// Using a temporary value for not loosing data
 		$temp = $newsValue;
 		foreach ($comments as $comment) {
 		    $temp = str_replace($comment, '', $temp);
 		}
 		$temp = str_replace("\n", '', $temp);

		// If $temp is empty, then $newsValue doesn't contain any worth content
		if (!empty($temp)) {
                    $newsValueText = '<div>' . ModUtil::getVar('IWmain', 'cronHeaderText') . '</div>';
                    $newsValueText .= '<table width="300">' . $newsValue . '</table>';
                    $newsValueText .= '<div>' . ModUtil::getVar('IWmain', 'cronFooterText') . '</div>';

                    $sendResult = ModUtil::apiFunc('Mailer', 'user', 'sendmessage', array('toname' => $value,
                                'toaddress' => $value,
                                'subject' => $subject,
                                'body' => $newsValueText,
                                'html' => 1));
                    if ($sendResult) {
                        $ok++;
                    } else {
                        $ko++;
                    }
                    if ($ko >= 5 && $ok == 0) {
                        $returnValue = '-1';
                        $msg .= __('The 5 firsts tries of cron execution has failed. The cron execution has been aborted.', $dom);
                        $result = array('value' => $returnValue,
                            'msg' => $msg);
                        return $result;
                    }
                }
            }
        } else {
            $msg = '<div>' . __('Your site have users without email address. You should correct this.', $dom) . '</div>';
        }
    }
    if ($ko == 0 && $ok == 0) {
        $returnValue = '1';
        $msg .= __('There have not found emails to send.', $dom);
    } elseif ($ko == 0 && $ok != 0) {
        $returnValue = '1';
        $msg .= __('The information about the new things to see has been send to users via email.', $dom) . ' ' . __('The number of send emails has been of ', $dom) . ' ' . $ok . '.';
    } elseif ($ko != 0 && $ok != 0) {
        $returnValue = '0';
        $msg .= __('Not has been possible to send all the resume messages with the new things to see to users. Some of them have failed.', $dom) . ' ' . $ko . ' ' . __('of', $dom) . ' ' . $ok . '. ';
    } else {
        $returnValue = '-1';
        $msg .= __('All the tries of sending messages have failed. The number of tries has been of', $dom) . ' ' . $ko . '.';
    }
    $result = array('value' => $returnValue,
        'msg' => $msg);
    return $result;
}
function cronShutdown() {
    Zikula_View_Theme::getInstance()->clear_all_cache();
    Zikula_View_Theme::getInstance()->clear_compiled();
    Zikula_View_Theme::getInstance()->clear_cssjscombinecache();
    Zikula_View::getInstance()->clear_all_cache();
    Zikula_View::getInstance()->clear_compiled();

    System::shutdown();

}