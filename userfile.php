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
 * This script delegates file serving to individual plugins
 *
 * @package    core
 * @subpackage file
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Disable moodle specific debug messages and any errors in output.
define('NO_DEBUG_DISPLAY', true);

require_once('config.php');
require_once('lib/filelib.php');

$relativepath = get_file_argument();
$forcedownload = optional_param('forcedownload', 0, PARAM_BOOL);
$preview = optional_param('preview', null, PARAM_ALPHANUM);

//file_pluginfile($relativepath, $forcedownload, $preview);
global $DB, $CFG, $USER;
// relative path must start with '/'
if (!$relativepath) {
    print_error('invalidargorconf');
} else if ($relativepath[0] != '/') {
    print_error('pathdoesnotstartslash');
}

// extract relative path components
$args = explode('/', ltrim($relativepath, '/'));

if (count($args) < 3) { // always at least context, component and filearea
    print_error('invalidarguments');
}

$contextid = (int)array_shift($args);
$context = context_user::instance($contextid, IGNORE_MISSING);
$contextid = $context->id;
$component = clean_param(array_shift($args), PARAM_COMPONENT);
$filearea  = clean_param(array_shift($args), PARAM_AREA);

list($context, $course, $cm) = get_context_info_array($contextid);

$fs = get_file_storage();
if (count($args) == 1) {
    $themename = theme_config::DEFAULT_THEME;
    $filename = array_shift($args);
} else {
    $themename = array_shift($args);
    $filename = array_shift($args);
}

// fix file name automatically
if ($filename !== 'f1' and $filename !== 'f2' and $filename !== 'f3') {
    $filename = 'f1';
}

if ((!empty($CFG->forcelogin) and !isloggedin()) ||
    (!empty($CFG->forceloginforprofileimage) && (!isloggedin() || isguestuser()))) {
    // protect images if login required and not logged in;
    // also if login is required for profile images and is not logged in or guest
    // do not use require_login() because it is expensive and not suitable here anyway
    $theme = theme_config::load($themename);
    redirect($theme->pix_url('u/'.$filename, 'moodle')); // intentionally not cached
}

if (!$file = $fs->get_file($context->id, 'user', 'icon', 0, '/', $filename.'.png')) {
    if (!$file = $fs->get_file($context->id, 'user', 'icon', 0, '/', $filename.'.jpg')) {
        if ($filename === 'f3') {
            // f3 512x512px was introduced in 2.3, there might be only the smaller version.
            if (!$file = $fs->get_file($context->id, 'user', 'icon', 0, '/', 'f1.png')) {
                $file = $fs->get_file($context->id, 'user', 'icon', 0, '/', 'f1.jpg');
            }
        }
    }
}
if (!$file) {
    // bad reference - try to prevent future retries as hard as possible!
    if ($user = $DB->get_record('user', array('id'=>$context->instanceid), 'id, picture')) {
        if ($user->picture > 0) {
            $DB->set_field('user', 'picture', 0, array('id'=>$user->id));
        }
    }
    // no redirect here because it is not cached
    $theme = theme_config::load($themename);
    $imagefile = $theme->resolve_image_location('u/'.$filename, 'moodle', null);
    send_file($imagefile, basename($imagefile), 60*60*24*14);
}

send_stored_file($file, 60*60*24*365, 0, false, array('preview' => $preview)); // enable long caching, there are many images on each page

