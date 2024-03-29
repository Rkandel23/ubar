<?php
/**
 * Initialize the Ubar framework. This is comprised of getting an instance of
 * Properties using ubar_config.properties, defining application constants,
 * creating a new instance of the Dispatcher and dispatching.
 *
 * @author		Joshua A. Ganderson <jag@josh.com>
 * @link		http://www.holisticmonkey.com/Framework.action
 * @copyright	Copyright (c) 2010, Joshua A. Ganderson
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License v3
 * @package		core
 * @subpackage	setup
 *
 */

/**
 * Global that stores all application constants. Using this instead of actual
 * constants due to lack of sandboxing in phpunit tests.
 */
global $UBAR_GLOB;
if(!isset($UBAR_GLOB)) {
	$UBAR_GLOB = array();
}

/**
 * Define the root of the framework as the current folder.
 */
if(!isset($UBAR_GLOB['UBAR_ROOT'])) {
	$UBAR_GLOB['UBAR_ROOT'] = dirname(__FILE__) . "/";
}

/**
 * Define the library folder location. This is a helper for calls to other
 * libray folders.
 */
$UBAR_GLOB['LIB_ROOT'] = $UBAR_GLOB['UBAR_ROOT'] . "../";

/**
 * Require necessary application functions.
 * TODO: When more function files, require the entire folder recursively.
 */
require_once($UBAR_GLOB['UBAR_ROOT'] . "/functions/misc.php");

/**
 * Add the constants, exception, and core folders recursively
 */
getClassPaths($UBAR_GLOB['UBAR_ROOT'] . "constants", TRUE);
getClassPaths($UBAR_GLOB['UBAR_ROOT'] . "exception", TRUE);
getClassPaths($UBAR_GLOB['UBAR_ROOT'] . "core", TRUE);

/**
 * Allow recognition of all line ending types, required to recognize mac
 * property files
 */
ini_set("auto_detect_line_endings", true);

/**
 * Get an instance of the config properties. Allow path to be overridden for
 * testing purposes.
 */
if(isset($UBAR_GLOB['UBAR_CONFIG_OVERRIDE'])) {
	$props = new Properties($UBAR_GLOB['UBAR_CONFIG_OVERRIDE'], true);
} else {
	$props = new Properties($UBAR_GLOB['UBAR_ROOT'] . "ubar_config.properties", true);
}

# DEFINE CONSTANTS FROM PROPERTIES FILE
/**
 * Define DEV_MODE, defaulting to the value found in DEV_MODE if not set.
 * NOTE: This must be defined prior to other defines due to their values
 * switching on DEV_MODE
 *
 * NOTE: Some test classes may define this before init script loaded.
 *
 * @see GlobalConstants::DEV_MODE
 */
if(!isset($UBAR_GLOB['DEV_MODE'])) {
	$UBAR_GLOB['DEV_MODE'] = $props->getBool('DEV_MODE', GlobalConstants :: DEV_MODE);
}

/**
 * Define a property appender based on dev mode to simplify the dev mode value
 * retrieval
 */
$UBAR_GLOB['PROP_APPEND'] = $UBAR_GLOB['DEV_MODE'] ? '_DEV_MODE' : '';

/**
 * Define the default locale.
 * @see GlobalConstants::LOCALE_DEFAULT
 */
$UBAR_GLOB['LOCALE_DEFAULT'] = $props->get('LOCALE_DEFAULT', GlobalConstants :: LOCALE_DEFAULT);

/**
 * Define display errors flag.
 * @see GlobalConstants::DISPLAY_ERRORS
 */
$UBAR_GLOB['DISPLAY_ERRORS'] = $props->getBool('DISPLAY_ERRORS', GlobalConstants :: DISPLAY_ERRORS);

/**
 * Define display html errors (vs plain text).
 * @see GlobalConstants::HTML_ERRORS
 */
$UBAR_GLOB['HTML_ERRORS'] = $props->getBool('HTML_ERRORS', GlobalConstants :: HTML_ERRORS);

/**
 * Define error display level. Currently unused.
 * @see GlobalConstants::ERROR_LEVEL
 *
 * @todo switch to int values and make use of this
 */
$UBAR_GLOB['ERROR_LEVEL'] = $props->get('ERROR_LEVEL' . $UBAR_GLOB['PROP_APPEND'], GlobalConstants :: ERROR_LEVEL);

/**
 * Define log errors flag. Currently unused.
 * @see GlobalConstants::LOG_ERRORS
 */
$UBAR_GLOB['LOG_ERRORS'] = $props->getBool('LOG_ERRORS' . $UBAR_GLOB['PROP_APPEND'], GlobalConstants :: LOG_ERRORS);

/**
 * Define magic quotes flag. Currently unused.
 * @see GlobalConstants::MAGIC_QUOTES
 */
$UBAR_GLOB['MAGIC_QUOTES'] = $props->getBool('MAGIC_QUOTES', GlobalConstants :: MAGIC_QUOTES);

/**
 * Define session lifetime in seconds.
 * @see GlobalConstants::SESSION_LIFETIME
 */
$UBAR_GLOB['SESSION_LIFETIME'] = $props->get('SESSION_LIFETIME', GlobalConstants :: SESSION_LIFETIME);

/**
 * Define default charset.
 * @see GlobalConstants::CHARSET
 */
$UBAR_GLOB['CHARSET'] = $props->get('CHARSET', GlobalConstants :: CHARSET);

/**
 * Define xhtml (vs html) flag.
 * @see GlobalConstants::USE_XHTML
 */
$UBAR_GLOB['USE_XHTML'] = $props->getBool('USE_XHTML', GlobalConstants :: USE_XHTML);

/**
 * Define path to properties folder relative to UBAR_ROOT.
 * @see GlobalConstants::BASE_PROPERTIES_PATH
 */
$UBAR_GLOB['PROPERTIES_PATH'] = $UBAR_GLOB['UBAR_ROOT'] . "/" . $props->get('PROPERTIES_PATH' . $UBAR_GLOB['PROP_APPEND'], GlobalConstants :: BASE_PROPERTIES_PATH);

/**
 * Define path to properties root name.
 * @see GlobalConstants::PROPERTIES_ROOT
 */
$UBAR_GLOB['PROPERTIES_ROOT'] = $props->get('PROPERTIES_ROOT', GlobalConstants :: PROPERTIES_ROOT);

/**
 * Define path to action folder. It is not recommended that you alter this.

 * @see GlobalConstants::BASE_ACTION_PATH
 */
$UBAR_GLOB['BASE_ACTION_PATH'] = $UBAR_GLOB['UBAR_ROOT'] . "/" . $props->get('BASE_ACTION_PATH' . $UBAR_GLOB['PROP_APPEND'], GlobalConstants :: BASE_ACTION_PATH);
if (!is_dir( $UBAR_GLOB['BASE_ACTION_PATH'])) {
	throw new Exception("Unable to find specified action root path at \"" . $UBAR_GLOB['BASE_ACTION_PATH'] . "\".");
}
getClassPaths($UBAR_GLOB['BASE_ACTION_PATH'], TRUE);

/**
 * Define path to view folder. It is not recommended that you alter this.

 * @see GlobalConstants::BASE_VIEW_PATH
 */
$UBAR_GLOB['BASE_VIEW_PATH'] = $UBAR_GLOB['UBAR_ROOT'] . "/" . $props->get('BASE_VIEW_PATH' . $UBAR_GLOB['PROP_APPEND'], GlobalConstants :: BASE_VIEW_PATH);
if (!is_dir($UBAR_GLOB['BASE_VIEW_PATH'])) {
	throw new Exception("Unable to find specified view root path at \"" . $UBAR_GLOB['BASE_VIEW_PATH'] . "\".");
}

/**
 * Define path to model folder. This is an optional convenience for autoloading
 * model classes.
 *
 * @todo Consider just having an autoload directory or easier addition of autoload folders.

 * @see GlobalConstants::BASE_MODEL_PATH
 */
$UBAR_GLOB['BASE_MODEL_PATH'] = $UBAR_GLOB['UBAR_ROOT'] . "/" . $props->get('BASE_MODEL_PATH' . $UBAR_GLOB['PROP_APPEND'], GlobalConstants :: BASE_MODEL_PATH);
if (is_dir($UBAR_GLOB['BASE_MODEL_PATH'])) {
	getClassPaths($UBAR_GLOB['BASE_MODEL_PATH'], TRUE);
}

// define default timezone
try {
	/**
	 * Define default timezone. If not defined, it will throw an exception that is ignored.
	 */
	$UBAR_GLOB['TIMEZONE_DEFAULT'] = $props->get('TIMEZONE_DEFAULT');
} catch (Exception $e) {
	// none defined, skip
}

# DEFINE DB SETTINGS
/**
 * Define flag for database usage. You may obviously use database connectivity
 * without this, this merely indicates an intention to use the built in
 * DBManager and errors will be thrown if configuration does not support
 * database connectivity.
 *
 * @see GlobalConstants::DB_USE
 */
$UBAR_GLOB['DB_USE'] = $props->getBool('DB_USE', GlobalConstants :: DB_USE);
if ($UBAR_GLOB['DB_USE']) {
	try {
		/**
		 * Define database server name.
		 */
		$UBAR_GLOB['DB_SERVER'] = $props->get('DB_SERVER' . $UBAR_GLOB['PROP_APPEND']);
		/**
		 * Define database username.
		 */
		$UBAR_GLOB['DB_USERNAME'] = $props->get('DB_USERNAME' . $UBAR_GLOB['PROP_APPEND']);
		/**
		 * Define database password for user.
		 */
		$UBAR_GLOB['DB_PASSWORD'] = $props->get('DB_PASSWORD' . $UBAR_GLOB['PROP_APPEND']);
		/**
		 * Define default database to connect to.
		 */
		$UBAR_GLOB['DB_NAME'] = $props->get('DB_NAME' . $UBAR_GLOB['PROP_APPEND']);
	} catch (Exception $e) {
		throw new Exception("This application is configured to connect to a database but one or more required configurations was missing");
	}
	try {
		/**
		 * Define current schema version, used for automatic schema migration.
		 */
		$UBAR_GLOB['SCHEMA_VERSION'] = $props->get('SCHEMA_VERSION');
		/**
		 * Define schema naming convention for .sql files used for automatic
		 * schema migration.
		 */
		$UBAR_GLOB['SCHEMA_PATH'] = $props->get('SCHEMA_PATH');
	} catch (Exception $e) {
		// values allowed to be undefined
	}
}

# CONFIGURE APPLICATION BASED ON PROPERTIES

/**
 * Set whether html errors is on.
 */
ini_set('html_errors', $UBAR_GLOB['HTML_ERRORS'] ? GLobalConstants :: INI_OFF : GLobalConstants :: INI_ON);

/**
 * Set whether errors are displayed or not.
 */
ini_set('display_errors', ($UBAR_GLOB['DEV_MODE'] || $UBAR_GLOB['DISPLAY_ERRORS']) ? GLobalConstants :: INI_ON : GLobalConstants :: INI_OFF);

/**
 * Set error reporting level. Note that values in the properties file are not
 * used at this time since they return string values.
 *
 * @todo Use values from properties, either with ints or converting to ints.
 * @link http://www.php.net/manual/en/errorfunc.configuration.php#ini.error-reporting Error Reporting
 */
ini_set('error_reporting', E_ALL | E_STRICT);

/**
 * Set custom error handlers. Currently there are no provisions for user
 * override.
 *
 * @todo Investigate all error conditions.
 */
if (function_exists("errorHandler")) {
	set_error_handler("errorHandler", E_ALL | E_STRICT);
}
if (function_exists("exceptionHandler")) {
	set_exception_handler("exceptionHandler");
}

/**
 * @todo Set cache headers here.
 */

/**
 * Set contitional headers for the use of xhtml. Note that these headers will
 * not be used if it is not supported.
 */
$contentType = "text/html";
if ($UBAR_GLOB['USE_XHTML'] && stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
	$contentType = 'application/xhtml+xml';
}
header("Content-Type: $contentType;charset=" . $UBAR_GLOB['CHARSET']);

/**
 * Set session lifetime.
 */
ini_set('session.gc_maxlifetime', $UBAR_GLOB['SESSION_LIFETIME']);

/**
 * Initialize the session. It may be that this needs to occur prior to this.
 */
session_start();

/**
 * Set the timezone.
 */
if (isset($UBAR_GLOB['TIMEZONE_DEFAULT'])) {
	date_default_timezone_set($UBAR_GLOB['TIMEZONE_DEFAULT']);
}

/**
 * Set the locale. Note that this has limited impact on message formatting at
 * this time. It will however influence a variety of framework elements
 * in the future.
 *
 * @link http://us.php.net/setlocale Locale Info
 */
$localeNameArray = $UBAR_GLOB['LOCALE_DEFAULT'];
$currentLocale = setlocale(LC_ALL, explode(",", $localeNameArray));
$UBAR_GLOB['LOCALE'] = $currentLocale;

/**
 * Allow gzip compression
 */
ini_set('zlib.output_compression', 1);
?>