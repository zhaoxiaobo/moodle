<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * REST web service entry point. The authentication is done via tokens.
 *
 * @package    webservice_rest
 * @copyright  2009 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * NO_DEBUG_DISPLAY - disable moodle specific debug messages and any errors in output
 */
define('NO_DEBUG_DISPLAY', true);

/**
 * NO_MOODLE_COOKIES - no cookies with web service
 */
define('NO_MOODLE_COOKIES', false);

require('../../config.php');
require_once("$CFG->dirroot/webservice/rest/locallib.php");
require_once("$CFG->dirroot/grade/querylib.php");

if (!webservice_protocol_is_enabled('rest')) {
    debugging('The server died because the web services or the REST protocol are not enable',
        DEBUG_DEVELOPER);
    die;
}

$courses = $DB->get_records('course');
foreach($courses as $course) {
    $context = context_course::instance($course->id, IGNORE_MISSING);
    list($enrolledsqlselect, $enrolledparams) = get_enrolled_sql($context);
    $enrolledsql = "SELECT COUNT('x') FROM ($enrolledsqlselect) enrolleduserids";
    $enrolledusercount = $DB->count_records_sql($enrolledsql, $enrolledparams);
    $course->usercount = $enrolledusercount;

    $sql = "select u.id,u.firstname ,u.lastname from {role_assignments} mra
			LEFT JOIN
			{role} r
			on
			mra.roleid = r.id
			LEFT JOIN
			{user} u
			on
			u.id = mra.userid
			where mra.contextid = $context->id
			and r.shortname in ('teacher','editingteacher','coursecreator')
			GROUP BY mra.userid";
            $teachers = $DB->get_records_sql($sql);
   
    $course->teacher = $teachers;
}

echo json_encode($courses);die();


