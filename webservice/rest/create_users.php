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

require_once($CFG->dirroot."/lib/weblib.php");
require_once($CFG->dirroot."/user/lib.php");
require_once($CFG->dirroot."/user/profile/lib.php"); //required for customfields related function



// Do basic automatic PARAM checks on incoming data, using params description
// If any problems are found then exceptions are thrown with helpful error messages
//$params = self::validate_parameters(self::create_users_parameters(), array('users'=>$users));
$user = $_REQUEST;

/*$user['username']= '11111';
$user['password']= "mima@d349W";
$user['firstname']= "ceshi";
$user['lastname']= 'ceshi';
$user['email']= 'ceshi@founder.com';
$user['mnethostid'] = 0;
$user['idnumber']= 'teacher';*/

$availableauths  = core_component::get_plugin_list('auth');
unset($availableauths['mnet']);       // these would need mnethostid too
unset($availableauths['webservice']); // we do not want new webservice users for now

$availablethemes = core_component::get_plugin_list('theme');
$availablelangs  = get_string_manager()->get_list_of_translations();

$transaction = $DB->start_delegated_transaction();

$userids = array();

    // Make sure that the username doesn't already exist
    if ($DB->record_exists('user', array('username'=>$user['username'], 'mnethostid'=>$CFG->mnet_localhost_id))) {
        throw new invalid_parameter_exception('Username already exists: '.$user['username']);
    }


    // Make sure lang is valid
    if (!empty($user['theme']) && empty($availablethemes[$user['theme']])) { //theme is VALUE_OPTIONAL,
        // so no default value.
        // We need to test if the client sent it
        // => !empty($user['theme'])


        throw new invalid_parameter_exception('Invalid theme: '.$user['theme']);
    }

    $user['confirmed'] = true;
    $user['mnethostid'] = $CFG->mnet_localhost_id;


    // Start of user info validation.
    // Lets make sure we validate current user info as handled by current GUI. see user/editadvanced_form.php function validation()
    if ($DB->record_exists('user', array('email'=>$user['email'], 'mnethostid'=>$user['mnethostid']))) {

        throw new invalid_parameter_exception('Email address already exists: '.$user['email']);
    }
    // End of user info validation.

    // create the user data now!
    $user['id'] = user_create_user($user);


    $userids[] = array('id'=>$user['id'], 'username'=>$user['username']);


$transaction->allow_commit();


echo json_encode($userids);die();


