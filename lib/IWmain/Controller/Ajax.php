<?php
class IWmain_Controller_Ajax extends Zikula_Controller_AbstractAjax
{
    /**
     * Delete all the information about an activity
     * @author:     Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	args   Id of the activity
     * @return:	the activity id removed from database
     */
    public function change($args) {
        if (!SecurityUtil::checkPermission('IWmain::', '::', ACCESS_ADMIN)) {
            AjaxUtil::error(DataUtil::formatForDisplayHTML($this->__('Sorry! No authorization to access this module.')));
        }

        $chid = FormUtil::getPassedValue('chid', -1, 'GET');
        if ($chid == -1) AjaxUtil::error('no change user id');
        $toDo = FormUtil::getPassedValue('toDo', -1, 'GET');
        if ($toDo == -1) AjaxUtil::error('no action defined');

        $error = '';

        if ($toDo == 'del') {
            //delete the file
            if (!ModUtil::func('IWmain', 'user', 'deleteAvatar',
                                array('avatarName' => substr($chid, 0, -4),
                                      'extensions' => array('jpg',
                                                            'png',
                                                            'gif')))) {
                $error = $this->__('Error deleting avatar');
            }

            //delete the small picture
            ModUtil::func('IWmain', 'user', 'deleteAvatar',
                           array('avatarName' => substr($chid, 0, -4) . '_s',
                                 'extensions' => array('jpg',
                                                       'png',
                                                       'gif')));
        } else {
            $file_extension = strtolower(substr(strrchr($chid, "."), 1));
            $formats = '$jpg$$png$$gif$';
            $formats = str_replace('$' . $file_extension . '$', '', $formats);
            $len = strlen($formats) - 2;
            $formatsArray = explode('$$', substr($formats, 1, $len));

            //change file name
            $changed = rename(ModUtil::getVar('IWmain', 'documentRoot') . '/' . ModUtil::getVar('IWmain', 'usersPictureFolder') . '/' . $chid, ModUtil::getVar('IWmain', 'documentRoot') . '/' . ModUtil::getVar('IWmain', 'usersPictureFolder') . '/' . substr($chid, 1, strlen($chid)));
            if ($changed) {
                ModUtil::func('IWmain', 'user', 'deleteAvatar',
                               array('avatarName' => substr($chid, 1, -4),
                                     'extensions' => $formatsArray));
            } else {
                $error = $this->__('Error changing avatar');
            }

            //Change small pictures
            $chid_s = substr($chid, 0, -4) . '_s.' . $file_extension;
            rename(ModUtil::getVar('IWmain', 'documentRoot') . '/' . ModUtil::getVar('IWmain', 'usersPictureFolder') . '/' . $chid_s, ModUtil::getVar('IWmain', 'documentRoot') . '/' . ModUtil::getVar('IWmain', 'usersPictureFolder') . '/' . substr($chid_s, 1, strlen($chid_s)));
            ModUtil::func('IWmain', 'user', 'deleteAvatar',
                           array('avatarName' => substr($chid_s, 1, -4),
                                 'extensions' => $formatsArray));
        }

        $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
        ModUtil::func('IWmain', 'user', 'userSetVar',
                       array('module' => 'IWmain_block_news',
                             'name' => 'have_news',
                             'value' => 'ch',
                             'sv' => $sv));


        AjaxUtil::output(array('chid' => $chid,
                               'error' => $error));
    }

    public function reloadNewsBlock() {
        // Security check
        if (!SecurityUtil::checkPermission('IWmain:newsBlock:', "::", ACCESS_READ) || !UserUtil::isLoggedIn()) {
            AjaxUtil::error(DataUtil::formatForDisplayHTML($this->__('Sorry! No authorization to access this module.')));
        }

        $uid = UserUtil::getVar('uid');

        //get the headlines saved in the user vars. It is renovate every 10 minutes
        $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
        $exists = ModUtil::apiFunc('IWmain', 'user', 'userVarExists',
                                    array('name' => 'news',
                                          'module' => 'IWmain_block_news',
                                          'uid' => $uid,
                                          'sv' => $sv));

        if (!$exists) ModUtil::func('IWmain', 'user', 'news');

        $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
        $have_news = ModUtil::func('IWmain', 'user', 'userGetVar',
                                    array('uid' => $uid,
                                          'name' => 'have_news',
                                          'module' => 'IWmain_block_news',
                                          'sv' => $sv));

        if ($have_news != '0') {
            ModUtil::func('IWmain', 'user', 'news',
                           array('where' => $have_news));

            $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
            ModUtil::func('IWmain', 'user', 'userSetVar',
                           array('uid' => $uid,
                                 'name' => 'have_news',
                                 'module' => 'IWmain_block_news',
                                 'sv' => $sv,
                                 'value' => '0'));
        }

        $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
        $news = ModUtil::func('IWmain', 'user', 'userGetVar',
                               array('uid' => $uid,
                                     'name' => 'news',
                                     'module' => 'IWmain_block_news',
                                     'sv' => $sv,
                                     'nult' => true));

        $view = Zikula_View::getInstance('IWmain', false);

        $view->assign('news', $news);
        $view->assign('ajax', 1);
        $content = $view->fetch('IWmain_block_iwnews.htm');

        AjaxUtil::output(array('content' => $content));
    }

    public function reloadFlaggedBlock() {
        // Security check
        if (!SecurityUtil::checkPermission('IWmain:flaggedBlock:', "::", ACCESS_READ) || !UserUtil::isLoggedIn()) {
            AjaxUtil::error(DataUtil::formatForDisplayHTML($this->__('Sorry! No authorization to access this module.')));
        }

        //get the headlines saved in the user vars. It is renovate every 10 minutes

        $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
        $exists = ModUtil::apiFunc('IWmain', 'user', 'userVarExists',
                                    array('name' => 'flagged',
                                          'module' => 'IWmain_block_flagged',
                                          'uid' => UserUtil::getVar('uid'),
                                          'sv' => $sv));
        $chars = 15;
        if (!$exists) {
            ModUtil::func('IWmain', 'user', 'flagged',
                           array('where' => '',
                                 'chars' => $chars));
        }
        $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
        $have_flags = ModUtil::func('IWmain', 'user', 'userGetVar',
                                     array('uid' => UserUtil::getVar('uid'),
                                           'name' => 'have_flags',
                                           'module' => 'IWmain_block_flagged',
                                           'sv' => $sv));
        if ($have_flags != '0') {
            ModUtil::func('IWmain', 'user', 'flagged',
                           array('where' => $have_flags,
                                 'chars' => $chars));
            //Posa la variable d'usuari have_news en blanc per no haver-la de tornar a llegir a la propera reiteraci�
            $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
            ModUtil::func('IWmain', 'user', 'userSetVar',
                           array('uid' => UserUtil::getVar('uid'),
                                 'name' => 'have_flags',
                                 'module' => 'IWmain_block_flagged',
                                 'sv' => $sv,
                                 'value' => '0'));
        }

        $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
        $flags = ModUtil::func('IWmain', 'user', 'userGetVar',
                                array('uid' => UserUtil::getVar('uid'),
                                      'name' => 'flagged',
                                      'module' => 'IWmain_block_flagged',
                                      'sv' => $sv,
                                      'nult' => true));

        $view = Zikula_View::getInstance('IWmain', false);

        $view->assign('flags', $flags);
        $content = $view->fetch('IWmain_block_iwflagged.htm');

        AjaxUtil::output(array('content' => $content));
    }
}