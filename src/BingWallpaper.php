<?php

namespace BingWallpaperCrawler;

use EasyTask\Helper;
use EasyTask\Log;

class BingWallpaper
{
    /**
     * 壁纸数据地址.
     *
     * @var string
     */
    protected static $url = 'https://cn.bing.com/HPImageArchive.aspx?format=hp&idx=0&n=1&nc=%s';

    /**
     * 获取请求地址.
     *
     * @return string
     */
    public static function getRequestUrl()
    {
        return \sprintf(self::$url, \time() * 1000);
    }

    /**
     * 下载壁纸.
     *
     * @return bool
     */
    public static function download()
    {
        $response = \file_get_contents(self::getRequestUrl());
        $response = \strip_tags($response);

        $content = \json_decode($response, true);

        if (empty($content) || \count($content['images']) == 0) {
            Log::writeInfo('未获取到壁纸数据');
            return false;
        }

        return self::save($content['images'][0]['hsh']);
    }

    /**
     * 保存壁纸.
     *
     * @param string $id
     * @return bool
     */
    protected static function save($id)
    {
        $saveDir = Helper::getRunTimePath().'Wallpapers';

        if (!\is_dir($saveDir)) {
            \mkdir($saveDir);
        }

        $url = sprintf('https://cn.bing.com/hpwp/%s', $id);
        $filename = \sprintf('BingWallpaper-%s.jpg', date('Y-m-d'));

        $path = sprintf('%s/%s', $saveDir, $filename);
        if (\file_exists($path)) {
            return true;
        }

        $wallpaper = \fopen($url, 'rb');
        if (!$wallpaper) {
            return false;
        }

        $file = \fopen($path, 'wb');
        if (!$file) {
            \fclose($wallpaper);
            return false;
        }

        try {
            while (!\feof($wallpaper)) {
                \fwrite($file, \fread($wallpaper, 1024 * 8), 1024 * 8);
            }
        } catch (\Exception $e) {
            Log::writeInfo('保存壁纸异常: '.$e->getMessage());
            return false;
        } finally {
            \fclose($wallpaper);
            \fclose($file);
        }

        Log::writeInfo('下载成功: '.$url);

        return true;
    }
}
