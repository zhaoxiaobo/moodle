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
 * External functions and service definitions.
 *
 * @package    local_mobile
 * @copyright  2014 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$functions = array(

    'local_founder_moodle_user_get_users_by_courseid' => array(
        'classname'   => 'core_enrol_external',
        'methodname'  => 'get_enrolled_users',
        'classpath'   => 'local/founder/enrol/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_enrol_get_enrolled_users()',
        'type'        => 'read',
        'capabilities'=> 'moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update, moodle/site:accessallgroups',
    ),

    'local_founder_core_enrol_get_enrolled_users' => array(
        'classname'   => 'core_enrol_external',
        'methodname'  => 'get_enrolled_users',
        'classpath'   => 'local/founder/enrol/externallib.php',
        'description' => 'Get enrolled users by course id.',
        'type'        => 'read',
        'capabilities'=> 'moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update, moodle/site:accessallgroups',
    ),


    'local_founder_core_course_get_contents' => array(
        'classname'   => 'core_course_external',
        'methodname'  => 'get_course_contents',
        'classpath'   => 'local/founder/course/externallib.php',
        'description' => 'Get course contents',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:update,moodle/course:viewhiddencourses',
    ),

    'local_founder_core_calendar_get_calendar_events' => array(
        'classname'   => 'core_calendar_external',
        'methodname'  => 'get_calendar_events',
        'description' => 'Get calendar events',
        'classpath'   => 'local/founder/calendar/externallib.php',
        'type'        => 'read',
        'capabilities'=> 'moodle/calendar:manageentries', 'moodle/calendar:manageownentries', 'moodle/calendar:managegroupentries'
    ),

    'local_founder_moodle_enrol_get_users_courses' => array(
        'classname'   => 'core_enrol_external',
        'methodname'  => 'get_users_courses',
        'classpath'   => 'local/founder/enrol/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_enrol_get_users_courses()',
        'type'        => 'read',
        'capabilities'=> 'moodle/course:viewparticipants',
    ),


    'local_founder_moodle_webservice_get_siteinfo' => array(
        'classname'   => 'core_webservice_external',
        'methodname'  => 'get_site_info',
        'classpath'   => 'local/founder/webservice/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has be renamed as core_webservice_get_site_info()',
        'type'        => 'read',
    ),
    'local_founder_get_report_by_course_id' => array(
        'classname' => 'local_report_external',
        'methodname' => 'get_report_by_course_id',
        'classpath' => 'local/founder/report/externallib.php',
        'description' => 'the report of course',
        'type' => 'read',
    ),

    'local_founder_mod_forum_get_forum_discussions' => array(
        'classname'   => 'mod_forum_external',
        'methodname'  => 'get_forum_discussions',
        'classpath'   => 'local/founder/mod/forum/externallib.php',
        'description' => 'Return discussions list',
        'type'        => 'read',
        'capabilities'=> 'moodle/calendar:manageentries', 'mod/forum:viewdiscussion', 'mod/forum:viewqandawithoutposting'
    ),
    'local_founder_mod_forum_get_forum_discussion_posts' => array(
        'classname'   => 'mod_forum_external',
        'methodname'  => 'get_forum_discussion_posts',
        'classpath'   => 'local/founder/mod/forum/externallib.php',
        'description' => 'Return posts list',
        'type'        => 'read',
        'capabilities'=> 'moodle/calendar:manageentries', 'mod/forum:viewdiscussion', 'mod/forum:viewqandawithoutposting'
    ),
    'local_founder_mod_forum_add_forum_discussion_posts' => array(
        'classname'   => 'mod_forum_external',
        'methodname'  => 'add_forum_discussion_posts',
        'classpath'   => 'local/founder/mod/forum/externallib.php',
        'description' => 'Return result of add posts',
        'type'        => 'write',
        'capabilities'=> 'moodle/calendar:manageentries', 'mod/forum:viewdiscussion', 'mod/forum:viewqandawithoutposting'
    ),
    'local_founder_mod_forum_add_forum_discussion_posts' => array(
        'classname'   => 'mod_forum_external',
        'methodname'  => 'add_forum_discussion_posts',
        'classpath'   => 'local/founder/mod/forum/externallib.php',
        'description' => 'Return result of add posts',
        'type'        => 'write',
        'capabilities'=> 'moodle/calendar:manageentries', 'mod/forum:viewdiscussion', 'mod/forum:viewqandawithoutposting'
    ),
    'local_founder_gradereport_user_get_grades_table' => array(
        'classname'   => 'gradereport_user_external',
        'methodname'  => 'get_grades_table',
        'classpath'   => 'local/founder/grade/report/user/externallib.php',
        'description' => 'Return user report',
        'type'        => 'write',
    ),
    'local_founder_core_message_get_messages' => array(
        'classname' => 'core_message_external',
        'methodname' => 'get_messages',
        'classpath' => 'local/founder/message/externallib.php',
        'description' => 'get message contents',
        'type' => 'read',
    ),
    'local_founder_core_message_get_contacts' => array(
        'classname'   => 'core_message_external',
        'methodname'  => 'get_contacts',
        'classpath'   => 'local/founder/message/externallib.php',
        'description' => 'Retrieve the contact list',
        'type'        => 'read',
        'capabilities'=> '',
    ),
    'local_founder_core_message_send_instant_messages' => array(
        'classname'   => 'core_message_external',
        'methodname'  => 'send_instant_messages',
        'classpath'   => 'local/founder/message/externallib.php',
        'description' => 'Send instant messages',
        'type'        => 'write',
        'capabilities'=> 'moodle/site:sendmessage',
    ),
    'local_founder_core_user_get_users_by_id' => array(
        'classname'   => 'core_user_external',
        'methodname'  => 'get_users_by_id',
        'classpath'   => 'local/founder/user/externallib.php',
        'description' => 'DEPRECATED: this deprecated function will be removed in a future version. This function has been replaced by core_user_get_users_by_field()',
        'type'        => 'read',
        'capabilities'=> 'moodle/user:viewdetails, moodle/user:viewhiddendetails, moodle/course:useremail, moodle/user:update',
    ),
    'local_founder_core_user_update_users' => array(
        'classname'   => 'core_user_external',
        'methodname'  => 'update_users',
        'classpath'   => 'local/founder/user/externallib.php',
        'description' => 'Update users.',
        'type'        => 'write',
        'capabilities'=> 'moodle/user:update',
    ),
    'local_founder_get_verification_code' => array(
        'classname'   => 'local_verification_external',
        'methodname'  => 'get_verification_code',
        'classpath'   => 'local/founder/verification/externallib.php',
        'description' => 'Return verification code',
        'type'        => 'write',
    ),
    'local_founder_mod_forum_add_forum_discussion' => array(
        'classname'   => 'mod_forum_external',
        'methodname'  => 'add_forum_discussion',
        'classpath'   => 'local/founder/mod/forum/externallib.php',
        'description' => 'Return result of add discussion',
        'type'        => 'write',
        'capabilities'=> 'moodle/calendar:manageentries', 'mod/forum:viewdiscussion', 'mod/forum:viewqandawithoutposting'
    ),
    'local_founder_gradereport_overview_view_grade_report' => array(
        'classname' => 'gradereport_user_cources_external',
        'methodname' => 'view_grade_report',
        'classpath' => 'local/founder/grade/report/overview/externallib.php',
        'description' => 'Trigger the report view event',
        'type' => 'read',
    ),

);

$services = array(
   'Moodle founder mobile web service'  => array(
        'functions' => array (
            'local_founder_moodle_user_get_users_by_courseid',
            'local_founder_core_course_get_contents',
            'local_founder_core_enrol_get_enrolled_users',
            'local_founder_core_calendar_get_calendar_events',
            'local_founder_moodle_enrol_get_users_courses',
            'local_founder_mod_forum_add_forum_discussion',
            'local_founder_gradereport_overview_view_grade_report',
            'local_founder_get_report_by_course_id',
            'local_founder_mod_forum_get_forum_discussions',
            'local_founder_mod_forum_get_forum_discussion_posts',
            'local_founder_mod_forum_add_forum_discussion_posts',
            'local_founder_mod_forum_add_forum_discussion_posts',
            'local_founder_gradereport_user_get_grades_table',
            'local_founder_core_message_get_messages',
            'local_founder_core_message_get_contacts',
            'local_founder_core_message_send_instant_messages',
            'local_founder_core_user_get_users_by_id',
            'local_founder_core_user_update_users',
            'local_founder_get_verification_code',
            'local_founder_moodle_webservice_get_siteinfo',
        ),
        'enabled' => 0,
        'restrictedusers' => 0,
       'shortname' => "founder_mobile_app",
       'downloadfiles' => 1,
       'uploadfiles' => 1
    ),
);