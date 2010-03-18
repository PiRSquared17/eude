<?php
/**
 * @author Alex10336
 * Dernière modification: $Id$
 * @license GNU Public License 3.0 ( http://www.gnu.org/licenses/gpl-3.0.txt )
 * @license Creative Commons 3.0 BY-SA ( http://creativecommons.org/licenses/by-sa/3.0/deed.fr )
 *
**/
if (!SCRIPT_IN) die('Need by included');

class tpl_header {
    /**
     *
     * @return string html header
     */
	static public function Get_Header() {
		$obj = DataEngine::tpl('');
                $version = DataEngine::Get_Version();

		if ($obj->page_title=="")
			$title = "EU2: Data Engine v{$obj->version}";
		else
			$title = $obj->page_title;

		if ($obj->css_file!="") {
			$css = <<<EOF
		<link rel="stylesheet" type="text/css" href="{$obj->css_file}?{$obj->version}" media="screen" />
EOF;
		} else {
			$css = '';
		}

if (DE_DEMO)
$stats = <<<st
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-12854322-1");
pageTracker._trackPageview();
} catch(err) {}</script>
st;
$doctype= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
$doctype= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
$doctype= '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
$doctype='';
		return<<<EOF
{$doctype}
<html xmlns="http://www.w3.org/1999/html" lang="fr" xml:lang="fr">
<head>
<title>{$title}</title>
{$css}
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
{$stats}
<script type="text/javascript" src="%INCLUDE_URL%prototype.js?1.6.1"></script>
<script type="text/javascript" src="%INCLUDE_URL%Script.js?{$version}"></script>
<div id="curseur" class="infobulle" style="z-index:7; position:absolute; visibility:hidden; border: 1px solid White; padding: 10px; font-family: Verdana, Arial, Times; font-size: 10px; background-color: #C0C0C0;white-space:nowrap;"></div>
%NEW_MESSAGE_ENTRY%
EOF;

	}
        static function messager(&$data, &$msg) {
            $html = <<<h
<div id="newmessage" style="z-index:99; position:absolute; top:0px; right:0px; bottom:0px; left:0px; background-color:#330033;"
 Onclick="$('newmessage').style.visibility='hidden';">
 <div class="color_header text_center" style="position:absolute; top:200px; right:100px; left:100px;">
    {$msg}
 </div>
</div>
h;
            if ($msg)
                $data = str_replace('%NEW_MESSAGE_ENTRY%',$html, $data);
            else
                $data = str_replace('%NEW_MESSAGE_ENTRY%','', $data);
            $msg='';
        }
}
