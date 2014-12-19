<?php
/**
 * Gigaom_Sniffs_PHP_DieFunctionSniff
 *
 * Throw a warning if die is used
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Oren Kredo <oren.kredo@gigaom.com>
 * @copyright 2014 Gigaom
 * @license   https://github.com/GigaOM/gigaom-codesniffer/blob/master/licence.txt BSD Licence
 * @version   Release: 1.4.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Gigaom_Sniffs_PHP_DieFunctionSniff extends Generic_Sniffs_PHP_ForbiddenFunctionsSniff
{
	/**
	 * A list of forbidden functions with their alternatives.
	 *
	 * The value is NULL if no alternative exists. IE, the
	 * function should just not be used.
	 *
	 * @var array(string => string|null)
	 */
	protected $forbiddenFunctions = array(
		'die' => 'wp_die(), wp_send_json_error(), or wp_send_json_success()',
	);

	/**
	* Returns an array of tokens this test wants to listen for.
	* We're overriding the parent's method here so that this class will only fire when sniffer hits the die keyword
	*
	* @return array
	*/
	public function register()
	{
		// Everyone has had a chance to figure out what forbidden functions
		// they want to check for, so now we can cache out the list.
		$this->forbiddenFunctionNames = array_keys( $this->forbiddenFunctions );

		if ( $this->patternMatch === true )
		{
			foreach ( $this->forbiddenFunctionNames as $i => $name )
			{
				$this->forbiddenFunctionNames[ $i ] = '/'.$name.'/i';
			}
		}

		return array( T_EXIT );
	}//end register

/**
	 * Generates the error or warning for this sniff.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the forbidden function
	 *                                        in the token array.
	 * @param string               $function  The name of the forbidden function.
	 * @param string               $unused_pattern   The pattern used for the match.
	 *
	 * @return void
	 */
	protected function addError( $phpcsFile, $stackPtr, $function, $unused_pattern = NULL )
	{
		$data = array( $function );
		$error = 'Wordpress best practice is to avoid die.';

		if ( $this->forbiddenFunctions[ $function ] )
		{
			$error .= ' Use ' . $this->forbiddenFunctions[ $function ] . ' instead.';
		}//end if

		$phpcsFile->addWarning( $error, $stackPtr, 'Found', $data );
	}//end addError
}//end class
