<?php
//
//namespace auth_sentry\output;
//
//use plugin_renderer_base;
//use moodle_url;
//
//defined('MOODLE_INTERNAL') || die();
//
//class renderer extends plugin_renderer_base {
//    public function loginpage_hook() {
//        if (is_enabled_auth('sentry')) {
//            $auth_sentry_settings = get_config('auth_sentry');
//            if (empty($auth_sentry_settings->enable_custom_button)) {
//                return '<a href="' . new moodle_url('/auth/sentry/index.php') . '" class="btn btn-danger py-3" style="position: absolute; margin-top: 202px; margin-left: 550px; z-index: 1;">Login With Face</a>';
//            }
//        }
//        return '';
//    }
//}
