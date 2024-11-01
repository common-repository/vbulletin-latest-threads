<?php  
/* 
	Plugin Name: Vbulletin Latest threads
	Plugin URI: http://vbulletinlt.toforge.com
	Description: Widget for display the latest vbullettin forum threads. This plugin use the external rss service of vbullettin forum and load the threads, also with user's avatar, in asynchronous mode.
	Author: Mauro Rocco
	Version: 0.1
	Author URI: http://www.rmhomepages.com 

    Copyright 2009  Mauro Rocco  (email : fireantology@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action("widgets_init", array('Vbulletin_lt', 'register'));

function vbulletin_lt_header() {
	echo "<link rel=\"stylesheet\" media=\"screen\" type=\"text/css\" href=\"".get_bloginfo('wpurl')."/wp-content/plugins/vbulletin-latest-threads/css/vbulletin_lt.css\"/>";
	echo "<script type=\"text/javascript\" src=\"".get_bloginfo('wpurl')."/wp-content/plugins/vbulletin-latest-threads/js/vbulletin_lt.js.php\"></script>";
}

add_action('wp_head', 'vbulletin_lt_header');


class Vbulletin_lt{

	function Vbulletin_lt() { //constructor
		
	}

	function control(){
		$data = get_option('vbulletin_lt');
	?>
		<p>
			<label for="vbulletin_lt_title">Title:
				<input class="widefat"  name="vbulletin_lt_title" type="text" value="<?php echo $data['title']; ?>" />
			</label>
		</p>
		<p>
			<label for="vbulletin_lt_forum_url">Forum url (with final slash "/"):
				<input class="widefat" name="vbulletin_lt_forum_url" type="text" value="<?php echo $data['forum_url']; ?>" />
			</label>
		</p>
		<p>
			<label for="vbulletin_lt_vb_charset">Vbulletin charset:
				<select class="widefat" name="vbulletin_lt_vb_charset">
					<option <?php if($data['vb_charset']=="UTF-8") echo "selected"; ?> value="UTF-8">UTF-8</option>
					<option <?php if($data['vb_charset']=="ISO-8859-1") echo "selected"; ?> value="ISO-8859-1">ISO-8859-1</option>
				</select>		
			</label>
		</p>
		<p>
			<label for="vbulletin_lt_wp_charset">Wordpress charset:
				<select class="widefat" name="vbulletin_lt_wp_charset">
					<option <?php if($data['wp_charset']=="UTF-8") echo "selected"; ?> value="UTF-8">UTF-8</option>
					<option <?php if($data['wp_charset']=="ISO-8859-1") echo "selected"; ?> value="ISO-8859-1">ISO-8859-1</option>
				</select>
			</label>
		</p>
		<p>
			<label for="vbulletin_lt_wp_limit">Max number of threads:
				<input class="widefat"  name="vbulletin_lt_limit" type="text" value="<?php echo $data['limit']; ?>" />
			</label>
		</p>
		<p>
			<label for="vbulletin_lt_show_avatar">Show user's avatar:
				<select class="widefat" name="vbulletin_lt_show_avatar">
					<option <?php if($data['show_avatar']=="Y") echo "selected"; ?> value="Y">YES</option>
					<option <?php if($data['show_avatar']=="N") echo "selected"; ?> value="N">NO</option>
				</select>
			</label>
		</p>
		<p>
			<label for="vbulletin_lt_wp_maxchar">N. of title's character (0 for unlim.):
				<input class="widefat"  name="vbulletin_lt_maxchar" type="text" value="<?php echo $data['maxchar']; ?>" />
			</label>
		</p>		
	<?php
		if (isset($_POST['vbulletin_lt_title'])){
			$data['title'] = attribute_escape($_POST['vbulletin_lt_title']);
			$data['forum_url'] = attribute_escape($_POST['vbulletin_lt_forum_url']);
			$data['vb_charset'] = attribute_escape($_POST['vbulletin_lt_vb_charset']);
			$data['wp_charset'] = attribute_escape($_POST['vbulletin_lt_wp_charset']);
			$data['limit'] = attribute_escape($_POST['vbulletin_lt_limit']);
			$data['show_avatar'] = attribute_escape($_POST['vbulletin_lt_show_avatar']);
			$data['maxchar'] = attribute_escape($_POST['vbulletin_lt_maxchar']);
			update_option('vbulletin_lt', $data);
		}
  	}

	function widget($args) {
		extract($args);
		$data = get_option('vbulletin_lt');
		echo $before_widget;
		echo $before_title.$data['title'].$after_title;
		echo "<ul id=\"vbulletin_lt_content\">";
		echo "</ul>";
		echo $after_widget;	
	}

	function get_content(){
		require_once('include/functions.php'); 
		$data = get_option('vbulletin_lt');
		if(strlen($data['forum_url'])<10){
			echo "Vbulletin Latest Threads error: Invalid url";
			return;
		}

		if(!is_numeric($data['limit'])){
			echo "Vbulletin Latest Threads error: Limit must be a number";
			return;
		}

		if(!is_numeric($data['maxchar'])){
			echo "Vbulletin Latest Threads error: Number of title's character must be a number";
			return;
		}
		$array= getLatestThreadsFromExternal($data['forum_url'],$data['limit'], $data['vb_charset'],$data['wp_charset']);
		$string="";
		foreach($array as $thread){
		$string.="<li>";
		
		if($data['maxchar']>0){
			$thread['title']=substr($thread['title'],0, $data['maxchar'])."... ";
		}		

		if($data['show_avatar']=="Y"){
			$string.="<div class=\"vblt_avatar\"><img height=\"50\"  src=\"".getAvatarUrl($thread['creator'], $data['forum_url'])."\" alt=\"".$thread['creator']."'s avatar\" title=\"".$thread['creator']."'s avatar\"></div>";
			$string.="<div class=\"vblt_title\"><a href=\"".$thread['link']."\" title=\"".$thread['title']."\">".$thread['title']."</a></div>";
		}
		else{
			$string.="<a href=\"".$thread['link']."\" title=\"".$thread['title']."\">".$thread['title']."</a> by ".$thread['creator'];
		}
		$string.="<div style=\"clear: both; display: block; font-size: 1px;\"></div></li>";

		}
		echo $string;
	}

	function register(){
	  register_sidebar_widget('Vbulletin Latest Threads', array('Vbulletin_lt', 'widget'));
	  register_widget_control('Vbulletin Latest Threads', array('Vbulletin_lt', 'control'));
	}

} // END CLASS

if (class_exists("Vbulletin_lt")) {
	$vbulletin_lt = new Vbulletin_lt();
}

?>
