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
 * External grade report user API
 *
 * @package    gradereport_user
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");



/**
 * External grade report API implementation
 *
 * @package    gradereport_user
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @category   external
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradereport_user_cources_external extends external_api {

    /**
     * Describes the parameters for get_grades_table.
     *
     * @return external_external_function_parameters
     * @since Moodle 2.9
     */
    public static function view_grade_report_parameters() {
        return new external_function_parameters (
            array(
                'userid'   => new external_value(PARAM_INT, 'Return grades only for this user (optional)', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Returns a list of grades tables for users in a course.
     *
     * @param int $courseid Course Id
     * @param int $userid   Only this user (optional)
     *
     * @return array the grades tables
     * @since Moodle 2.9
     */
    public static function view_grade_report($userid) {
        global $DB, $USER;



        // Validate the parameter.
        $params = self::validate_parameters(self::view_grade_report_parameters(),
            array(
                'userid' => $userid)
            );

        // Compact/extract functions are not recommended.
        //$userid   = $params['userid'];
        $result = array();
        // Function get_course internally throws an exception if the course doesn't exist.
       // $courses = get_course($courseid);

        if($params['userid'] == $USER->id) {
            //$courses = enrol_get_users_courses($userid);
            $courses = enrol_get_users_courses($params['userid'], true, 'id, shortname, fullname, summary,idnumber, visible');


            foreach($courses as $course) {


                $sql = "SELECT gg.finalgrade FROM {grade_grades} gg ,{grade_items} gi WHERE gi.itemtype='course' AND gi.courseid = $course->id AND gi.id = gg.itemid";
                $grade = $DB->get_record_sql($sql);
                if(!$grade) {
                    $grade->finalgrade = null;
                }
                $result[] = array(
                    'courseid'=>$course->id,
                    'fullname'=>$course->fullname,
                    'finalgrade'=>$grade->finalgrade
                );
            }
        } else {
            throw new moodle_exception("用户ID错误！");
        }




        return $result;
    }



    /**
     * Describes tget_grades_table return value.
     *
     * @return external_single_structure
     * @since Moodle 2.9
     */
    public static function view_grade_report_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'courseid' => new external_value(PARAM_INT, 'course id'),
                    'fullname' => new external_value(PARAM_RAW, 'course fullname'),
                    'finalgrade'   => new external_value(PARAM_RAW, 'course finalgrade')
                )
            )
        );


    }
}
