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
 * Update settings form.
 *
 * @package   block_quickset
 * @author    Michelle Melton <meltonml@appstate.edu>
 * @copyright 2019, Michelle Melton
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once("$CFG->libdir/formslib.php");

/**
 * Update settings form.
 *
 * @package    block_quickset
 * @copyright  2019, Michelle Melton
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_settings_form extends moodleform {
    /**
     * Define update settings form.
     * {@inheritDoc}
     * @see moodleform::definition()
     */
    public function definition() {
        global $CFG, $COURSE;
        $mform = $this->_form;

        $mform->addElement('selectyesno', 'coursevisible', get_string('coursevisible', 'block_quickset'));
        $mform->setDefault('coursevisible', $COURSE->visible);
        $mform->addHelpButton('coursevisible', 'coursevisible', 'block_quickset');

        $mform->addElement('selectyesno', 'gradesvisible', get_string('gradesvisible', 'block_quickset'));
        $mform->setDefault('gradesvisible', $COURSE->showgrades);
        $mform->addHelpButton('gradesvisible', 'gradesvisible', 'block_quickset');

        $this->add_action_buttons(false, get_string('submit', 'block_quickset'));
        $mform->addElement('html', '<div><a href=' . $CFG->wwwroot . '/course/edit.php?id=' . $COURSE->id . '>' .
            get_string('editsettings', 'moodle') . '</a></div>');
        $mform->addElement('html', '<div class="small">' . get_string('note', 'block_quickset') . '</div>');
    }

    /**
     * Validate update settings form submissions.
     * {@inheritDoc}
     * @see moodleform::validation()
     *
     * @param array $data Array of ("fieldname"=>value) of submitted data.
     * @param array $files Array of uploaded files "element_name"=>tmp_file_path.
     * @return array Array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     *
     */
    public function validation($data, $files) {
        $errors = array();
        return $errors;
    }
}