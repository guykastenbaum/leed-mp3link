<?php
/*
@name mp3link
@author GuyK <g01@kastenbaum.net>
@link http://blog.kastenbaum.net
@licence CC
@version 1.0
@description ajoute un lien pour télécharger les mp3
*/

// Plugin::addCss("/css/style.css");
// Plugin::addJs("/js/main.js");

function fmp3_str2utf8($v_str){ return ($v_str!=utf8_decode(utf8_encode($v_str)))? utf8_encode($v_str):$v_str; }
function fmp3_str2iso($v_str) { return ($v_str!=utf8_decode(utf8_encode($v_str)))? $v_str:utf8_decode($v_str); }
function fmp3_desaccentuation($v_str)
{
        $str=fmp3_str2utf8($v_str);
        $str=html_entity_decode($str,ENT_NOQUOTES,"UTF-8");
        $str=htmlentities($str,ENT_NOQUOTES,"UTF-8");
        $str=preg_replace("/[\xa0-\xbf\xd7\xf7]/"," ",$str);
        $str=str_replace("&nbsp;"," ",$str);
        $str=str_replace("&lt;","<",$str);
        $str=str_replace("&gt;",">",$str);
        $str=str_replace("&amp;","&",$str);
        $str=str_replace("&quot;",'"',$str);
        $str=str_replace("&apos;","'",$str);
        $str=str_replace("&hellip;","...",$str);
        $str=preg_replace("/\&(.)[^\;]*\;/","$1",$str);
        //$str2=iconv("ISO-8859-1", "ASCII//TRANSLIT", fmp3_str2iso($v_str));
        return($str);
}

function mp3_audio_addlink($cnt,$title)
{
	if (!preg_match("/<audio/",$cnt)) return($cnt);
	$lnk=preg_replace(':^.*<audio src="([^"]*)".*$:s','$1',$cnt);
	if ($cnt==$lnk) return($cnt);
	// set downloadable nice clean name with extension
	$extclean=preg_replace("/^.*\./",".",$lnk);
	$extclean=preg_replace("/\?.*$/","",$extclean);
	$downloadable=$title;
	if (!$downloadable) $downloadable=preg_replace(':^.*/([a-zA-Z0-9\._\-]*)\.[\d\w]*$:','$1',$lnk);
	$downloadable=fmp3_desaccentuation($downloadable);
	$downloadable=preg_replace("/[^a-zA-Z0-9]/","_",$downloadable);
	$downloadable=$downloadable.$extclean;
	// set html link
	$htmlnk= ' <a href="'.$lnk.'" download="'.$downloadable.'">Download</a>';
	// insert after audio (tempted to remove audio ..)
	$cnt2=preg_replace(':</audio>:','</audio>'.$htmlnk,$cnt);
	return($cnt2);
}
function mp3_plugin_addlink(&$event)
{
	$title=$event->getTitle();
	// patch content
	$cnt=$event->getContent();
	$cnt2=mp3_audio_addlink($cnt,$title);
	if ($cnt2 and $cnt!=$cnt2) $event->setContent($cnt2);
	// patch description
	$cnt=$event->getDescription();
	$cnt2=mp3_audio_addlink($cnt,$title);
	if ($cnt2 and $cnt!=$cnt2) $event->setDescription($cnt2);
	return;
}

Plugin::addHook('feed_before_event_save','mp3_plugin_addlink');


?>
