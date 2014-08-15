Feature: WeeklyChallenge controller

Scenario: Query the last notification
  Given that I want to find a "WeeklyChallenge"
  When I request "/weeklychallenge/current"
  Then the response status code should be 200
  Then the response is JSON
  Then the "description" data property equals "COLLECT THE MOST COINS ACROSS THE WHOLE WEEK!" of type "string"

Scenario: List challenges player
    Given that I want to find a "WeeklyChallenge"
    And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
    When I request "/weeklychallenges"
    Then the response status code should be 200
    Then the response is JSON
    Then the "success" property equals "true" of type "string"

# Scenario: Create a new weekly challenge
#   Given that I want to make a new "WeeklyChallenge"
#   And that its "description" is "Get all the chicks this week"
#   And that its "type" is "1"
#   When I request "/weeklychallenge"
#   Then the response status code should be 204
