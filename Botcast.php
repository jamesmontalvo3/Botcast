<?php
/** 
 * The MeetingMinutes extension provides JS and CSS to enable recording meeting
 * minutes in SMW. See README.md.
 * 
 * Documentation: https://github.com/enterprisemediawiki/MeetingMinutes
 * Support:       https://github.com/enterprisemediawiki/MeetingMinutes
 * Source code:   https://github.com/enterprisemediawiki/MeetingMinutes
 *
 * @file MeetingMinutes.php
 * @addtogroup Extensions
 * @author James Montalvo
 * @copyright © 2014 by James Montalvo
 * @licence GNU GPL v3+
 */

# Not a valid entry point, skip unless MEDIAWIKI is defined
if ( ! defined( 'MEDIAWIKI' ) ) {
	die( 'MeetingMinutes extension' );
}

$GLOBALS['wgExtensionCredits']['parserhook'][] = array(
	'path'           => __FILE__,
	'name'           => 'MeetingMinutes',
	'url'            => 'http://github.com/enterprisemediawiki/MeetingMinutes',
	'author'         => 'James Montalvo',
	'descriptionmsg' => 'meetingminutes-desc',
	'version'        => '0.2.0'
);

$GLOBALS['wgMessagesDirs']['Botcast'] = __DIR__ . '/i18n';
$GLOBALS['wgExtensionMessagesFiles']['BotcastMagic'] = __DIR__ . '/Magic.php';

// Autoload
$GLOBALS['wgAutoloadClasses']['Botcast\Setup'] = __DIR__ . '/includes/Setup.php';
$GLOBALS['wgAutoloadClasses']['Botcast\ParserFunction'] = __DIR__ . '/includes/ParserFunction.php';
$GLOBALS['wgAutoloadClasses']['Botcast\PushToWiki'] = __DIR__ . '/includes/PushToWiki.php';

// Setup parser functions
$GLOBALS['wgHooks']['ParserFirstCallInit'][] = 'Botcast\Setup::setupParserFunctions';



// $ExtensionMeetingMinutesResourceTemplate = array(
// 	'localBasePath' => __DIR__ . '/modules',
// 	'remoteExtPath' => 'MeetingMinutes/modules',
// );

// $GLOBALS['wgResourceModules'] += array(

// 	'ext.meetingminutes.form' => $ExtensionMeetingMinutesResourceTemplate + array(
// 		'styles' => 'form/meeting-minutes.css',
// 		'scripts' => array( 'form/SF_MultipleInstanceRefire.js', 'form/meeting-minutes.js' ),
// 		// 'dependencies' => array( 'mediawiki.Uri' ),
// 	),

// 	'ext.meetingminutes.template' => $ExtensionMeetingMinutesResourceTemplate + array(
// 		'styles' => 'template/template.css',
// 	),

// );