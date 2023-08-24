<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Image Upload Functionalities
 *
 * @package    auth_sentry
 * @copyright  2023, Brain Station 23
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $DB, $PAGE, $OUTPUT, $CFG, $USER;
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/auth/sentry/classes/form/upload_image_form.php');
require_once('lib.php');

$PAGE->set_url(new moodle_url('/auth/sentry/upload_image.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('title_upload', 'auth_sentry'));

require_login();

if (!is_siteadmin()) {
    redirect($CFG->wwwroot, get_string('no_permission', 'auth_sentry'), null, \core\output\notification::NOTIFY_ERROR);
}

//$courseid = optional_param('cid', 0, PARAM_INT);
$userid = optional_param('id', -1, PARAM_INT);
//$username = optional_param('username','null',PARAM_TEXT);
$sql = 'SELECT u.username FROM {user} u WHERE u.id = :userid';
$username = $DB->get_field_sql($sql, ['userid' => $userid]);

// Instantiate imageupload_form.
$mform = new imageupload_form();

// Checking form.
if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot . '/auth/sentry/userlist.php',
        get_string('cancel_image_upload', 'auth_sentry'),
        null,
        \core\output\notification::NOTIFY_INFO);
} else if ($data = $mform->get_data()) {
    // ... store or update $user
    file_save_draft_area_files(
        $data->auth_student_photo,
        $data->context_id,
        'auth_sentry',
        'auth_student_photo',
        $data->id,
        array('subdirs' => 0, 'maxfiles' => 50)
    );
    if ($DB->record_exists_select('auth_sentry', 'userid = :id', array('id' => $data->id))) {
        $record = $DB->get_record_select('auth_sentry', 'userid = :id', array('id' => $data->id));
        $record->userid = $data->id;
        $record->username = $username;
        $record->password = '';
        $record->photo_draft_id = $data->auth_student_photo;
        $record->timecreated = time();
        $record->timemodified = time();
        $DB->update_record('auth_sentry', $record);
        redirect($CFG->wwwroot . '/auth/sentry/userlist.php', 'Image updated',
            null, \core\output\notification::NOTIFY_SUCCESS);
    } else {
        $record = new stdClass;
        $record->userid = $data->id;
        $record->username = $username;
        $record->password = '';
        $record->photo_draft_id = $data->auth_student_photo;
        $record->timecreated = time();
        $record->timemodified = time();
        $record->usermodified = time();
        $DB->insert_record('auth_sentry', $record);
        redirect($CFG->wwwroot . '/auth/sentry/userlist.php', 'Image updated',
            null, \core\output\notification::NOTIFY_SUCCESS);
    }
}

$context = context_system::instance();
$username = $DB->get_record_select('user', 'id=:id', array('id' => $userid), 'firstname ,lastname');

if (empty($user->id)) {
    $user = new stdClass;
    $user->id = $userid;
    $user->username = $username->firstname . ' ' . $username->lastname;
    $user->context_id = $context->id;
}

$draftitemid = file_get_submitted_draft_itemid('auth_student_photo');

file_prepare_draft_area(
    $draftitemid,
    $context->id,
    'auth_sentry',
    'auth_student_photo',
    $user->id,
    array('subdirs' => 0, 'maxfiles' => 1)
);

$user->auth_student_photo = $draftitemid;

$mform->set_data($user);

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
