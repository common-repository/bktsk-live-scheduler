=== BKTSK Live Scheduler ===
Contributors: sasagar
Donate link: https://paypal.me/sasagar
Tags: schedule, calendar, youtube, live
Requires at least: 4.3
Tested up to: 5.2.3
Stable tag: 0.4.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

To set up custom post type and taxonomy for YouTube Live Schedules.

== Description ==

This plugin will set up a custom post type and taxonomy for live schedule like YouTube Live.

On the new post type, you can use original custom field section including type of the schedule (time fixed schedule, time not fixed (only date fixed) schedule, canceled schedule and day off schedule) and the urls.

Also iCalendar file can be delivered.  It can be imported with URL to the major calendar apps (e.g. Google Calendar, Apple Calendar App and so on).

== Installation ==

### This plugin can be installed directly from your site.

Log in and navigate to Plugins → Add New.
Type "BKTSK Live Scheduler" into the Search and hit Enter.
Locate the BKTSK Live Scheduler in the list of search results and click "Install Now".
Once installed, click the Activate link.

== Frequently asked questions ==

### What can be done with this plugin?

You can make YouTube Live schedule as each posts.  Of course, this information will be automatically used for making iCalendar file.
The iCalendar file would be delivered using specified url (you can change the slug for that on the setting page), so everyone can subscribe it to major calendar apps or services.

### Is this able to show the calendar?

At last, we got the shortcode to show the calendar.
Use this shortcode to show the one.
```
[bktsk-live-calendar]
```

If you put this shortcode in the page, the pagenation will be automatically enabled.

Also if you need to keep the exact month calendar to show, you can use the shortcode like below.

```
[bktsk-live-calendar year=2020 month=5]
```

If you set only month option, the year will be this year (as same as now).
Of course you can set only year option, the month will be this month.

### Donation?

I'm so happy to get a donation.
There's an account to donate on PayPal.
https://paypal.me/sasagar

### How can I use the post type?

You can use any WordPress tags and functions for the post type and taxonomy.  Here are some slug to help you.

- post type slug: bktskytlive
- taxonomy slug: bktsk-yt-live-taxonomy

== Screenshots ==

Now In Progress...

== Changelog ==

### 0.4.0
Enable month pagenation when the shortcode is on the page.
(This feature is enabled on page only, not for post or any post type.)

### 0.3.1
Fixing FAQ.

### 0.3.0
Hooray! Now we got the shortcode to show the calendar.
(Only this month, no paging.)

### 0.2.0
Enable quick edit for Live URL.

### 0.1.6
Fixing Class name.
Fixing file name.

### 0.1.5
Changes to support languages.

### 0.1.4
Fixing readme.
Testing Automation.

### 0.1.3
Fixing readme.
Testing Automation.

### 0.1.2
Release automation.

### 0.1.0
The first release.

== Upgrade notice ==

none.
