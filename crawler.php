<?php

require_once './vendor/autoload.php';

use EasyTask\Task;

//初始化
$task = new Task();

// 设置常驻内存
$task->setDaemon(true);

// 设置项目名称
$task->setPrefix('Crawler');

// 设置记录运行时目录(日志或缓存目录)
$task->setRunTimePath(__DIR__.'/Runtime/');

// 添加任务定时执行类的方法
$task->addClass(\BingWallpaperCrawler\BingWallpaper::class, 'download', 'crawler', '@daily');

$method = $argc > 1 ? $argv[1] : '';

if (!in_array($method, ['start', 'status', 'stop'])) {
    exit('使用方法: php crawler.php start|status|stop'.PHP_EOL);
}

$task->{$method}();
