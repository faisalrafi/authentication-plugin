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
 * Externallib file for sevices functions
 *
 * @package    auth_sentry
 * @copyright  2023, Brain Station 23
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

class auth_sentry_external extends external_api {


    public static function get_user_image_parameters() {
        return new external_function_parameters(
            array(
                'username' => new external_value(PARAM_TEXT, "User Name"),
            )
        );
    }

    public static function get_user_image($username) {
        global $DB;
        $student= $DB->get_record_select('user', "username = :username", array('username' => $username));
        $studentid = $student->id;
        $context = context_system::instance();

        $fs = get_file_storage();
        if ($files = $fs->get_area_files($context->id, 'auth_sentry', 'auth_student_photo')) {
            foreach ($files as $file) {
                if ($studentid == $file->get_itemid() && $file->get_filename() != '.') {
                    // Build the File URL. Long process! But extremely accurate.
                    $fileurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(),
                        $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename(), false);
                    // Display the image.
                    $downloadurl = $fileurl->get_port() ? $fileurl->get_scheme() . '://' . $fileurl->get_host() .
                        $fileurl->get_path() . ':' . $fileurl->get_port() : $fileurl->get_scheme() . '://'
                        . $fileurl->get_host() . $fileurl->get_path();

                    $returnvalue = [
                        'image_url' => $downloadurl,
                        'username' => $username
                    ];

                    return $returnvalue;
                }
            }
        }
        return [
            'image_url' => false,
            'username' => $username
        ];
    }

    public static function get_user_image_returns() {
        return new external_single_structure(
            array(
                'image_url' => new external_value(PARAM_URL, 'Url of user image'),
                'username' => new external_value(PARAM_TEXT, 'Course name')
            )
        );
    }

    /**
     * calling api using curl
     */
    public static function face_recognition_api_parameters() {
        return new external_function_parameters(
            array(
                'studentimg' => new external_value(PARAM_RAW, "Student id"),
                'webcampicture' => new external_value(PARAM_RAW, "Student id"),
            )
        );
    }

    public static function face_recognition_api($studentimg, $webcampicture) {
        global $CFG;
        $studentimg = str_replace('data:image/png;base64,', '', $studentimg);
        $webcampicture = str_replace('data:image/png;base64,', '', $webcampicture);

        $data = array (
            'original_img_response' => $studentimg,
            'face_img_response' => $webcampicture,
        );

        $payload = json_encode($data);

        $bsapi = get_config('auth_sentry', 'bsapi');
        $bsapikey = get_config('auth_sentry', 'bs_api_key');

        // Check similarity.
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $bsapi,
            CURLOPT_HTTPHEADER => array(
                'x-api-key: ' . $bsapikey,
                "Content-Type: application/json",
            ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
        ]);

        $similarityresult = curl_exec($curl);

        curl_close($curl);

        $response = json_decode($similarityresult);

        $output = array(
            'original_img_response' => $studentimg,
            'face_img_response' => $webcampicture,
            'distance' => $response->body->distance
        );

        return $output;
    }

    public static function face_recognition_api_returns() {
        return new external_single_structure(
            array(
                'original_img_response' => new external_value(PARAM_RAW, 'updated or failed', VALUE_OPTIONAL),
                'face_img_response' => new external_value(PARAM_RAW, 'updated or failed', VALUE_OPTIONAL),
                'distance' => new external_value(PARAM_TEXT, 'distance value', VALUE_OPTIONAL)
            )
        );
    }
}
