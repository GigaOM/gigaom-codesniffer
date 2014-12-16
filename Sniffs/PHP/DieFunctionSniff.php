<?php
/**
 * Gigaom_Sniffs_PHP_DieFunctionSniff
 *
 * Throw an error if die is used; suggest alternatives.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Oren Kredo <oren.kredo@gigaom.com>
 * @copyright 2014 Gigaom
 * @license   https://github.com/GigaOM/gigaom-codesniffer/blob/master/licence.txt BSD Licence
 * @version   Release: 1.4.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Gigaom_Sniffs_PHP_DieFunctionSniff implements PHP_CodeSniffer_Sniff
{
	/**
	 * Returns the token types that this sniff is interested in.
	 *
	 * @return array(int)
	 */
	public function register()
	{
		return array(
			T_STRING,
		);
	}//end register

	/**
	 * Processes the tokens that this sniff is interested in.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
	 * @param int                  $stackPtr  The position in the stack where
	 *                                        the token was found.
	 *
	 * @return void
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr )
	{
		$tokens = $phpcsFile->getTokens();
		if ( $tokens[ $stackPtr ][ 'content' ]{0} === 'die' )
		{
			$error = 'It is poor design to rely on die() for error handling in a web site. Use wp_die() with a useful error message. ';
			$error .= 'Note: if this is the result of an ajax call, use wp_send_json_error for the failure case and wp_send_json_success for the success case.';
			$data  = array( trim( $tokens[ $stackPtr ][ 'content' ] ) );
			$phpcsFile->addWarning( $error, $stackPtr, 'Found', $data );
		}
	}//end process
}//end class
