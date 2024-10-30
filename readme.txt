=== Blinking Robots ===
Contributors: Blinking Robots
Tags: rss, feed, rss feed, gpt, chatGPT, AI, OpenAI, artificial intelligence
Requires at least: 4.7
Tested up to: 6.5.2
Stable tag: 1.1.0
Requires PHP: 5.4
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Imports articles from specified RSS feeds. Send them to GPT for rewriting their text but preserve primary meaning. Create posts from them on your site.

== Description ==
This plugin allow imports articles from added different RSS feed url's. Then send their to OpenAI for rewriting their text to unique one but preserve primary meaning of original from feed. And then create posts on your site from these new rewritten articles.
Already added feeds are regularly checked for the presence of new articles, if they exist, they will be imported to the site according to the previously specified algorithm.
All you need to fill your site with new original articles on your favorite topics is just to buy a subscription and add a feed.

**About the use of 3rd Party or external services by the plugin:**
The Rest API used by this plugin doesn't send any confidential or private data to third-party services. A text string written by the user that contains a description with instructions such as the topic, the content of the article that will be used to generate this article in open AI - the only thing that is sent to a third-party Rest API service.
The mentioned article instructions text string is transferring to OpenAI on https://api.openai.com , and also receive from it by routes responded generated article through the Rest API agent on the link https://feed.blinkingrobots.com which is used by this plugin.

Terms and Conditions:
https://feed.blinkingrobots.com/terms-condition

Privacy Policy:
https://feed.blinkingrobots.com/privacy-policy


== Installation ==
1. Buy your subscription with number of allowed feeds you can use.
2. Install and activate this plugin.
3. On your admin site panel go to the section 'Feeds' > 'New Feed' for new feed adding.
4. On 'New Feed' page adding write an URL of XML RSS feed and press 'Save Draft' button firstly. If XML RSS feed is correct, then feed will be fetched.
5. From the received feed, the fields that feed contains will be displayed in column 'Key'.
6. Relate these 'Key' column fields with matched fields from column 'Field'. Required fields: 'feed url' and Field column 'title', 'content', 'publish_date_and_time'.
7. This is necessary so that the plugin knows the values of which fields are required for AI, it should take from the received feed, in order to send and generate the article in AI.
8. Press 'Update' button for save and send feed to AI where each article of current feed will be rewriting from matched specified above fields.
9. Rewritten in AI articles from feed will add on your site as records of post type Post. New last articles of active feed are coming rewritten from OpenAI to your site on regular base.


== Changelog ==

= 1.1.7 =
Added: Feeds admin page. Added feed website favicon against feed url.
Updated: Single page content. button text updated Read Full Version > Article Source.

= 1.1.6 =
Update: print notices.

= 1.1.5 =
Added: notice on feed edit page about failed fetched server feed URL.

= 1.1.4 =
Updated: Test with the WordPress 6.5.2.

= 1.1.3 =
Updated: Plugin usage guide.

= 1.1.2 =
Updated: Single page content: add link to Read Full Version of the article.

= 1.1.1 =
Updated: Error logging.

= 1.1.0 =
Updated: User guide change radio to checkboxes.
Updated: User guide added line-through text-decoration to completed items.
Updated: User guide added feed item check feed title to be valid URL.
Updated: Feed title change placeholder.
Added: Feed title validation (don't allow to save/update the feed if the title is not a valid URL).

= 1.0.2 =
Updated: Code improvements.
Added: Plugin using guide.

= 1.0.1 =
Updated: Contributors, Author, Author URI.
Added: Assets icon.

= 1.0.0 =
Initial Release.
