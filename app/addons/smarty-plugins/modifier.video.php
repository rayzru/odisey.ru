<?php
/**
 * Smarty video modifier plugin
 *
 * Type:     modifier<br>
 * Name:     video<br>
 * @param string
 * @return string
 */


function smarty_modifier_video($str)
{
	$youtubeCode = getVideoProvider($str);
	if (strlen($youtubeCode)) {
		$iframe = "<iframe width='560' height='315' src='https://www.youtube.com/embed/{$youtubeCode}' " .
			"frameborder='0' allow='accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>";
		return $iframe;
	}

}

function getVideoProvider($str)
{
	$regExp = '/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/';
	$matches = [];
	$m = preg_match($regExp, $str, $matches);
	return ($matches && strlen($matches[7]) == 11) ? $matches[7] : false;
}

function getYoutubeCode($str)
{

}