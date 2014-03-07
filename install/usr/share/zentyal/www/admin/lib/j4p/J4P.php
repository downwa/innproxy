<?php

/**
 * Contains "J4P" request handler class
 *
 * LICENSE: MIT
 *
 * @author     Peter Pippinger
 * @category   PP
 * @package    PP_J4P
 * @copyright  Copyright (c) 2013 Peter Pippinger
 * @license    http://opensource.org/licenses/MIT
 * @version    1.1.0
 * @since      Class exists since release 1.0.0
 */

// Execute ajax request.
J4P::executeRequest();

/**
 * "J4P" request handler class
 *
 * Provides handling of ajax requests and output for client side javascript
 *
 * @author     Peter Pippinger
 * @category   PP
 * @package    PP_J4P
 * @copyright  Copyright (c) 2013 Peter Pippinger
 * @license    http://opensource.org/licenses/MIT
 * @version    1.1.0
 * @since      Class exists since release 1.0.0
 */
class J4P
{
	/**
	 * Contains the "J4P" object instance for singleton.
	 * @var Object
	 */
	private static $_j4pObj = null;
	
	/**
	 * Counts stored responses.
	 * @var Integer
	 */
	private static $_responseCount = -1;
	
	/**
	 * Stores responses.
	 * @var Array
	 */
	private static $_responses = array();
	
	/**
	 * Handles gets on non existing class members for fluent interface.
	 * @param $name member name
	 * @return Object "J4P" object for fluent interface
	 */
	function __get($name)
	{
		J4P::$_responses[J4P::$_responseCount][] = $name;
		return $this;
	}
	
	/**
	 * Handles sets on non existing class members for fluent interface.
	 * @param $name member name
	 * @param $value value to set
	 */
	function __set($name, $value)
	{
		$value = json_encode($value);
		J4P::$_responses[J4P::$_responseCount][] = $name . "=" . $value;
	}
	
	/**
	 * Handles requests on non existing methods for fluent interface.
	 * @param $name method name
	 * @param $arguments arguments given to method
	 * @return Object "J4P" object for fluent interface
	 */
	function __call($name, $arguments)
	{
		foreach ($arguments as $argumentNumber => $argument) {
			$arguments[$argumentNumber] = json_encode($argument);
		}
		J4P::$_responses[J4P::$_responseCount][] = $name . "(" . implode(",", $arguments) . ")";
		return $this;
	}
	
	/**
	 * Adds a response to be send to the client. This is the first
	 * method in the fluent interface chain.
	 * @return Object "J4P" object for fluent interface
	 */
	public static function addResponse()
	{
		J4P::$_responseCount++;
		J4P::$_responses[J4P::$_responseCount] = array();
		if (is_a(J4P::$_j4pObj, "J4P")) {
			return J4P::$_j4pObj;
		} else {
			J4P::$_j4pObj = new J4P();
			return J4P::$_j4pObj;
		}
	}
	
	/**
	 * Outputs needed client side javascript for ajax requests.
	 * @param	<p>$ajaxDebugLevel Integer</p>
	 * 			<p>0 = Show no response errors and no response code.</p>
	 * 			<p>1 = Show response errors but no response code.</p>
	 * 			<p>2 = Show response errors and response code.</p>
	 */
	public static function outputJs($ajaxDebugLevel = 0)
	{
		echo '<script>' . PHP_EOL .
			'			function php(){$.ajax({async:arguments[0]=="async",type:"POST",url:"' . $_SERVER["REQUEST_URI"] . '",' . PHP_EOL .
			'			data:{ajaxRequest:true,arguments:JSON.stringify(arguments)}}).done(' . PHP_EOL . 
			'			function(response){' . ($ajaxDebugLevel > 1 ? 'alert("Response is:\n"+' .
			'response.replace(/(<([^>]+)>)/ig,""));' : '') . 'try{eval(response);}catch(e){' .
			($ajaxDebugLevel > 0 ? 'alert(e+"\n\nResponse was:\n"+response);' : '') . '}});}' . PHP_EOL . 
			'		</script>' . PHP_EOL;
	}	
				
	/**
	 * Executes the ajax request sent by the client and 
	 * outputs the response for the client.
	 * Notice: PHP processing ends after the response.
	 */
	public static function executeRequest()
	{
		if (isset($_POST["ajaxRequest"])) {
			$requestArguments = json_decode($_POST["arguments"], true);
			array_shift($requestArguments);
			$functionName = "j4p_" . array_shift($requestArguments);
			if (function_exists($functionName))
				call_user_func_array($functionName, $requestArguments);
			else 
				J4P::addResponse()->alert("PHP function \"$functionName\" not found error.");
			$responseJs = "";
			foreach (J4P::$_responses as $responseNumber => $responsePieces) {
				$responseJs .= implode(".", $responsePieces) . ";" . PHP_EOL;
			}
			echo($responseJs);		
			die();
		}
	}
}