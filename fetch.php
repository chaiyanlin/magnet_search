<?php
require('./Snoopy.php');
require('./simple_html_dom.php');

//###基础函数
function get_title($item) {
    $title = decode_uri_component($item->find('div[class=item-title]', 0)->find('a', 0)->innertext());
    $title = preg_replace('/[<b>|<\/b>]/i', '', $title);
    return $title;
}

function get_magnent_url($item) {
    $magnet = $item->find('div[class=item-detail]', 0)->find('span', 0)->innertext();
    $magnent_decoded = decode_uri_component($magnet);
    preg_match('/href=\'(.+?)\'/i', $magnent_decoded, $matches, PREG_OFFSET_CAPTURE);
    $magnent_url = $matches[1][0];
    return $magnent_url;
}

function get_size($item) {
	$size_str = $item->find('div[class=item-detail]', 0)->find('span', 2)->innertext();
	return preg_replace('/[<b>|<\/b>]/i', '', $size_str);
}

function decode_uri_component($str) {
    preg_match('/decodeURIComponent\((.+?)\)/i', $str, $matches, PREG_OFFSET_CAPTURE);
    $str_decoded = preg_replace('/\+/', '', $matches[1][0]);
    $str_decoded = preg_replace('/"/', '', $str_decoded);
    $str_decoded = iconv('UTF-8', 'UTF-8', urldecode($str_decoded));
    return $str_decoded;
}

function get_parameter($argv) {
	if( count($argv) <= 1 ) {
		trace('请输入关键字，多个使用空格隔开');
		exit;
	}
	array_shift($argv);
	trace('输入关键字：');
	trace($argv);
	return $argv;
}

function trace( $input ) {
	if ( is_string($input) ) {
		$str = date("Y-m-d H:i:s") . ": $input".PHP_EOL;
	}
	else {
		$str = date("Y-m-d H:i:s") . ':' . PHP_EOL;
		$str .= print_r($input, true);
	}
	echo $str;
}
//###


//执行开始
$codes = get_parameter($argv);
$url = 'http://www.btlibrary.net';
foreach ( $codes as $code ) {
	trace('正在搜索关键字：' . $code);
	$data = array('s'=> $code);

	$snoopy = new Snoopy();
	$snoopy->submit($url, $data);

	$result = $snoopy->results;

	$html = str_get_html($result);

	$list = array();
	foreach ($html->find('div[class=item]') as $item) {
	    $title   = get_title($item);
	    $size    = get_size($item);
	    $magnent = get_magnent_url($item);
	    $list[] = compact('title', 'size', 'magnent');
	}

	print_r($list);
}
