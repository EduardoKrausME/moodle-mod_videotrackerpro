# Changelog

This changelog documents only the two most recent published GitHub releases. Each entry describes changes verified by comparing its tag with the immediately preceding published release. Later changes on `master` are excluded.

## 1.0.5 - 2026-10-15

### Changed

- Added the plugin icon at `pix/icon.png` and reorganized the SVG logo asset as `pix/monologo.svg`.
- Updated the plugin release identifier from `1.0.4` to `1.0.5` and its build number from `2026101500` to `2026101501`.

No functional changes to the Video Tracker Pro activity were identified in this tag comparison.

[Compare 1.0.4...1.0.5](https://github.com/EduardoKrausME/moodle-mod_videotrackerpro/compare/1.0.4...1.0.5)

## 1.0.4 - 2026-10-15

### Changed

- Updated the GitHub Actions CI PostgreSQL service from version 16 to 17.
- Updated the MariaDB CI service from version 10 to 11.4, including revised startup environment variables and health-check configuration.
- Added a PHPUnit `@covers` annotation for `mod_videotrackerpro\report\manager::mediahash` to `tests/report_manager_test.php`, clarifying the coverage target of the media-hash stability test.
- Reformatted the `local_video_bridge` dependency declaration in `version.php` as a multiline array; its required build number `2026100616` did not change.
- Updated the plugin release identifier from `1.0.3` to `1.0.4` and its build number from `2026101200` to `2026101500`.

No functional changes to the Video Tracker Pro activity were identified in this tag comparison.

[Compare 1.0.3...1.0.4](https://github.com/EduardoKrausME/moodle-mod_videotrackerpro/compare/1.0.3...1.0.4)
