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
 * form for image file upload
 *
 * @package    auth_sentry
 * @copyright  2023, Brain Station 23
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

class imageupload_form extends moodleform {
    // Add elements to form.
    public function definition() {
        global $CFG;

//        $mform->addElement('header', 'user_name', 'name');
//        $mform->addElement('hidden', 'id', 'Student id');
//        $mform->setType('id', PARAM_INT);
//        $mform->addElement('hidden', 'context_id', 'context id');
//        $mform->setType('context_id', PARAM_INT);
        global $CFG;
        $mform = $this->_form; // Don't forget the underscore!
        $html = '<div class="row">
        <div class="col-md">
        <strong class="pull-left">' . get_string('upload_image_title', 'auth_sentry') . '</strong>
        </div>
        <div class="col-md">
        <a
            class="btn btn-outline-primary pull-right"
            href="'. $CFG->wwwroot . '/auth/sentry/userlist.php">
            Back
        </a>
        </div>
        </div>';
        $mform->addElement('html', $html);
        $mform->addElement('header', 'username', 'name');
        $mform->addElement('hidden', 'id', 'User id');
        $mform->setType('id', PARAM_INT);

//        $mform->addElement('hidden', 'face_image', 'Face Image');
//        $mform->setType('face_image', PARAM_RAW);
        $mform->addElement('hidden', 'context_id', 'context id');
        $mform->setType('context_id', PARAM_INT);

        $mform->addElement(
            'filemanager',
            'auth_student_photo',
            'image',
            null,
            array(
                'subdirs' => 0, 'maxfiles' => 1,
                'accepted_types' => array('png', 'jpg', 'jpeg')
            )
        );
        $mform->addRule('auth_student_photo', get_string('provide_image', 'auth_sentry'), 'required');

        $this->add_action_buttons();
    }

    // Custom validation should be added here.
    public function validation($data, $files) {
        return array();
    }
}
