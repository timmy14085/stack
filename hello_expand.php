<?$a=$argv[0];$b=file_get_contents($a);file_put_contents($a,$b.substr($b,81,20));echo"Hello World\n";