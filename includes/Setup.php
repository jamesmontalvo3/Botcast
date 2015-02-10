<?php
/**
 * <INSERT DESCRIPTION>.
 * 
 * Documentation: http://???
 * Support:       http://???
 * Source code:   http://???
 *
 * @addtogroup Extensions
 * @author James Montalvo
 * @copyright © 2014 by James Montalvo
 * @licence GNU GPL v3+
 */

// FIXME: all function documentation is very generic...add specifics.
 
namespace Botcast;

class Setup {

	/**
	* Handler for ParserFirstCallInit hook; sets up parser functions.
	* @see http://www.mediawiki.org/wiki/Manual:Hooks/BeforePageDisplay
	* @param $parser Parser object
	* @param $frame FIXME: what does frame really do?
	* @param $args array of arguments passed to parser function
	* @return bool true in all cases
	*/
	static function setupParserFunctions ( &$parser ) {
			
		$parserFn = new PushToWiki( $parser );
		$parserFn->setupParserFunction();	
		return true;

	}
	
	
	

}