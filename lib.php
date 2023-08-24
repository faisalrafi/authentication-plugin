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
 *
 *
 * @package    auth_sentry
 * @category   check
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function auth_sentry_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    global $DB;

    if ($context->contextlevel != CONTEXT_SYSTEM) {
        return false;
    }

    require_login();

    if ($filearea != 'auth_student_photo') {
        return false;
    }

    $itemid = (int)array_shift($args);

    $fs = get_file_storage();

    $filename = array_pop($args);
    if (empty($args)) {
        $filepath = '/';
    } else {
        $filepath = '/' . implode('/', $args) . '/';
    }

    $file = $fs->get_file($context->id, 'auth_sentry', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false;
    }

    // Finally send the file.
    send_stored_file($file, 0, 0, false, $options);
}

/**
 * Add security check to make sure this isn't on in production.
 *
 * @return string check
 */

function auth_sentry_get_image_url($userid) {
    $context = context_system::instance();

    $fs = get_file_storage();
    if ($files = $fs->get_area_files($context->id, 'auth_sentry', 'auth_student_photo')) {

        foreach ($files as $file) {
            if ($userid == $file->get_itemid() && $file->get_filename() != '.') {
                // Build the File URL. Long process! But extremely accurate.
                $fileurl = moodle_url::make_pluginfile_url(
                    $file->get_contextid(), $file->get_component(), $file->get_filearea(),
                    $file->get_itemid(), $file->get_filepath(), $file->get_filename(), true);
                // Display the image.
                $downloadurl = $fileurl->get_port() ?
                    $fileurl->get_scheme().'://'.$fileurl->get_host().$fileurl->get_path().':'.$fileurl->get_port() :
                    $fileurl->get_scheme().'://'.$fileurl->get_host().$fileurl->get_path();
                return $downloadurl;
            }
        }
    }
    return false;
}

/**
 * Returns the image file of a specific user.
 *
 * @param int $userid User id
 * @return mixed image file
 */
function auth_sentry_get_image_file($userid) {
    $context = context_system::instance();

    $fs = get_file_storage();
    if ($files = $fs->get_area_files($context->id, 'auth_sentry', 'auth_student_photo')) {

        foreach ($files as $file) {
            if ($userid == $file->get_itemid() && $file->get_filename() != '.') {
                // Return the image file.
                return $file;
            }
        }
    }
    return false;
}

/**
 * Returns the url of face image.
 *
 * @param string $data
 * @param $USER
 * @param int $courseid
 * @param stdClass $record
 * @param $context
 * @param $fs
 *
 * @return mixed
 */
function auth_sentry_geturl_of_faceimage(string $data, int $userid, stdClass $record, $context, $fs) {
    list(, $data) = explode(',', $data);
    $data = base64_decode($data);
    $filename = 'faceimage-' . $userid . '-' . time() . random_int(1, 1000) . '.png';

    $record->filename = $filename;
    $record->contextid = $context->id;
    $record->userid = $userid;

    $fs->create_file_from_string($record, $data);

    return moodle_url::make_pluginfile_url(
        $context->id,
        $record->component,
        $record->filearea,
        $record->itemid,
        $record->filepath,
        $record->filename,
        false
    );
}

