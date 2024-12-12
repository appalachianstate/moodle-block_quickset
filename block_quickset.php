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
 * Quick settings block class.
 *
 * @package   block_quickset
 * @author    Michelle Melton <meltonml@appstate.edu>
 * @copyright 2019, Michelle Melton
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/blocks/quickset/classes/forms/update_settings_form.php');

/**
 * Quickset block functions.
 *
 * @package    block_quickset
 * @copyright  2019, Michelle Melton
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_quickset extends block_base {

    /**
     * Initializes block.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_quickset');
    }

    /**
     * Allows only one instance of the block per course.
     *
     * @return false
     */
    public function instance_allow_multiple() {
        return false;
    }

    /**
     * There is no configuration for the block.
     *
     * @return false
     */
    public function has_config() {
        return false;
    }

    /**
     * Displays or processes the Quickset form.
     * {@inheritDoc}
     * @see block_base::get_content()
     *
     * @return string block content
     */
    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        global $CFG, $COURSE, $DB;

        $this->content = new stdClass;
        $this->content->text = '';

        $context = context_course::instance($COURSE->id);
        $url = "$CFG->wwwroot/course/view.php";
        $urlparams = array('id' => $COURSE->id);
        $redirect = new moodle_url($url, $urlparams);
        if (has_capability('moodle/course:update', $context)) {
            $updatesettingsform = new update_settings_form($redirect);
            if ($fromform = $updatesettingsform->get_data()) {
                // Process validated data.
                $course = $DB->get_record('course', array('id' => $COURSE->id));
                if ($course) {
                    if (property_exists($fromform, 'coursevisible')) {
                        course_change_visibility($course->id, $fromform->coursevisible);
                    }

                    if (property_exists($fromform, 'gradesvisible')) {
                        $course->showgrades = $fromform->gradesvisible;
                        $DB->set_field('course', 'showgrades', $fromform->gradesvisible);
                    }

                    redirect($redirect, get_string('success', 'moodle'), null, \core\output\notification::NOTIFY_SUCCESS);
                }
            } else {
                // Display form first time, or if form is submitted with invalid data.
                $this->content->text = $updatesettingsform->render();
                $this->content->text = preg_replace('/col-md-3/', 'col-md-8', $this->content->text, 2);
                $this->content->text = preg_replace('/col-md-9/', 'col-md-4', $this->content->text, 2);
                $this->content->text = preg_replace('/col-md-3/', 'col-md-0', $this->content->text, 1);
                $this->content->text = preg_replace('/col-md-9/', 'col-md-12', $this->content->text, 1);
            }
        }
        $this->content->footer = '';
        return $this->content;
    }

    /**
     * Quickset block is only allowed on course view pages.
     * {@inheritDoc}
     * @see block_base::applicable_formats()
     *
     * @return array allowed page views
     */
    public function applicable_formats() {
        return array('course-view' => true);
    }
}