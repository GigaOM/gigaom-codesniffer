<?php
/**
 * GigaOM_Sniffs_ControlStructures_OpeningControlStructureSniff
 *
 * This is a shameless copy of the work done by Squizlabs, specifically
 * Greg Sherwood <gsherwood@squiz.net> and Marc McIntyre <mmcintyre@squiz.net>,
 * but modified to match GigaOM standards.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Matthew Batchelder <borkweb@gmail.com>
 * @author    Zachary Tirrell <zbtirrell@gmail.com>
 * @copyright 2012 GigaOM
 * @license   https://github.com/GigaOM/gigaom-codesniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Generic_Sniffs_Functions_OpeningFunctionBraceBsdAllmanSniff.
 *
 * Checks that the opening brace of a function is on the line after the
 * function declaration.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Matthew Batchelder <borkweb@gmail.com>
 * @author    Zachary Tirrell <zbtirrell@gmail.com>
 * @copyright 2012 GigaOM
 * @license   https://github.com/GigaOM/gigaom-codesniffer/blob/master/licence.txt BSD Licence
 * @version   Release: 1.4.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class GigaOM_Sniffs_ControlStructures_OpeningControlStructureSniff implements PHP_CodeSniffer_Sniff
{
	/**
	 * Registers the tokens that this sniff wants to listen for.
	 *
	 * @return void
	 */
	public function register()
	{
		return array(
			T_IF,
			T_ELSE,
			T_ELSEIF,
			T_WHILE,
			T_FOR,
			T_FOREACH,
			T_SWITCH,
			T_DO,
		);
	}//end register

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token in the
	 *                                        stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		$tokens = $phpcsFile->getTokens();

		if (isset($tokens[$stackPtr]['scope_opener']) === false)
		{
			return;
		}//end if

		$openingBrace = $tokens[$stackPtr]['scope_opener'];

		// The end of the function occurs at the end of the argument list. Its
		// like this because some people like to break long function declarations
		// over multiple lines.
		$controlLine = $tokens[$tokens[$stackPtr]['parenthesis_closer']]['line'];
		$braceLine    = $tokens[$openingBrace]['line'];

		$lineDifference = ($braceLine - $controlLine);

		if ($lineDifference === 0)
		{
			$error = 'Opening brace should be on a new line';
			$phpcsFile->addError($error, $openingBrace, 'BraceOnSameLine');
			return;
		}//end if

		// We need to actually find the first piece of content on this line,
		// as if this is a method with tokens before it (public, static etc)
		// or an if with an else before it, then we need to start the scope
		// checking from there, rather than the current token.
		$lineStart = $stackPtr;
		while (($lineStart = $phpcsFile->findPrevious(array(T_WHITESPACE), ($lineStart - 1), null, false)) !== false)
		{
			if (strpos($tokens[$lineStart]['content'], $phpcsFile->eolChar) !== false)
			{
				break;
			}//end if
		}//end while

		// We found a new line, now go forward and find the first non-whitespace
		// token.
		$lineStart = $phpcsFile->findNext(array(T_WHITESPACE), $lineStart, null, true);

		// The opening brace is on the correct line, now it needs to be
		// checked to be correctly indented.
		$startColumn = $tokens[$lineStart]['column'];
		$braceIndent = $tokens[$openingBrace]['column'];

		if ($braceIndent !== $startColumn)
		{
			$error = 'Opening brace indented incorrectly; expected %s spaces, found %s';
			$data  = array(
				($startColumn - 1),
				($braceIndent - 1),
			);
			$phpcsFile->addError($error, $openingBrace, 'BraceIndent', $data);
		}//end if

	}//end process
}//end class
