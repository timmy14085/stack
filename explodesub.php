<?php
$input = $argv[1];
foreach(str_split($input) as $p=>$letter)
{
	$spaces = str_repeat(" ", $p);
	echo $spaces.$letter."\n";
	$p++;
	for($i=$p;$i<strlen($input);$i++)
	{
		echo $spaces.$letter.substr($input, $p, $i)."\n";
	}
}
?>