<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Config for the CodeIgniter Redis library
 *
 * @see ../libraries/Redis.php
 */

// 主redis
$config['master'][0]['host'] = '127.0.0.1';		// IP address or host
$config['master'][0]['port'] = '6379';			// Default Redis port is 6379
$config['master'][0]['password'] = '';			// Can be left empty when the server does not require AUTH

$config['master'][1]['host'] = '127.0.0.1';		// IP address or host
$config['master'][1]['port'] = '6379';			// Default Redis port is 6379 
$config['master'][1]['password'] = '';			// Can be left empty when the server does not require AUTH

// 从redis
$config['slave'][0]['host'] = '192.168.3.64';
$config['slave'][0]['port'] = '6379';
$config['slave'][0]['password'] = '';