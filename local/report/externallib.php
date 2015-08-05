<?php
require_once($CFG->libdir . "/externallib.php");

class local_report_external extends external_api {
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_report_by_course_id_parameters() {
        return new external_function_parameters(
                array('courseid' => new external_value(PARAM_INT, 'the id of course')
                    )
        );
    }

    /**
     * Returns course code
     * @return array course info
     */
    public static function get_report_by_course_id($courseid) {
        global $USER,$DB,$CFG;
        require_once($CFG->dirroot.'/report/outline/locallib.php');

        $params = self::validate_parameters(self::get_report_by_course_id_parameters(),
                array('courseid' => $courseid));
                $course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
        require_login($course);
        $context = context_course::instance($course->id);
        require_capability('report/outline:view', $context);
        $showlastaccess = true;
        $hiddenfields = explode(',', $CFG->hiddenuserfields);

        if (array_search('lastaccess', $hiddenfields) !== false and !has_capability('moodle/user:viewhiddendetails', $context)) {
            $showlastaccess = false;
        }
        if (!$logstart = $DB->get_field_sql("SELECT MIN(time) FROM {log}")) {
            print_error('logfilenotavailable');
        }

        $outlinetable = new html_table();
        $result = array();
        $modinfo = get_fast_modinfo($course);

        $sql = "SELECT cm.id, COUNT('x') AS numviews, MAX(time) AS lasttime
                  FROM {course_modules} cm
                       JOIN {modules} m ON m.id = cm.module
                       JOIN {log} l     ON l.cmid = cm.id
                 WHERE cm.course = ? AND l.action LIKE 'view%' AND m.visible = 1
              GROUP BY cm.id";
        $views = $DB->get_records_sql($sql, array($course->id));
        $count = 0;
        $prevsecctionnum = 0;
        foreach ($modinfo->sections as $sectionnum=>$section) {
            foreach ($section as $cmid) {
                $cm = $modinfo->cms[$cmid];
                if (!$cm->has_view()) {
                    continue;
                }
                if (!$cm->uservisible) {
                    continue;
                }
                if ($prevsecctionnum != $sectionnum) {
                    $sectioncell = new html_table_cell();
                    $sectiontitle = get_section_name($course, $sectionnum);

                    $result[$sectionnum]["text"] = $sectiontitle;
                }
                else{
                    $result[$sectionnum]["text"] = "常规";
                }
                $dimmed = $cm->visible ? '' : 'class="dimmed"';
                $modulename = get_string('modulename', $cm->modname);
                $reportrow = new html_table_row();
                $activitycell = new html_table_cell();
                $activitycell->attributes['class'] = 'activity';
                
                $attributes = array();
                if (!$cm->visible) {
                    $attributes['class'] = 'dimmed';
                }
                $activitycell->text = $cm->name;
                $reportrow->cells[] = $activitycell;

                $result[$sectionnum]["data"][$count]["text"] = $activitycell -> text;

                $numviewscell = new html_table_cell();
                $numviewscell->attributes['class'] = 'numviews';
                if (!empty($views[$cm->id]->numviews)) {
                    $numviewscell->text = $views[$cm->id]->numviews;
                } else {
                    $numviewscell->text = '-';
                }
                $reportrow->cells[] = $numviewscell;

                $result[$sectionnum]["data"][$count]["numviews"] = $numviewscell -> text;

                if ($CFG->useblogassociations) {
                    require_once($CFG->dirroot.'/blog/lib.php');
                    $blogcell = new html_table_cell();
                    $blogcell->attributes['class'] = 'blog';
                    if ($blogcount = blog_get_associated_count($course->id, $cm->id)) {
                        $blogurl = new moodle_url('/blog/index.php', array('modid' => $cm->id));
                        $blogcell->text = html_writer::link($blogurl, $blogcount);
                    } else {
                        $blogcell->text = '-';
                    }
                    $reportrow->cells[] = $blogcell;

                    $result[$sectionnum]["data"][$count]["blog"] = $blogcell -> text;
                }
                if ($showlastaccess) {
                    $lastaccesscell = new html_table_cell();
                    $lastaccesscell->attributes['class'] = 'lastaccess';
                    if (isset($views[$cm->id]->lasttime)) {
                        $timeago = format_time(time() - $views[$cm->id]->lasttime);
                        $lastaccesscell->text = userdate($views[$cm->id]->lasttime)." ($timeago)";
                    }
                    $reportrow->cells[] = $lastaccesscell;

                    $result[$sectionnum]["data"][$count]["lastaccess"] = $lastaccesscell -> text;
                }
            $count++;
            }
        }

// print_r($result);
//         $result1 = array();
//         $result1[0]["id"]=$courseid;
//         $result1[1]["id"]='2';
        return $result;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_report_by_course_id_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'text' => new external_value(PARAM_RAW, 'title'),
                    'data' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'text' => new external_value(PARAM_RAW, 'content info'),
                                'numviews' => new external_value(PARAM_RAW, 'numviews info'),
                                'blog' => new external_value(PARAM_RAW, 'blog info'),
                                'lastaccess' => new external_value(PARAM_TEXT, 'lastaccess info'),
                            )
                        )
                    )
                )
             ) 
        );   
    }
}