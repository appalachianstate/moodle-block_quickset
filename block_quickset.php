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


/* Quickset to set most commonly changed course settings
* as well as rename, rearrange, insert and delete course sections
* @package quickset
* @author: Bob Puffer Luther College <puffro01@luther.edu>
* @date: 2010 ->
*/

class block_quickset extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_quickset');
    }

    // Only one instance of this block is required
    function instance_allow_multiple() {
        return false;
    }

    // Label and button values can be set in admin
    function has_config() {
        return false;
    }

    function get_content() {
        global $CFG, $COURSE, $USER, $PAGE, $DB;
        
        if ($this->content !== null) {
        	return $this->content;
        }
        
        $this->content = new stdClass;
        
        $available = 1;
        $unavailable = 0;
        
        $returnurl = "$CFG->wwwroot/course/view.php?id=$COURSE->id";
        //$numsections = $DB->get_field('course_format_options', 'value', array('courseid' => $COURSE->id, 'name' => 'numsections'));

        $context = context_course::instance($COURSE->id);
        if (has_capability('moodle/course:update', $context)) {
            $coursevisible = '';
            $gradesvisible = '';
        	if ($COURSE->visible == 1) {
                $coursevisible = ' selected';
            }
            if ($COURSE->showgrades == 1) {
                $gradesvisible = ' selected';
            }
            
            $this->content->text = '<form id="quickset" action="' . $CFG->wwwroot . '/blocks/quickset/edit.php" method="post">'
            		. '<input type="hidden" value="' . $PAGE->course->id . '" name="courseid">'
            		. '<input type="hidden" value="' . sesskey() . '" name="sesskey">'
            		. '<input type="hidden" value="' . $returnurl . '" name="pageurl">'
            		. '<input type="hidden" value="grader" name="report">'
            		. '<div class="form-group row">'
            		. '<label for="coursevisible" class="col-sm-8 col-form-label">Students see course</label>'
            		. '<div class="col-sm-4">'
            		. '<select class="form-control" id="coursevisible">'
            		. '<option' . $coursevisible . '>No</option>'
            		. '<option' . $coursevisible . '>Yes</option>'
            		. '</select>'
            		. '</div>'
            		. '</div>'
            		. '<div class="form-group row">'
            		. '<label for="gradesvisible" class="col-sm-8 col-form-label">Students see grades</label>'
            		. '<div class="col-sm-4">'
            		. '<select class="form-control" id="gradesvisible">'
            		. '<option' . $gradesvisible . '>No</option>'
            				. '<option' . $gradesvisible . '>Yes</option>'
            		. '</select>'
            		. '</div>'
            		. '</div>'
            		. '<button type="submit" class="btn btn-primary">Submit</button>'
            		. '</form>'
            		. '<div>'
            		. '<a href="' . $CFG->wwwroot . '/course/edit.php?id=' . $COURSE->id . '">More settings</a>'
            		. '</div>'
            		.'<div class="small">Note: This block invisible to students.</div>';
        }
        $this->content->footer = '';
        return $this->content;
    }
}