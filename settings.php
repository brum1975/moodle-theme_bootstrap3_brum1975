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
 *
 * For full information about creating Moodle themes, see:
 * http://docs.moodle.org/dev/Themes_2.0
 *
 * @package   theme_bootstrap3_brum1975
 * @copyright 2013 Moodle, moodle.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$ADMIN->add('themes', new admin_category('theme_bootstrap3_brum1975', 'brum1975'));
$settings = null;
    // "genericsettings" settingpage
	$temp = new admin_settingpage('theme_bootstrap3_brum1975_generic',  get_string('genericsettings', 'theme_bootstrap3_brum1975'));


    // Invert Navbar to dark background.
    $name = 'theme_bootstrap3_brum1975/invert';
    $title = get_string('invert', 'theme_bootstrap3_brum1975');
    $description = get_string('invertdesc', 'theme_bootstrap3_brum1975');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Logo file setting.
    $name = 'theme_bootstrap3_brum1975/logo';
    $title = get_string('logo','theme_bootstrap3_brum1975');
    $description = get_string('logodesc', 'theme_bootstrap3_brum1975');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'logo');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Custom CSS file.
    $name = 'theme_bootstrap3_brum1975/customcss';
    $title = get_string('customcss', 'theme_bootstrap3_brum1975');
    $description = get_string('customcssdesc', 'theme_bootstrap3_brum1975');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Footnote setting.
    $name = 'theme_bootstrap3_brum1975/footnote';
    $title = get_string('footnote', 'theme_bootstrap3_brum1975');
    $description = get_string('footnotedesc', 'theme_bootstrap3_brum1975');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    // Include Awesome Font from Bootstrapcdn
/*    $name = 'theme_bootstrap3_brum1975/bootstrapcdn';
    $title = get_string('bootstrapcdn', 'theme_bootstrap3_brum1975');
    $description = get_string('bootstrapcdndesc', 'theme_bootstrap3_brum1975');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);   

    // Toggle dashboard display in custommenu.
    $name = 'theme_bootstrap3_brum1975/displaymydashboard';
    $title = get_string('displaymydashboard', 'theme_bootstrap3_brum1975');
    $description = get_string('displaymydashboarddesc', 'theme_bootstrap3_brum1975');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting); 
*/
    // Toggle courses display in custommenu.
    $name = 'theme_bootstrap3_brum1975/displaymycourses';
    $title = get_string('displaymycourses', 'theme_bootstrap3_brum1975');
    $description = get_string('displaymycoursesdesc', 'theme_bootstrap3_brum1975');
    $default = true;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default, true, false);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);    
    
    // Set terminology for dropdown course list
	$name = 'theme_bootstrap3_brum1975/mycoursetitle';
	$title = get_string('mycoursetitle','theme_bootstrap3_brum1975');
	$description = get_string('mycoursetitledesc', 'theme_bootstrap3_brum1975');
	$default = 'course';
	$choices = array(
		'course' => get_string('mycourses', 'theme_bootstrap3_brum1975'),
		'unit' => get_string('myunits', 'theme_bootstrap3_brum1975'),
		'class' => get_string('myclasses', 'theme_bootstrap3_brum1975'),
		'module' => get_string('mymodules', 'theme_bootstrap3_brum1975')
	);
	$setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
	$setting->set_updatedcallback('theme_reset_all_caches');
	$temp->add($setting);    
    
    $ADMIN->add('theme_bootstrap3_brum1975', $temp);

	/* Social Network Settings */
	$temp = new admin_settingpage('theme_bootstrap3_brum1975_social', get_string('socialheading', 'theme_bootstrap3_brum1975'));
	$temp->add(new admin_setting_heading('theme_bootstrap3_brum1975_social', get_string('socialheadingsub', 'theme_bootstrap3_brum1975'),
            format_text(get_string('socialdesc' , 'theme_bootstrap3_brum1975'), FORMAT_MARKDOWN)));
	
    // Website url setting.
    $name = 'theme_bootstrap3_brum1975/website';
    $title = get_string('website', 'theme_bootstrap3_brum1975');
    $description = get_string('websitedesc', 'theme_bootstrap3_brum1975');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    // Facebook url setting.
    $name = 'theme_bootstrap3_brum1975/facebook';
    $title = get_string(    	'facebook', 'theme_bootstrap3_brum1975');
    $description = get_string(    	'facebookdesc', 'theme_bootstrap3_brum1975');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    // Flickr url setting.
    $name = 'theme_bootstrap3_brum1975/flickr';
    $title = get_string('flickr', 'theme_bootstrap3_brum1975');
    $description = get_string('flickrdesc', 'theme_bootstrap3_brum1975');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Twitter url setting.
    $name = 'theme_bootstrap3_brum1975/twitter';
    $title = get_string('twitter', 'theme_bootstrap3_brum1975');
    $description = get_string('twitterdesc', 'theme_bootstrap3_brum1975');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Google+ url setting.
    $name = 'theme_bootstrap3_brum1975/googleplus';
    $title = get_string('googleplus', 'theme_bootstrap3_brum1975');
    $description = get_string('googleplusdesc', 'theme_bootstrap3_brum1975');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // LinkedIn url setting.
    $name = 'theme_bootstrap3_brum1975/linkedin';
    $title = get_string('linkedin', 'theme_bootstrap3_brum1975');
    $description = get_string('linkedindesc', 'theme_bootstrap3_brum1975');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    // Pinterest url setting.
    $name = 'theme_bootstrap3_brum1975/pinterest';
    $title = get_string('pinterest', 'theme_bootstrap3_brum1975');
    $description = get_string('pinterestdesc', 'theme_bootstrap3_brum1975');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    // Instagram url setting.
    $name = 'theme_bootstrap3_brum1975/instagram';
    $title = get_string('instagram', 'theme_bootstrap3_brum1975');
    $description = get_string('instagramdesc', 'theme_bootstrap3_brum1975');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    // YouTube url setting.
    $name = 'theme_bootstrap3_brum1975/youtube';
    $title = get_string('youtube', 'theme_bootstrap3_brum1975');
    $description = get_string('youtubedesc', 'theme_bootstrap3_brum1975');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
    
    // Skype url setting.
    $name = 'theme_bootstrap3_brum1975/skype';
    $title = get_string('skype', 'theme_bootstrap3_brum1975');
    $description = get_string('skypedesc', 'theme_bootstrap3_brum1975');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);
 /*
    // VKontakte url setting.
    $name = 'theme_bootstrap3_brum1975/vk';
    $title = get_string('vk', 'theme_bootstrap3_brum1975');
    $description = get_string('vkdesc', 'theme_bootstrap3_brum1975');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting); 
 */   
    $ADMIN->add('theme_bootstrap3_brum1975', $temp);
    
    
