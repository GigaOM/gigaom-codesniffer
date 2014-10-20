<?php
/**
 * Gigaom_Sniffs_PHP_ExtractFunctionSniff
 *
 * Throw an error if extract is used
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Matthew Batchelder <borkweb@gmail.com>
 * @author    Zachary Tirrell <zbtirrell@gmail.com>
 * @copyright 2012 Gigaom
 * @license   https://github.com/GigaOM/gigaom-codesniffer/blob/master/licence.txt BSD Licence
 * @version   Release: 1.4.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Gigaom_Sniffs_PHP_ExtractFunctionSniff extends Generic_Sniffs_PHP_ForbiddenFunctionsSniff
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
		'extract' => 'separate assignments or access the array directly',
	);

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
		$data  = array( $function );
		$error = 'Extract is the devil.';

		if ( $this->forbiddenFunctions[ $function ] )
		{
			$error .= ' Use ' . $this->forbiddenFunctions[ $function ] . ' instead.';
		}//end if

		$type  = 'Found';

		if ( TRUE === $this->error )
		{
			$phpcsFile->addError( $error, $stackPtr, $type, $data );
		}//end if
		else
		{
			$phpcsFile->addWarning( $error, $stackPtr, $type, $data );
		}//end else
	}//end addError
}//end class
