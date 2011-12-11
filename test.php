<?php
/**
 * Add a statistic to a graph
 *
 * Usage:
 *		$aptgraph = new Aptgraph;
 *		$aptgraph
 *			->add(11,123)
 *			->send();
 *
 * Returns 'this' to enable method chaining
 *
 * @param Integer		The graph id
 * @param Number		The value to add to the graph
 * @return Aptgraph	This class (as instanced object)
 */

include('Aptgraph.php');
$apikey = 'wKJtkVq3QXoUJW5hryrFXRXDEoJhiwFmvA2KEWMSNH3AcMMPsx';
$graphid = '8jspy';
$a = new Aptgraph($apikey);

echo '<pre>';
if ( $status = $a
		->add($graphid, 123)
		->add($graphid, 777, '2011-06-06')
		//->increment($graphid)
		->send())
{
	echo 'Its all good<hr/>';
}
else
{
	echo 'Its all bad<hr/>';
	var_dump($a->error_messages);
	var_dump($a->error_codes);
}

var_dump($a->get_result());
var_dump($a->get_result(FALSE));
echo '</pre>';

