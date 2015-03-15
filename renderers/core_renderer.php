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
 * bootstrap3_brum1975 theme with the underlying Bootstrap theme.
 *
 * @package    theme
 * @subpackage bootstrap3_brum1975
 * @author     Matthew Cannings
 * @author     Based on code originally written by G J Barnard, Mary Evans, Bas Brands, Stuart Lamour and David Scotson.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 class theme_brum1975bootstrap3_core_renderer extends theme_bootstrap_core_renderer {

 	/*
     * This renders a notification message.
     * Uses bootstrap compatible html.
     */
    public function notification($message, $classes = 'notifyproblem') {
        $message = clean_text($message);
        $type = '';

        if ($classes == 'notifyproblem') {
            $type = 'alert alert-error';
        }
        if ($classes == 'notifysuccess') {
            $type = 'alert alert-success';
        }
        if ($classes == 'notifymessage') {
            $type = 'alert alert-info';
        }
        if ($classes == 'redirectmessage') {
            $type = 'alert alert-block alert-info';
        }
        return "<div class=\"$type\">$message</div>";
    }

    /**
     * Outputs the page's footer
     * @return string HTML fragment
     */
    public function footer() {
        global $CFG, $DB, $USER;

        $output = $this->container_end_all(true);

        $footer = $this->opencontainers->pop('header/footer');

        if (debugging() and $DB and $DB->is_transaction_started()) {
            // TODO: MDL-20625 print warning - transaction will be rolled back
        }

        // Provide some performance info if required
        $performanceinfo = '';
        if (defined('MDL_PERF') || (!empty($CFG->perfdebug) and $CFG->perfdebug > 7)) {
            $perf = get_performance_info();
            if (defined('MDL_PERFTOLOG') && !function_exists('register_shutdown_function')) {
                error_log("PERF: " . $perf['txt']);
            }
            if (defined('MDL_PERFTOFOOT') || debugging() || $CFG->perfdebug > 7) {
                $performanceinfo = bootstrap3_brum1975_performance_output($perf);
            }
        }

        $footer = str_replace($this->unique_performance_info_token, $performanceinfo, $footer);

        $footer = str_replace($this->unique_end_html_token, $this->page->requires->get_end_code(), $footer);

        $this->page->set_state(moodle_page::STATE_DONE);

        if(!empty($this->page->theme->settings->persistentedit) && property_exists($USER, 'editing') && $USER->editing && !$this->really_editing) {
            $USER->editing = false;
        }

        return $output . $footer;
    }

    protected function render_custom_menu(custom_menu $menu) {
    	/*
    	* This code replaces adds the current enrolled
    	* courses to the custommenu.
    	*/
        global $CFG, $USER, $DB;
        $excludecategories = array(36,72,111,122,103,95,124,6,100,121,125,3,131,112);
        $url = $CFG->wwwroot.'/my/';
        $mycourses = enrol_get_my_courses(NULL, 'sortorder ASC');
        $content = html_writer::start_tag('ul', array('class'=>'nav  navbar-nav'));
        $content .= html_writer::start_tag('li',array('class'=>'dropdown'));
        $content .= html_writer::link($CFG->wwwroot.'/my/', 'My Courses<b class="caret"></b>',
            array('title'=>'My Courses', 'class'=>'dropdown-toggle' ,'data-toggle'=>'dropdown'));
        $content .= html_writer::start_tag('ul', array('class'=>'dropdown-menu', 'role'=>'menu'));
        if (!$mycourses) {//If has no courses then show minimum menu items
			$content .= html_writer::start_tag('li', array('class'=>'dropdown-submenu'));
			$url = $CFG->wwwroot.'/course/index.php?categoryid=2';//'.$catname->id;
			$content .= html_writer::link($url, "Students' Union",
					 array('class'=>'dropdown-toggle', 'data-toggle'=>'dropdown', 'title'=>"Students' Union"));
			$content .= html_writer::start_tag('ul', array('class'=>'dropdown-menu', 'role'=>'menu'));
			$url = $CFG->wwwroot.'/course/view.php?id=2';
			$content .= $this->render_custom_menu_entry($url, "Students' Union Main Site", null, true);
			$content .= html_writer::end_tag('ul');
			$content .= html_writer::end_tag('li');

			$content .= '<li class="divider"></li>';
            $url = $CFG->wwwroot.'/course/index.php';
            $content .= $this->render_custom_menu_entry($url, 'View All Courses');
        } else { // If has courses...how many and how many categories?
            $archcatcount = $currcatcount = $thisarchcat =0;
            $lastarchcat = $lastcurrcat = $thiscurrcat = 0;
            foreach ($mycourses as $c) {
                if (!in_array($c->category,$excludecategories)){
                    $sql = "SELECT path AS categorypath
                        FROM {course_categories}
                        WHERE id = ".$c->category."
                        LIMIT 1";
                    $thisyear = $DB->get_records_sql($sql);
                    foreach ($thisyear as $y) {
                        $currentcourses[] = $c;
                        $lastcurrcat = $thiscurrcat;
                        $thiscurrcat = $c->category;
                        if ($thiscurrcat!==$lastcurrcat) {
                            $currcatcount++;
                        }
                    }
                }
            }

            if ($currcatcount == 0) {
            //do nothing as they are not enrolled on any current courses
            } else if ($currcatcount == 1) {//Just the one category, then just show courses
                foreach ($currentcourses as $c) {
                    $url = $CFG->wwwroot.'/course/view.php?id='.$c->id;
                        if (has_capability('moodle/site:manageblocks', context_course::instance($c->id))) {
                            $content .= $this->render_custom_menu_entry($url, $c->fullname, null, true);
                        } else {
                            $content .= $this->render_custom_menu_entry($url, $c->fullname);
                        }
                }
            }  else { //this is going to involve sub-menus :(
                $lastcategory=0;
                foreach ($currentcourses as $c) {
                    $thiscategory=$c->category;
                    $catname= $DB->get_record("course_categories", array('id'=>$c->category));
                    //get this category if not same as current
                    if ($lastcategory==0 && $thiscategory!==$lastcategory) {
                        $content .= html_writer::start_tag('li', array('class'=>'dropdown-submenu'));

                        $url = $CFG->wwwroot.'/course/index.php?categoryid='.$catname->id;
                        $content .= html_writer::link($url, $catname->name,
                                     array('class'=>'dropdown-toggle', 'data-toggle'=>'dropdown', 'title'=>$catname->name));
                        $content .= html_writer::start_tag('ul', array('class'=>'dropdown-menu', 'role'=>'menu'));
                        $url = $CFG->wwwroot.'/course/view.php?id='.$c->id;
                        if (has_capability('moodle/site:manageblocks', context_course::instance($c->id))) {
                            $content .= $this->render_custom_menu_entry($url, $c->fullname, null, true);
                        } else {
                            $content .= $this->render_custom_menu_entry($url, $c->fullname);
                        }
                        $lastcategory = $thiscategory;
                    } elseif ($lastcategory>0&&$thiscategory!==$lastcategory) {
						if (isStaffMember()){
							$lastcatname= $DB->get_record("course_categories", array('id'=>$lastcategory));
							$url = $CFG->wwwroot.'/course/index.php?categoryid='.$lastcategory;
							$content .= '<li class="divider"></li>';
							$content .= $this->render_custom_menu_entry($url, 'All '.$lastcatname->name.' Courses');
						}
                        $content .= html_writer::end_tag('ul');
                        $content .= html_writer::end_tag('li');


                        $content .= html_writer::start_tag('li', array('class'=>'dropdown-submenu'));

                        $url = $CFG->wwwroot.'/course/index.php?categoryid='.$catname->id;
                        $content .= html_writer::link($url, $catname->name,
                                    array('class'=>'dropdown-toggle', 'data-toggle'=>'dropdown', 'title'=>$catname->name));
                        $content .= html_writer::start_tag('ul', array('class'=>'dropdown-menu', 'role'=>'menu'));
                        $url = $CFG->wwwroot.'/course/view.php?id='.$c->id;
                        if (has_capability('moodle/site:manageblocks', context_course::instance($c->id))) {
                            $content .= $this->render_custom_menu_entry($url, $c->fullname, null, true);
                        } else {
                            $content .= $this->render_custom_menu_entry($url, $c->fullname);
                        }
                        $lastcategory = $thiscategory;
                    } elseif($thiscategory==$lastcategory) {
                        $url = $CFG->wwwroot.'/course/view.php?id='.$c->id;
                        if (has_capability('moodle/site:manageblocks', context_course::instance($c->id))) {
                            $content .= $this->render_custom_menu_entry($url, $c->fullname, null, true);
                        } else {
                            $content .= $this->render_custom_menu_entry($url, $c->fullname);
                        }
                    }
                }
                $content .= html_writer::end_tag('ul');
                $content .= html_writer::end_tag('li');


            }
            $content .= '<li class="divider"></li>';
            $url = $CFG->wwwroot.'/course/index.php';
            $content .= html_writer::start_tag('li');
            $content .= html_writer::link($url, 'View All Courses');
            $content .= html_writer::end_tag('li');
        }
        $content .= html_writer::end_tag('ul');
        $content .= html_writer::end_tag('li');
        $content .= html_writer::end_tag('ul');
        return $content;
    }

    protected function render_user_menu(custom_menu $menu) {
        global $CFG, $USER, $DB;

        $addusermenu = true;
        $addlangmenu = true;

        $langs = get_string_manager()->get_list_of_translations();
        if (count($langs) < 2
        or empty($CFG->langmenu)
        or ($this->page->course != SITEID and !empty($this->page->course->lang))) {
            $addlangmenu = false;
        }

        if ($addlangmenu) {
            $language = $menu->add(get_string('language'), new moodle_url('#'), get_string('language'), 10000);
            foreach ($langs as $langtype => $langname) {
                $language->add($langname, new moodle_url($this->page->url, array('lang' => $langtype)), $langname);
            }
        }

        if ($addusermenu) {
            if (isloggedin()) {
                $usermenu = $menu->add(fullname($USER), new moodle_url('#'), fullname($USER), 10001);
                $usermenu->add(
                    '<span class="glyphicon glyphicon-home"></span>' . 'My Home',
                    new moodle_url('/my/', array('sesskey' => sesskey(), 'alt' => 'home')),
                    'My Home'
                );

                $usermenu->add(
                    '<span class="glyphicon glyphicon-user"></span>' . get_string('viewprofile'),
                    new moodle_url('/user/profile.php', array('id' => $USER->id)),
                    get_string('viewprofile')
                );

                $usermenu->add(
                    '<span class="glyphicon glyphicon-cog"></span>' . get_string('editmyprofile'),
                    new moodle_url('/user/edit.php', array('id' => $USER->id)),
                    get_string('editmyprofile')
                );
                $usermenu->add(
                    '<span class="glyphicon glyphicon-list-alt"></span>' . get_string('grades'),
                    new moodle_url('/course/user.php', array('mode' => 'grade' ,'id' => 1,'user' => $USER->id)),
                    get_string('grades')
                ); 
                $usermenu->add(
                    '<span class="glyphicon glyphicon-calendar"></span>' . get_string('calendar','calendar'),
                    new moodle_url('/calendar/view.php', array('id' => $USER->id)),
                    get_string('calendar','calendar')
                ); 
                $usermenu->add(
                    '<span class="glyphicon glyphicon-inbox"></span>' . get_string('messages','message'),
                    new moodle_url('/message/index.php', array('user1' => $USER->id)),
                    get_string('messages','message')
                );
                $usermenu->add(
                    '<span class="glyphicon glyphicon-briefcase"></span>' . get_string('coursebadges','badges'),
                    new moodle_url('/badges/mybadges.php', array('user1' => $USER->id)),
                    get_string('coursebadges','badges')
                );                 
                $usermenu->add(
                    '<span class="glyphicon glyphicon-off"></span>' . get_string('logout'),
                    new moodle_url('/login/logout.php', array('sesskey' => sesskey(), 'alt' => 'logout')),
                    get_string('logout')
                );                
            } else {
                $usermenu = $menu->add(get_string('login'), new moodle_url('/login/index.php'), get_string('login'), 10001);
            }
        }

        $content = '<ul class="nav navbar-nav navbar-right">';
        foreach ($menu->get_children() as $item) {
            $content .= $this->render_custom_menu_item($item, 1);
        }

        return $content.'</ul>';
    }    
    
    private function render_custom_menu_entry($url, $text, $title=null, $taught=false) {
        if (!$title) {$title = $text;}
        /*if ($taught) {
            $content = html_writer::start_tag('li', array('class'=>'taught'));
        } else { */
            $content = html_writer::start_tag('li');
        //}
        $content .= html_writer::link($url, $text,
                    array('title'=>$title));
        $content .= html_writer::end_tag('li');
        return $content;
    }


 	/*
    * This code replaces the icons in the Admin block with
    * FontAwesome variants where available.
    */

	protected function render_pix_icon(pix_icon $icon) {
		if (self::replace_moodle_icon($icon->pix) !== false && $icon->attributes['alt'] === '') {
			return self::replace_moodle_icon($icon->pix);
		} else {
			return parent::render_pix_icon($icon);
		}
	}

    private static function replace_moodle_icon($name) {
        $icons = array(
            'add' => 'plus',
            'book' => 'book',
            'chapter' => 'file',
            'docs' => 'question-sign',
            'generate' => 'gift',
            'i/backup' => 'cloud-download',
            'i/checkpermissions' => 'user',
            'i/edit' => 'pencil',
            'i/filter' => 'filter',
            'i/grades' => 'table',
            'i/group' => 'group',
            'i/hide' => 'eye',
            'i/import' => 'upload',
            'i/move_2d' => 'arrows',
            'i/navigationitem' => 'circle',
            'i/outcomes' => 'magic',
            'i/publish' => 'globe',
            'i/reload' => 'refresh',
            'i/report' => 'list-alt',
            'i/restore' => 'cloud-upload',
            'i/return' => 'repeat',
            'i/roles' => 'user',
            'i/settings' => 'cogs',
            'i/show' => 'eye-slash',
            'i/switchrole' => 'random',
            'i/user' => 'user',
            'i/users' => 'user',

        );
        if (isset($icons[$name])) {
            return "<i class=\"fa fa-$icons[$name]\" id=\"icon\"></i>";
        } else {
            return false;
        }
    }

    /**
    * Get the HTML for blocks in the given region.
    *
    * @since 2.5.1 2.6
    * @param string $region The region to get HTML for.
    * @return string HTML.
    * Written by G J Barnard
    */

    public function bootstrap3_brum1975_blocks($region, $classes = array(), $tag = 'aside') {
        $classes = (array)$classes;
        $classes[] = 'block-region';
        $attributes = array(
            'id' => 'block-region-'.preg_replace('#[^a-zA-Z0-9_\-]+#', '-', $region),
            'class' => join(' ', $classes),
            'data-blockregion' => $region,
            'data-droptarget' => '1'
        );
        return html_writer::tag($tag, $this->blocks_for_region($region), $attributes);
    }

    /**
    * Returns HTML to display a "Turn editing on/off" button in a form.
    *
    * @param moodle_url $url The URL + params to send through when clicking the button
    * @return string HTML the button
    * Written by G J Barnard
    */

    public function edit_button(moodle_url $url) {
        $url->param('sesskey', sesskey());
        if ($this->page->user_is_editing()) {
            $url->param('edit', 'off');
            $btn = 'btn-danger';
            $title = get_string('turneditingoff');
            $icon = 'fa-power-off';
            $label = get_string('turneditingoff').'&nbsp;';
        } else {
            $url->param('edit', 'on');
            $btn = 'btn-success';
            $title = get_string('turneditingon');
            $icon = 'fa-edit';
            $label = get_string('turneditingon').'&nbsp;';
        }
        return html_writer::tag('a', $label.html_writer::start_tag('i', array('class' => $icon.' fa fa-fw')).
               html_writer::end_tag('i'), array('href' => $url, 'class' => 'btn '.$btn, 'title' => $title));
    }

   /**
     * Return the standard string that says whether you are logged in (and switched
     * roles/logged in as another user).
     * @param bool $withlinks if false, then don't include any links in the HTML produced.
     * If not set, the default is the nologinlinks option from the theme config.php file,
     * and if that is not set, then links are included.
     * @return string HTML fragment.
     */
    public function login_info($withlinks = null) {
        global $USER, $CFG, $DB, $SESSION;

        if (during_initial_install()) {
            return '';
        }

        if (is_null($withlinks)) {
            $withlinks = empty($this->page->layout_options['nologinlinks']);
        }

        $loginpage = ((string)$this->page->url === get_login_url());
        $course = $this->page->course;
        if (\core\session\manager::is_loggedinas()) {
            $realuser = \core\session\manager::get_realuser();
            $fullname = fullname($realuser, true);
            if ($withlinks) {
                $loginastitle = get_string('loginas');
                $realuserinfo = " [<a href=\"$CFG->wwwroot/course/loginas.php?id=$course->id&amp;sesskey=".sesskey()."\"";
                $realuserinfo .= "title =\"".$loginastitle."\">$fullname</a>] ";
            } else {
                $realuserinfo = " [$fullname] ";
            }
        } else {
            $realuserinfo = '';
        }

        $loginurl = get_login_url();

        if (empty($course->id)) {
            // $course->id is not defined during installation
            return '';
        } else if (isloggedin()) {
            $context = context_course::instance($course->id);

            $fullname = fullname($USER, true);
            // Since Moodle 2.0 this link always goes to the public profile page (not the course profile page)
            if ($withlinks) {
                $linktitle = get_string('viewprofile');
                $username = "<a href=\"$CFG->wwwroot/user/profile.php?id=$USER->id\" title=\"$linktitle\">$fullname</a>";
            } else {
                $username = $fullname;
            }
            if (is_mnet_remote_user($USER) and $idprovider = $DB->get_record('mnet_host', array('id'=>$USER->mnethostid))) {
                if ($withlinks) {
                    $username .= " from <a href=\"{$idprovider->wwwroot}\">{$idprovider->name}</a>";
                } else {
                    $username .= " from {$idprovider->name}";
                }
            }
            if (isguestuser()) {
                $loggedinas = $realuserinfo.get_string('loggedinasguest');
                if (!$loginpage && $withlinks) {
                    $loggedinas .= " (<a href=\"$loginurl\">".get_string('login').'</a>)';
                }
            } else if (is_role_switched($course->id)) { // Has switched roles
                $rolename = '';
                if ($role = $DB->get_record('role', array('id'=>$USER->access['rsw'][$context->path]))) {
                    $rolename = ': '.role_get_name($role, $context);
                }
                $loggedinas = get_string('loggedinas', 'moodle', $username).$rolename;
                if ($withlinks) {
                    $url = new moodle_url('/course/switchrole.php', array('id'=>$course->id,'sesskey'=>sesskey(), 'switchrole'=>0, 'returnurl'=>$this->page->url->out_as_local_url(false)));
                    $loggedinas .= '('.html_writer::tag('a', get_string('switchrolereturn'), array('href'=>$url)).')';
                }
            } else {
                $loggedinas = $realuserinfo.get_string('loggedinas', 'moodle', $username);
                if ($withlinks) {
                    $loggedinas .= " (<a href=\"$CFG->wwwroot/login/logout.php?sesskey=".sesskey()."\">".get_string('logout').'</a>)';
                }
            }
        } else {
            $loggedinas = get_string('loggedinnot', 'moodle');
            if (!$loginpage && $withlinks) {
                $loggedinas .= " (<a href=\"$loginurl\">".get_string('login').'</a>)';
            }
            $loggedinas = "<a class=\"btn\" href=\"$loginurl\">".get_string('login').'</a>';
        }

        $loggedinas = '<div class="logininfo">'.$loggedinas.'</div>';

        if (isset($SESSION->justloggedin)) {
            unset($SESSION->justloggedin);
            if (!empty($CFG->displayloginfailures)) {
                if (!isguestuser()) {
                    // Include this file only when required.
                    require_once($CFG->dirroot . '/user/lib.php');
                    if ($count = user_count_login_failures($USER)) {
                        $loggedinas .= '<div class="loginfailures">';
                        $a = new stdClass();
                        $a->attempts = $count;
                        $loggedinas .= get_string('failedloginattempts', '', $a);
                        if (file_exists("$CFG->dirroot/report/log/index.php") and has_capability('report/log:view', context_system::instance())) {
                            $loggedinas .= html_writer::link(new moodle_url('/report/log/index.php', array('chooselog' => 1,
                                    'id' => 0 , 'modid' => 'site_errors')), '(' . get_string('logs') . ')');
                        }
                        $loggedinas .= '</div>';
                    }
                }
            }
        }

        return $loggedinas;
    }


}


include_once($CFG->dirroot . "/course/format/topics/renderer.php");

class theme_brum1975bootstrap3_format_topics_renderer extends format_topics_renderer {
    /*
    protected function get_nav_links($course, $sections, $sectionno) {
        // FIXME: This is really evil and should by using the navigation API.
        $course = course_get_format($course)->get_course();
        $previousarrow= '<i class="fa fa-chevron-circle-left"></i>';
        $nextarrow= '<i class="fa fa-chevron-circle-right"></i>';
        $canviewhidden = has_capability('moodle/course:viewhiddensections', context_course::instance($course->id))
            or !$course->hiddensections;

        $links = array('previous' => '', 'next' => '');
        $back = $sectionno - 1;
        while ($back > 0 and empty($links['previous'])) {
            if ($canviewhidden || $sections[$back]->uservisible) {
                $params = array('id' => 'previous_section');
                if (!$sections[$back]->visible) {
                    $params = array('class' => 'dimmed_text');
                }
                $previouslink = html_writer::start_tag('div', array('class' => 'nav_icon'));
                $previouslink .= $previousarrow;
                $previouslink .= html_writer::end_tag('div');
                $previouslink .= html_writer::start_tag('span', array('class' => 'text'));
                $previouslink .= html_writer::start_tag('span', array('class' => 'nav_guide'));
                $previouslink .= get_string('previoussection', 'theme_brum1975bootstrap3');
                $previouslink .= html_writer::end_tag('span');
                $previouslink .= html_writer::empty_tag('br');
                $previouslink .= get_section_name($course, $sections[$back]);
                $previouslink .= html_writer::end_tag('span');
                $links['previous'] = html_writer::link(course_get_url($course, $back), $previouslink, $params);
            }
            $back--;
        }

        $forward = $sectionno + 1;
        while ($forward <= $course->numsections and empty($links['next'])) {
            if ($canviewhidden || $sections[$forward]->uservisible) {
                $params = array('id' => 'next_section');
                if (!$sections[$forward]->visible) {
                    $params = array('class' => 'dimmed_text');
                }
                $nextlink = html_writer::start_tag('div', array('class' => 'nav_icon'));
                $nextlink .= $nextarrow;
                $nextlink .= html_writer::end_tag('div');
                $nextlink .= html_writer::start_tag('span', array('class' => 'text'));
                $nextlink .= html_writer::start_tag('span', array('class' => 'nav_guide'));
                $nextlink .= get_string('nextsection', 'theme_brum1975bootstrap3');
                $nextlink .= html_writer::end_tag('span');
                $nextlink .= html_writer::empty_tag('br');
                $nextlink .= get_section_name($course, $sections[$forward]);
                $nextlink .= html_writer::end_tag('span');
                $links['next'] = html_writer::link(course_get_url($course, $forward), $nextlink, $params);
            }
            $forward++;
        }

        return $links;
    }
    */
    /**
     * Output the html for a multiple section page
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections (argument not used)
     * @param array $mods (argument not used)
     * @param array $modnames (argument not used)
     * @param array $modnamesused (argument not used)
     */
    public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused) {
        global $PAGE;

        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();

        $context = context_course::instance($course->id);
        // Title with completion help icon.
        $completioninfo = new completion_info($course);
        echo $completioninfo->display_help_icon();
        echo $this->output->heading($this->page_title(), 2, 'accesshide');

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, 0);

        // Now the list of sections..
        echo $this->start_section_list();

        foreach ($modinfo->get_section_info_all() as $section => $thissection) {
            if ($section == 0) {
                // 0-section is displayed a little different then the others
                if ($thissection->summary or !empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
                    echo $this->section_header($thissection, $course, false, 0);
                    echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                    echo $this->courserenderer->course_section_add_cm_control($course, 0, 0);
                    echo $this->section_footer();
                }
                continue;
            }
            if ($section > $course->numsections) {
                // activities inside this section are 'orphaned', this section will be printed as 'stealth' below
                continue;
            }
            // Show the section if the user is permitted to access it, OR if it's not available
            // but there is some available info text which explains the reason & should display.
            $showsection = $thissection->uservisible ||
                    ($thissection->visible && !$thissection->available &&
                    !empty($thissection->availableinfo));
            if (!$showsection) {
                // If the hiddensections option is set to 'show hidden sections in collapsed
                // form', then display the hidden section message - UNLESS the section is
                // hidden by the availability system, which is set to hide the reason.
                if (!$course->hiddensections && $thissection->available) {
                    echo $this->section_hidden($section, $course->id);
                }

                continue;
            }

            if (!$PAGE->user_is_editing() && $course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                // Display section summary only.
                echo $this->section_summary($thissection, $course, null);
            } else {
                echo $this->section_header($thissection, $course, false, 0);
                if ($thissection->uservisible) {
                    echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                    echo $this->courserenderer->course_section_add_cm_control($course, $section, 0);
                }
                echo $this->section_footer();
            }
        }

        if ($PAGE->user_is_editing() and has_capability('moodle/course:update', $context)) {
            // Print stealth sections if present.
            foreach ($modinfo->get_section_info_all() as $section => $thissection) {
                if ($section <= $course->numsections or empty($modinfo->sections[$section])) {
                    // this is not stealth section or it is empty
                    continue;
                }
                echo $this->stealth_section_header($section);
                echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                echo $this->stealth_section_footer();
            }

            echo $this->end_section_list();

            echo html_writer::start_tag('div', array('id' => 'changenumsections', 'class' => 'mdl-right'));

            echo html_writer::start_tag('div', array('class'=>'btn-group'));
            // Increase number of sections.
            $straddsection = get_string('increasesections', 'moodle');
            $url = new moodle_url('/course/changenumsections.php',
                array('courseid' => $course->id,
                      'increase' => true,
                      'sesskey' => sesskey()));

            echo html_writer::link($url, '+', array('class' => 'increase-sections btn btn-success','type'=>'button'));

            echo html_writer::start_tag('button', array('type'=>'button','class'=>'btn btn-success'));
            echo 'Sections';
            echo html_writer::end_tag('button');
            if ($course->numsections > 0) {
                // Reduce number of sections sections.
                $strremovesection = get_string('reducesections', 'moodle');
                $url = new moodle_url('/course/changenumsections.php',
                    array('courseid' => $course->id,
                          'increase' => false,
                          'sesskey' => sesskey()));
                //$icon = $this->output->pix_icon('t/switch_plus', $strremovesection);
                echo html_writer::link($url, '-', array('class' => 'increase-sections btn btn-success','type'=>'button'));
            }



            echo html_writer::end_tag('div');
        } else {
            echo $this->end_section_list();
        }

    }

}

include_once($CFG->dirroot . "/course/format/weeks/renderer.php");

class theme_brum1975bootstrap3_format_weeks_renderer extends format_weeks_renderer {
    /*
    protected function get_nav_links($course, $sections, $sectionno) {
        // FIXME: This is really evil and should by using the navigation API.
        $course = course_get_format($course)->get_course();
        $previousarrow= '<i class="fa fa-chevron-circle-left"></i>';
        $nextarrow= '<i class="fa fa-chevron-circle-right"></i>';
        $canviewhidden = has_capability('moodle/course:viewhiddensections', context_course::instance($course->id))
            or !$course->hiddensections;

        $links = array('previous' => '', 'next' => '');
        $back = $sectionno - 1;
        while ($back > 0 and empty($links['previous'])) {
            if ($canviewhidden || $sections[$back]->uservisible) {
                $params = array('id' => 'previous_section');
                if (!$sections[$back]->visible) {
                    $params = array('class' => 'dimmed_text');
                }
                $previouslink = html_writer::start_tag('div', array('class' => 'nav_icon'));
                $previouslink .= $previousarrow;
                $previouslink .= html_writer::end_tag('div');
                $previouslink .= html_writer::start_tag('span', array('class' => 'text'));
                $previouslink .= html_writer::start_tag('span', array('class' => 'nav_guide'));
                $previouslink .= get_string('previousweek', 'theme_brum1975bootstrap3');
                $previouslink .= html_writer::end_tag('span');
                $previouslink .= html_writer::empty_tag('br');
                $previouslink .= get_section_name($course, $sections[$back]);
                $previouslink .= html_writer::end_tag('span');
                $links['previous'] = html_writer::link(course_get_url($course, $back), $previouslink, $params);
            }
            $back--;
        }

        $forward = $sectionno + 1;
        while ($forward <= $course->numsections and empty($links['next'])) {
            if ($canviewhidden || $sections[$forward]->uservisible) {
                $params = array('id' => 'next_section');
                if (!$sections[$forward]->visible) {
                    $params = array('class' => 'dimmed_text');
                }
                $nextlink = html_writer::start_tag('div', array('class' => 'nav_icon'));
                $nextlink .= $nextarrow;
                $nextlink .= html_writer::end_tag('div');
                $nextlink .= html_writer::start_tag('span', array('class' => 'text'));
                $nextlink .= html_writer::start_tag('span', array('class' => 'nav_guide'));
                $nextlink .= get_string('nextweek', 'theme_brum1975bootstrap3');
                $nextlink .= html_writer::end_tag('span');
                $nextlink .= html_writer::empty_tag('br');
                $nextlink .= get_section_name($course, $sections[$forward]);
                $nextlink .= html_writer::end_tag('span');
                $links['next'] = html_writer::link(course_get_url($course, $forward), $nextlink, $params);
            }
            $forward++;
        }

        return $links;
    }
    */
    /**
     * Output the html for a multiple section page
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections (argument not used)
     * @param array $mods (argument not used)
     * @param array $modnames (argument not used)
     * @param array $modnamesused (argument not used)
     */
    public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused) {
        global $PAGE;

        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();

        $context = context_course::instance($course->id);
        // Title with completion help icon.
        $completioninfo = new completion_info($course);
        echo $completioninfo->display_help_icon();
        echo $this->output->heading($this->page_title(), 2, 'accesshide');

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, 0);

        // Now the list of sections..
        echo $this->start_section_list();

        foreach ($modinfo->get_section_info_all() as $section => $thissection) {
            if ($section == 0) {
                // 0-section is displayed a little different then the others
                if ($thissection->summary or !empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
                    echo $this->section_header($thissection, $course, false, 0);
                    echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                    echo $this->courserenderer->course_section_add_cm_control($course, 0, 0);
                    echo $this->section_footer();
                }
                continue;
            }
            if ($section > $course->numsections) {
                // activities inside this section are 'orphaned', this section will be printed as 'stealth' below
                continue;
            }
            // Show the section if the user is permitted to access it, OR if it's not available
            // but there is some available info text which explains the reason & should display.
            $showsection = $thissection->uservisible ||
                    ($thissection->visible && !$thissection->available &&
                    !empty($thissection->availableinfo));
            if (!$showsection) {
                // If the hiddensections option is set to 'show hidden sections in collapsed
                // form', then display the hidden section message - UNLESS the section is
                // hidden by the availability system, which is set to hide the reason.
                if (!$course->hiddensections && $thissection->available) {
                    echo $this->section_hidden($section, $course->id);
                }

                continue;
            }

            if (!$PAGE->user_is_editing() && $course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                // Display section summary only.
                echo $this->section_summary($thissection, $course, null);
            } else {
                echo $this->section_header($thissection, $course, false, 0);
                if ($thissection->uservisible) {
                    echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                    echo $this->courserenderer->course_section_add_cm_control($course, $section, 0);
                }
                echo $this->section_footer();
            }
        }

        if ($PAGE->user_is_editing() and has_capability('moodle/course:update', $context)) {
            // Print stealth sections if present.
            foreach ($modinfo->get_section_info_all() as $section => $thissection) {
                if ($section <= $course->numsections or empty($modinfo->sections[$section])) {
                    // this is not stealth section or it is empty
                    continue;
                }
                echo $this->stealth_section_header($section);
                echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                echo $this->stealth_section_footer();
            }

            echo $this->end_section_list();

            echo html_writer::start_tag('div', array('id' => 'changenumsections', 'class' => 'mdl-right'));

            echo html_writer::start_tag('div', array('class'=>'btn-group'));
            // Increase number of sections.
            $straddsection = get_string('increasesections', 'moodle');
            $url = new moodle_url('/course/changenumsections.php',
                array('courseid' => $course->id,
                      'increase' => true,
                      'sesskey' => sesskey()));

            echo html_writer::link($url, '+', array('class' => 'increase-sections btn btn-success','type'=>'button'));

            echo html_writer::start_tag('button', array('type'=>'button','class'=>'btn btn-success'));
            echo 'Sections';
            echo html_writer::end_tag('button');
            if ($course->numsections > 0) {
                // Reduce number of sections sections.
                $strremovesection = get_string('reducesections', 'moodle');
                $url = new moodle_url('/course/changenumsections.php',
                    array('courseid' => $course->id,
                          'increase' => false,
                          'sesskey' => sesskey()));
                //$icon = $this->output->pix_icon('t/switch_plus', $strremovesection);
                echo html_writer::link($url, '-', array('class' => 'increase-sections btn btn-success','type'=>'button'));
            }



            echo html_writer::end_tag('div');
        } else {
            echo $this->end_section_list();
        }

    }

}
