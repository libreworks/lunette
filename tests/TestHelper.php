<?php
/**
 * Lunette Platform
 * 
 * Lunette is free software: you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later
 * version.
 *
 * Lunette is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Lunette. If not, see <http://www.gnu.org/licenses/>.
 * 
 * @copyright Copyright (c) SI Tec Consulting, LLC (http://www.sitec-consulting.net)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @category Lunette
 * @package Lunette
 * @subpackage Tests
 * @version $Id$
 */
/*
 * Include PHPUnit dependencies
 */
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/Runner/Version.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Util/Filter.php';

// Set error reporting to the level to which Lunette code must comply
error_reporting( E_ALL | E_STRICT );

// Determine the root, src, and tests directories of the platform distribution
$lunetteRoot    = dirname(dirname(__FILE__));
$lunetteSrc = $lunetteRoot . DIRECTORY_SEPARATOR . 'src';
$lunetteTests   = $lunetteRoot . DIRECTORY_SEPARATOR . 'tests';

/*
 * Omit from code coverage reports the contents of the tests directory
 */
foreach (array('php', 'phtml', 'csv') as $suffix) {
    PHPUnit_Util_Filter::addDirectoryToFilter($lunetteTests, ".$suffix");
}

/*
 * Prepend the Lunette src/ and tests/ directories to the include_path.
 * This allows the tests to run out of the box and helps prevent loading other
 * copies of the platform code and tests that would supersede this copy.
 */
$path = array(
    $lunetteSrc,
    $lunetteSrc . DIRECTORY_SEPARATOR . 'library',
    $lunetteTests,
    $lunetteTests . DIRECTORY_SEPARATOR . 'library', 
    get_include_path()
    );
set_include_path(implode(PATH_SEPARATOR, $path));

/*
 * Load the user-defined test configuration file, if it exists; otherwise, load
 * the default configuration.
 */
$userConfig = $lunetteTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php';
if (is_readable($userConfig)) {
    require_once $userConfig;
} else {
    require_once $userConfig . '.dist';
}

/*
 * Add Lunette src/ directory to the PHPUnit code coverage whitelist.
 * This has the effect that only production code source files appear
 * in the code coverage report and that all production code source files, even
 * those that are not covered by a test yet, are processed.
 */
if (defined('TESTS_GENERATE_REPORT') && TESTS_GENERATE_REPORT === true &&
    version_compare(PHPUnit_Runner_Version::id(), '3.1.6', '>=')) {
    PHPUnit_Util_Filter::addDirectoryToWhitelist($lunetteSrc);
}

/*
 *  Unset global variables that are no longer needed
 */
unset($lunetteRoot, $lunetteSrc, $lunetteTests, $path, $userConfig);
