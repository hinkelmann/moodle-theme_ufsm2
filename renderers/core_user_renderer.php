<?php
/**
 * Moodle's ufsm theme
 * @package    theme_ufsm
 * @copyright  2016 Núcleo de Tecnologia Educacional {@link http://nte.ufsm.br}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/user/renderer.php");

class theme_ufsm2_core_user_renderer extends core_user_renderer {

 /**
 * Prints user search utility that can search user by first initial of firstname and/or first initial of lastname
 * Prints a header with a title and the number of users found within that subset
 * @param string $url the url to return to, complete with any parameters needed for the return
 * @param string $firstinitial the first initial of the firstname
 * @param string $lastinitial the first initial of the lastname
 * @param int $usercount the amount of users meeting the search criteria
 * @param int $totalcount the amount of users of the set/subset being searched
 * @param string $heading heading of the subset being searched, default is All Participants
 * @return string html output
 */
    public function user_search($url, $firstinitial, $lastinitial, $usercount, $totalcount, $heading = null) {
        global $OUTPUT;

        $strall = get_string('all');
        $alpha  = explode(',', get_string('alphabet', 'langconfig'));

        if (!isset($heading)) {
            $heading = get_string('allparticipants');
        }
        $content = html_writer::start_tag('div',['class'=>'col-md-6']);
        $content .= html_writer::start_tag('div',['class'=>'panel']);
        $content .= html_writer::start_tag('div',['class'=>'panel-body']);
        $content .= html_writer::start_tag('form', array('action' => new moodle_url($url)));
        $content .= html_writer::start_tag('div');

        // Search utility heading.
        $content .= $OUTPUT->heading($heading.get_string('labelsep', 'langconfig').$usercount.'/'.$totalcount, 3);

        // Bar of first initials.
        $content .= html_writer::start_tag('div', array('class' => 'initialbar firstinitial'));
        $content .= html_writer::label(get_string('firstname').' : ', null);

        if (!empty($firstinitial)) {
            $content .= html_writer::link($url.'&sifirst=', $strall);
        } else {
            $content .= html_writer::tag('strong', $strall);
        }

        foreach ($alpha as $letter) {
            if ($letter == $firstinitial) {
                $content .= html_writer::tag('strong', $letter);
            } else {
                $content .= html_writer::link($url.'&sifirst='.$letter, $letter);
            }
        }
        $content .= html_writer::end_tag('div');

        // Bar of last initials.
        $content .= html_writer::start_tag('div', array('class' => 'initialbar lastinitial'));
        $content .= html_writer::label(get_string('lastname').' : ', null);

        if (!empty($lastinitial)) {
            $content .= html_writer::link($url.'&silast=', $strall);
        } else {
            $content .= html_writer::tag('strong', $strall);
        }

        foreach ($alpha as $letter) {
            if ($letter == $lastinitial) {
                $content .= html_writer::tag('strong', $letter);
            } else {
                $content .= html_writer::link($url.'&silast='.$letter, $letter);
            }
        }
        $content .= html_writer::end_tag('div');

        $content .= html_writer::end_tag('div');
        $content .= html_writer::tag('div', '&nbsp;');
        $content .= html_writer::end_tag('form');
        $content .= html_writer::end_tag('div');
        $content .= html_writer::end_tag('div');
        $content .= html_writer::end_tag('div');


        return $content;
    }

    /**
     * Displays the list of tagged users
     *
     * @param array $userlist
     * @param bool $exclusivemode if set to true it means that no other entities tagged with this tag
     *             are displayed on the page and the per-page limit may be bigger
     * @return string
     */
    public function user_list($userlist, $exclusivemode) {
        $tagfeed = new core_tag\output\tagfeed();
        foreach ($userlist as $user) {
            $userpicture = $this->output->user_picture($user, array('size' => $exclusivemode ? 100 : 35));
            $fullname = fullname($user);
            if (user_can_view_profile($user)) {
                $profilelink = new moodle_url('/user/view.php', array('id' => $user->id));
                $fullname = html_writer::link($profilelink, $fullname);
            }
            $tagfeed->add($userpicture, $fullname);
        }

        $items = $tagfeed->export_for_template($this->output);

        if ($exclusivemode) {
            $output = '<div><ul class="inline-list">';
            foreach ($items['items'] as $item) {
                $output .= '<li><div class="user-box">'. $item['img'] . $item['heading'] ."</div></li>\n";
            }
            $output .= "</ul></div>\n";
            return $output;
        }

        return $this->output->render_from_template('core_tag/tagfeed', $items);
    }
}