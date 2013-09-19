=== Plugin Name ===
Contributors: scarpenter, dnorman
Donate link: https://my.fsf.org/donate
Tags: posts, revisions, corrections, transparency
Requires at least: 2.6
Tested up to: 3.0.3
Stable tag: 0.9

Displays post-publication revisions along with differences/changes from current revision on single post views.

== Description ==

Want a way to show an "audit trail" for blog posts? Possible uses:

* For web publishers, to allow the freedom of updating stories without worrying about confusing or deceiving readers. Anyone can see the changes.
* For academic/student use of blogs, so students can't "sneak" posts around submission deadlines by futzing around with the publish date.

On single post views, this plugin will display a list of all post-publication revisions, showing revision times, authors, and links to view them. When viewing a previous revision, differences ("diffs") between the previous and current revision will be shown.

Also when a previous revision is displayed: `<meta name="robots" content="noindex, nofollow" />` will be added to the page header if the standard `<?php wp_head() ?>` hook is used in the theme's header file.

**CSS**

You'll probably want to add styling for the diffs so they look as pretty as they do in the screenshots tab. For example:

`table.diff { width: 100%; }
table.diff th { text-align: left; }
table.diff .diff-deletedline { background-color:#fdd;
                               width: 50%; }
table.diff .diff-deletedline del { background-color:#f99;
                                   text-decoration: none; }
table.diff .diff-addedline { background-color:#dfd;
                             width: 50%; }
table.diff .diff-addedline ins { background-color:#9f9;
                                 text-decoration: none; }
table.diff .diff-context { display: none; }`

*Other CSS*

`<div class="revision-header">
<div class="post-revisions">
<div id="revision-diffs">`

The `revision-header` is the note shown above the content when a previous revision is displayed. Possible styling to make it stand out:

`.revision-header {
	background-color: yellow;
	border: 1px solid #3a8b8c;
	padding: 10px; }

div.revision-header { padding: 0 10px; }`

See the [movingtofreedom.org plugin page](http://www.movingtofreedom.org/2010/07/30/wordpress-plugin-post-revision-and-diff-viewer/) for more samples and info about the CSS.


**Automatic vs. Manual Mode**

In "automatic mode," once you activate the plugin, revision information will show up on single post views (e.g. `single.php`) as shown in screenshots one and two. There is no need to modify theme files. There is also an option to show revision info on pages (e.g. `page.php`).

With "manual mode," you can specify where in your theme you want revision info to be displayed. (Set this mode as an option on the admin page.) Maybe you want to have the note about viewing a previous revision to appear above the post title, or maybe you want the revision list and diffs to appear below other meta information at the end of the post.

Most commonly, you'll make calls from `single.php` to:

`<?php the_revision_note_prd() ?>
<?php the_revision_list_prd() ?>
<?php the_revision_diffs_prd() ?>`

Which I think are pretty self-explanatory. (I think you'll want the note above the content so that it will be immediately obvious when someone is viewing a revision.)

*Previous to version 0.8,* there was the function `<?php prd_set_manual_mode() ?>`, to be used in case none of the calls came before the content, but this has been deprecated now that there is a manual mode checkbox option. This function doesn't do anything anymore and will likely go away in a future version.

With manual mode, you can put revision info on pages with multiple posts  (e.g. the main `index.php` page or archive pages), although in those cases it only makes sense to show the revision list.

When calling `the_revision_list_prd()` from within "the loop," you'll need to force an update of the revision info for each post by using the first optional parameter, `$refreshGlobals`:

`the_revision_list_prd(true)`

When you call the three `_prd` functions, the first call will load all the revision info into a set of global variables. Without forcing the update with `$refreshGlobals=true`, you'd see the revision list for the first post for every following post displayed on the page.

*Previous to version 0.8,* there were parameters for specifying optional list and diffs headers by way of the function calls. This has now been added to the admin options page and works for both automatic and manual modes. The optional parameters are still there, but they don't do anything and are deprecated.

**Admin Options**

WordPress Admin `>>` Settings `>>` Post Revision Display

* **Show revisions on single posts.** (default = true / checked)
* **Show revisions on pages.** (default = false / unchecked)
* **Hide the message that appears if there are no post-publication revisions.** (default = false / unchecked) So, if this option is set, you won't see the message "Post Revisions: This post has not been revised since publication."
* **Revision list header:** (default = `<h4>Post Revisions</h4>`)
* **Revision diffs header:** (default = `<h4>Changes</h4>`)
* **Manual Mode.** (default = false / unchecked)

(See also screenshot #3.)

When the plugin is first installed, or when it's deactivated and re-activated before setting the options, it will have certain defaults that correspond to how automatic mode worked in versions previous to 0.8. If you're upgrading from an older version, you should check your options to make sure they make sense.


**More about the plugin**

* [D'Arcy Norman's original plugin page.](http://www.darcynorman.net/wordpress/post-revision-display/)
* [Scott Carpenter's latest plugin page.](http://www.movingtofreedom.org/2010/07/30/wordpress-plugin-post-revision-and-diff-viewer/)
* [Scott Rosenberg's call for web publishers to "show your work!"](http://www.wordyard.com/2010/08/03/change-is-good-but-show-your-work/)


== Installation ==

1. Upload `post-revision-display.php` to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. By default in automatic mode, it should "just work," although you'll want to include CSS for styling the diff/revision comparison and probably also the note at the top indicating when a revision is displayed. See the description tab for sample CSS and instructions on how to use "manual" mode to control where things are displayed.
1. If upgrading, make sure to check the options page right away. The defaults may not be set right. (If you deactivate/re-activate before visiting the options page, you should see defaults that correspond to the old automatic mode.)

== Frequently Asked Questions ==

= Why don't you show revision diffs the way the built-in WordPress admin revision comparison works? That way looks really cool! =

This has been done in v0.7! You have to add appropriate CSS to make it look nice. See the description tab for sample CSS that is similar to how WordPress does it.

= Why are there extra spaces in the diff text? I have a URL "http://blah/blah/blah/", but it shows up as "http://blah/blah/[SPACE]blah/". =

There is code to break up long strings so that the text will wrap better in the diff/comparison table columns. At this time, this behavior can only be changed by modifying the code to remove calls to the `prd_break_up_lines` function. You can read more about this at the [movingtofreedom.org plugin page](http://www.movingtofreedom.org/2010/07/30/wordpress-plugin-post-revision-and-diff-viewer/).

= How can I show revision info for "pages?" (i.e. page.php) =

In v0.8, there are admin checkbox options for placing revision info on single posts and/or on pages. (In manual mode, this is still done by placing the function calls in your page.php file.)

== Screenshots ==

1. Revision history displayed at end of post.
2. Previous revision view with changes shown.
3. Admin settings page.

== Changelog ==

= 0.9 (12/9/10) =
* Internationalization support added, with Spanish language files. (Thanks to Fran Ontanaya! contacto at franontanaya dot com)
* Language support assumes plugin is in plugins/post-revision-display sub-directory, so you may have to reactivate your plugin and delete the old file if you've been keeping it in the plugins root dir.

= 0.8.1 (11/19/10) =
* Automatic mode should only display revision info on single post and page views, as determined by the admin options, but in 0.8 it would also show up on other pages if `the_content` was called. This version closes that loophole.
* The built-in WordPress diff function uses a class named "content" which may be more likely to clash with user themes: `<col class="content">`. Let's rename it to get: `<col class="diff-content">`. (This class isn't used in the example CSS, and you likely won't need to do anything with it.)

= 0.8 (11/14/10) =
* Admin settings page. (See screenshot #3.) On new installs, or if you deactivate/reactivate before visiting the options page, you should have defaults that correspond to how automatic mode has previously worked.  In other cases, you may have to check a box or two after installing.
* Options for automatic mode to choose where revision info is listed: on single posts and/or pages.
* Option to hide "no revisions" message when there are no post-publication revisions.
* Options to set list and diffs headers.
* Option for manual mode.
* Changes in how manual mode works. `prd_set_manual_mode()` is deprecated (no longer needed now that there is a manual mode checkbox option), and `$header` parameter for `the_revision_list_prd()` and `the_revision_diffs_prd` is also deprecated.
* Fixes bug with how revisions links work when pretty permalinks aren't turned on.

= 0.7 (8/24/10) =
* Better looking diffs using the built-in WordPress `wp_text_diff` function. (Requires some styling with CSS to qualify as "better.")

= 0.6 =
* Added "manual" mode to allow specific placement of revision info via function calls in the theme. (Automatic mode will be used by default. No need for theme changes.)
* Manual mode functions have optional parameters to change header text for the revision list and diffs.

= 0.5.2 =
* Revisions now publicly viewable.
* Only shows post-publication revisions.
* List of revisions only shown on single post views.
* Shows differences ("diffs") between previous and current revision. (In content and title.)
* Robots "noindex" added to page meta if a previous revision is displayed.

= 0.1 =
* initial version

== Upgrade Notice ==

= 0.9 =

Internationalization support added and Spanish language files.

= 0.8.1 =

Minor changes: Fixes a bug in automatic mode where revision info could appear outside of single post and page views when "the_content" function is called. Renames the col "content" class used by the built-in WordPress diff function, which may class with user themes.

= 0.8 =

Admin settings page. (See screenshot #3.) If upgrading, make sure to check options since defaults may not be set right. And some things have changed for manual mode, so please take a look at the docs...

= 0.7 =
Nicer looking diffs! Much easier to compare versions and see the differences. Be sure to add CSS or it will look bland and crowded. (See plugin page for sample CSS.)

= 0.6 =
New "manual" mode allows you to specify where revision info is displayed in the theme, and also provide different headers for rev list and diff sections. "Automatic" mode is the default, with the plugin behavior the same as previous version.

= 0.5.2 =
Several nifty new features, including making post-publication revisions publicly viewable and showing differences between previous and current versions.
