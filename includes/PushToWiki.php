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

class PushToWiki extends ParserFunction {


	public function __construct ( \Parser &$parser ) {

		parent::__construct(
			$parser,
			'push_to_wiki',
			array(),
			array(
				'wikis'    => '',
				'template' => '',
				'fields'   => ' fields default',
				'values'   => '',
			)
		);

	}

	public function render ( \Parser &$parser, $params ) {
		$wikis = $params['wikis'];
		$templates = $params['template'];
		$fields = $params['fields'];
		$values = $params['values'];

		$output = "this is the push_to_wiki function, which has the current params $wikis, $templates, $fields, $values";

		// $output = print_r( $params, true );

		return $output;
	}

}