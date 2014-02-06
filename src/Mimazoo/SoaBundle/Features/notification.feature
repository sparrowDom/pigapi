Feature: Notification entity
  In order to manage notification
  As a rest api user
  I need to be able to Update notifications

Scenario: Create new notificaiton
  Given that I want to update a "Notification"
  And that its "apple_push_token" is "just_some_random_apple_push_token"
  When I request "/notification"
  Then the response status code should be 204