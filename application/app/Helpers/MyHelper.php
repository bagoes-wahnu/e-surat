<?php

namespace App\Helpers;

class MyHelper
{
	public static function myBasePath($replace = '', $to = '')
	{
		$root = str_replace('application/public', '', public_path());

		if (!empty($replace)) {
			$root = str_replace($replace, $to, $root);
		}

		return $root;
	}

	public static function myAssetPath($replace = '', $to = '')
	{
		$root = public_path();
		$root = str_replace('application\public', 'assets\extends\font\ARI.ttf', $root);
		$root = str_replace('application/public', 'assets/extends/font/ARI.ttf', $root);

		if (!empty($replace)) {
			$root = str_replace($replace, $to, $root);
		}

		return $root;
	}

	public static function myStorage($url_path = '')
	{
		$dir = 'aefwg4/' . $url_path;

		return $dir;
	}

	public static function rrmdir($dir)
	{
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (is_dir($dir . DIRECTORY_SEPARATOR . $object) && !is_link($dir . "/" . $object))
						MyHelper::rrmdir($dir . DIRECTORY_SEPARATOR . $object);
					else
						unlink($dir . DIRECTORY_SEPARATOR . $object);
				}
			}
			rmdir($dir);
		}
	}

	public static function encText($value = '', $md5 = false)
	{
		$result = $value . '-energeek';

		return (($md5 == false) ? $result : md5($result));
	}
}
