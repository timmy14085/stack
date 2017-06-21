<?//noshort

/* This is a little script built for php on linux to short your PHP Scripts,
 * it is not, it will never and it is not trying to be perfect,
 * but it might still help you with your code golfes!
 *******************************************************/

if(empty($argv[1]))
{
	echo "Syntax: php shorten.php <file>\n";
	exit;
}

$file = str_replace(".php", "", $argv[1]);
$newf = $file.".short.php";
$file .= ".php";
$cont = file_get_contents($file);
$old = $cont;

if(substr($cont, 0, 15) == "<?php //noshort")
{
	echo "Can't short this file.\n";
	exit;
}


function r($a, $b, $nowhile = false)
{
	global $cont;
	do
	{
		$cont = str_replace($a, $b, $cont);
	} while(strpos($cont, $a) > -1 && !$nowhile);
}

function p($a, $b)
{
	global $cont;
	$cont = preg_replace($a, $b, $cont);
}

function e($msg)
{
	echo "Script halted; fix, remove or comment out the following error:\n{$msg}\n";
	exit;
}

function cmp($a_, $b_)
{
	$a = strlen($a_);
	$b = strlen($b_);
	if($a == $b)
	{
		return 0;
	}
	return ($a > $b) ? -1 : 1;
}

p("/((\/\/.*)|(#.*))/", "");
p("/(\/\*.*\*\/)/s", "");

r("\n", "");

r("\r", "");
r("\t", "");
r("<?php", "<?");
r("<? ", "<?");
if(substr($cont, -2) == "?>")
{
	$cont = substr($cont, 0, -2);
}
r("as ", "as");
$operators = array("+", "-", "*", "/", "%", "&&", "||", "&", "?", ":", ".", ",", ";", "=", "==", "===", "!", "!=", "!==", "{", "[", "(", "<", ">", ")", "]", "}");
foreach($operators as $op)
{
	r(" ".$op, $op);
	r($op." ", $op);
	r(" ".$op." ", $op);
}
r("array()", "[]");
r("print", "echo");
r("true", "1");
r("false", "0");
r("echo ", "echo", true);
r("echo'", "echo_'", true);
r("echo\"", "echo_\"", true);
r("echo$", "echo_$", true);
r("echo(", "echo_(", true); /* )) */
r("echo", "echo ", true);
r("echo _", "echo", true);

if(strpos($cont, ">=") > -1 || strpos($cont, "<=") > -1)
{
	//e("There is something like '\$a >= 1' that should be like '\$a > 0'");
	preg_match_all("/((>=|<=)[0-9]+)/", $cont, $matches, PREG_PATTERN_ORDER);
	$script_vars = $matches[0];
	usort($script_vars, "cmp");
	foreach($script_vars as $match)
	{
		// $match looks unused
		$up = (substr($match, 0, 1) == ">");
		$val = intval(substr($match, 2));
		if($up)
		{
			$val--;
		} else
		{
			$val++;
		}
		r($match, substr($match, 0, 1).$val);
	}
}

preg_match_all("/(function [a-zA-Z_]+\\()/", $cont, $matches, PREG_PATTERN_ORDER); /* ) */
$script_vars = $matches[0];
$other_vars = array_merge(range("a", "z"), range("A", "Z"), array("_"));
$var_cache = array();
usort($script_vars, "cmp");
$p = 0;
foreach($script_vars as $match)
{
	// $match is still unlit
	if(empty($var_cache[$match]))
	{
		if(count($other_vars) > $p)
		{
			preg_match_all("/((?!function)[a-zA-Z_]+\\()/", $cont, $m); /* ) */
			if(count($m[0]) < 2)
			{
				e("Function '".str_replace("function ", "", str_replace("(", "", $match))."'"." declared but unused"); /* ) */
			}
			$var = $other_vars[$p];
			$p++;
			$var_cache[$match] = $var;
			r(str_replace("function ", "", $match), $var."("); /* ) */
		} else
		{
			echo "Error: Got no more replace vars.\n";
			break;
		}
	}
}

preg_match_all("/(\\\$[a-zA-Z_]+)/", $cont, $matches, PREG_PATTERN_ORDER);
$script_vars = $matches[0];
$other_vars = array_merge(range("a", "z"), range("A", "Z"), array("_"));
$var_cache = array();
$p = 0;
usort($script_vars, "cmp");
$var_exludes = array("_GET", "_POST", "_SESSION", "argv");
foreach($script_vars as $match)
{
	if(in_array(substr($match, 1), $var_exludes))
	{
		continue;
	}
	// $match isn't even hot
	if(empty($var_cache[$match]))
	{
		if(count($other_vars) > $p)
		{
			$var = $other_vars[$p];
			$p++;
			$var_cache[$match] = $var;
			r($match, "\$".$var);
		} else
		{
			echo "Error: Got no more replace vars.\n";
			break;
		}
	}
}

$dif = (strlen($old) - strlen($cont));
if($dif == 0)
{
	echo "Couldn't shorten script. O.o\n";
	exit;
}
if($dif == 1)
{
	echo "Only shortened script by one char. .-.\n";
} else
{
	echo "Shortened script by {$dif} chars. :D\n";
}
file_put_contents($newf, $cont);
?>