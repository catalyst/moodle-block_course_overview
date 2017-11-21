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
 * Main class for course listing
 *
 * @package    block_course_overview
 * @copyright  2017 Howard Miller <howardsmiller@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_course_overview\output;

defined('MOODLE_INTERNAL') || die;

use renderable;
use renderer_base;
use templatable;

/**
 * Class contains data for course_overview
 *
 * @copyright  2017 Howard Miller <howardsmiller@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class main implements renderable, templatable {

    private $sortedcourses;

    private $overviews;

    private $totalcourses;

    private $isediting;

    /**
     * Constructor
     * @param array $sortedcourses
     * @param array $overviews
     * @param int $totalcourses
     * @param boolean $isediting
     */
    public function __construct($sortedcourses, $overviews, $totalcourses, $isediting) {
        $this->sortedcourses = $sortedcourses;
        $this->overviews = $overviews;
        $this->totalcourses = $totalcourses;
        $this->isediting = $isediting;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {

        // Add extra info (and make zero indexed).
        $courselist = [];
        foreach ($this->sortedcourses as $course) {
            $course->link = new \moodle_url('/course/view.php', array('id' => $course->id));
            $course->favouritelink = new \moodle_url('/my', array('favourite' => $course->id));
            if (!empty($this->overviews[$course->id])) {
                $course->hasoverviews = true;
                $overviews = array();
                foreach ($this->overviews[$course->id] as $activity => $overview_text) {
                    $overview = new \stdClass;
                    $overview->coursename = $course->fullname;
                    $overview->activity = $activity;
                    $overview->text = str_replace('p-y-1', '', $overview_text);
                    $description = get_string('activityoverview', 'block_course_overview', get_string('pluginname', 'mod_' . $activity));
                    $overviewid = $activity . '_' . $course->id;
                    $overview->overviewid = $overviewid;
                    $overview->icon = $output->pix_icon('icon', $description, 'mod_' . $activity);
                    $overviews[] = $overview;
                }
                $course->overviews = $overviews;
            } else {
                $course->hasoverviews = false;
            }
            $courselist[] = $course;
        }

        // 'courses to show' select box
        $options = array('0' => get_string('alwaysshowall', 'block_course_overview'));
        for ($i = 1; $i <= $this->totalcourses; $i++) {
            $options[$i] = $i;
        }
        $url = new \moodle_url('/my/index.php', ['sesskey' => sesskey()]);
        $select = new \single_select($url, 'mynumber', $options, block_course_overview_get_max_user_courses(), array());
        $select->set_label(get_string('numtodisplay', 'block_course_overview'));

        return [
            'courses' => $courselist,
            'isediting' => $this->isediting,
            'select' => $output->render($select),
            'viewingfavourites' => false,
        ];
    }

}
