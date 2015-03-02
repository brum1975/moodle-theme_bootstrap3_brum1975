<header id="page-header" class="clearfix">
    <?php //echo $html->heading; ?>
    <div id="page-navbar" class="clearfix">
        <nav class="breadcrumb-nav"><?php echo $OUTPUT->navbar(); ?></nav>
        <span class="breadcrumb-button">
        <?php
        if ($COURSE->id > 1){
            if (has_capability('moodle/role:switchroles', context_course::instance($COURSE->id))){
                $key = 5;//Student role
                $url = new moodle_url('/course/switchrole.php', array('id'=>$COURSE->id, 'sesskey'=>sesskey(), 'switchrole'=>$key, 'returnurl'=>$this->page->url->out_as_local_url(false)));
                $text = 'Switch To Student View';
                $icon ='random';

                echo '<a id="role-switcher" href="'.$url.'" class="btn btn-info" title="'.$text.'">'.$text.'&nbsp;<i class="fa-'.$icon.' fa fa-fw"></i></a>';
            }
            if (is_role_switched($COURSE->id)) {
                $key = 0;//Student role
                $url = new moodle_url('/course/switchrole.php', array('id'=>$COURSE->id, 'sesskey'=>sesskey(), 'switchrole'=>$key, 'returnurl'=>$this->page->url->out_as_local_url(false)));
                $text = 'Return To Normal Role';
                $icon ='reply';
                echo '<a id="role-switcher" href="'.$url.'" class="btn btn-info" title="'.$text.'">'.$text.'&nbsp;<i class="fa-'.$icon.' fa fa-fw"></i></a>';

            }
        }
        ?>


                <?php echo $OUTPUT->page_heading_button(); ?>
        </span>
    </div>
    <div id="course-header">
    <?php echo $OUTPUT->course_header(); ?>
    </div>
</header>
