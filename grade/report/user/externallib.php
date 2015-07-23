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
class gradereport_user_external extends external_api {

    /**
     * Describes the parameters for get_grades_table.
     *
     * @return external_external_function_parameters
     * @since Moodle 2.9
     */
    public static function get_grades_table_parameters() {
        return new external_function_parameters (
            array(
                'courseid' => new external_value(PARAM_INT, 'Course Id', VALUE_REQUIRED),
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
    public static function get_grades_table($courseid, $userid = 0) {
        global $CFG, $USER;

        $warnings = array();

        // Validate the parameter.
        $params = self::validate_parameters(self::get_grades_table_parameters(),
            array(
                'courseid' => $courseid,
                'userid' => $userid)
            );

        // Compact/extract functions are not recommended.
        $courseid = $params['courseid'];
        $userid   = $params['userid'];

        // Function get_course internally throws an exception if the course doesn't exist.
        $course = get_course($courseid);

        $context = context_course::instance($courseid);
        self::validate_context($context);

        // Specific capabilities.
        require_capability('gradereport/user:view', $context);

        $user = null;

        if (empty($userid)) {
            require_capability('moodle/grade:viewall', $context);
        } else {
            $user = core_user::get_user($userid, '*', MUST_EXIST);
        }

        $access = false;

        if (has_capability('moodle/grade:viewall', $context)) {
            // Can view all course grades.
            $access = true;
        } else if ($userid == $USER->id and has_capability('moodle/grade:view', $context) and $course->showgrades) {
            // View own grades.
            $access = true;
        }

        if (!$access) {
            throw new moodle_exception('nopermissiontoviewgrades', 'error');
        }

        // Require files here to save some memory in case validation fails.
        require_once($CFG->dirroot . '/group/lib.php');
        require_once($CFG->libdir  . '/gradelib.php');
        require_once($CFG->dirroot . '/grade/lib.php');
        require_once($CFG->dirroot . '/grade/report/user/lib.php');

        $gpr = new grade_plugin_return(
            array(
                'type' => 'report',
                'plugin' => 'user',
                'courseid' => $courseid,
                'userid' => $userid)
            );

        $tables = array();        
        // Just one user.
        if ($user) {
            $report = new grade_report_user($courseid, $gpr, $context, $userid);
            $report->fill_table();

            $report_count = count($report->tabledata);
            //add by zxb 对数据进行处理
            $grade_info_array = array();
            $count = 0;
            $couse_name = "";
            foreach ($report->tabledata as $key => $value) {
                if(isset($value["leader"])){
                    $couse_name_array = explode('/>',$value["itemname"]["content"]);
                    if(isset($couse_name_array[1])){
                        $couse_name = $couse_name_array[1];
                    }
                }else{
                    //$content_name_array = explode('/>',$value["itemname"]["content"]);
                    if($count == $report_count-2)
                        continue;
                    preg_match_all("/\/>(.*)/i",$value["itemname"]["content"],$arr);
                    $grade_info_array[$count]["modname"] = str_replace('</a>','',$arr[1][0]);
                    $grade_info_array[$count]["grade"] = $value["grade"]["content"];
                    $grade_info_array[$count]["range"] = $value["range"]["content"];
                    $grade_info_array[$count]["percentage"] = $value["percentage"]["content"];
                    $grade_info_array[$count]["feedback"] = $value["feedback"]["content"];   
                    $count++;              
                }
            }
            $tables[] = array(
                'courseid'      => $courseid,
                'userid'        => $user->id,
                'userfullname'  => fullname($user),
                'maxdepth'      => $report->maxdepth,
                'cousename'      => $couse_name,
                'tabledata'     => $grade_info_array
            );

        } else {
            $defaultgradeshowactiveenrol = !empty($CFG->grade_report_showonlyactiveenrol);
            $showonlyactiveenrol = get_user_preferences('grade_report_showonlyactiveenrol', $defaultgradeshowactiveenrol);
            $showonlyactiveenrol = $showonlyactiveenrol || !has_capability('moodle/course:viewsuspendedusers', $context);

            $gui = new graded_users_iterator($course);
            $gui->require_active_enrolment($showonlyactiveenrol);
            $gui->init();

            while ($userdata = $gui->next_user()) {
                $currentuser = $userdata->user;
                $report = new grade_report_user($courseid, $gpr, $context, $currentuser->id);
                $report->fill_table();
                $report_count = count($report->tabledata);
                //add by zxb 对数据进行处理
                $grade_info_array = array();
                $count = 0;
                $couse_name = "";
                foreach ($report->tabledata as $key => $value) {
                    if(isset($value["leader"])){
                        $couse_name_array = explode('/>',$value["itemname"]["content"]);
                        if(isset($couse_name_array[1])){
                            $couse_name = $couse_name_array[1];
                        }
                    }else{
                        //$content_name_array = explode('/>',$value["itemname"]["content"]);
                        if($count == $report_count-2)
                            continue;
                        preg_match_all("/\/>(.*)/i",$value["itemname"]["content"],$arr);
                        $grade_info_array[$count]["modname"] = str_replace('</a>','',$arr[1][0]);
                        $grade_info_array[$count]["grade"] = $value["grade"]["content"];
                        $grade_info_array[$count]["range"] = $value["range"]["content"];
                        $grade_info_array[$count]["percentage"] = $value["percentage"]["content"];
                        $grade_info_array[$count]["feedback"] = $value["feedback"]["content"];   
                        $count++;              
                    }
                }

                $tables[] = array(
                    'courseid'      => $courseid,
                    'userid'        => $currentuser->id,
                    'userfullname'  => fullname($currentuser),
                    'maxdepth'      => $report->maxdepth,
                    'cousename'     => $couse_name,
                    'tabledata'     => $grade_info_array
                    
                );
            }
            $gui->close();
        }

        $result = array();
        $result['data'] = $tables;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Creates a table column structure
     *
     * @return array
     * @since  Moodle 2.9
     */
    private static function grades_table_column() {
        return array (
            'class'   => new external_value(PARAM_RAW, 'class'),
            'content' => new external_value(PARAM_RAW, 'cell content'),
            'headers' => new external_value(PARAM_RAW, 'headers')
        );
    }

    /**
     * Describes tget_grades_table return value.
     *
     * @return external_single_structure
     * @since Moodle 2.9
     */
    public static function get_grades_table_returns() {
        return new external_single_structure(
            array(
                'data' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'courseid' => new external_value(PARAM_INT, 'course id'),
                            'userid'   => new external_value(PARAM_INT, 'user id'),
                            'userfullname' => new external_value(PARAM_TEXT, 'user fullname'),
                            'maxdepth'   => new external_value(PARAM_INT, 'table max depth (needed for printing it)'),
                            'cousename' => new external_value(PARAM_TEXT, 'course name'),
                            // 'tabledata' => new external_multiple_structure(
                            //     new external_single_structure(
                            //         array(
                            //             'modname' => new external_value(PARAM_RAW, 'modname'),
                            //             'grade' => new external_single_structure(PARAM_RAW, 'grade'),
                            //             'range' => new external_single_structure(PARAM_RAW, 'range'),
                            //             'percentage' => new external_single_structure(PARAM_RAW, 'percentage'),
                            //             'feedback' => new external_single_structure(PARAM_RAW, 'feedback'),
                            //         ), 'table'
                            //     )
                            // )
                            // 
                            'tabledata' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'modname' => new external_value(PARAM_RAW, 'modname'),       
                                        'grade' => new external_value(PARAM_TEXT, 'grade'),  
                                        'range' => new external_value(PARAM_TEXT, 'range'),
                                        'percentage' => new external_value(PARAM_TEXT, 'percentage'),
                                        'feedback' => new external_value(PARAM_RAW, 'feedback'),                              
                                    ), 'table'
                                )
                            )
                        )
                    )
                ),
                'warnings' => new external_warnings()
            )
        );
    }
}
