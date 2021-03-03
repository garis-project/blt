<?php
defined('BASEPATH') or exit('No direct script access allowed');


$autoload['packages'] = array();
$autoload['libraries'] = array('form_validation', 'email', 'session', 'database');
$autoload['drivers'] = array();
$autoload['helper'] = array('string', 'url', 'blt');
$autoload['config'] = array();
$autoload['language'] = array();
$autoload['model'] = array(['M_Admin' => 'admin', 'M_Blt' => 'blt']);
