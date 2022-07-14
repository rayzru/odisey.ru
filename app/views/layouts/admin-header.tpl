<!DOCTYPE html>
<html lang="ru">
<head>
	<title>{$site->title}</title>

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<link rel="icon" type="image/svg+xml" href="/assets/svg/logo-sign.svg?v2" >
	<link rel="icon" type="image/gif" href="/assets/favicon.gif" >
	<link rel="icon" type="image/png" href="/assets/favicon.png" >

	{foreach from=$site->styles key=k item=style}
		<link rel="stylesheet" type="text/css" href="{$style.src}">
	{/foreach}
	{foreach from=$site->scripts key=k item=script}
		{if $script.inHead}
			<script src="{$script.src}" {if $script.async}async="async"{/if} {if $script.defer}defer{/if}></script>
		{/if}
	{/foreach}
</head>
<body>