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
 * Created by IntelliJ IDEA.
 * User: nina
 * Date: 16.03.16
 * Time: 09:23
 */
class block_groups extends block_base
{

    /**Initialises the block*/
    public function init(){
        $this->title = get_string('pluginname','block_groups');
    }
    /** Returns the content object
     *
     * @return stdObject
     */

    public function get_content()
    {

        global $COURSE;

        if($this->content !== null){
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $allgroups = groups_get_all_groups($COURSE->id);
        $allgroupings = groups_get_all_groupings($COURSE->id);
        if(empty($allgroups) && empty($allgroupings)){
            $this->content->text = '';
        }

        $coursecontext = context_course::instance($COURSE->id);
        $access = has_capability('moodle/course:managegroups',  $coursecontext);

        // Users who are able to manage groups see all groups
        if($access === TRUE){
            $this->content->text .= $this->get_content_teaching();
        }

        if (!empty($allgroups)) {
                $this->content->text .= $this->get_content_groupmembers();
        }
        return $this->content;
    }



    /** Returns a list of Groups and Groupings
     *
     * To every Member who has the capability to manage courses is an overview of all existing groups and groupings displayed
     *
     * @return stdObject
     */
    private function get_content_teaching(){
        global  $COURSE, $CFG;
        $grouparray = array();
        $groupingarray = array();
        $allgroups = groups_get_all_groups($COURSE->id);
        $allgroupings = groups_get_all_groupings($COURSE->id);
        $groupstext = '';

        foreach ($allgroups as $g => $value) {
            if (is_object($value) && property_exists($value, 'name')) {
                $grouparray[$g] = $value->name ;
            }
        }
        foreach ($allgroupings as $g => $value) {
            if (is_object($value) && property_exists($value, 'name')) {
                $groupingarray[$g] = $value->name ;
            }
        }

        if(count($grouparray)==0) {
            $groupstext = '';
            return $groupstext;
        }
        else{

            if(!(empty($groupingarray)) ){
                $groupstext = get_string('viewallgroupings', 'block_groups') . "</br>";
                $listallgrouping = html_writer::alist($groupingarray);
                $groupstext .= $listallgrouping;
            }

            $groupstext .= get_string('viewallgroups', 'block_groups') . "</br>";
            $listallgroups = html_writer::alist($grouparray);
            $groupstext .= $listallgroups;
            $courseshown = $this->page->course->id;
            $groupstext .= '<a href="' . $CFG->wwwroot . '/group/index.php?id=' . $courseshown . '">modify groups</a></br>';
            return $groupstext;
        }
    }



    private function get_content_groupmembers(){
        global  $COURSE, $USER;

        $memberarray = array();
        $allgroups = groups_get_my_groups();


        foreach ($allgroups as $allgroupnr => $valueall) {

                if ($valueall->courseid == $COURSE->id) {
                    $memberarray[] = $valueall->name;
                }
        }

        if (empty($memberarray)) {
            $groupstext ='';
            return $groupstext;
        }

        $groupstext ='';
        $groupstext .= get_string('member', 'block_groups') . "</br>";
        $groupstext .= html_writer::alist($memberarray);
        return $groupstext;
    }

}