<?php
/*
 * Copyright 2020 New Relic Corporation. All rights reserved.
 * SPDX-License-Identifier: Apache-2.0
 */

/*DESCRIPTION
The agent treats bad input to newrelic_get_browser_timing_footer() the same as
newrelic_get_browser_timing_footer(true). This is skipped on PHP 8+ because this
will now throw an TypeError instead of a warning. Since that is a fatal error,
this test is invalid and not needed in php 8+.
*/

/*SKIPIF
<?php
if (version_compare(PHP_VERSION, "7.4", ">")) {
  die("skip: PHP > 7.4.0 not supported\n");
}
*/

/*EXPECT
ok - starts with script tag
ok - ends with script tag
ok - content looks like RUM loader
ok - subsequent calls return empty string
*/

require_once(realpath (dirname ( __FILE__ )) . '/../../../include/tap.php');

define('OPENTAG', '<script type="text/javascript">');
define('CLOSETAG', '</script>');

/* Need to get the header before we can get the footer. */
newrelic_get_browser_timing_header(true);

$header = @newrelic_get_browser_timing_footer(array());   /* suppress warnings */
$prefix = substr($header, 0, strlen(OPENTAG));
$suffix = substr($header, -strlen(CLOSETAG));

tap_equal(OPENTAG,  $prefix, 'starts with script tag');
tap_equal(CLOSETAG, $suffix, 'ends with script tag');

tap_not_equal(false, strpos($header, 'NREUM'), 'content looks like RUM loader');

$header2 = newrelic_get_browser_timing_footer(true);
tap_equal('', $header2, 'subsequent calls return empty string');
