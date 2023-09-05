<?php

require_once('../../config.php');

global $CFG, $OUTPUT, $PAGE, $USER, $SESSION;

$auth = empty($user->auth) ? 'sentry' : $user->auth;
if(!is_enabled_auth($auth)){
    redirect($CFG->wwwroot, get_string('notenable', 'auth_sentry'), null, \core\output\notification::NOTIFY_ERROR);
}

if (isloggedin()) {
    return redirect($CFG->wwwroot.'/my');
}
$context = context_system::instance();
$PAGE->set_context($context);

$display = (object)[
    'wwwroot' => $CFG->wwwroot,
];

$successmessage = get_config('auth_sentry', 'successmessage');

if (empty($successmessage)) {
    $successmessage = get_string('successmessagetextdefault', 'auth_sentry');
}

$failedmessage = get_config('auth_sentry', 'failedmessage');

if (empty($failedmessage)) {
    $failedmessage = get_string('failedmessagetextdefault', 'auth_sentry');
}

$nouser = get_string('nouserfound', 'auth_sentry');


if (empty($threshold)) {
    $threshold = 0.68;
}

    $modelurl = $CFG->wwwroot . '/auth/sentry/thirdpartylibs/models';
    $PAGE->requires->js("/auth/sentry/amd/build/face-api.min.js", true);
    $PAGE->requires->js_call_amd('auth_sentry/login_modal',
        'init', array($USER->id, $successmessage, $failedmessage, $threshold, $modelurl, $nouser));

    echo $OUTPUT->header();

    echo $OUTPUT->render_from_template('auth_sentry/login', $display);

    echo $OUTPUT->footer();

