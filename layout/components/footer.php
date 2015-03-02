<?php
$footerl = 'footer-left';
$footerm = 'footer-middle';
$footerr = 'footer-right';
$hasfacebook    = (empty($PAGE->theme->settings->facebook)) ? false : $PAGE->theme->settings->facebook;
$hastwitter     = (empty($PAGE->theme->settings->twitter)) ? false : $PAGE->theme->settings->twitter;
$hasgoogleplus  = (empty($PAGE->theme->settings->googleplus)) ? false : $PAGE->theme->settings->googleplus;
$haslinkedin    = (empty($PAGE->theme->settings->linkedin)) ? false : $PAGE->theme->settings->linkedin;
$hasyoutube     = (empty($PAGE->theme->settings->youtube)) ? false : $PAGE->theme->settings->youtube;
$hasflickr      = (empty($PAGE->theme->settings->flickr)) ? false : $PAGE->theme->settings->flickr;
$hasvk          = (empty($PAGE->theme->settings->vk)) ? false : $PAGE->theme->settings->vk;
$haspinterest   = (empty($PAGE->theme->settings->pinterest)) ? false : $PAGE->theme->settings->pinterest;
$hasinstagram   = (empty($PAGE->theme->settings->instagram)) ? false : $PAGE->theme->settings->instagram;
$hasskype       = (empty($PAGE->theme->settings->skype)) ? false : $PAGE->theme->settings->skype;
$hasios         = (empty($PAGE->theme->settings->ios)) ? false : $PAGE->theme->settings->ios;
$hasandroid     = (empty($PAGE->theme->settings->android)) ? false : $PAGE->theme->settings->android;
$haswebsite     = (empty($PAGE->theme->settings->website)) ? false : $PAGE->theme->settings->website;
// If any of the above social networks are true, sets this to true.
$hassocialnetworks = ($hasfacebook || $hastwitter || $hasgoogleplus || $hasflickr || $hasinstagram || $hasvk || $haslinkedin || $haspinterest || $hasskype || $haslinkedin || $haswebsite || $hasyoutube ) ? true : false;
$hasmobileapps = ($hasios || $hasandroid ) ? true : false;
?>

<footer id="page-footer">
	<div class="row-fluid">
		<div class="span4">
        </div>
		<div id="bootstrap3_brum1975-footer" class="span4">
        <div id="course-footer"><?php echo $OUTPUT->course_footer(); ?></div>
        <p class="helplink"><?php echo $OUTPUT->page_doc_link(); ?></p>
        <?php
        echo $html->footnote;
        echo $OUTPUT->login_info();
        echo $OUTPUT->home_link();
        echo $OUTPUT->standard_footer_html();
        ?>
        </div>
		<div class="span4">
        <?php
        if ($hassocialnetworks) {
        ?>
		<div id="social-table">
        <p id="socialheading"><?php echo 'College Social'?></p>
            <ul class="socials">
                <?php if ($hasgoogleplus) { ?>
                <li><a href="<?php echo $hasgoogleplus; ?>" class="socialicon googleplus"><i class="fa fa-google-plus fa-inverse"></i></a></li>
                <?php } ?>
                <?php if ($hasfacebook) { ?>
                <li><a href="<?php echo $hasfacebook; ?>" class="socialicon facebook"><i class="fa fa-facebook fa-inverse"></i></a></li>
                <?php } ?>                
                <?php if ($hastwitter) { ?>
                <li><a href="<?php echo $hastwitter; ?>" class="socialicon twitter"><i class="fa fa-twitter fa-inverse"></i></a></li>
                <?php } ?>

                <?php if ($haslinkedin) { ?>
                <li><a href="<?php echo $haslinkedin; ?>" class="socialicon linkedin"><i class="fa fa-linkedin fa-inverse"></i></a></li>
                <?php } ?>
                <?php if ($hasyoutube) { ?>
                <li><a href="<?php echo $hasyoutube; ?>" class="socialicon youtube"><i class="fa fa-youtube fa-inverse"></i></a></li>
                <?php } ?>
                <?php if ($hasflickr) { ?>
                <li><a href="<?php echo $hasflickr; ?>" class="socialicon flickr"><i class="fa fa-flickr fa-inverse"></i></a></li>
                <?php } ?>
                <?php if ($haspinterest) { ?>
                <li><a href="<?php echo $haspinterest; ?>" class="socialicon pinterest"><i class="fa fa-pinterest fa-inverse"></i></a></li>
                <?php } ?>
                <?php if ($hasinstagram) { ?>
                <li><a href="<?php echo $hasinstagram; ?>" class="socialicon instagram"><i class="fa fa-instagram fa-inverse"></i></a></li>
                <?php } ?>
                <?php /*if ($hasvk) { ?>
                <li><a href="<?php echo $hasvk; ?>" class="socialicon vk"><i class="fa fa-vk fa-inverse"></i></a></li>
                <?php } */?>
                <?php if ($hasskype) { ?>
                <li><a href="<?php echo $haskype; ?>" class="socialicon skype"><i class="fa fa-skype fa-inverse"></i></a></li>
                <?php } ?>
                <?php if ($haswebsite) { ?>
                		<li><a href="<?php echo $haswebsite; ?>" class="socialicon website"><i class="fa fa-globe fa-inverse"></i></a></li>
                <?php } ?>
	    </ul>
		</div>
        <?php 
        }
        ?>
        </div>        
 	</div>

</footer>