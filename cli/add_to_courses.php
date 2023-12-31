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
 * Add quickset block to all courses via CLI.
 *
 * @package   block_quickset
 * @author    Michelle Melton <meltonml@appstate.edu>
 * @copyright 2019, Michelle Melton
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/clilib.php');
global $DB;

$usage = "Add the quickset block to the top of all courses.

Usage:
    # php add_to_courses.php [--help|-h]

Options:
    -h --help               Print this help.
";

list($options, $unrecognised) = cli_get_params([
        'help' => false,
], [
        'h' => 'help'
]);

if ($unrecognised) {
    $unrecognised = implode(PHP_EOL.'  ', $unrecognised);
    cli_error(get_string('cliunknowoption', 'core_admin', $unrecognised));
}

if ($options['help']) {
    cli_writeln($usage);
    exit(2);
}

if (!$DB->get_record('block', array('name' => 'quickset'))) {
    cli_error('Missing mandatory quickset block.');
}

// Set up block_instance record.
$coursecontexts = $DB->get_records('context', array('contextlevel' => 50), '', 'id, instanceid');
$quicksetinstance = new stdClass();
$quicksetinstance->blockname = 'quickset';
$quicksetinstance->showinsubcontexts = 0;
$quicksetinstance->requiredbytheme = 0;
$quicksetinstance->pagetypepattern = 'course-view-*';
$quicksetinstance->defaultregion = 'side-pre';
$quicksetinstance->defaultweight = 4;
$quicksetinstance->timecreated = time();
$quicksetinstance->timemodified = time();

foreach ($coursecontexts as $coursecontext) {
    if ($DB->get_record('block_instances', array('parentcontextid' => $coursecontext->id, 'blockname' => 'quickset'))) {
        cli_problem('Quickset block already exists for course ' . $coursecontext->instanceid);
        continue;
    }

    $quicksetinstance->parentcontextid = $coursecontext->id;
    $blockinstanceid = $DB->insert_record('block_instances', $quicksetinstance, true);
    if ($blockinstanceid) {
        // Set up block_position record.
        $courseformat = $DB->get_field('course', 'format', array('id' => $coursecontext->instanceid));
        if ($courseformat) {
            $quicksetposition = new stdClass();
            $quicksetposition->blockinstanceid = $blockinstanceid;
            $quicksetposition->contextid = $coursecontext->id;
            $quicksetposition->pagetype = 'course-view-' . $courseformat;
            $quicksetposition->subpage = '';
            $quicksetposition->visible = 1;
            $quicksetposition->region = 'side-pre';
            $quicksetposition->weight = -2;
            $DB->insert_record('block_positions', $quicksetposition);
            unset($quicksetposition);
        }
    }
}