<?php
/**
 * Moodle's ufsm theme
 * @package    theme_ufsm
 * @copyright  2016 Núcleo de Tecnologia Educacional {@link http://nte.ufsm.br}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/course/renderer.php");

class theme_ufsm2_core_course_renderer extends core_course_renderer {

    protected function coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = '') {
        global $CFG;
        if (!isset($this->strings->summary)) {
            $this->strings->summary = get_string('summary');
        }
        if ($chelper->get_show_courses() <= self::COURSECAT_SHOW_COURSES_COUNT) {
            return '';
        }
        if ($course instanceof stdClass) {
            require_once($CFG->libdir. '/coursecatlib.php');
            $course = new course_in_list($course);
        }
        $content = '';
        $classes = trim('panel panel-default coursebox clearfix '. $additionalclasses);
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $classes .= ' collapsed';
        }

        // Start .coursebox div.
        $content .= html_writer::start_tag('div', array(
            'class' => $classes,
            'data-courseid' => $course->id,
            'data-type' => self::COURSECAT_TYPE_COURSE,
        ));

        $content .= html_writer::start_tag('div', array('class' => 'panel-heading info'));

        // Course name.
        $coursename = $chelper->get_course_formatted_name($course);
        $coursenamelink = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)),
                                            $coursename, array('class' => $course->visible ? '' : 'dimmed'));
        $content .= html_writer::tag('span', $coursenamelink, array('class' => 'coursename'));
        // If we display course in collapsed form but the course has summary or course contacts, display the link to the info page.
        $content .= html_writer::start_tag('span', array('class' => 'moreinfo'));
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            if ($course->has_summary() || $course->has_course_contacts() || $course->has_course_overviewfiles()) {
                $url = new moodle_url('/course/info.php', array('id' => $course->id));
                $image = html_writer::empty_tag('img', array('src' => $this->output->pix_url('i/info'),
                    'alt' => $this->strings->summary));
                $content .= html_writer::link($url, $image, array('title' => $this->strings->summary));
                // Make sure JS file to expand course content is included.
                $this->coursecat_include_js();
            }
        }
        $content .= html_writer::end_tag('span'); // End .moreinfo span.

        // Print enrolmenticons.
        if ($icons = enrol_get_course_info_icons($course)) {
            $content .= html_writer::start_tag('div', array('class' => 'enrolmenticons'));
            foreach ($icons as $pixicon) {
                $content .= $this->render($pixicon);
            }
            $content .= html_writer::end_tag('div'); // End .enrolmenticons div.
        }

        $content .= html_writer::end_tag('div'); // End .info div.

        $content .= html_writer::start_tag('div', array('class' => 'content panel-body'));
        $content .= $this->coursecat_coursebox_content($chelper, $course);

        $content .= html_writer::end_tag('div'); // End .content div.

        $content .= html_writer::end_tag('div'); // End .coursebox div.

        return $content;

    }

    protected function coursecat_coursebox_content(coursecat_helper $chelper, $course) {
        global $CFG;
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            return '';
        }
        if ($course instanceof stdClass) {
            require_once($CFG->libdir. '/coursecatlib.php');
            $course = new course_in_list($course);
        }
        $content = '';

        // Display course overview files.
        $contentimages = $contentfiles = '';
        foreach ($course->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            $url = file_encode_url("$CFG->wwwroot/pluginfile.php",
                    '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                    $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
            if ($isimage) {
                    $contentimages .= html_writer::start_tag('div', array('class' => 'imagebox'));

                    $images = html_writer::empty_tag('img', array('src' => $url, 'alt' => 'Course Image '. $course->fullname,
                        'class' => 'courseimage'));
                    $contentimages .= html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)), $images);

                    $contentimages .= html_writer::end_tag('div');
            } else {
                $image = $this->output->pix_icon(file_file_icon($file, 24), $file->get_filename(), 'moodle');
                $filename = html_writer::tag('span', $image, array('class' => 'fp-icon')).
                        html_writer::tag('span', $file->get_filename(), array('class' => 'fp-filename'));
                $contentfiles .= html_writer::tag('span',
                        html_writer::link($url, $filename),
                        array('class' => 'coursefile fp-filename-icon'));
            }
        }
        $content .= $contentimages. $contentfiles;

        // Display course summary.
        if ($course->has_summary()) {
            $content .= $chelper->get_course_formatted_summary($course);
        }

        // Display course contacts. See course_in_list::get_course_contacts().
        if ($course->has_course_contacts()) {
            $content .= html_writer::start_tag('ul', array('class' => 'teachers'));
            foreach ($course->get_course_contacts() as $userid => $coursecontact) {
                $name = $coursecontact['rolename'].': '.
                        html_writer::link(new moodle_url('/user/view.php',
                                array('id' => $userid, 'course' => SITEID)),
                            $coursecontact['username']);
                $content .= html_writer::tag('li', $name);
            }
            $content .= html_writer::end_tag('ul'); // End .teachers div.
        }

        // Display course category if necessary (for example in search results).
        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT) {
            require_once($CFG->libdir. '/coursecatlib.php');
            if ($cat = coursecat::get($course->category, IGNORE_MISSING)) {
                $content .= html_writer::start_tag('div', array('class' => 'coursecat'));
                $content .= get_string('category').': '.
                        html_writer::link(new moodle_url('/course/index.php', array('categoryid' => $cat->id)),
                                $cat->get_formatted_name(), array('class' => $cat->visible ? '' : 'dimmed'));
                $content .= html_writer::end_tag('div'); // End .coursecat div.
            }
        }

        return $content;
    }

    public function course_search_form($value = '', $format = 'plain') {
        static $count = 0;
        $formid = 'coursesearch';
        if ((++$count) > 1) {
            $formid .= $count;
        }
        $inputid = 'coursesearchbox';
        $inputsize = 30;

        if ($format === 'navbar') {
            $formid = 'coursesearchnavbar';
            $inputid = 'navsearchbox';
        }

        $strsearchcourses = get_string("searchcourses");
        $searchurl = new moodle_url('/course/search.php');

        $form = array('id' => $formid, 'action' => $searchurl, 'method' => 'get', 'class' => "form-inline", 'role' => 'form');
        $output = html_writer::start_tag('form', $form);
        $output .= html_writer::start_div('input-group');
        $output .= html_writer::tag('label', $strsearchcourses, array('for' => $inputid, 'class' => 'sr-only'));
        $search = array('type' => 'text', 'id' => $inputid, 'size' => $inputsize, 'name' => 'search',
                        'class' => 'form-control', 'value' => s($value), 'placeholder' => $strsearchcourses);
        $output .= html_writer::empty_tag('input', $search);
        $button = array('type' => 'submit', 'class' => 'btn btn-default');
        $output .= html_writer::start_span('input-group-btn');
        $output .= html_writer::tag('button', get_string('go'), $button);
        $output .= html_writer::end_span();
        $output .= html_writer::end_div(); // Close form-group.
        $output .= html_writer::end_tag('form');

        return $output;
    }
    /**
     * Renders HTML to display one course module in a course section
     *
     * This includes link, content, availability, completion info and additional information
     * that module type wants to display (i.e. number of unread forum posts)
     *
     * This function calls:
     * {@link core_course_renderer::course_section_cm_name()}
     * {@link core_course_renderer::course_section_cm_text()}
     * {@link core_course_renderer::course_section_cm_availability()}
     * {@link core_course_renderer::course_section_cm_completion()}
     * {@link course_get_cm_edit_actions()}
     * {@link core_course_renderer::course_section_cm_edit_actions()}
     *
     * @param stdClass $course
     * @param completion_info $completioninfo
     * @param cm_info $mod
     * @param int|null $sectionreturn
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm($course, &$completioninfo, cm_info $mod, $sectionreturn, $displayoptions = array()) {
        $output = '';
        // We return empty string (because course module will not be displayed at all)
        // if:
        // 1) The activity is not visible to users
        // and
        // 2) The 'availableinfo' is empty, i.e. the activity was
        //     hidden in a way that leaves no info, such as using the
        //     eye icon.
        if (!$mod->uservisible && empty($mod->availableinfo)) {
            return $output;
        }

        $indentclasses = 'mod-indent';
        if (!empty($mod->indent)) {
            $indentclasses .= ' mod-indent-'.$mod->indent;
            if ($mod->indent > 15) {
                $indentclasses .= ' mod-indent-huge';
            }
        }

        $output .= html_writer::start_tag('div');

        if ($this->page->user_is_editing()) {
            $output .= course_get_cm_move($mod, $sectionreturn);
        }

        $output .= html_writer::start_tag('div', array('class' => 'mod-indent-outer'));

        // This div is used to indent the content.
        $output .= html_writer::div('', $indentclasses);

        // Start a wrapper for the actual content to keep the indentation consistent
        $output .= html_writer::start_tag('div');

        // Display the link to the module (or do nothing if module has no url)
        $cmname = $this->course_section_cm_name($mod, $displayoptions);

        if (!empty($cmname)) {
            // Start the div for the activity title, excluding the edit icons.
            $output .= html_writer::start_tag('div', array('class' => 'activityinstance'));
            $output .= $cmname;


            // Module can put text after the link (e.g. forum unread)
            $output .= $mod->afterlink;

            // Closing the tag which contains everything but edit icons. Content part of the module should not be part of this.
            $output .= html_writer::end_tag('div'); // .activityinstance
        }

        // If there is content but NO link (eg label), then display the
        // content here (BEFORE any icons). In this case cons must be
        // displayed after the content so that it makes more sense visually
        // and for accessibility reasons, e.g. if you have a one-line label
        // it should work similarly (at least in terms of ordering) to an
        // activity.
        $contentpart = $this->course_section_cm_text($mod, $displayoptions);
        $url = $mod->url;
        if (empty($url)) {
            $output .= $contentpart;
        }

        $modicons = '';
        if ($this->page->user_is_editing()) {
            $editactions = $this->course_get_cm_edit_actions($mod, $mod->indent, $sectionreturn);
            $modicons .= ' '. $this->course_section_cm_edit_actions($editactions, $mod, $displayoptions);
            $modicons .= $mod->afterediticons;
        }

        $modicons .= $this->course_section_cm_completion($course, $completioninfo, $mod, $displayoptions);

        if (!empty($modicons)) {
            $output .= html_writer::span($modicons, 'actions');
        }

        // If there is content AND a link, then display the content here
        // (AFTER any icons). Otherwise it was displayed before
        if (!empty($url)) {
            $output .= $contentpart;
        }
        // show availability info (if module is not available)
        $output .= $this->course_section_cm_availability($mod, $displayoptions);
        $output .= html_writer::end_tag('div'); // $indentclasses
        // End of indentation div.
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');
        return $output;
    }

    /**
     * Returns the list of all editing actions that current user can perform on the module
     *
     * @param cm_info $mod The module to produce editing buttons for
     * @param int $indent The current indenting (default -1 means no move left-right actions)
     * @param int $sr The section to link back to (used for creating the links)
     * @return array array of action_link or pix_icon objects
     */
    function course_get_cm_edit_actions(cm_info $mod, $indent = -1, $sr = null) {
        global $COURSE, $SITE;

        static $str;

        $coursecontext = context_course::instance($mod->course);
        $modcontext = context_module::instance($mod->id);

        $editcaps = array('moodle/course:manageactivities', 'moodle/course:activityvisibility', 'moodle/role:assign');
        $dupecaps = array('moodle/backup:backuptargetimport', 'moodle/restore:restoretargetimport');

        // No permission to edit anything.
        if (!has_any_capability($editcaps, $modcontext) and !has_all_capabilities($dupecaps, $coursecontext)) {
            return array();
        }

        $hasmanageactivities = has_capability('moodle/course:manageactivities', $modcontext);

        if (!isset($str)) {
            $str = get_strings(array('delete', 'move', 'moveright', 'moveleft',
                'editsettings', 'duplicate', 'hide', 'show'), 'moodle');
            $str->assign         = get_string('assignroles', 'role');
            $str->groupsnone     = get_string('clicktochangeinbrackets', 'moodle', get_string("groupsnone"));
            $str->groupsseparate = get_string('clicktochangeinbrackets', 'moodle', get_string("groupsseparate"));
            $str->groupsvisible  = get_string('clicktochangeinbrackets', 'moodle', get_string("groupsvisible"));
        }

        $baseurl = new moodle_url('/course/mod.php', array('sesskey' => sesskey()));

        if ($sr !== null) {
            $baseurl->param('sr', $sr);
        }
        $actions = array();

        // Update.
        if ($hasmanageactivities) {
            $actions['update'] = new action_menu_link_secondary(
                new moodle_url($baseurl, array('update' => $mod->id)),
                null, //new pix_icon('t/edit', $str->editsettings, 'moodle', array('class' => 'iconsmall', 'title' => '')),
               // html_writer::tag('i',null,['class'=>'fa fa-gear']) ." ".
                $str->editsettings,
                array('class' => 'editing_update', 'data-action' => 'update')
            );
        }

        // Indent.
        if ($hasmanageactivities && $indent >= 0) {
            $indentlimits = new stdClass();
            $indentlimits->min = 0;
            $indentlimits->max = 16;
            if (right_to_left()) {   // Exchange arrows on RTL
                $rightarrow = 't/left';
                $leftarrow  = 't/right';
            } else {
                $rightarrow = 't/right';
                $leftarrow  = 't/left';
            }

            if ($indent >= $indentlimits->max) {
                $enabledclass = 'hidden';
            } else {
                $enabledclass = '';
            }
            $actions['moveright'] = new action_menu_link_secondary(
                new moodle_url($baseurl, array('id' => $mod->id, 'indent' => '1')),
                new pix_icon($rightarrow, $str->moveright, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                $str->moveright,
                array('class' => 'editing_moveright ' . $enabledclass, 'data-action' => 'moveright', 'data-keepopen' => true)
            );

            if ($indent <= $indentlimits->min) {
                $enabledclass = 'hidden';
            } else {
                $enabledclass = '';
            }
            $actions['moveleft'] = new action_menu_link_secondary(
                new moodle_url($baseurl, array('id' => $mod->id, 'indent' => '-1')),
                new pix_icon($leftarrow, $str->moveleft, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                $str->moveleft,
                array('class' => 'editing_moveleft ' . $enabledclass, 'data-action' => 'moveleft', 'data-keepopen' => true)
            );

        }

        // Hide/Show.
        if (has_capability('moodle/course:activityvisibility', $modcontext)) {
            if ($mod->visible) {
                $actions['hide'] = new action_menu_link_secondary(
                    new moodle_url($baseurl, array('hide' => $mod->id)),
                    new pix_icon('t/hide', $str->hide, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                    $str->hide,
                    array('class' => 'editing_hide', 'data-action' => 'hide')
                );
            } else {
                $actions['show'] = new action_menu_link_secondary(
                    new moodle_url($baseurl, array('show' => $mod->id)),
                    new pix_icon('t/show', $str->show, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                    $str->show,
                    array('class' => 'editing_show', 'data-action' => 'show')
                );
            }
        }

        // Duplicate (require both target import caps to be able to duplicate and backup2 support, see modduplicate.php)
        //if (has_all_capabilities($dupecaps, $coursecontext) &&
        //    plugin_supports('mod', $mod->modname, FEATURE_BACKUP_MOODLE2)) {
            $actions['duplicate'] = new action_menu_link_secondary(
                new moodle_url($baseurl, array('duplicate' => $mod->id)),
                new pix_icon('t/copy', $str->duplicate, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                $str->duplicate,
                array('class' => 'editing_duplicate', 'data-action' => 'duplicate', 'data-sr' => $sr)
            );
        //}

        // Groupmode.
        if ($hasmanageactivities && !$mod->coursegroupmodeforce) {
            if (plugin_supports('mod', $mod->modname, FEATURE_GROUPS, 0)) {
                if ($mod->effectivegroupmode == SEPARATEGROUPS) {
                    $nextgroupmode = VISIBLEGROUPS;
                    $grouptitle = $str->groupsseparate;
                    $actionname = 'groupsseparate';
                    $groupimage = 'i/groups';
                } else if ($mod->effectivegroupmode == VISIBLEGROUPS) {
                    $nextgroupmode = NOGROUPS;
                    $grouptitle = $str->groupsvisible;
                    $actionname = 'groupsvisible';
                    $groupimage = 'i/groupv';
                } else {
                    $nextgroupmode = SEPARATEGROUPS;
                    $grouptitle = $str->groupsnone;
                    $actionname = 'groupsnone';
                    $groupimage = 'i/groupn';
                }

                $actions[$actionname] = new action_menu_link_primary(
                    new moodle_url($baseurl, array('id' => $mod->id, 'groupmode' => $nextgroupmode)),
                    new pix_icon($groupimage, null, 'moodle', array('class' => 'iconsmall')),
                    $grouptitle,
                    array('class' => 'editing_'. $actionname, 'data-action' => $actionname, 'data-nextgroupmode' => $nextgroupmode, 'aria-live' => 'assertive')
                );
            } else {
                $actions['nogroupsupport'] = new action_menu_filler();
            }
        }

        // Assign.
        if (has_capability('moodle/role:assign', $modcontext)){
            $actions['assign'] = new action_menu_link_secondary(
                new moodle_url('/admin/roles/assign.php', array('contextid' => $modcontext->id)),
                new pix_icon('t/assignroles', $str->assign, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                $str->assign,
                array('class' => 'editing_assign', 'data-action' => 'assignroles')
            );
        }

        // Delete.
        if ($hasmanageactivities) {
            $actions['delete'] = new action_menu_link_secondary(
                new moodle_url($baseurl, array('delete' => $mod->id)),
                new pix_icon('t/delete', $str->delete, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                $str->delete,
                array('class' => 'editing_delete', 'data-action' => 'delete')
            );
        }

        return $actions;
    }

}
