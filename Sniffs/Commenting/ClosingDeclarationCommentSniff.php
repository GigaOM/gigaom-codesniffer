<?php
/**
 * Gigaom_Sniffs_Commenting_ClosingDeclarationCommentSniff.
 *
 * This is a shameless copy of the work done by Squizlabs, specifically
 * Greg Sherwood <gsherwood@squiz.net> and Marc McIntyre <mmcintyre@squiz.net>,
 * but modified to match Gigaom standards.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Matthew Batchelder <borkweb@gmail.com>
 * @author    Zachary Tirrell <zbtirrell@gmail.com>
 * @copyright 2012 Gigaom
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
 * @copyright 2012 Gigaom
 * @license   https://github.com/GigaOM/gigaom-codesniffer/blob/master/licence.txt BSD Licence
 * @version   Release: 1.4.0
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Gigaom_Sniffs_Commenting_ClosingDeclarationCommentSniff implements PHP_CodeSniffer_Sniff
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

		if ( T_FUNCTION === $tokens[ $stackPtr ]['code'] )
		{
			$methodProps = $phpcsFile->getMethodProperties( $stackPtr );

			// Abstract methods do not require a closing comment.
			if ( TRUE === $methodProps['is_abstract'] )
			{
				return;
			}//end if

			// Closures do not require a closing comment.
			if ( TRUE === $methodProps['is_closure'] )
			{
				return;
			}//end if

			// If this function is in an interface then we don't require
			// a closing comment.
			if ( TRUE === $phpcsFile->hasCondition( $stackPtr, T_INTERFACE ) )
			{
				return;
			}//end if

			if ( FALSE === isset( $tokens[ $stackPtr ]['scope_closer'] ) )
			{
				$error = 'Possible parse error: non-abstract method defined as abstract';
				$phpcsFile->addWarning( $error, $stackPtr, 'Abstract' );
				return;
			}//end if

			$decName = $phpcsFile->getDeclarationName( $stackPtr );
			$comment = '//end ' . $decName;
			$comment_alt = '// end ' . $decName;
		}//end if
		elseif ( T_CLASS === $tokens[ $stackPtr ]['code'] )
		{
			$decName = $phpcsFile->getDeclarationName( $stackPtr );
			$comment = '//end class';
			$comment_alt = '// end class';
		}//end elseif
		elseif ( T_INTERFACE === $tokens[ $stackPtr ]['code'] )
		{
			$comment = '//end interface';
			$comment_alt = '// end interface';
		}//end elseif
		elseif ( T_IF === $tokens[ $stackPtr ]['code'] )
		{
			$comment = '//end if';
			$comment_alt = '// end if';
		}//end elseif
		elseif ( T_ELSE === $tokens[ $stackPtr ]['code'] )
		{
			$comment = '//end else';
			$comment_alt = '// end else';
		}//end elseif
		elseif ( T_ELSEIF === $tokens[ $stackPtr ]['code'] )
		{
			$comment = '//end elseif';
			$comment_alt = '// end elseif';
		}//end elseif
		elseif ( T_WHILE === $tokens[ $stackPtr ]['code'] )
		{
			$comment = '//end while';
			$comment_alt = '// end while';
		}//end elseif
		elseif ( T_FOR === $tokens[ $stackPtr ]['code'] )
		{
			$comment = '//end for';
			$comment_alt = '// end for';
		}//end elseif
		elseif ( T_FOREACH === $tokens[ $stackPtr ]['code'] )
		{
			$comment = '//end foreach';
			$comment_alt = '// end foreach';
		}//end elseif
		elseif ( T_SWITCH === $tokens[ $stackPtr ]['code'] )
		{
			$comment = '//end switch';
			$comment_alt = '// end switch';
		}//end elseif

		if ( FALSE === isset( $tokens[ $stackPtr ]['scope_closer'] ) )
		{
			$closing_paren = isset( $tokens[ $stackPtr ]['parenthesis_closer'] ) ? $tokens[ $stackPtr ]['parenthesis_closer'] : null;

			if ( ':' == $tokens[ $closing_paren + 1 ]['content'] || ':' == $tokens[ $closing_paren + 2 ]['content'] )
			{
				$error = 'Colon syntax control structures are not allowed';
				$data  = array($tokens[ $stackPtr ]['content']);
				$phpcsFile->addError( $error, $stackPtr, 'ColonSyntax', $data );
				return;
			}//end if

			if ( 'while' == $tokens[ $stackPtr ]['content'] && ';' != $tokens[ $closing_paren + 1 ]['content'] )
			{
				$error = 'Possible parse error: %s missing opening or closing brace';
				$data  = array($tokens[ $stackPtr ]['content']);
				$phpcsFile->addWarning( $error, $stackPtr, 'MissingBrace', $data );
			}//end if
			return;
		}//end if

		$closingBracket = $tokens[ $stackPtr ]['scope_closer'];

		if ( $closingBracket === null )
		{
			// Possible inline structure. Other tests will handle it.
			return;
		}//end if

		if ( $tokens[ $closingBracket ]['line'] - $tokens[ $stackPtr ]['line'] >= 10 )
		{
			$error = 'Expected '.$comment;
			if (
				! isset( $tokens[ ( $closingBracket + 1 ) ] )
				|| (
					isset( $tokens[ ( $closingBracket + 2 ) ]['code'] )
					&& T_COMMENT !== $tokens[ ( $closingBracket + 1 ) ]['code']
					&& T_COMMENT !== $tokens[ ( $closingBracket + 2 ) ]['code']
				)
			)
			{
					$phpcsFile->addWarning( $error, $closingBracket, 'Missing' );
					return;
			}//end if

			if (
					 strtolower( rtrim( $tokens[ ( $closingBracket + 1 ) ]['content']) ) !== $comment
				&& strtolower( rtrim( $tokens[ ( $closingBracket + 1 ) ]['content']) ) !== $comment_alt
				&& isset( $tokens[ ( $closingBracket + 2 ) ]['content'] )
				&& strtolower( rtrim( $tokens[ ( $closingBracket + 2 ) ]['content']) ) !== $comment
				&& strtolower( rtrim( $tokens[ ( $closingBracket + 2 ) ]['content']) ) !== $comment_alt
				&& ( T_CLASS && rtrim( $tokens[ ( $closingBracket + 2 ) ]['content']) !== '//end ' . $decName )
				&& ( T_CLASS && rtrim( $tokens[ ( $closingBracket + 2 ) ]['content']) !== '// end ' . $decName )
				&& ( T_CLASS && rtrim( $tokens[ ( $closingBracket + 2 ) ]['content']) !== '//END ' . $decName )
				&& ( T_CLASS && rtrim( $tokens[ ( $closingBracket + 2 ) ]['content']) !== '// END ' . $decName )
			)
			{
				$phpcsFile->addError( $error, $closingBracket, 'Incorrect' );
				return;
			}//end if
		}//end if
	}//end process
}//end class
