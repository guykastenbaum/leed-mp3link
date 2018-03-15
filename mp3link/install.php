<?php
function mp3link_plugin_install(){
$line='Plugin::callHook("feed_before_event_save", array(&$event));';
$lineb='$event->save();';
$feedf="Feed.class.php";
$feed=$_SERVER["SCRIPT_FILENAME"];
$feed=preg_replace(":/plugins/.*$:","/".$feedf,$feed);

if (!file_exists($feed)) return("no $feed");
$fdc=file_get_contents($feed);
if (strpos($fdc,$line)) return("$feed already patched");

$fdc=str_replace($lineb,"$line \n $lineb",$fdc);
$res=file_put_contents($feed,$fdc);

if ($res) return("ok, $feed patched ok");
return("cannot patch $feed , 
 change write mode on the file and reinstall plugin, 
 or edit it manually ,
 before line ' $lineb ' insert the line : 
 $line
");
}
print mp3link_plugin_install();


