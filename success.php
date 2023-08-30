<?php
global $CFG;

require_once('../../config.php');
require_once($CFG->dirroot . '/auth/sentry/auth.php');
if (isloggedin()) {
    return redirect($CFG->wwwroot.'/my');
}

defined('MOODLE_INTERNAL') || die();

$username   = required_param('username', PARAM_TEXT);
$password   = required_param('token', PARAM_TEXT);

$userinfo = new stdClass();
$userinfo->username = $username;
$userinfo->password = $password;

foreach($userinfo as $key => $value) {

    if(!$value) {
        echo 'No Params';
    }
}

$authplugin = get_auth_plugin('sentry');

if(isset($authplugin))
{
    $authplugin->login($userinfo);
}
else {
    echo 'Plugin Not Found';
}
