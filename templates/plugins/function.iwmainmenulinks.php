<?php
function smarty_function_iwmainmenulinks()
{
	$dom = ZLanguage::getModuleDomain('IWmain');
	// set some defaults
	if (!isset($params['start'])) {
		$params['start'] = '[';
	}
	if (!isset($params['end'])) {
		$params['end'] = ']';
	}
	if (!isset($params['seperator'])) {
		$params['seperator'] = '|';
	}
	if (!isset($params['class'])) {
		$params['class'] = 'pn-menuitem-title';
	}

	$mainmenulinks = "<span class=\"" . $params['class'] . "\">" . $params['start'] . " ";

	if (SecurityUtil::checkPermission('IWmain::', "::", ACCESS_ADMIN)) {
		$mainmenulinks .= "<a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('IWmain', 'admin', 'main')) . "\">" . __('Programmed sequences information',$dom) . "</a> " . $params['seperator'];
		$mainmenulinks .= " <a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('IWmain', 'admin', 'conf')) . "\">" . __('Configure parameters',$dom) . "</a> " . $params['seperator'];
		$mainmenulinks .= " <a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('IWmain', 'admin', 'changeAvatarView')) . "\">" . __('Avatar change request',$dom) . "</a> " . $params['seperator'];
		$mainmenulinks .= " <a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('IWmain', 'admin', 'filesList')) . "\">" . __('Manage files',$dom) . "</a> ";
	}

	$mainmenulinks .= $params['end'] . "</span>\n";

	return $mainmenulinks;
}
