<?php
/**
 * Aptgraph api library
 *
 * @author		Aptgraph team
 * @package		Aptgraph
 * @category	Library
 * @copyright	(c) 2011 Aptgraph team
 * @version		1.0
 * @link		http://aptgraph.com/
 * @since		Available since Release 1.0
 */
class Aptgraph {

	protected $url = 'http://aptgraph.cascade/api/v1/';
	protected $payload = array();
	protected $apikey;
	protected $result;
	protected $result_headers;
	public $error_messages;
	public $error_codes;

	/**
	 * __construct 
	 * 
	 * @param string $key The apikey
	 * @access public
	 * @return void
	 */
	public function __construct($key = FALSE)
	{
		if ($key)
		{
			$this->apikey = $key;
		}
	}

	/**
	 * Increment a statistic in a graph
	 *
	 * Usage:
	 *		$aptgraph = new Aptgraph($your_api_key);
	 *		$aptgraph
	 *			->increment(23) // For graphid '23'
	 *			->send();
	 *
	 * Returns 'this' to enable method chaining
	 *
	 * @param Integer The graph id
	 * @return Aptgraph	This class (as instanced object)
	 */
	public function increment($graphid)
	{
		$item = array('section' => 'statistic', 'action' => 'increment',
			'graphid' => $graphid);

		// Add to the payload
		$this->add_to_payload($item);

		return $this;
	}

	/**
	 * Add a statistic to a graph
	 *
	 * Usage:
	 *		$aptgraph = new Aptgraph($your_api_key);
	 *		$aptgraph
	 *			->add(11,123)
	 *			->add(11,124,'2010-09-07 13:30:00') // Add a timestamp
	 *			->add(11,125)
	 *			->add(11,726)
	 *			->send();
	 *
	 * Returns 'this' to enable method chaining
	 *
	 * @param Integer The graph id
	 * @param Number The value to add to the graph
	 * @return Aptgraph	This class (as instanced object)
	 */
	public function add($graphid, $value, $timestamp = NULL)
	{
		$item = array('section' => 'statistic', 'action' => 'add',
			'graphid' => $graphid, 'value' => $value);

		// Add the timestamp if its been passed in
		if ($timestamp !== NULL)
		{
			$item['timestamp'] = $timestamp;
		}

		// Add to the payload
		$this->add_to_payload($item);

		return $this;
	}

	/**
	 * Adds the item (action to process) to the payload. If a apikey has been
	 * set it adds it in too.
	 * 
	 * @param array $item Command to run
	 * @access private
	 * @return void
	 */
	private function add_to_payload(array $item)
	{
		// If the apikey has been set, then add it into the item.
		if ($this->apikey !== NULL)
		{
			$item['apikey'] = $this->apikey;
		}

		$this->payload[] = $item;
	}
	
	/**
	 * Clear all commands from the payload. 
	 * 
	 * @access public
	 * @return void
	 */
	public function reset_payload()
	{
		$this->payload = array();
	}

	/**
	 * Sends the payload to the api
	 *
	 * @return bool The result of the request
	 */
	public function send()
	{
		if (empty($this->payload)) return;
		
		// Reset results, errors and headers
		$this->result_headers = array();
		$this->error_messages = array();
		$this->error_codes = array();
		$this->result = '';
		
		// Send the payload as JSON
		if (($json_payload = json_encode($this->payload)) === NULL)
		{
			throw new Exception('There was a problem converting the payload to JSON');
			return FALSE;
		}

		// Construct the headers
		$headers = array(
			'Content-type: application/json; charset="utf-8"',
			'Content-Length: '.strlen($json_payload),
			'Accept: application/json',
			'Expect:'
		);

		// Send the payload to the api
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_FILETIME, TRUE);

		// Execute and store the results
		if (($this->result = curl_exec($ch)) === FALSE)
		{
			throw new Exception('There was a error requesting the Aptgraph api: '.curl_error($ch),1000);
		}
		
		// Save the headers and close curl
		$this->result_headers = curl_getinfo($ch);
		curl_close($ch);
echo '<h1>RESULT</h1><hr/>';
var_dump($this->result);
echo '<hr>';
die;
		// Reset the payload for the next call
		$this->reset_payload();

		// TODO Process the result and give useful information to the user
		$decoded_result = json_decode($this->result, TRUE);

		// Check each item in the result for a status != 0
		$status = TRUE;
		foreach ($decoded_result as $item)
		{
			if ($item['status'] != 0)
			{
				$status = FALSE;
				$this->error_messages [] = $item['errors'];
				$this->error_codes[] = $item['status'];
			}
		}

		// Returns TRUE if there were no errors
		return $status;
	}
	
	/**
	 * Returns the result headers
	 *
	 * @return Array Result headers
	 */
	public function get_result_headers()
	{
		return $this->result_headers;
	}
	
	/**
	 * Returns the results
	 *
	 * @param bool $decode Send the result back decoded or not 
	 * @access public
	 * @return mixed Returns either an array (default) or the raw result (as a JSON string)
	 */
	public function get_result($decode = TRUE)
	{
		if ($decode)
		{
			return json_decode($this->result);
		}
		else
		{
			return $this->result;
		}
	}
}
