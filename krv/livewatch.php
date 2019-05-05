<?php
if (!isset($_REQUEST['key'])) { echo "Alles OK"; }
else {
	if(preg_match('/^[a-f0-9]{32}$/', $_REQUEST['key']))
	{
		echo $_REQUEST['key'];
	}
}