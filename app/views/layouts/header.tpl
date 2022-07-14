<!DOCTYPE html>
<html lang="ru">
<head>
	<title>{$site->title}</title>

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="title" content="{$site->title|escape}">
	<meta name="description" content="{$site->description|strip_tags:false|escape|trim}">
	<meta name="keywords" content="{foreach from=$site->keywords key=k item=kw}{if $k != 0},{/if}{$kw|strip_tags|trim}{/foreach}">

	<meta property="og:title" content="{$site->title|escape}">
	<meta property="og:type" content="article">
	<meta property="og:description" content="{$site->description|strip_tags:false|escape|trim}">
	{if !empty($site->canonical)}<meta property="og:url" content="https://odisey.ru{$site->canonical}" />{/if}
	{if !empty($site->image)}<meta property="og:image" content="{$site->image}" />{/if}

	<meta name="twitter:card" content="summary_large_image">
	<meta property="twitter:title" content="{$site->title|escape}">
	<meta property="twitter:description" content="{$site->description|strip_tags:false|escape|trim}">
	{if !empty($site->canonical)}<meta property="twitter:url" content="https://odisey.ru{$site->canonical}" />{/if}
	{if !empty($site->image)}<meta property="twitter:image" content="{$site->image}" />{/if}

	<link rel="icon" type="image/svg+xml" href="/assets/svg/logo-sign.svg?v2" >
	<link rel="icon" type="image/gif" href="/assets/favicon.gif" >
	<link rel="icon" type="image/png" href="/assets/favicon.png" >
	{if !empty($site->canonical)}<link rel="canonical" href="https://odisey.ru{$site->canonical}" />{/if}
	{foreach from=$site->styles key=k item=style}<link rel="stylesheet" type="text/css" href="{$style.src}">
	{/foreach}
	{foreach from=$site->scripts key=k item=script}
		{if $script.inHead}
			<script src="{$script.src}" {if $script.async}async="async"{/if} {if $script.defer}defer{/if}></script>
		{/if}
	{/foreach}

	{include file="partials/tracking-scripts.tpl"}


</head>
<body>
