# Change log

## [Unreleased]
### Added
- Fixed an error with translations in Booking settings
- Fixing [#48](https://github.com/MultinetInteractive/EduAdmin-WordPress/issues/48), to allow users to choose "username" field
- Login-code checks the given login field instead of email.
- It is now possible to add your own translation directly in the plugin. (Again)
- Added extra filter to course list (ajax) to skip "next" event if there isn't a public price name set.

## [0.9.9.2.40]
### Added
- Set `date_default_timezone_set` to `UTC` to get rid of warnings instead.

### Removed
- Removed all error suppression regarding dates.


## [0.9.9.2.39]
### Added
- More "fixes" for the broken host, only error suppression for `date` and `strtotime`

## [0.9.9.2.38]
### Added
- Lots, and lots of warning suppression (all `strtotime`)

### Updated
- `CONTRIBUTING.md` is updated (ripped from [jmaynard](https://medium.com/@jmaynard/a-contribution-policy-for-open-source-that-works-bfc4600c9d83#.c42dikaxi))

## [0.9.9.2.37]
### Added
- This changelog
- Bugfix: if phrase doesn't exist in our dictionary, it threw an error. It shouldn't do that.
- Bugfix: Some users have a faulty php-config and gives warnings about that we need to set a timezone before we run `strtotime`

## [0.9.9.2.36] - 2017-01-05
### Removed
- Removing our translation, making it possible for third party plugins to translate the plugin by using standard WordPress-translation

## [0.9.9.2.25] - 2016-12-05
### Added
- Added GF-course view (Hard coded with cities)
- Added attributes `order`, `orderby` on listview and detail info shortcodes
- Added attribute `mode` to listview shortcode, so you can select mode

## [0.9.9.2.5] - 2016-10-04
### Added
- Added support for sub events
- Changed links to be absolute
- Added support for event dates

## [0.9.7.5] - 2016-09-13
### Added
- Added attribute `numberofevents` to shortcode `[eduadmin-listview]`
- Fix in rewrite-script
- Added missing translations
- Also adds event inquiries for fullbooked events

## 0.9.7 - 2016-09-06
### Added
- Added inquiry support in course


[Unreleased]: https://github.com/MultinetInteractive/EduAdmin-WordPress/compare/v0.9.9.2.40...HEAD
[0.9.9.2.40]: https://github.com/MultinetInteractive/EduAdmin-WordPress/compare/v0.9.9.2.39...v0.9.9.2.40
[0.9.9.2.39]: https://github.com/MultinetInteractive/EduAdmin-WordPress/compare/v0.9.9.2.38...v0.9.9.2.39
[0.9.9.2.38]: https://github.com/MultinetInteractive/EduAdmin-WordPress/compare/v0.9.9.2.37...v0.9.9.2.38
[0.9.9.2.37]: https://github.com/MultinetInteractive/EduAdmin-WordPress/compare/v0.9.9.2.36...v0.9.9.2.37
[0.9.9.2.36]: https://github.com/MultinetInteractive/EduAdmin-WordPress/compare/v0.9.9.2.25...v0.9.9.2.36
[0.9.9.2.25]: https://github.com/MultinetInteractive/EduAdmin-WordPress/compare/v0.9.9.2.5...v0.9.9.2.25
[0.9.9.2.5]: https://github.com/MultinetInteractive/EduAdmin-WordPress/compare/v0.9.7.5...v0.9.9.2.5
[0.9.7.5]: https://github.com/MultinetInteractive/EduAdmin-WordPress/compare/v0.9.7...v0.9.7.5