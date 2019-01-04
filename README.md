
Widget Anywhere (Question2Answer plugin)
=================================================

A plugin for [Question2Answer](http://www.question2answer.org) that allows the placement of any content snippet on any page, in a variety of locations.

Suggestions/bugs can be reported to me here on Github or at [this post on Question2Answer](http://www.question2answer.org/qa/15066/)


Pay What You Like
-------------------------------------------------

Most of my code is released under the open source GPLv3 license, and provided with a 'Pay What You Like' approach. Feel free to download and modify the code to suit your needs, and I hope you value it enough to make a small donation - any amount is welcome.

### [Donate here](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4R5SHBNM3UDLU&source=url)


Installation & Usage
-------------------------------------------------

1. Download and extract the files to your Q2A plugins folder (e.g. `qa-plugin/widget-anywhere`). Check the [releases page](https://github.com/svivian/q2a-tagging-tools/releases) for the latest official version.

2. If your site uses a different language, copy `qa-wa-lang-default.php` to a new file with the required country code (e.g. `qa-wa-lang-de.php` for German) and edit the phrases for your language.

3. In Q2A go to Admin > Plugins, enable the plugin and run the database initialization.

4. In the plugin options, click 'Add new widget' to create a module. Enter a title (this is for your reference and is not displayed on the front-end), the position, pages to appear on, ordering (relative to other WA modules) and the HTML content. Anything is allowed, including scripts.

5. To edit a module, click the appropriate link from the plugin options. Make the required changes and save. To delete a module, go to its edit page, tick the "Delete widget" checkbox and save.

Widgets can be added on custom pages: tick 'Show on custom page(s)' and enter the page slugs (URL fragments) in the field. Also note that certain positions (e.g. 'After question text') are not available on all pages.

**Make sure the HTML syntax is correct before saving!** If you are missing closing tags (especially for `<script>` and `<style>`) you may end up making your site unusable. If this happens you'll need to edit the database manually (the `qa_widgetanyw` table) to fix the HTML.
