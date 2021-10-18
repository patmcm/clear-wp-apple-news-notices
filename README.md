# Clear WP Apple News Notices

This is a simple plugin that works in tandem with https://github.com/alleyinteractive/apple-news

The apple-news plugin stores notices in the user meta field `apple_news_notice`.

Most of these notices show up in `wp-admin`, and can be dismissed there.

Once dismissed, the notices get cleared from that user meta field.

### What this plugin solves

If you have a WordPress user that is only used for API calls, and you use this user to publish posts via the API but don't login with them, that user meta field will continue to grow.

This can cause an increase in load times for user meta calls when this value gets larger than 1mb.

This plugin runs a twice daily cron that clears the `apple_news_notice` field when the values aren't dismissable.
