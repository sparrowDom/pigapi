Feature: WeeklyChallenge controller

Scenario: Query the last notification
  Given that I want to find a "WeeklyNotification"
  When I request "/weeklychallenge/current"
  Then the response status code should be 200
  Then the response is JSON
  Then the "description" data property equals "COLLECT THE MOST COINS ACROSS THE WHOLE WEEK!" of type "string"

Scenario: Complete the last notification
  Given that I want to make a new "WeeklyNotification"
  And that its "description" is "Get all the chicks this week"
  And that its "type" is "1"
  When I request "/weeklychallenge"
  Then the response status code should be 204
