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
	
		// setup #synopsize parser function
		$parser->setFunctionHook(
			'push_to_wiki', 
			array(
				'Botcast\Setup',
				'renderPushToWiki' 
			),
			SFH_OBJECT_ARGS
		);
		
		return true;

	}
	
	
	
	/**
	* Handler for synopsize parser function.
	* @see http://www.mediawiki.org/wiki/Manual:Hooks/BeforePageDisplay
	* @param $parser Parser object
	* @param $frame FIXME: what does frame really do?
	* @param $args array of arguments passed to parser function
	* @return bool true in all cases
	*/
	static function renderPushToWiki ( &$parser, $frame, $args ) {

		$args = self::processParams( $frame, $args, array("", 255, 1) );
			
		$full_text  = $args[0];
		$max_length = $args[1];
		$max_lines  = $args[2];
		
		$needle = "\n";
		for($i=0; $i<$max_lines; $i++) {
			if ($newline_pos)
				$offset = $newline_pos + strlen($needle);
			else
				$offset = 0;
			$newline_pos = strpos($full_text, $needle, $offset);
		}

		if ($newline_pos) {
			// trim to specified number of newlines
			$synopsis = substr($full_text, 0, $newline_pos);
		}
		else {
			$synopsis = $full_text;
		}
		
		// trim at max characters
		if (strlen($synopsis) > $max_length) {
			$synopsis = substr($synopsis, 0, $max_length);
			$last_space = strrpos($synopsis, ' ');
			$synopsis = substr($synopsis, 0, $last_space) . ' ...';
		}

		return $synopsis;
	}

	static function processParams ( $frame, $params, $defaults ) {

		// <REMOVE> ?
			$inFormName = $inValue = $inButtonStr = $inQueryStr = '';
			$inQueryArr = array();
			$positionalParameters = false;
			$inAutocompletionSource = '';
			$inRemoteAutocompletion = false;
			$inSize = 25;
			$classStr = "";
			$inPlaceholder = "";
		// </REMOVE> ?

		$unNamedParam = 0;

		// assign params - support unlabelled params, for backwards compatibility
		foreach ( $params as $i => $param ) {
			$elements = explode( '=', $param, 2 );

			// set paramName and value
			if ( count( $elements ) > 1 ) {
				$paramName = trim( $elements[0] );

				// parse (@TODO ? and sanitize) parameter values
				$value = trim( $elements[1] );
			} else {
				$paramName = $unNamedParam;
				$unNamedParam++;

				// parse (@TODO ? and sanitize) parameter values
				$value = trim( $param );
			}




			if ( $param_name == 'form' )
				$inFormName = $value;
			elseif ( $param_name == 'size' )
				$inSize = $value;
			elseif ( $param_name == 'default value' )
				$inValue = $value;
			elseif ( $param_name == 'button text' )
				$inButtonStr = $value;
			elseif ( $param_name == 'query string' ) {
				// Change HTML-encoded ampersands directly to
				// URL-encoded ampersands, so that the string
				// doesn't get split up on the '&'.
				$inQueryStr = str_replace( '&amp;', '%26', $value );
				// "Decode" any other HTML tags.
				$inQueryStr = html_entity_decode( $inQueryStr, ENT_QUOTES );

				parse_str($inQueryStr, $arr);
				$inQueryArr = SFUtils::array_merge_recursive_distinct( $inQueryArr, $arr );
			} elseif ( $param_name == 'autocomplete on category' ) {
				$inAutocompletionSource = $value;
				$autocompletion_type = 'category';
			} elseif ( $param_name == 'autocomplete on namespace' ) {
				$inAutocompletionSource = $value;
				$autocompletion_type = 'namespace';
			} elseif ( $param_name == 'remote autocompletion' ) {
				$inRemoteAutocompletion = true;
			} elseif ( $param_name == 'placeholder' ) {
				$inPlaceholder = $value;
			} elseif ( $param_name == null && $value == 'popup' ) {
				SFUtils::loadScriptsForPopupForm( $parser );
				$classStr = 'popupforminput';
			} elseif ( $param_name !== null && !$positionalParameters ) {

				$value = urlencode($value);
				parse_str("$param_name=$value", $arr);
				$inQueryArr = SFUtils::array_merge_recursive_distinct( $inQueryArr, $arr );

			} elseif ( $i == 0 ) {
				$inFormName = $value;
				$positionalParameters = true;
			} elseif ( $i == 1 ) {
				$inSize = $value;
			} elseif ( $i == 2 ) {
				$inValue = $value;
			} elseif ( $i == 3 ) {
				$inButtonStr = $value;
			} elseif ( $i == 4 ) {
				// Change HTML-encoded ampersands directly to
				// URL-encoded ampersands, so that the string
				// doesn't get split up on the '&'.
				$inQueryStr = str_replace( '&amp;', '%26', $value );

				parse_str($inQueryStr, $arr);
				$inQueryArr = SFUtils::array_merge_recursive_distinct( $inQueryArr, $arr );
			}
		}

		$fs = SpecialPageFactory::getPage( 'FormStart' );

		$fs_url = $fs->getTitle()->getLocalURL();
		$str = <<<END
			<form name="createbox" action="$fs_url" method="get" class="$classStr">
			<p>

END;
		$formInputAttrs = array( 'size' => $inSize );

		if ( $wgHtml5 ) {
			$formInputAttrs['placeholder'] = $inPlaceholder;
			$formInputAttrs['autofocus'] = 'autofocus';
		}

		// Now apply the necessary settings and Javascript, depending
		// on whether or not there's autocompletion (and whether the
		// autocompletion is local or remote).
		$input_num = 1;
		if ( empty( $inAutocompletionSource ) ) {
			$formInputAttrs['class'] = 'formInput';
		} else {
			self::$num_autocompletion_inputs++;
			$input_num = self::$num_autocompletion_inputs;
			// place the necessary Javascript on the page, and
			// disable the cache (so the Javascript will show up) -
			// if there's more than one autocompleted #forminput
			// on the page, we only need to do this the first time
			if ( $input_num == 1 ) {
				$parser->disableCache();
				SFUtils::addJavascriptAndCSS( $parser );
			}

			$inputID = 'input_' . $input_num;
			$formInputAttrs['id'] = $inputID;
			$formInputAttrs['class'] = 'autocompleteInput createboxInput formInput';
			global $sfgMaxLocalAutocompleteValues;
			$autocompletion_values = SFUtils::getAutocompleteValues( $inAutocompletionSource, $autocompletion_type );
			if ( count($autocompletion_values) > $sfgMaxLocalAutocompleteValues  || $inRemoteAutocompletion ) {
				$formInputAttrs['autocompletesettings'] = $inAutocompletionSource;
				$formInputAttrs['autocompletedatatype'] = $autocompletion_type;
			} else {
				global $sfgAutocompleteValues;
				$sfgAutocompleteValues[$inputID] = $autocompletion_values;
				$formInputAttrs['autocompletesettings'] = $inputID;
			}
		}

		$str .= "\t" . Html::input( 'page_name', $inValue, 'text', $formInputAttrs ) . "\n";

		// if the form start URL looks like "index.php?title=Special:FormStart"
		// (i.e., it's in the default URL style), add in the title as a
		// hidden value
		if ( ( $pos = strpos( $fs_url, "title=" ) ) > - 1 ) {
			$str .= Html::hidden( "title", urldecode( substr( $fs_url, $pos + 6 ) ) );
		}
		if ( $inFormName == '' ) {
			$str .= SFUtils::formDropdownHTML();
		} else {
			$str .= Html::hidden( "form", $inFormName );
		}

		// Recreate the passed-in query string as a set of hidden variables.
		if ( !empty( $inQueryArr ) ) {
			// query string has to be turned into hidden inputs.

			$query_components = explode( '&', http_build_query( $inQueryArr, '', '&' ) );

			foreach ( $query_components as $query_component ) {
				$var_and_val = explode( '=', $query_component, 2 );
				if ( count( $var_and_val ) == 2 ) {
					$str .= Html::hidden( urldecode( $var_and_val[0] ), urldecode( $var_and_val[1] ) );
				}
			}
		}

		$button_str = ( $inButtonStr != '' ) ? $inButtonStr : wfMessage( 'sf_formstart_createoredit' )->escaped();
		$str .= <<<END
			<input type="submit" value="$button_str" id="input_button_$input_num" class="forminput_button"/></p>
			</form>

END;
		if ( ! empty( $inAutocompletionSource ) ) {
			$str .= "\t\t\t" .
				Html::element( 'div',
					array(
						'class' => 'page_name_auto_complete',
						'id' => "div_$input_num",
					),
					// it has to be <div></div>, not
					// <div />, to work properly - stick
					// in a space as the content
					' '
				) . "\n";
		}

		// hack to remove newline from beginning of output, thanks to
		// http://jimbojw.com/wiki/index.php?title=Raw_HTML_Output_from_a_MediaWiki_Parser_Function
		return $parser->insertStripItem( $str, $parser->mStripState );
	}

}