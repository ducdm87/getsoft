<?php


function print_debug($level, $message){
	if (!DEBUG) {
		return ;
	}
	echo '<br />';
	for ($ij = 0; $ij < $level; $ij++){
		echo '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;';
	}
	echo date('Y-m-d H:i:s').' <b>'.$message."</b>\n";
}