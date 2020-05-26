#!/usr/local/bin/php
<?php

declare(strict_types=1);

array_shift($argv);
$callback = array_shift($argv);
if (empty($callback) || empty($argv))
	exit("Usage: watch <callback> <dir1> <dir2> ... <dirN>\n");

$inotify = inotify_init();
stream_set_blocking($inotify, true);

$wd = [];

function watchdir(string $dir)
{
	global $inotify, $wd;
	echo date("[d-M-Y H:i:s]", time())," NOTICE: watching $dir\n";
	$wd[inotify_add_watch($inotify, $dir, IN_CREATE|IN_DELETE|IN_ISDIR|IN_MOVE|IN_MODIFY)] = $dir;
	$p = opendir($dir);
	if (!$p) return;
	while (!empty($entry = readdir($p)))
	{
		if ($entry[0] == ".") continue;
		$entry = "$dir/$entry";
		if (is_dir($entry) && !is_link($entry))
			watchdir($entry);
	}
	closedir($p);
}

foreach ($argv as $dir) watchdir($dir);

$lasttime = time();
echo date("[d-M-Y H:i:s]", $lasttime)," NOTICE: updating sturdy...\n";
passthru($callback);
for (;;)
{
	// TODO: process mutations
	$events = inotify_read($inotify);
	foreach ($events?:[] as $event)
	{
		$target = "{$wd[$event['wd']]}/{$event['name']}";
		if (is_dir($target) && !is_link($target))
			watchdir($target);
	}
	$currenttime = time();
	if ($lasttime != $currenttime) {
		echo date("[d-M-Y H:i:s]", $currenttime)," NOTICE: updating sturdy...\n";
	}
	$lasttime = $currenttime;
	passthru($callback);
}
