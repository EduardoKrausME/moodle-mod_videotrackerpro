@mod @mod_videotrackerpro
Feature: Configure Video Tracker Pro
  In order to analyse video playback without provider-specific code
  As a teacher
  I need to configure an activity using a tracking-capable Video Bridge source

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | One | teacher@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |

  Scenario: Teacher can open the add activity form
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I add a "Video Tracker Pro" to section "1"
    Then I should see "Video source"
    And I should see "Analytics"
