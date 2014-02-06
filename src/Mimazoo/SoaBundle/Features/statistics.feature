Feature: Statistics entity
  In order to manage statistics
  As a rest api user
  I need to be able to Update statistics

  Scenario: Create new statistics entry
    Given that I want to update a "UserStatistics"
    And that its "token" is "just_some_random_token"
    And that its "distance" is "500"
    And that its "storyProgress" is "8"
    And that its "coins" is "250"
    And that its "gamesPlayed" is "88"
    And that its "userId" is "123"
    When I request "/user/statistics"
    Then the response status code should be 204


  Scenario: Create new statistics entry
    Given that I want to update a "UserStatistics"
    And that its "distance" is "500"
    And that its "storyProgress" is "8"
    And that its "coins" is "250"
    And that its "gamesPlayed" is "88"
    When I request "/user/statistics"
    Then the response status code should be 204


  Scenario: Create new statistics entry
    Given that I want to update a "UserStatistics"
    And that its "token" is "just_some_random_token"
    And that its "distance" is "500"
    And that its "userId" is "123"
    When I request "/user/statistics"
    Then the response status code should be 204