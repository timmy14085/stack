<?php
error_reporting(0);
$p=$argv[1];
$img = ImageCreateFromPng($p);
$width = imagesx($img);
$height = imagesy($img);
$new = ImageCreateFromPng($p);
alphaFix($img);
alphaFix($new);
$inv = '2130706432';
for($w=0;$w<$width;$w++)
	for($h=0;$h<$height;$h++)
	{
		$color = ImageColorAt($img, $w, $h);
		if($color != $inv)
			for($x=0;$x<$width;$x++)
				for($y=0;$y<$height;$y++)
				{
					if(abs($h - $y) + abs($w - $x) <= intval($argv[3]))
					{
						$_color = ImageColorAt($img, $x, $y);
						if($_color == $inv)
			 				imagesetpixel($new, $x, $y, hexdec($argv[2]));
					}
				}
	}
imagepng($new, str_replace(".png", ".border.png", $p));

function alphaFix(&$img)
{
	$background = imagecolorallocate($img, 0, 0, 0);
	imagecolortransparent($img, $background);
	imagealphablending($img, false);
	imagesavealpha($img, true);
}
?>
