<?php
/**
 * MSergeev
 * @package core
 * @author Mikhail Sergeev
 * @copyright 2016 Mikhail Sergeev
 */

require_once ("tools/tools.msdebug.php");
require_once ("tools/tools.html.php");

function __include_once ($path,$echo=false)
{
	if ($echo)
	{
		echo $path."<br>";
	}
	include_once($path);
}

function maskValue ($value=null)
{
	static $triple_char = array(
		"!><" => "NB",  //not between
	);
	static $double_char = array(
		"!=" => "NI",   //not Identical
		"!%" => "NS",   //not substring
		"><" => "B",    //between
		">=" => "GE",   //greater or equal
		"<=" => "LE",   //less or equal
	);
	static $single_char = array(
		"=" => "I",     //Identical
		"%" => "S",     //substring
		"?" => "?",     //logical
		">" => "G",     //greater
		"<" => "L",     //less
		"!" => "N",     //not field LIKE val
	);

	$op = substr($value,0,3);
	if ($op && isset($triple_char[$op]))
		return array("value"=>substr($value,3),"mask"=>$op,"operation"=>$triple_char[$op]);
	$op = substr($value,0,2);
	if ($op && isset($double_char[$op]))
		return array("value"=>substr($value,2),"mask"=>$op,"operation"=>$double_char[$op]);
	$op = substr($value,0,1);
	if ($op && isset($single_char[$op]))
		return array("value"=>substr($value,1),"mask"=>$op,"operation"=>$single_char[$op]);

	return false;
}

