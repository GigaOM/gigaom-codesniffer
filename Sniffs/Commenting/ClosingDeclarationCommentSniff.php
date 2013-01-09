<?php
/**
 * GigaOM_Sniffs_Commenting_ClosingDeclarationCommentSniff.
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
 * Squiz_Sniffs_Commenting_ClosingDeclarationCommentSniff.
 *
 * Checks the //end ... comments on classes, interfaces and functions.
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
class GigaOM_Sniffs_Commenting_ClosingDeclarationCommentSniff implements PHP_CodeSniffer_Sniff
{
	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register()
	{
		return array(
			T_FUNCTION,
			T_CLASS,
			T_INTERFACE,
			T_IF,
			T_ELSE,
			T_ELSEIF,
			T_FOREACH,
			T_FOR,
			T_WHILE,
			T_SWITCH,
		);

	}//end register

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token in the
	 *                                        stack passed in $tokens..
	 *
	 * @return void
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$stackPtr]['code'] === T_FUNCTION) {

			$methodProps = $phpcsFile->getMethodProperties($stackPtr);

			// Abstract methods do not require a closing comment.
			if ($methodProps['is_abstract'] === true) {
				return;
			}//end if

			// Closures do not require a closing comment.
			if ($methodProps['is_closure'] === true) {
				return;
			}//end if

			// If this function is in an interface then we don't require
			// a closing comment.
			if ($phpcsFile->hasCondition($stackPtr, T_INTERFACE) === true) {
				return;
			}//end if

			if (isset($tokens[$stackPtr]['scope_closer']) === false) {
				$error = 'Possible parse error: non-abstract method defined as abstract';
				$phpcsFile->addWarning($error, $stackPtr, 'Abstract');
				return;
			}//end if

			$decName = $phpcsFile->getDeclarationName($stackPtr);
			$comment = '//end '.$decName;
			$comment_alt = '// end '.$decName;
		}//end if
		elseif ($tokens[$stackPtr]['code'] === T_CLASS) {
			$comment = '//end class';
			$comment_alt = '// end class';
		}//end elseif
		elseif ( $tokens[$stackPtr]['code'] === T_INTERFACE ) {
			$comment = '//end interface';
			$comment_alt = '// end interface';
		}//end elseif
		elseif ( $tokens[$stackPtr]['code'] === T_IF ) {
			$comment = '//end if';
			$comment_alt = '// end if';
		}//end elseif
		elseif ( $tokens[$stackPtr]['code'] === T_ELSE ) {
			$comment = '//end else';
			$comment_alt = '// end else';
		}//end elseif
		elseif ( $tokens[$stackPtr]['code'] === T_ELSEIF ) {
			$comment = '//end elseif';
			$comment_alt = '// end elseif';
		}//end elseif
		elseif ( $tokens[$stackPtr]['code'] === T_WHILE ) {
			$comment = '//end while';
			$comment_alt = '// end while';
		}//end elseif
		elseif ( $tokens[$stackPtr]['code'] === T_FOR ) {
			$comment = '//end for';
			$comment_alt = '// end for';
		}//end elseif
		elseif ( $tokens[$stackPtr]['code'] === T_FOREACH ) {
			$comment = '//end foreach';
			$comment_alt = '// end foreach';
		}//end elseif
		elseif ( $tokens[$stackPtr]['code'] === T_SWITCH ) {
			$comment = '//end switch';
			$comment_alt = '// end switch';
		}//end elseif

		if (isset($tokens[$stackPtr]['scope_closer']) === false) {
			$closing_paren = isset( $tokens[ $stackPtr ]['parenthesis_closer'] ) ? $tokens[ $stackPtr ]['parenthesis_closer'] : null;

			if ( ':' == $tokens[ $closing_paren + 1]['content'] || ':' == $tokens[ $closing_paren + 2]['content'] )
			{
				$error = 'Colon syntax control structures are not allowed';
				$data  = array($tokens[$stackPtr]['content']);
				$phpcsFile->addError($error, $stackPtr, 'ColonSyntax', $data);
				return;
			}//end if

			$error = 'Possible parse error: %s missing opening or closing brace';
			$data  = array($tokens[$stackPtr]['content']);
			$phpcsFile->addWarning($error, $stackPtr, 'MissingBrace', $data);
			return;
		}//end if

		$closingBracket = $tokens[$stackPtr]['scope_closer'];

		if ($closingBracket === null) {
			// Possible inline structure. Other tests will handle it.
			return;
		}//end if

		if ( $tokens[$closingBracket]['line'] - $tokens[$stackPtr]['line'] >= 10 ) {
			$error = 'Expected '.$comment;
			if (
				! isset( $tokens[ ( $closingBracket + 1 ) ] )
				|| (
					isset( $tokens[ ( $closingBracket + 2 ) ]['code'] )
					&& $tokens[ ( $closingBracket + 1 ) ]['code'] !== T_COMMENT
					&& $tokens[ ( $closingBracket + 2 ) ]['code'] !== T_COMMENT
				)
			) {
					$phpcsFile->addWarning($error, $closingBracket, 'Missing');
					return;
			}//end if

			if (
					 strtolower( rtrim($tokens[($closingBracket + 1)]['content']) ) !== $comment
				&& strtolower( rtrim($tokens[($closingBracket + 1)]['content']) ) !== $comment_alt
				&& isset($tokens[($closingBracket + 2)]['content']) 
				&& strtolower( rtrim($tokens[($closingBracket + 2)]['content']) ) !== $comment
				&& strtolower( rtrim($tokens[($closingBracket + 2)]['content']) ) !== $comment_alt
			) {
				$phpcsFile->addError($error, $closingBracket, 'Incorrect');
				return;
			}//end if
		}//end if
	}//end process
}//end class
