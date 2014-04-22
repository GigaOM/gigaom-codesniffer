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
			T_DO,
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
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr )
	{
		$tokens = $phpcsFile->getTokens();

		$acceptable_comments = array();

		switch ( $tokens[ $stackPtr ]['code'] )
		{
			case T_FUNCTION:
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
				$acceptable_comments[] = '//end ' . $decName;
				$acceptable_comments[] = '// end ' . $decName;
				$acceptable_comments[] = '//END ' . $decName;
				$acceptable_comments[] = '// END ' . $decName;

				break;
			case T_CLASS:
				$acceptable_comments[] = '//end class';
				$acceptable_comments[] = '// end class';
				$acceptable_comments[] = '//END class';
				$acceptable_comments[] = '// END class';

				$decName = $phpcsFile->getDeclarationName( $stackPtr );
				$acceptable_comments[] = '//end ' . $decName;
				$acceptable_comments[] = '// end ' . $decName;
				$acceptable_comments[] = '//END ' . $decName;
				$acceptable_comments[] = '// END ' . $decName;
				break;
			case T_INTERFACE:
				$acceptable_comments[] = '//end interface';
				$acceptable_comments[] = '// end interface';
				$acceptable_comments[] = '//END interface';
				$acceptable_comments[] = '// END interface';

				$decName = $phpcsFile->getDeclarationName( $stackPtr );
				$acceptable_comments[] = '//end ' . $decName;
				$acceptable_comments[] = '// end ' . $decName;
				$acceptable_comments[] = '//END ' . $decName;
				$acceptable_comments[] = '// END ' . $decName;
				break;
			case T_IF:
				$acceptable_comments[] = '//end if';
				$acceptable_comments[] = '// end if';
				$acceptable_comments[] = '//END if';
				$acceptable_comments[] = '// END if';
				break;
			case T_ELSE:
				$acceptable_comments[] = '//end else';
				$acceptable_comments[] = '// end else';
				$acceptable_comments[] = '//END else';
				$acceptable_comments[] = '// END else';
				break;
			case T_ELSEIF:
				$acceptable_comments[] = '//end elseif';
				$acceptable_comments[] = '// end elseif';
				$acceptable_comments[] = '//END elseif';
				$acceptable_comments[] = '// END elseif';
				break;
			case T_WHILE:
				$acceptable_comments[] = '//end while';
				$acceptable_comments[] = '// end while';
				$acceptable_comments[] = '//END while';
				$acceptable_comments[] = '// END while';
				break;
			case T_DO:
				$acceptable_comments[] = '//end do';
				$acceptable_comments[] = '// end do';
				$acceptable_comments[] = '//END do';
				$acceptable_comments[] = '// END do';
				break;
			case T_FOR:
				$acceptable_comments[] = '//end for';
				$acceptable_comments[] = '// end for';
				$acceptable_comments[] = '//END for';
				$acceptable_comments[] = '// END for';
				break;
			case T_FOREACH:
				$acceptable_comments[] = '//end foreach';
				$acceptable_comments[] = '// end foreach';
				$acceptable_comments[] = '//END foreach';
				$acceptable_comments[] = '// END foreach';
				break;
			case T_SWITCH:
				$acceptable_comments[] = '//end switch';
				$acceptable_comments[] = '// end switch';
				$acceptable_comments[] = '//END switch';
				$acceptable_comments[] = '// END switch';
				break;
			default:
				return;
		}// end switch

		if ( FALSE === isset( $tokens[ $stackPtr ]['scope_closer'] ) )
		{
			$closing_paren = isset( $tokens[ $stackPtr ]['parenthesis_closer'] ) ? $tokens[ $stackPtr ]['parenthesis_closer'] : null;

			if ( ':' == $tokens[ $closing_paren + 1 ]['content'] || ':' == $tokens[ $closing_paren + 2 ]['content'] )
			{
				$error = 'Colon syntax control structures are not allowed';
				$data  = array( $tokens[ $stackPtr ]['content'] );
				$phpcsFile->addError( $error, $stackPtr, 'ColonSyntax', $data );
				return;
			}//end if

			if ( 'while' == $tokens[ $stackPtr ]['content'] && ';' != $tokens[ $closing_paren + 1 ]['content'] )
			{
				$error = 'Possible parse error: %s missing opening or closing brace';
				$data  = array( $tokens[ $stackPtr ]['content'] );
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

		$error = 'Expected '.$acceptable_comments[0];

		$comment_token = FALSE;
		if ( isset( $tokens[ ( $closingBracket + 1 ) ] ) )
		{
			if ( T_COMMENT == $tokens[ ( $closingBracket + 1 ) ]['code']
				&& trim( $tokens[ ( $closingBracket + 1 ) ]['content'] )
			)
			{
				$comment_token = $tokens[ ( $closingBracket + 1 ) ];
			}// end if
			elseif (
				isset( $tokens[ ( $closingBracket + 2 ) ] )
				&& T_COMMENT == $tokens[ ( $closingBracket + 2 ) ]['code']
				&& trim( $tokens[ ( $closingBracket + 2 ) ]['content'] )
			)
			{
				$comment_token = $tokens[ ( $closingBracket + 2 ) ];
			}// end elseif
		}// end if

		if ( ! $comment_token )
		{
			if ( ( $tokens[ $closingBracket ]['line'] - $tokens[ $stackPtr ]['line'] ) >= 10 )
			{
				$phpcsFile->addWarning( $error, $closingBracket, 'Missing' );
			}//end if
			return;
		}//end if

		$comment_token_content = rtrim( $comment_token['content'] );
		if ( ! in_array( $comment_token_content, $acceptable_comments ) )
		{
			$phpcsFile->addError( $error, $closingBracket, 'Incorrect' );
			return;
		}//end if
	}//end process
}//end class
