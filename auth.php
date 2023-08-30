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
 * Anobody can login with any password.
 *
 * @package auth_sentry
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
global $CFG;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/authlib.php');

/**
 * Plugin for no authentication.
 */
class auth_plugin_sentry extends auth_plugin_base {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->authtype = 'sentry';
        $this->config = get_config('auth_sentry');
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function auth_plugin_sentry() {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct();
    }

    /**
     * Hook for overriding behaviour of login page.
     *  */
    function loginpage_hook() {
        global $PAGE, $CFG;


        $PAGE->requires->jquery();
        $PAGE->requires->js_init_code("buttonsAddMethod = 'auto';");
        $content = str_replace(array("\n", "\r"), array("\\\n", "\\\r",), $this->get_buttons_string());
        $PAGE->requires->js_init_code("buttonsCode = '$content';");
        $PAGE->requires->js(new moodle_url($CFG->wwwroot . "/auth/sentry/script.js"));

    }

    private function get_buttons_string() {
        global $CFG;

        $link = $CFG->wwwroot.'/auth/sentry/login.php';
        $content = '<div class="">
                        <a class="btn btn-danger btn-block btn-lg mt-3" 
                            href="'.$link.'" >'.get_string("sentrybutton", "auth_sentry") .'
                        </a><br>
                    </div>';

        return $content;
    }

    /**
     * Returns true if the username and password work or don't exist and false
     * if the user exists and the password is wrong.
     *
     * @param string $username The username
     * @param string $password The password
     * @return bool Authentication success or failure.
     */

    function user_login($username, $password) {
        global $CFG, $DB;
        if (empty($username)) {
            echo 'no username';
            return false;
        }
        if(empty($password)){
            echo 'no password';
            return false;
        }
        $user = $DB->get_record('user', array('username' => $username, 'mnethostid' => $CFG->mnet_localhost_id));
            if (!$user) {
                return false;
            }
        return true;
    }

//    function user_login ($username, $password) {
//        global $CFG, $DB;
//        if ($user = $DB->get_record('user', array('username' => $username, 'mnethostid' => $CFG->mnet_localhost_id, 'auth' => 'sentry'))) {
//            if(!isset($_REQUEST['logintoken']) && $_SERVER['REQUEST_METHOD'] == 'GET' && isset($_REQUEST['id'])){
//                return true;
//            } else {
//                return validate_internal_user_password($user, $password);
//            }
//        }
//        return false;
//
//    }

    public function login($userinfo) {
        global $DB, $SESSION, $CFG;
        try{
            if ($userinfo->username) {
                $user = $DB->get_record('user',array('username' => $userinfo->username));
//                echo "<pre>";
//                var_dump($user);
//                echo "</pre>";
//                die();
                if(!$user){
                    echo "No user Found";
                    return;
                }

                if($this->user_login($userinfo->username, $userinfo->password)) {
                    // Now completes the user login.
                    complete_user_login($user);

//                    $DB->delete_records('auth_sentry_linked_login', array('username' => $userinfo->username, 'token' => $userinfo->password));

                    redirect($CFG->wwwroot . '/my/');

                } else {
                    echo "Sorry";
                }
            }
        } catch (Exception $e){

            echo "Sorry Bro";

        }
    }


//    /**
//     * Test the various configured Oauth2 providers.
//     */
//    public function test_settings() {
//        var_dump('hiii');
//        die();
//        global $OUTPUT;
//
//        $authplugin = get_auth_plugin('sentry');
//        $idps = $authplugin->loginpage_idp_list('');
//        $templateidps = [];
//
//        if (empty($idps)) {
//            echo $OUTPUT->notification(get_string('noconfiguredidps', 'auth_sentry'), 'notifyproblem');
//            return;
//        } else {
//            foreach ($idps as $idp) {
//                $idpid = $idp['url']->get_param('id');
//                $sesskey = $idp['url']->get_param('sesskey');
//                $testurl = new moodle_url('/auth/sentry/test.php', ['id' => $idpid, 'sesskey' => $sesskey]);
//
//                $templateidps[] = ['name' => $idp['name'], 'url' => $testurl->out(), 'iconurl' => $idp['iconurl']];
//            }
//            echo $OUTPUT->render_from_template('auth_sentry/idps', ['idps' => $templateidps]);
//        }
//    }

    /**
     * Updates the user's password.
     *
     * called when the user password is updated.
     *
     * @param  object  $user        User table object
     * @param  string  $newpassword Plaintext password
     * @return boolean result
     *
     */
//    function user_update_password($user, $newpassword) {
//        $user = get_complete_user_data('id', $user->id);
//        // This will also update the stored hash to the latest algorithm
//        // if the existing hash is using an out-of-date algorithm (or the
//        // legacy md5 algorithm).
//        return update_internal_user_password($user, $newpassword);
//    }

//    function prevent_local_passwords() {
//        return false;
//    }

    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @return bool
     */
    function is_internal() {
        return true;
    }

    function can_edit_profile(){
        return true;
    }

    /**
     * Returns true if this authentication plugin can change the user's
     * password.
     *
     * @return bool
     */
    function can_change_password() {
        return true;
    }

    /**
     * Returns the URL for changing the user's pw, or empty if the default can
     * be used.
     *
     * @return moodle_url
     */
    function change_password_url() {
        return null;
    }

    /**
     * Returns true if plugin allows resetting of internal password.
     *
     * @return bool
     */
    function can_reset_password() {
        return true;
    }

    /**
     * Returns true if plugin can be manually set.
     *
     * @return bool
     */
    function can_be_manually_set() {
        return true;
    }

}


