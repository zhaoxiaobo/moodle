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
 * Display user activity reports for a course (totals)
 *
 * @package    report
 * @subpackage outline
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/report/outline/locallib.php');

$id = required_param('id',PARAM_INT);       // course id

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);



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
            $result[$sectionnum]["text"] = $OUTPUT->heading($sectiontitle, 3);
        }
        else{
            $result[$sectionnum]["text"] = "常规";
        }
        $dimmed = $cm->visible ? '' : 'class="dimmed"';
        $modulename = get_string('modulename', $cm->modname);
        $reportrow = new html_table_row();
        $activitycell = new html_table_cell();
        $activitycell->attributes['class'] = 'activity';
        $activityicon = $OUTPUT->pix_icon('icon', $modulename, $cm->modname, array('class'=>'icon'));
        $attributes = array();
        if (!$cm->visible) {
            $attributes['class'] = 'dimmed';
        }
        $activitycell->text = $activityicon . html_writer::link("$CFG->wwwroot/mod/$cm->modname/view.php?id=$cm->id", format_string($cm->name), $attributes);
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
print_r($result);



