<?php
$file = fopen('CH1.txt','r');

while ($line = fgets($file) !== false){
	if (preg_match('/[A-Z][A-Z][A-Z][0-9][0-9][0-9]/', $line))
}

?>