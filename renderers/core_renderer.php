<?php
/**
 * Moodle's ufsm theme
 * @package    theme_ufsm
 * @copyright  2016 Núcleo de Tecnologia Educacional {@link http://nte.ufsm.br}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class theme_ufsm2_core_renderer extends core_renderer
{

    public function notification($message, $classes = 'notifyproblem')
    {
        /* $message = clean_text($message); */

        if ($classes == 'notifyproblem') {
            return html_writer::div($message, 'alert alert-danger');
        }
        if ($classes == 'notifywarning') {
            return html_writer::div($message, 'alert alert-warning');
        }
        if ($classes == 'notifysuccess') {
            return html_writer::div($message, 'alert alert-success');
        }
        if ($classes == 'notifymessage') {
            return html_writer::div($message, 'alert alert-info');
        }
        if ($classes == 'redirectmessage') {
            return html_writer::div($message, 'alert alert-block alert-info');
        }
        if ($classes == 'notifytiny') {
            // Not an appropriate semantic alert class!
            return $this->debug_listing($message);
        }
        return html_writer::div($message, $classes);
    }

    public function msg_boas_vindas()
    {
        global $USER;
        return html_writer::tag('h1', strtoupper("OLÁ {$USER->firstname}, BEM-VINDO AO MOODLE"));
    }

    private function debug_listing($message)
    {
        $message = str_replace('<ul style', '<ul class="list-unstyled" style', $message);
        return html_writer::tag('pre', $message, array('class' => 'alert alert-info'));
    }

    public function navbar()
    {
        $items = $this->page->navbar->get_items();
        if (empty($items)) { // MDL-46107.
            return '';
        }
        $breadcrumbs = '';
        foreach ($items as $item) {
            $item->hideicon = true;
            $breadcrumbs .= '<li>' . $this->render($item) . '</li>';
        }

        $title = html_writer::tag('span', get_string('pagepath'), array('class' => 'accesshide', 'id' => 'navbar-label'));
        return $title . html_writer::start_tag('nav',
                array('aria-labelledby' => 'navbar-label',
                    'aria-label' => 'breadcrumb',
                    'class' => 'breadcrumb-nav',
                    'role' => 'navigation')) .
            html_writer::tag('ul', "$breadcrumbs", array('class' => 'breadcrumb')) .
            html_writer::end_tag('nav');
    }

    public function custom_menu($custommenuitems = '')
    {
        // The custom menu is always shown, even if no menu items
        // are configured in the global theme settings page.
        global $CFG;

        if (empty($custommenuitems) && !empty($CFG->custommenuitems)) { // MDL-45507.
            $custommenuitems = $CFG->custommenuitems;
        }
        $custommenu = new custom_menu($custommenuitems, current_language());
        return $this->render_custom_menu($custommenu);
    }

    protected function render_custom_menu(custom_menu $menu)
    {

        // Add the lang_menu to the left of the menu.
        $this->add_lang_menu($menu);

        $content = '<ul class="nav navbar-nav pull-right">';
        foreach ($menu->get_children() as $item) {
            $content .= $this->render_custom_menu_item($item, 1);
        }

        return $content . '</ul>';
    }

    protected function render_custom_menu_item(custom_menu_item $menunode, $level = 0, $direction = '')
    {
        static $submenucount = 0;

        if ($menunode->has_children()) {

            if ($level == 1) {
                $dropdowntype = 'dropdown';
            } else {
                $dropdowntype = 'dropdown-submenu';
            }

            $content = html_writer::start_tag('li', array('class' => $dropdowntype));
            // If the child has menus render it as a sub menu.
            $submenucount++;
            if ($menunode->get_url() !== null) {
                $url = $menunode->get_url();
            } else {
                $url = '#cm_submenu_' . $submenucount;
            }
            $linkattributes = array(
                'href' => $url,
                'class' => 'dropdown-toggle',
                'data-toggle' => 'dropdown',
                'title' => $menunode->get_title(),
            );
            $content .= html_writer::start_tag('a', $linkattributes);
            $content .= $menunode->get_text();
            if ($level == 1) {
                $content .= '<b class="caret"></b>';
            }
            $content .= '</a>';
            $content .= '<ul class="dropdown-menu ' . $direction . '">';
            foreach ($menunode->get_children() as $menunode) {
                $content .= $this->render_custom_menu_item($menunode, 0);
            }
            $content .= '</ul>';
        } else {
            $content = '<li>';
            // The node doesn't have children so produce a final menuitem.
            $class = $menunode->get_title();
            if (preg_match("/^#+$/", $menunode->get_text())) {
                $content = '<li class="divider" role="presentation">';
            } else {
                $content = '<li>';
                // The node doesn't have children so produce a final menuitem.
                if ($menunode->get_url() !== null) {
                    $url = $menunode->get_url();
                } else {
                    $url = '#';
                }
                $content .= html_writer::link($url, $menunode->get_text(), array('class' => $class,
                    'title' => $menunode->get_title()));
            }
        }
        return $content;
    }

    /**
     * Adds a lang submenu in a custom_menu
     *
     * @return string The lang menu HTML or empty string
     */
    protected function add_lang_menu(custom_menu $menu, $force = false)
    {
        // TODO: eliminate this duplicated logic, it belongs in core, not
        // here. See MDL-39565.

        $haslangmenu = $this->lang_menu() != '';

        if ($force || (!empty($this->page->layout_options['langmenu']) && $haslangmenu)) {
            $langs = get_string_manager()->get_list_of_translations();
            $strlang = get_string('language');
            $currentlang = current_language();
            if (isset($langs[$currentlang])) {
                $currentlang = $langs[$currentlang];
            } else {
                $currentlang = $strlang;
            }
            $this->language = $menu->add($currentlang, new moodle_url('#'), $strlang, 10000);
            foreach ($langs as $langtype => $langname) {
                $this->language->add($langname, new moodle_url($this->page->url, array('lang' => $langtype)), $langname);
            }
        }
    }

    /**
     * This code renders the navbar brand link displayed in the left navbar
     * on smaller screens.
     *
     * @return string HTML fragment
     */
    protected function navbar_brand()
    {
        global $CFG, $SITE;
        return html_writer::link($CFG->wwwroot,
            html_writer::img($CFG->wwwroot . '/theme/' . $CFG->theme . '/pix/marcas/monograma.svg', 'Marca da UFSM', ['width' => '100'])
            , array('class' => 'navbar-brand'));
    }

    /**
     * This code renders the navbar button to control the display of the custom menu
     * on smaller screens.
     *
     * Do not display the button if the menu is empty.
     *
     * @return string HTML fragment
     */
    protected function navbar_button()
    {
        global $CFG;

        if (empty($CFG->custommenuitems)) {
            return '';
        }

        $accessibility = html_writer::tag('span', get_string('togglenav', 'theme_bootstrap'),
            array('class' => 'sr-only'));
        $iconbar = html_writer::tag('span', '', array('class' => 'icon-bar'));
        $button = html_writer::tag('button', $accessibility . "\n" . $iconbar . "\n" . $iconbar . "\n" . $iconbar,
            array('class' => 'navbar-toggle', 'data-toggle' => 'collapse', 'data-target' => '#moodle-navbar', 'type' => 'button'));
        return $button;
    }

    /**
     * @param tabtree $tabtree
     * @return string
     */
    protected function render_tabtree(tabtree $tabtree)
    {
        if (empty($tabtree->subtree)) {
            return '';
        }
        $firstrow = $secondrow = '';
        foreach ($tabtree->subtree as $tab) {
            $firstrow .= $this->render($tab);
            if (($tab->selected || $tab->activated) && !empty($tab->subtree) && $tab->subtree !== array()) {
                $secondrow = $this->tabtree($tab->subtree);
            }
        }
        return html_writer::tag('ul', $firstrow, array('class' => 'nav nav-tabs')) . $secondrow;
    }

    /**
     * @param tabobject $tab
     * @return string
     */
    protected function render_tabobject(tabobject $tab)
    {
        if ($tab->selected or $tab->activated) {
            return html_writer::tag('li', html_writer::tag('a', $tab->text), array('class' => 'active'));
        } else if ($tab->inactive) {
            return html_writer::tag('li', html_writer::tag('a', $tab->text), array('class' => 'disabled'));
        } else {
            if (!($tab->link instanceof moodle_url)) {
                $link = "<a href=\"$tab->link\" title=\"$tab->title\">$tab->text</a>";
            } else {
                $link = html_writer::link($tab->link, $tab->text, array('title' => $tab->title));
            }
            return html_writer::tag('li', $link);
        }
    }

    /**
     * @param string $contents
     * @param string $classes
     * @param null $id
     * @param array $attributes
     * @return string
     */
    public function box($contents, $classes = 'generalbox', $id = null, $attributes = array())
    {
        if (isset($attributes['data-rel']) && $attributes['data-rel'] === 'fatalerror') {
            return html_writer::div($contents, 'alert alert-danger', $attributes);
        }
        return parent::box($contents, $classes, $id, $attributes);
    }

    /**
     * overridding context_header function.
     * @param bool $headerinfo
     * @param integer $headinglevel
     * @return context_header| string
     */
    public function context_header($headerinfo = null, $headinglevel = 1)
    {
        if ($headinglevel == 1 && !empty($this->page->theme->settings->logo)) {
            return html_writer::tag('div', '', array('class' => 'logo'));
        }
        return parent::context_header($headerinfo, $headinglevel);
    }

    /**
     * overridding user_profile_picture function.
     */
    public function user_profile_picture()
    {
        GLOBAL $USER;
        $userpic = parent::user_picture($USER, array('link' => false, 'size' => 28));
        return $userpic;
    }

    /**
     * overridding favicon function.
     */
    public function favicon()
    {
        GLOBAL $PAGE, $CFG;
        $checkfavicon = $PAGE->theme->setting_file_url('favicon', 'favicon');
        if (!empty($checkfavicon)) {
            return $PAGE->theme->setting_file_url('favicon', 'favicon');
        } else {
            return $CFG->wwwroot . '/theme/' . $CFG->theme . '/pix/favicon.ico';
        }
    }

    /**
     * overridding favicon function.
     */
    public function get_marcas()
    {
        GLOBAL $PAGE, $CFG;
        $diretorio = "{$CFG->wwwroot}/theme/{$CFG->theme}/pix/marcas/";

        $attr = ['width' => '100', 'class' => 'img-responsive'];
        $marcaMoodle = html_writer::img($diretorio . "moodle-logo.png", 'Marca do Moodle', $attr);
        $marcaMoodle = html_writer::link('https://moodle.org', $marcaMoodle);
        $html = html_writer::div($marcaMoodle, 'col-xs-6');
        //$attr['class'] = 'pull-right';
        //$attr['class'] = "pull-right";
        $marcaNte = html_writer::img($diretorio . "nte.png", "Marca do NTE", $attr);
        $marcaNte = html_writer::link('https://nte.ufsm.br', $marcaNte);
        $html .= html_writer::div($marcaNte, 'col-offset-6');

        return $html;

    }

    /**
     * Construct a user menu, returning HTML that can be echoed out by a
     * layout file.
     *
     * @param stdClass $user A user object, usually $USER.
     * @param bool $withlinks true if a dropdown should be built.
     * @return string HTML fragment.
     */
    public function menu_principal($user = null, $withlinks = null)
    {
        global $USER, $CFG;
        require_once($CFG->dirroot . '/user/lib.php');


        $menu = [];
        $menu2 = [];
        $menuUsuario = [];

        $pagina_ajuda_url = new moodle_url(get_docs_url(page_get_doc_link_path($this->page)));

        //Usuario passado por parametro
        if (is_null($user)) {
            $user = $USER;
        }

        if (is_null($withlinks)) {
            $withlinks = empty($this->page->layout_options['nologinlinks']);
        }

        //Verifica se está na instalação
        if (during_initial_install()) {
            return null;
        }

        //Verifica se não esta logado e não esta na pagina de login
        if (!isloggedin()) {
            if (!$this->is_login_page()) {
                $menu[] = html_writer::link(get_login_url(), get_string('mysingin', 'theme_ufsm2'));
                $this->page->requires->js_call_amd('theme_ufsm2/void', 'init', []);
                return html_writer::alist($menu, ['class' => 'nav navbar-nav pull-right']);
            }
            return null;
        }

        // Verifica se o usuário é convidado
        if (isguestuser()) {
            if (!$this->is_login_page() && $withlinks) {
                $menu[] = html_writer::link('#', get_string('myuser', 'theme_ufsm2', get_string('loggedinasguest')));
                $menu[] = html_writer::link(get_login_url(), get_string('mysingin', 'theme_ufsm2'));
                $this->page->requires->js_call_amd('theme_ufsm2/void', 'init', []);
                return html_writer::alist($menu, ['class' => 'nav navbar-nav pull-right']);
            }
        }

        $opts = user_get_user_navigation_info($user, $this->page);


        $avatarclasses = "avatars";

        $avatarcontents = html_writer::span($opts->metadata['useravatar'], 'avatar current');
        $usertextcontents = $opts->metadata['userfullname'];
        $usertextcontents = $USER->firstname;

       /* var_dump($opts->metadata);
        exit();
        */

        // Other user.
        if (!empty($opts->metadata['asotheruser'])) {
            $avatarcontents .= html_writer::span(
                $opts->metadata['realuseravatar'],
                'avatar realuser'
            );
            $usertextcontents = substr($opts->metadata['realuserfullname'], 0, strrpos($opts->metadata['realuserfullname'], ' '));

            $usertextcontents .= html_writer::tag(
                'span',
                get_string(
                    'loggedinas',
                    'moodle',
                    html_writer::span(
                    //$opts->metadata['userfullname'],
                        substr($opts->metadata['userfullname'], 0, strrpos($opts->metadata['userfullname'], ' ')),
                        'value'
                    )
                ),
                array('class' => 'meta viewingas')
            );
        }

        // Role.
        if (!empty($opts->metadata['asotherrole'])) {
            $role = core_text::strtolower(preg_replace('#[ ]+#', '-', trim($opts->metadata['rolename'])));
            $usertextcontents .= " " . html_writer::span(
                $opts->metadata['rolename'],
                'meta role role-' . $role
            );
        }

        // MNet.
        if (!empty($opts->metadata['asmnetuser'])) {
            $mnet = strtolower(preg_replace('#[ ]+#', '-', trim($opts->metadata['mnetidprovidername'])));
            $usertextcontents .= " " . html_writer::span(
                $opts->metadata['mnetidprovidername'],
                'meta mnet mnet-' . $mnet
            );
        }

        if ($withlinks) {
            foreach ($opts->navitems as $key => $value) {
                switch ($value->itemtype) {
                    case 'divider':
                        $menuUsuario[] = html_writer::tag('hr', '');
                        break;
                    case 'link':
                        switch ($value->pix) {
                            case 'i/course':
                                $menuUsuarioIcone = 'graduation-cap';
                                break;
                            case 'i/user':
                                $menuUsuarioIcone = 'user';
                                break;
                            case 't/grades':
                                $menuUsuarioIcone = 'th ';
                                break;
                            case 't/message':
                                $menuUsuarioIcone = 'envelope-o';
                                break;
                            case 't/preferences':
                                $menuUsuarioIcone = 'cog';
                                break;
                            case 'a/logout':
                                $menuUsuarioIcone = 'sing-out';
                                break;
                            default:
                                $menuUsuarioIcone = 'th ' . $value->pix;
                                break;
                        }
                        if ($value->pix != 'a/logout') {
                            $menuUsuario[] = html_writer::link($value->url, "<i class='topbar-sub-icon  fa fa-$menuUsuarioIcone'></i> " . $value->title);
                        }
                        break;
                }
            }
        }
        $attrLnkDD = [
            "class" => "dropdown-toggle",
            "data-toggle" => "dropdown",
            "role" => "button",
            "aria-haspopup" => "true",
            "aria-expanded" => "false"
        ];

        $attrDD = ["class" => "dropdown-toggle avatar hidden-xs hidden-sm",
            "data-toggle" => "dropdown",
            "aria-expanded" => "false"
        ];

        $attrDD1 = ["class" => "dropdown-toggle avatar hidden-md hidden-lg",
            "data-toggle" => "dropdown",
            "aria-expanded" => "false"
        ];

        $lnkMsg = new moodle_url('/message/', ['user1' => $USER->id, 'viewing' => 'recentconversations']);
        $lnkNewMsg = new moodle_url('/message/',['user1' => $USER->id, 'viewing' => 'search']);
        $lnkNotif = new moodle_url('/message/', ['user1' => $USER->id, 'viewing' => 'recentnotifications']);
        $lnkMsgConfig = new moodle_url('/message/edit.php', ['id' => $USER->id]);
        $lnkDocs = new moodle_url('/theme/ufsm2/docs/MoodleMudou.pdf');

        $submenuMsg = html_writer::start_tag('div', ['class' => 'notification-heading']);
        $submenuMsg .= html_writer::tag('h4', null, ['class' => 'menu-title']);
        $submenuMsg .= html_writer::tag('div', html_writer::link($lnkNewMsg, get_string('newmsg', 'theme_ufsm2')), ['class' => 'menu-title pull-left']);
        $submenuMsg .= html_writer::tag('div',html_writer::link('#',get_string('readAll', 'theme_ufsm2'),['class'=>'readAllMsg']), ['class' => 'menu-title pull-right']);
        $submenuMsg .= html_writer::end_tag('div');
        $submenuMsg .= html_writer::tag('li', null, ['class' => 'divider']);
        $submenuMsg .= html_writer::div('', 'notifications-wrapper', ['id' => 'msg-list']);
        $submenuMsg .= html_writer::tag('li', null, ['class' => 'divider']);
        $submenuMsg .= html_writer::start_tag('div', ['class' => 'notification-footer']);
        $submenuMsg .= html_writer::link($lnkMsg, get_string('viewall', 'theme_ufsm2'),['class'=>'pull-left']);
        $submenuMsg .= html_writer::link($lnkMsgConfig, get_string('msgconfig', 'theme_ufsm2'),['class'=>'pull-right']);
        $submenuMsg .= html_writer::end_tag('div');
        $submenuMsg = html_writer::tag('ul', $submenuMsg, ['class' => 'dropdown-menu notifications']);

        $submenuNotif = html_writer::start_tag('div', ['class' => 'notification-heading']);
        $submenuNotif .= html_writer::tag('h4', null, ['class' => 'menu-title']);
        $submenuNotif .= html_writer::tag('div', html_writer::link('#',get_string('readAll', 'theme_ufsm2'),['class'=>'readAllNotif']), ['class' => 'menu-title pull-right']);
        $submenuNotif .= html_writer::end_tag('div');
        $submenuNotif .= html_writer::tag('li', null, ['class' => 'divider']);
        $submenuNotif .= html_writer::div('', 'notifications-wrapper', ['id' => 'notif-list']);
        $submenuNotif .= html_writer::tag('li', null, ['class' => 'divider']);
        $submenuNotif .= html_writer::start_tag('div', ['class' => 'notification-footer']);
        $submenuNotif .= html_writer::link($lnkNotif, get_string('viewall', 'theme_ufsm2'),['class'=>'pull-left']);
        $submenuNotif .= html_writer::link($lnkMsgConfig, get_string('msgconfig', 'theme_ufsm2'),['class'=>'pull-right']);
        $submenuNotif .= html_writer::end_tag('div');
        $submenuNotif = html_writer::tag('ul', $submenuNotif, ['class' => 'dropdown-menu notifications']);


        $submenuAjuda = html_writer::tag('li',
            html_writer::link($lnkDocs,
                html_writer::tag('i', null, ['class' => 'icon-featured fa fa-star-half-full']) .
                get_string('helpSubmenu1', 'theme_ufsm2'),
                ['title' => get_string('helpSubmenu1Desc', 'theme_ufsm2'), 'target' => '_blank'])
        );
        $submenuAjuda .= html_writer::tag('li',
            html_writer::link($pagina_ajuda_url,
                html_writer::tag('i', null, ['class' => 'fa fa-question-circle-o']) .
                get_string('helpSubmenu2', 'theme_ufsm2'),
                ['title' => get_string('helpSubmenu2Desc', 'theme_ufsm2'), 'target' => '_blank'])
        );
        $submenuAjuda = html_writer::tag('ul', $submenuAjuda, ['class' => 'dropdown-menu']);
        $n0 = $this->get_exist_new_messages(0);
        $n1 = $this->get_exist_new_messages(1);

        $msgN = '';
        $notifN = '';

        if ($n0) {
            $msgN = html_writer::tag('sup', $n0);
        }
        if ($n1) {
            $notifN = html_writer::tag('sup', $n1);
        }


        $menu = '';
        $menu .= html_writer::start_tag('ul', ['class' => 'nav navbar-nav', 'id' => 'main-menu']);
        $menu .= html_writer::tag('li', html_writer::link($CFG->wwwroot, get_string('mydashboard', 'theme_ufsm2')));
        $menu .= html_writer::tag('li', html_writer::link('#', get_string('mynotifications', 'theme_ufsm2') . $notifN, $attrLnkDD) . $submenuNotif, ['id' => 'menu-notification']);
        $menu .= html_writer::tag('li', html_writer::link('#', get_string('mymessages', 'theme_ufsm2') . $msgN, $attrLnkDD) . $submenuMsg, ['id' => 'menu-messages']);
        $menu .= html_writer::tag('li', html_writer::link('#', get_string('myhelp', 'theme_ufsm2'), $attrLnkDD) . $submenuAjuda);
        $menu .= html_writer::tag('li', html_writer::link(new moodle_url('/user/preferences.php'), html_writer::tag('i',null,['class'=>'fa fa-cogs'])." ".get_string('preferences')),['class'=>'visible-xs-block']);
        $menu .= html_writer::end_tag('ul');

        /**
         * helpSubmenu1
         */


        $htmlO = html_writer::tag('i', ' ', ['class' => 'fa fa-chevron-down']);
        $htmlO = $htmlO . html_writer::span('Abrir menu dropdown', 'sr-only');
        $htmlO = $htmlO . html_writer::span('', 'toggle drop down');


        $m = html_writer::link('#', $avatarcontents . $usertextcontents . " " . $htmlO, $attrDD);

        $menu2[] = $m . html_writer::alist($menuUsuario, ['class' => 'dropdown-menu']);
        $menu2[] = html_writer::link(new moodle_url("/login/logout.php",['sesskey'=> sesskey()]),
            get_string('logout'));


        $this->page->requires->js_call_amd('theme_ufsm2/navbar', 'init', []);

        return $menu . html_writer::alist($menu2, ['class' => 'nav navbar-nav pull-right']);


    }


    /**
     * Renders an action menu component.
     *
     * ARIA references:
     *   - http://www.w3.org/WAI/GL/wiki/Using_ARIA_menus
     *   - http://stackoverflow.com/questions/12279113/recommended-wai-aria-implementation-for-navigation-bar-menu
     *
     * @param action_menu $menu
     * @return string HTML
     */
    public function render_action_menu_theme(action_menu $menu)
    {
        $menu->initialise_js($this->page);
        $output = html_writer::start_tag('ul', $menu->attributesprimary);
        foreach ($menu->get_primary_actions($this) as $action) {
            if ($action instanceof renderable) {
                $content = $this->render($action);
            } else {
                $content = $action;
            }
            $output .= html_writer::tag('li', $content, array('role' => 'presentation'));
        }
        $output .= html_writer::end_tag('ul');
        $output .= html_writer::start_tag('ul', $menu->attributessecondary);
        foreach ($menu->get_secondary_actions() as $action) {
            if ($action instanceof renderable) {
                $content = $this->render($action);
            } else {
                $content = $action;
            }
            $output .= html_writer::tag('li', $content, array('role' => 'presentation'));
        }
        $output .= html_writer::end_tag('ul');
        $output .= html_writer::end_tag('div');
        return $output;
    }

    /**
     * Produces a header for a block
     *
     * @param block_contents $bc
     * @return string
     */
    public function block_header(block_contents $bc)
    {
        $icone = '';
        $title = '';
        if ($bc->title) {
            $attributes = array();
            if ($bc->blockinstanceid) {
                $attributes['id'] = 'instance-' . $bc->blockinstanceid . '-header';
            }
            switch ($bc->attributes['data-block']) {
                case 'calendar_month':
                    $icone = 'calendar';
                    break;
                case 'my_calendar':
                    $icone = 'calendar';
                    break;
                case 'calendar_upcoming':
                    $icone = 'calendar-check-o';
                    break;
                case 'my_tasks':
                    $icone = 'th-list';
                    break;
                case 'settings':
                    $icone = 'cogs';
                    break;
                case 'navigation':
                    $icone = 'sitemap';
                    break;
                case 'participants':
                    $icone = 'users';
                    break;
                case 'private_files':
                    $icone = 'file-text';
                    break;
                case 'search_forums':
                    $icone = 'search';
                    break;
                case 'recent_activity':
                    $icone = 'list-ol';
                    break;
                case 'news_items':
                    $icone = 'bullhorn';
                    break;
                case 'messages':
                    $icone = 'commenting-o';
                    break;
                case 'jmail':
                    $icone = 'envelope-o';
                    break;
                case 'course_list':
                    $icone = 'object-group';
                    break;
                case 'tags':
                    $icone = 'tags';
                    break;
                case 'blog_tags':
                    $icone = 'tags';
                    break;
                case 'online_users':
                    $icone = 'user';
                    break;
                case 'admin_bookmarks':
                    $icone = 'star-o';
                    break;
                case 'html':
                    $icone = 'code';
                    break;
                case 'notas_sie':
                    $icone = 'list-alt';
                    break;
                case 'comments':
                    $icone = 'commenting';
                    break;
                case 'activity_results':
                    $icone = 'line-chart';
                    break;
                case 'selfcompletion':
                    $icone = 'check-square-o';
                    break;
                case 'completionstatus':
                    $icone = 'check-square';
                    break;
                case 'rss_client':
                    $icone = 'rss';
                    break;
                case 'mentees':
                    $icone = 'address-card';
                    break;
                case 'course_summary':
                    $icone = 'file-text-o';
                    break;
                default:
                    $icone = 'th';
            }

            $title = html_writer::tag('h2', "<i class='fa fa-{$icone}'></i> " . $bc->title, $attributes);
        }

        $blockid = null;
        if (isset($bc->attributes['id'])) {
            $blockid = $bc->attributes['id'];
        }
        $controlshtml = $this->block_controls($bc->controls, $blockid);

        $output = '';
        if ($title || $controlshtml) {
            $output .= html_writer::tag('div', html_writer::tag('div', html_writer::tag('div', '', array('class' => 'block_action')) . $title . $controlshtml, array('class' => 'title')), array('class' => 'header'));
        }
        return $output;
    }

    /**
     * Prints a nice side block with an optional header.
     *
     * The content is described
     * by a {@link core_renderer::block_contents} object.
     *
     * <div id="inst{$instanceid}" class="block_{$blockname} block">
     *      <div class="header"></div>
     *      <div class="content">
     *          ...CONTENT...
     *          <div class="footer">
     *          </div>
     *      </div>
     *      <div class="annotation">
     *      </div>
     * </div>
     *
     * @param block_contents $bc HTML for the content
     * @param string $region the region the block is appearing in.
     * @return string the HTML to be output.
     */
    public function block(block_contents $bc, $region)
    {
        $bc = clone($bc); // Avoid messing up the object passed in.
        if (empty($bc->blockinstanceid) || !strip_tags($bc->title)) {
            $bc->collapsible = block_contents::NOT_HIDEABLE;
        }
        if (!empty($bc->blockinstanceid)) {
            $bc->attributes['data-instanceid'] = $bc->blockinstanceid;
        }
        $skiptitle = strip_tags($bc->title);
        if ($bc->blockinstanceid && !empty($skiptitle)) {
            $bc->attributes['aria-labelledby'] = 'instance-' . $bc->blockinstanceid . '-header';
        } else if (!empty($bc->arialabel)) {
            $bc->attributes['aria-label'] = $bc->arialabel;
        }
        if ($bc->dockable) {
            $bc->attributes['data-dockable'] = 1;
        }
        if ($bc->collapsible == block_contents::HIDDEN) {
            $bc->add_class('hidden');
        }
        if (!empty($bc->controls)) {
            $bc->add_class('block_with_controls');
        }
        if (empty($skiptitle)) {
            $output = '';
            $skipdest = '';
        } else {
            $output = html_writer::link('#sb-' . $bc->skipid, get_string('skipa', 'access', $skiptitle),
                array('class' => 'skip skip-block', 'id' => 'fsb-' . $bc->skipid));
            $skipdest = html_writer::span('', 'skip-block-to',
                array('id' => 'sb-' . $bc->skipid));
        }
        $bc->add_class('hidden-print');
        $output .= html_writer::start_tag('div', $bc->attributes);

        $output .= $this->block_header($bc);
        $output .= $this->block_content($bc);

        $output .= html_writer::end_tag('div');

        $output .= $this->block_annotation($bc);

        $output .= $skipdest;

        $this->init_block_hider_js($bc);
        return $output;
    }

    /**
     * Override the JS require function to hide a block.
     * This is required to call a custom YUI3 module.
     *
     * @param block_contents $bc A block_contents object
     */
    protected function init_block_hider_js(block_contents $bc)
    {
        if (!empty($bc->attributes['id']) and $bc->collapsible != block_contents::NOT_HIDEABLE) {
            $config = new stdClass;
            $config->id = $bc->attributes['id'];
            $config->title = strip_tags($bc->title);
            $config->preference = 'block' . $bc->blockinstanceid . 'hidden';
            $config->tooltipVisible = get_string('hideblocka', 'access', $config->title);
            $config->tooltipHidden = get_string('showblocka', 'access', $config->title);
            $this->page->requires->yui_module(
                'moodle-theme_ufsm2-blockhider',
                'M.theme_ufsm2.init_block_hider',
                array($config)
            );
            user_preference_allow_ajax_update($config->preference, PARAM_BOOL);
        }
    }


    public function root_category()
    {
        global $DB, $COURSE;

        $category = $DB->get_record('course_categories', ['id' => $COURSE->category]);
        $path = explode('/', $category->path);
        $root_category_id = $root_category_id = (count($path) - 2 > 0) ? $path[count($path) - 2] : end($path);
        $root_category = $DB->get_record('course_categories', ['id' => $root_category_id]);
        return $root_category->name;
    }

    public function get_header_course()
    {
        global $DB;
        $courses = enrol_get_my_courses("category", "category desc, fullname asc");
        $root_categorys = [];
        foreach ($courses as $t) {
            $category = $DB->get_record('course_categories', array('id' => $t->id));
            $path = explode('/', $category->path);
            $root_category_id = $path[2];
            $root_category = $DB->get_record('course_categories', array('id' => $root_category_id));
            $root_categorys[] = $root_category->name;
        }
        return $root_categorys;
    }

    public function get_exist_new_messages($notification)
    {
        global $USER, $DB, $PAGE, $CFG;
        if (!isloggedin() || isguestuser()) {
            return;
        }
        $messagecount = $DB->count_records('message', array('useridto' => $USER->id));
        if ($messagecount < 1) {
            return;
        }
        $messagesql = "SELECT m.id, c.blocked
                     FROM {message} m
                     JOIN {message_working} mw      ON m.id=mw.unreadmessageid
                     JOIN {message_processors} p    ON mw.processorid=p.id
                     LEFT JOIN {message_contacts} c ON c.contactid = m.useridfrom
                                                    AND c.userid = m.useridto
                    WHERE m.useridto = :userid
                      AND m.notification=$notification
                      ";
        $waitingmessages = $DB->get_records_sql($messagesql, array('userid' => $USER->id, 'lastpopuptime' => 0));
        $validmessages = 0;
        foreach ($waitingmessages as $messageinfo) {
            if ($messageinfo->blocked) {
                // Message is from a user who has since been blocked so just mark it read.
                // Get the full message to mark as read.
                $messageobject = $DB->get_record('message', array('id' => $messageinfo->id));
                message_mark_message_read($messageobject, time());
            } else {
                $validmessages++;
            }
        }
        return $validmessages;
    }

    /**
     * Returns HTML to display a "Turn editing on/off" button in a form.
     *
     * @param moodle_url $url The URL + params to send through when clicking the button
     * @return string HTML the button
     */
    /*
    public function edit_button(moodle_url $url) {

        $url->param('sesskey', sesskey());
        if ($this->page->user_is_editing()) {
            $url->param('edit', 'off');
            $editstring = html_writer::tag('i',null,['class'=>'fa fa-toggle-on']).get_string('turneditingoff');
        } else {
            $url->param('edit', 'on');
            $editstring = html_writer::tag('i',null,['class'=>'fa fa-toggle-on']).get_string('turneditingon');
        }

        return $this->single_button($url, $editstring);
    }
    */

}
