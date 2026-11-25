@message @message_notifyhub
Feature: The notification hub page
  In order to control how notifications reach people
  As an admin
  I need to order channels, take them out of the hub and see what was delivered

  Background:
    # Behat sites start with the email output off.
    Given I log in as "admin"
    And I navigate to "Messaging > Notification settings" in site administration
    And I set the field "email" to "1"
    And I press "Save changes"

  Scenario: An admin reorders channels and takes one out of the hub
    When I visit "/message/output/notifyhub/index.php"
    Then I should see "The hub is not on yet"
    And I should see "Email" in the "message-notifyhub-channels" "table"
    And I should see "Channels you can install"
    And I should see "Notification channel" in the "message-notifyhub-catalogue" "table"
    When I follow "Take Email out of the hub"
    Then I should see "Off" in the "[data-channel='email']" "css_element"
    When I follow "Use Email in the hub"
    Then I should see "On" in the "[data-channel='email']" "css_element"

  Scenario: An admin sends a test through email and sees it in the log
    Given I visit "/message/output/notifyhub/index.php"
    When I follow "Send me a test through Email"
    Then I should see "Test sent through Email."
    And I should see "message_notifyhub / test" in the "message-notifyhub-log" "table"
    And I should see "Sent" in the "message-notifyhub-log" "table"

  Scenario: The hub page is listed under Messaging
    When I navigate to "Messaging > Notification hub" in site administration
    Then I should see "Recent deliveries"

  @javascript @accessibility
  Scenario: The hub page meets accessibility standards
    Given I visit "/message/output/notifyhub/index.php"
    And I follow "Send me a test through Email"
    Then I should see "Recent deliveries"
    And the page should meet accessibility standards
