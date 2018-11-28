<?php

require_once(APPPATH.'third_party/misc/JavaScriptPacker.class.php');

class JSHandler {
	
	private $valid;
	
	function __construct($token) {
		
		$this->valid = true;
		
	}
	
	function isValid() {
		
		return $this->valid;
		
	}
	
	static function getSource($name) {
		
		require_once(APPPATH.'javascript/_builds.data.php');

    $errorFile = '_errorcodes.js';

		if (!isset($_js_builds[$name]))
			return false;
			
		header("Content-Type: text/javascript");
		$dir = APPPATH."javascript/source";
		$files = array_merge($_js_core, $_js_builds[$name]);
		foreach ($files as $file) {
			readfile($dir.'/'.$file);
			echo "\n\n";
		}
    readfile($dir.'/'.$errorFile);
	
	}
	
	function createBuilds() {
		
		require_once(APPPATH.'javascript/_builds.data.php');

		$taskKey = self::encodeTasks();
		
		foreach ($_js_builds as $title => $fileList) {
				
			$file0 = APPPATH.'javascript/build/nc-'.$title.'.js';
			$file1 = APPPATH.'javascript/build/rm-'.$title.'.js';
			$file2 = APPPATH.'javascript/build/ss-'.$title.'.js';
				
			$fileList = array_merge($_js_core, $fileList);
				
			$contents = array();
			$dir = APPPATH."javascript/source";
			foreach ($fileList as $file) {
				$str = file_get_contents($dir."/".$file);
				$contents[] = $str;
			}
			$uncompressed = $content = implode("\n\n", $contents);
			file_put_contents($file0, $uncompressed);
			
		  $info = self::rminify($uncompressed);
			$uncompressed = $info['string'];
			foreach ($taskKey['keys'] as $key => $task) {
				$uncompressed = preg_replace('/ajax\("'.$task.'"/', 'ajax("'.$key.'"', $uncompressed);
				$uncompressed = preg_replace("/ajax\(\'".$task."\'/", "ajax('".$key."'", $uncompressed);
				$uncompressed = preg_replace('/ajaxForm\("'.$task.'"/', 'ajaxForm("'.$key.'"', $uncompressed);
				$uncompressed = preg_replace("/ajaxForm\(\'".$task."\'/", "ajaxForm('".$key."'", $uncompressed);
				$uncompressed = preg_replace("/task\(\'".$task."\'\)/", "'".$key."'", $uncompressed);
				$uncompressed = preg_replace('/task\("'.$task.'"\)/', '"'.$key.'"', $uncompressed);
			}
			$uncompressed = str_replace("data.task = ", 'data.'.$taskKey['taskword'].' = ', $uncompressed);
			$uncompressed = preg_replace('/ttask\(([a-z\.]*)task([a-z\.]*)\)/', "$1".$taskKey['taskword']."$2", $uncompressed);
			
			file_put_contents($file1, $uncompressed);
			exec("java -jar ".APPPATH."third_party/shrinksafe/shrinksafe.jar ".es($file1)." > ".es($file2));
			$content = file_get_contents($file2);
			$pack = new JavaScriptPacker($content);
			$compressed = $pack->pack();
      $errorBlock = file_get_contents(APPPATH.'javascript/source/_errorcodes.js');
			$final = $info['arrayDump']."\n".$compressed."\n".$errorBlock;
			file_put_contents(WEBROOT."js/build-".$title.".js", $final);
		}
		
	}
	
	static function rminify($string) {
			
		//Replace most common tokens with short var names
		$common = array(
			"_class"	=> "c",
			"obj"		=> "l",
			"postObj"	=> "o"
		);
		$string = str_replace(array_keys($common), array_values($common), $string);
		
		//Minify object method names
		$matches = array();
		preg_match_all('/\s([a-zA-Z_]+)\:\sfunction\(/', $string, $matches);
		$functions = $matches[1];
		ksort($functions);
		$reserved = array("init", "request", "setStyle", "setStyles", "setOptions", "onComplete", 
      "onChange", "dispose", "hide", "show", "toggle", "get", "set", "condition", 
      "mouseleave", "mousemove", "click", "ajax", "ajaxForm");
		$i = 0;
		foreach ($functions as $fn) {
			if (in_array($fn, $reserved))
				continue;
			$string = preg_replace('/([\s\t\.]+)'.$fn.'(\.|\(|\:|;|(\)(?!({|\s{))))/', '$1_'.base62($i++).'$2', $string);
		}
		
		//Index all IDs and classes called using the $() and $$() functions and obfuscate these function calls
		$matches = array();
		preg_match_all('/[^\$][\$]{1,2}\(("|\')\#([a-zA-Z0-9_-]+)\1\)/', $string, $matches);
		$strings = $matches[2];
		$strings = array_values(array_unique($strings));
		$i = 0;
		foreach ($strings as $i => $aString) {
			$string = preg_replace('/\$defined\(\$\(("|\')'.$aString.'\1\)\)(?![\s]*:)/', 'd('.$i.')', $string);
			$string = preg_replace('/(?<!\$)\$\(("|\')\#'.$aString.'\1\)(?![\s]*:)/', 'v('.$i.')', $string);
			$string = preg_replace('/("|\')\.'.$aString.'\1(?![\s]*:)/', 'p('.$i.')', $string);
			$string = preg_replace('/(?<!\$)[\$]{2}\(p\(([0-9]+)\)\)/', 'q($1)', $string);
		}
		$arrayDump = 'var sv_="'.implode('|', $strings).'";var s_=sv_.split("|");';
		
    /**
		//Follow a similar procedure for hasClass, addClass, and removeClass function calls
		$matches = array();
		preg_match_all('/\.(hasClass|addClass|removeClass)\(("|\')([a-zA-Z0-9]+)\2\)/', $string, $matches);
		$strings = $matches[3];
		$strings = array_values(array_unique($strings));
		$i = 0;
		foreach ($strings as $i => $aString) {
			$string = preg_replace('/\.hasClass\(("|\')'.$aString.'\1\)/', '.c1('.$i.')', $string);
			$string = preg_replace('/\.addClass\(("|\')'.$aString.'\1\)/', '.c2('.$i.')', $string);
			$string = preg_replace('/\.removeClass\(("|\')'.$aString.'\1\)/', '.c3('.$i.')', $string);
		}
		$string = preg_replace('/e\(([0-9]+)\)\.c([0-9]{1})\(([0-9]+)\)/', 'c$2_($1,$3)', $string);
		$arrayDump .= 'var cv_="'.implode('|', $strings).'";var c_=cv_.split("|");';
    **/
			
		return array("arrayDump" => $arrayDump, "string" => $string);
		
	}
	
	static function encodeTasks() {
				
		//Get contents of ALL source files
		$contents = array();
		$dir = APPPATH.'javascript/source';
		if ($handle = opendir($dir)) {
			while (false !== ($file = readdir($handle))) {
				if (!preg_match('/\.js/', $file))
					continue;
				$filename = $dir.'/'.$file;
				if (!file_exists($filename))
					continue;
				$contents[] = file_get_contents($filename);
			}
			closedir($handle);
		}
		$text = implode("\n\n", $contents);
		
		$single = $double = $singleForm = $doubleForm = array();
		preg_match_all('/ajax\("([a-zA-Z]+)"/', $text, $double);
		preg_match_all("/ajax\(\'([a-zA-Z]+)\'/", $text, $single);
		preg_match_all('/ajaxForm\("([a-zA-Z]+)"/', $text, $doubleForm);
		preg_match_all("/ajaxForm\(\'([a-zA-Z]+)\'/", $text, $singleForm);
		
		$tasks = array_merge($double[1], $single[1], $doubleForm[1], $singleForm[1]);
		
		$key = array();
		$i = 0;
		foreach ($tasks as $task) {
			if (in_array($task, array_values($key)))
				continue;
      $seed = 'abcdefghijklmnopqrstuvwxyz';
      $rand = '';
      for ($j = 0; $j < 4; $j++)
        $rand .= $seed[rand(0,26)];
			$coded = $rand.base62($i++);
			$key[$coded] = $task;
		}
		
		$keyData = array();
		foreach ($key as $code => $task)
			$keyData[] = qq($code)." => ".qq($task);
			
    $seed = 'abcdefghijklmnopqrstuvwxyz';
    $taskword = '';
    for ($i = 0; $i < 5; $i++)
      $taskword .= $seed[rand(0,25)];
    $taskword = $taskword.'_';
		$bluePrint = file_get_contents(APPPATH."javascript/_key.txt");
		$keyText = str_replace(array("[data]", "[hash]"), array(implode(",\n\t", $keyData), $taskword), $bluePrint);
		file_put_contents(APPPATH."javascript/_key.inc.php", $keyText);
		
		return array("keys" => $key, "taskword" => $taskword);
		
	}
	
}

function JSCompress($string) {
	
	$pack = new JavaScriptPacker($string);
	return $pack->pack();
	
}

?>
