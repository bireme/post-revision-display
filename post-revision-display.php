<?php
/*
Plugin Name: Post Revision Display
Plugin URI: http://www.movingtofreedom.org/10/07/30/wordpress-plugin-post-revision-and-diff-viewer
Description: Show list of post-publication revisions on single post pages, and provide links to display them along with diffs between old and current revision. Revision pages are marked "noindex" for robot crawlers. Inspired by <a href="http://www.wordyard.com/2010/07/23/help-with-a-wordpress-plugin-for-published-versions/">Scott Rosenberg's request for a plugin to demo versioning on blog posts</a> and built on <a href="http://www.darcynorman.net/wordpress/post-revision-display/">D'Arcy Norman's original plugin</a>.
Version: 0.9
Author: Scott Carpenter, D'Arcy Norman
Author URI: http://www.movingtofreedom.org
License: GPL2
*/
/*  Copyright 2008  D'Arcy Norman  (email : dlnorman@ucalgary.ca)
	Copyright 2010  Scott Carpenter (email: scottc@movingtofreedom.org)

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

// Enable translation strings
load_plugin_textdomain('post-revision-display', '/wp-content/plugins/post-revision-display/languages/', 'post-revision-display/languages/');

define('REV_LIST_HEADER', '<h4>' . __('Post Revisions:', 'post-revision-display') . '</h4>');
define('REV_DIFFS_HEADER', '<h4>' . __('Changes:', 'post-revision-display') . '</h4>');

add_action('wp_head', 'prd_noindex_header');
add_filter('the_content', 'prd_display_post_revisions');

// only runs when plugin activated from admin page
function prd_install()
{
	add_option('prd_revs_on_posts', '1');
	//add_option('prd_revs_on_pages', '');							//default = false
	//add_option('prd_hide_message_when_no_revs', '');				//default = false
	add_option('prd_rev_list_header', '<h4>' . __('Post Revisions:', 'post-revision-display') . '</h4>');
	add_option('prd_rev_diffs_header', '<h4>' . __('Changes:', 'post-revision-display') . '</h4>');
	//add_option('prd_manual_mode', '');							//default = false
}

// Generate links in the admin menu to the YARQ admin pages
function prd_generate_admin_menu()
{
	if (function_exists('add_options_page')) {
		add_options_page(__('Post Revision Display', 'post-revision-display'), __('Post Revision Display', 'post-revision-display'), 10, basename(__FILE__), 'prd_admin_options');
	}
}

function prd_admin_options()
{

	if (isset($_POST['update_options'])) {
		update_option('prd_revs_on_posts', $_POST['prd_revs_on_posts']);
		update_option('prd_revs_on_pages', $_POST['prd_revs_on_pages']);
		update_option('prd_hide_message_when_no_revs', $_POST['prd_hide_message_when_no_revs']);
		update_option('prd_rev_list_header', $_POST['prd_rev_list_header']);
		update_option('prd_rev_diffs_header', $_POST['prd_rev_diffs_header']);
		update_option('prd_manual_mode', $_POST['prd_manual_mode']);
		echo '<div class="updated"><p><b>' . __('Options updated.', 'post-revision-display') . '</b></p></div>';
	}

	echo '<div class="wrap">' . "\n";
	echo '<h2>' . __('Post Revision Display', 'post-revision-display') . "</h2>\n";

	echo '<form action="" method="post">' . "\n";

	if (prd_get_option_revs_on_posts()) {
		$chk1 = 'checked="checked"';
	}
	echo '<p><input type="checkbox" id="prd_revs_on_posts" name="prd_revs_on_posts" class="code" value="1"' . 
		 "$chk1 />" . __('Show revisions on single posts', 'post-revision-display') . "*</p>\n";
	echo "</fieldset>\n";

	if (prd_get_option_revs_on_pages()) {
		$chk2 = 'checked="checked"';
	} 
	echo '<p><input type="checkbox" id="prd_revs_on_pages" name="prd_revs_on_pages" class="code" value="1"' . 
		 "$chk2 />" . __('Show revisions on pages', 'post-revision-display') . "*</p>\n";
	echo "</fieldset>\n";

	if (prd_get_option_hide_no_revs()) {
		$chk3 = 'checked="checked"';
	}
	echo '<p><input type="checkbox" id="prd_hide_message_when_no_revs" name="prd_hide_message_when_no_revs" class="code" value="1"' . 
		 "$chk3 />" . __('Hide the message that appears if there are no post-publication revisions.', 'post-revision-display') . "</p>\n";
	echo "</fieldset>\n";

	echo '<p>' . __('Revision list header:', 'post-revision-display') . '<input type="text" id="prd_rev_list_header" name="prd_rev_list_header" size="40" maxlength="255" class="code" value="' . stripslashes(htmlspecialchars(prd_get_rev_lists_header())) . '" /> (Default = ' . htmlspecialchars(REV_LIST_HEADER) . ")</p>\n";
	echo "</fieldset>\n";

	echo '<p>' . __('Revision diffs header:', 'post-revision-display') . '<input type="text" id="prd_rev_diffs_header" name="prd_rev_diffs_header" size="40" maxlength="255" class="code" value="' . stripslashes(htmlspecialchars(prd_get_rev_diffs_header())) . '" /> (Default = ' . htmlspecialchars(REV_DIFFS_HEADER) . ")</p>\n";
	echo "</fieldset>\n";

	if (prd_get_option_manual_mode()) {
		$chk4 = 'checked="checked"';
	}
	echo '<p><input type="checkbox" id="prd_manual_mode" name="prd_manual_mode" class="code" value="1"' . 
		 "$chk4 />" .  __('Manual mode.', 'post-revision-display') . "**</p>\n";
	echo "</fieldset>\n";

	echo '<div class="submit"><input type="submit" name="update_options" value="' . __('Update Options', 'post-revision-display') . '" /></div>' . "\n";
	echo "</form>\n";
	echo '<p>* ' . __("Only applies to 'automatic' mode. If using 'manual' mode, this will be determined by where you place the function calls in your template files, most likely in single.php and/or page.php.", 'post-revision-display') . "</p>\n";
	echo '<p>** ' . __("Turns off automatic placement of revision information and requires calls to 'the_revision*' functions from your template files. You'll have to read the documentation on this one.", 'post-revision-display') . "</p>\n";
	echo "</div>\n";
}

function prd_get_option_revs_on_posts()
{
	if (get_option('prd_revs_on_posts') == 1) {
		return true;
	} else {
		return false;
	}
}
function prd_get_option_revs_on_pages()
{
	if (get_option('prd_revs_on_pages') == 1) {
		return true;
	} else {
		return false;
	}
}
function prd_get_option_hide_no_revs()
{
	if (get_option('prd_hide_message_when_no_revs') == 1) {
		return true;
	} else {
		return false;
	}
}
function prd_get_rev_lists_header()
{
	$opt = trim(get_option('prd_rev_list_header'));
	if (empty($opt)) {
		$opt = REV_LIST_HEADER;
	}
	return $opt;
}
function prd_get_rev_diffs_header()
{
	$opt = trim(get_option('prd_rev_diffs_header'));
	if (empty($opt)) {
		$opt = REV_DIFFS_HEADER;
	}
	return $opt;
}
function prd_get_option_manual_mode()
{
	if (get_option('prd_manual_mode') == 1) {
		return true;
	} else {
		return false;
	}
}

function prd_noindex_header()
{
	if (isset($_GET['rev'])) {
		// like Wikipedia does for its old revisions
		echo "\t\t" . '<meta name="robots" content="noindex, nofollow" />' . "\n";
	}
}

function prd_display_post_revisions($content) {

	if (!$post = get_post(get_the_ID())) {
		return $content;
	}
	if (!prd_get_option_manual_mode() && (is_single() || is_page())) {
		// "automatic" mode -- everything is handled here
		if ( (is_single() && !prd_get_option_revs_on_posts()) ||
			 (is_page()   && !prd_get_option_revs_on_pages()) ) {
			return $content;
		}
		prd_set_globals();
		if ($GLOBALS['prd_is_rev']) {
			return $GLOBALS['prd_revision_note'] . $GLOBALS['prd_revision_content'] .
				   $GLOBALS['prd_revision_list'] . $GLOBALS['prd_revision_diffs'];
		} else {
			return $content . $GLOBALS['prd_revision_list'];
		}
	} else {
		// "manual" mode -- calls are made from theme to specify where things go
		if ($GLOBALS['prd_is_rev']) {
			return $GLOBALS['prd_revision_content'];
		} else {
			return $content;
		}
	}
}

// question: should we be applying filters to all the parts and not just content?
function prd_set_globals() {
	if (!$post = get_post(get_the_ID())) {
		return;
	}
	$revision = null;
	$rev_id = 0;
	$is_rev = false;
	$note = prd_get_revision_note($post, $revision, $rev_id, $is_rev);
	if ($is_rev) {
		$diffs = prd_get_revision_diffs($post, $revision);

		/* seems clunky, but I don't know a better way -- want to apply_filters
		   for old revision, but need to skip prd_display_post_revisions filter
		   to avoid getting stuck in a loop, so will remove filter and add
		   right back in */
		remove_filter('the_content', 'prd_display_post_revisions');
		$rev_content = apply_filters('the_content', $revision->post_content);
		add_filter('the_content', 'prd_display_post_revisions');
	}
	$revs = prd_get_revision_list($post, array('since_publish' => true, 'type' => 'revision',
											   'rev_id' => $rev_id, 'is_rev' => $is_rev));
	$GLOBALS['prd_is_rev'] = $is_rev;
	$GLOBALS['prd_revision_note'] = $note;
	$GLOBALS['prd_revision_list'] = $revs;
	$GLOBALS['prd_revision_diffs'] = $diffs;
	$GLOBALS['prd_revision_content'] = $rev_content;
	$GLOBALS['prd_globals_set'] = true;
}

// kind of like wp_list_post_revisions()
function prd_get_revision_list($post, $args=null)
{
	$defaults = array( 'parent' => false, 'right' => false, 'left' => false, 'format' => 'list', 'type' => 'all', 'since_publish' => false, 'rev_id' => 0, 'is_rev' => false );
	extract( wp_parse_args( $args, $defaults ), EXTR_SKIP );

	$rev_list = '';
	switch ($type) {
		case 'autosave' :
			if (!$autosave = wp_get_post_autosave($post->ID)) {
				$rev_list = '<p>' . __('No autosave', 'post-revision-display') . '</p>';
			}
			$revisions = array( $autosave );
			break;
		case 'revision' : // just revisions - remove autosave later
		case 'all' :
		default :
			if (!$revisions = wp_get_post_revisions($post->ID)) {
				$rev_list = '<p>' . __('There are no revisions for this post.', 'post-revision-display') . '</p>';
			}
			break;
	}

	$titlef = _c('%1$s by %2$s|post revision 1:datetime, 2:name', 'post-revision-display');

	if ($parent) {
		array_unshift( $revisions, $post );
	}

	$rows = '';
	$class = false;
	//$can_edit_post = current_user_can( 'edit_post', $post->ID ); // not used?
	if ($since_publish) {
		$post_time = strtotime($post->post_date_gmt);
	}
	foreach ($revisions as $revision) {
		if ('revision' === $type && wp_is_post_autosave($revision)) {
			continue;
		}
		if ($since_publish && strtotime($revision->post_date_gmt) < $post_time) {
			continue;
		}
		// 2nd param to wp_post_revision_title determines if link (if true, only links if you have access)
		$date = wp_post_revision_title($revision, false);
		if ($rev_id != $revision->ID) {
			$plink = get_permalink();
			// 11.14.10 - if pretty permalinks not being used, url will already have question mark,
			//			separator: http://blah.com/?p=1234, so need to use ampersand instead
			//			  (would like to know if there's a better way to do this, but this should
			//			work well enough...) (and it seems that this could be run just once outside 
			//			of the foreach loop, but not sure if the first get_permalink() could somehow
			//			be different than others...)
			if (strpos($plink, '?') === false) {
				$sep = '?';
			} else {
				$sep = '&';
			}
			$date = '<a href="' . $plink . $sep . 'rev=' . $revision->ID . '">' . "$date</a>";
		}
		$name = get_author_name($revision->post_author);

		$title = sprintf( $titlef, $date, $name );
		$rows .= "\t<li>$title</li>\n";
	}

	if (!empty($rows)) {
		// add current revision to the top
		$date = wp_post_revision_title($post, false);
		if ($is_rev) {
			$date = '<a href="' . get_permalink() . '">' . $date . '</a>';
		}
		$name = get_author_name($post->post_author);
		$title = sprintf($titlef, $date, $name);
		$rows = "\t<li>$title</li>\n$rows";

		$rev_list = '<ul class="post-revisions">' .
					"\n$rows\n</ul>";
	} else if (empty($rev_list)) {
		if (prd_get_option_hide_no_revs()) {
			return '';
		}
		$rev_list = '<p>' . __('This post has not been revised since publication.', 'post-revision-display') . '</p>';
	}
	return '<div class="post-revisions">' . "\n" .
		   prd_get_rev_lists_header() .
		   "\n$rev_list\n</div>\n";
}

// only passing $rev_id and $is_rev here so that we can use them in
// prd_get_revision_list() to check and not link the displayed revision --
// is it worth the trouble ???
function prd_get_revision_note($post, &$revision, &$rev_id, &$is_rev)
{
	$is_rev = false;
	$note = '';
	if (isset($_GET['rev'])) {
		$rev_id = intval($_GET['rev']);
		$current_rev_link = '<a href="' . get_permalink() . '">' . __('current revision', 'post-revision-display') . '</a>';
		$view_current = sprintf(__('(Viewing %s instead.)', 'post-revision-display'), $current_rev_link);
		if (!$revision = get_post($rev_id)) {
			$note = sprintf(__('Revision %1$s not found. %2$s', 'post-revision-display'), $rev_id, $view_current);
		} else {
			$post_id = $post->ID;
			if ($revision->post_parent == 0) {
				$note = __('This is the current revision.' , 'post-revision-display');
			} else if ($revision->post_parent != $post->ID) { // this check should come before the date check
				$note = sprintf(__('Revision %1$s is not a revision of this post. %2$s', 'post-revision-display'), $rev_id, $view_current);
			} else if (strtotime($revision->post_date_gmt) < strtotime($post->post_date_gmt)) {
				$note = sprintf(__('Revision %1$s is a pre-publication revision. %2$s', 'post-revision-display'), $rev_id, $view_current);
			} else {
				$is_rev = true;
				$note = sprintf(__( 'You are viewing an old revision of this post, from %s.', 'post-revision-display'), wp_post_revision_title($revision, false) ) . ' ' . sprintf(__( '%1$sSee below for differences%2$s between this version and the %3$s', 'post-revision-display'), '<a href="#revision-diffs">', '</a>', $current_rev_link) . '.';
			}
		}
		$note = '<div class="revision-header"><p>' . $note . "</p></div>\n";
	}
	return $note;
}

function prd_break_up_lines($text)
{
	$pattern = '/([^\s]{12}[-+_<>\/=";:()])([^\s]{12})/';
	do {
		$text = preg_replace($pattern, "$1 $2", $text, -1, $num_replaced);
	} while ($num_replaced > 0);

	return $text;
}

function prd_add_diff_header_part($hdr, $text)
{
	$pattern = '/(<tbody[^>]*>)/i';
	$text = preg_replace($pattern, "$1\n" . '<tr><th colspan="4">' . $hdr . '</th></tr>', $text);
	return $text;
}

function prd_add_diff_header_revs($old_rev, $new_rev, $text)
{
	$pattern = '/(<tbody[^>]*>)/i';
	$text = preg_replace($pattern, "$1\n" . '<tr><th class="diff-deletedline" colspan="2">' .
								   $old_rev . '</th><th class="diff-addedline" colspan="2">' .
								   $new_rev . '</th></tr>', $text, 1);
	return $text;
}

function prd_rename_diff_content_class($diffs)
{
	// 11-19-10 - for ovidiu, because the "content" class was clashing with his theme --
	//			built-in wp diff function outputs:
	//<table class="diff">
	//<col class="ltype"><col class="content"><col class="ltype"><col class="content"><tbody>

	// let's use a less generic name; add "diff-" to get: class="diff-content"
	// (12-3-10 doesn't work with tabs before # comments?)
	return preg_replace('/(         # first capturing group
						  <col\     # <col tag opening with escaped "space"
						  [^>]*     # any number of chars before close of col
						  [^-]      # dash not counted as word boundary -- make sure "content"
									# not part of bigger name, e.g. class="blah-content"
						  \b        # word boundary before class name
						)           # end first capturing group
						(           # second capturing group
						  content   # troublesome class name from built-in wp diff function
						  \b        # word boundary after class name
						  [^-]      # also guard against dash after, e.g. class="content-blah"
						)           # end second group
						/x', '\\1diff-\\2', $diffs); // x = extended/verbose mode
}

function prd_get_revision_diffs($post, $revision)
{
	$previous = prd_break_up_lines($revision->post_content);
	$current = prd_break_up_lines($post->post_content);

	// diff the unfiltered content
	$diffs = wp_text_diff($previous, $current);
	$diffs_title = wp_text_diff($revision->post_title, $post->post_title);

	// add header/footer rows for "parts"
	$diffs = prd_add_diff_header_part(__('Content', 'post-revision-display'), $diffs);
	$diffs_title = prd_add_diff_header_part(__( 'Title', 'post-revision-display'), $diffs_title);

	$diffs = $diffs_title . $diffs;
	if (empty($diffs)) {
		$diffs = '<p>' . sprintf( __('There are no differences between the %s revision and the current revision. (Maybe only post meta information was changed.)', 'post-revision-display'),  wp_post_revision_title($revision, false)) . '</p>';
	} else {

		//rename col class = "content" to less generic class = "diff-content"
		$diffs = prd_rename_diff_content_class($diffs);
		// add top header row for previous and current revision
		// (this has to follow prd_add_diff_header_part calls so will be at top of table)
		$diffs = prd_add_diff_header_revs
				 (wp_post_revision_title($revision, false), __('Current Revision', 'post-revision-display'), $diffs) .
				 "\n<p>" . sprintf(__('%1$sNote:%2$s Spaces may be added to comparison text to allow better line wrapping.', 'post-revision-display'), '<i>', '</i>') . "</p>\n";
	}
	return '<div id="revision-diffs">' . "\n" .
		   prd_get_rev_diffs_header() .
		   "\n$diffs\n</div>\n";
}

/*
 * Manual Mode
 *
 */
function prd_set_manual_mode() {
	// deprecated -- in v0.8, use admin option "manual mode"
}

// all of these "the_revision" functions check to see if globals already set --
// (seemed kind of wasteful to set them each time) -- we check in each one since
// we don't know which one will be called first...
function the_revision_note_prd() {
	if (empty($GLOBALS['prd_globals_set'])) {
		prd_set_globals();
	}
	echo $GLOBALS['prd_revision_note'];
}

// rev list is the only thing that really makes sense to show in the loop --
// set $refreshGlobals to true to force it to update for each post (otherwise
// will keep using whatever the first one was, including no message for a post
// that had no post-publication revisions)
//
// $deprecated used to be $header, which is now set in admin options page
// (will possibly/likely go away in some future version, so best not to supply it)
function the_revision_list_prd($refreshGlobals=false, $deprecated='') {
	if (empty($GLOBALS['prd_globals_set']) || $refreshGlobals) {
		prd_set_globals();
	}
	echo $GLOBALS['prd_revision_list'];
}

// $deprecated used to be $header, which is now set in admin options page
// (will possibly/likely go away in some future version, so best not to supply it)
function the_revision_diffs_prd($deprecated='') {
	if (empty($GLOBALS['prd_globals_set'])) {
		prd_set_globals();
	}
	echo $GLOBALS['prd_revision_diffs'];
}

register_activation_hook(__FILE__,'prd_install');
add_action('admin_menu', 'prd_generate_admin_menu');

?>
