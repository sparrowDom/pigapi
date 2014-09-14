Feature: WeeklyChallenge controller
   #// EVERYTHING NEEDS MORE TESTING!

Scenario: Create a new weekly challenge
  Given that I want to make a new "WeeklyChallenge"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
  And that its "description" is "RUN THE FURTHEST YOU CAN!"
  And that its "type" is "4"
  When I request "/weeklychallenge"
  Then the response status code should be 204

Scenario: Query the last weekly challenge
  Given that I want to find a "WeeklyChallenge"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
  When I request "/weeklychallenge/current"
  Then the response status code should be 200
  Then the response is JSON
  Then the "description" data property equals "COLLECT THE MOST COINS ACROSS THE WHOLE WEEK!" of type "string"
  Then the response data at 0 count has propery "started_on"

Scenario: Query the last weekly challenge wrong token
  Given that I want to find a "WeeklyChallenge"
  And that query parameter's "token" value is "false token man"
  When I request "/weeklychallenge/current"
  Then the response status code should be 400

Scenario: Query the last weekly challenge no token
  Given that I want to find a "WeeklyChallenge"
  When I request "/weeklychallenge/current"
  Then the response status code should be 400

Scenario: Create another challenge, current should be the first one
  Given that I want to make a new "WeeklyChallenge"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
  And that query parameter's "isFloat" value is "yes"
  And that its "description" is "DEFEAT WOLF IN THE MINIMUM AMOUNT OF TIME!"
  And that its "type" is "6"
  When I request "/weeklychallenge"
  Then the response status code should be 204

  Given that I want to find a "WeeklyChallenge"
  When I request "/weeklychallenge/current"
  Then the response status code should be 200
  Then the response is JSON
  Then the response data at 0 count has propery "started_on"
  Then the "description" data property equals "COLLECT THE MOST COINS ACROSS THE WHOLE WEEK!" of type "string"

Scenario: List weekly challenges
  Given that I want to find a "WeeklyChallenge"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
  When I request "/weeklychallenges"
  Then the response status code should be 200
  Then the response is JSON
  Then the "success" property equals "true" of type "string"
  Then the response data is an array that has "3" items

Scenario: Post an additive score to the challenge
  Given that I want to update a "WeeklyChallengeScore"
  And that its "coins" is "15"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
  When I request "/weeklychallenge/score"
  Then the response status code should be 204

  Given that I want to find a "WeeklyChallenge"
  When I request "/weeklychallenge/topscores"
  Then the response status code should be 200
  Then the response is JSON
  Then the response data is an array that has "1" items
  Then the response data at 0 count "score" property is "15"

  Given that I want to update a "WeeklyChallengeScore"
  And that its "coins" is "15"
  When I request "/weeklychallenge/score"
  Then the response status code should be 204

  Given that I want to find a "WeeklyChallenge"
  When I request "/weeklychallenge/topscores"
  Then the response status code should be 200
  Then the response is JSON
  Then the "success" property equals "true" of type "string"
  Then the response data is an array that has "1" items
  Then the response data at 0 count "score" property is "30"

Scenario: Complete coins challenge and post to furthest run (with console command instead of post to controller)
  When I run "weeklyChallenge:change" command
  Then I should see
  """
  New challenge started
  """

  Given that I want to find a "Notification"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
  When I request "/notifications"
  Then the response status code should be 200
  Then the response data is an array that has "4" items


  Given that I want to update a "WeeklyChallengeScore"
  And that its "distance" is "400"
  When I request "/weeklychallenge/score"
  Then the response status code should be 204

  Given that I want to find a "WeeklyChallenge"
  When I request "/weeklychallenge/topscores"
  Then the response status code should be 200
  Then the response is JSON
  Then the response data is an array that has "1" items
  Then the response data at 0 count "score" property is "400"

  Given that I want to update a "WeeklyChallengeScore"
  And that its "distance" is "300"
  When I request "/weeklychallenge/score"
  Then the response status code should be 204

  Given that I want to find a "WeeklyChallenge"
  When I request "/weeklychallenge/topscores"
  Then the response status code should be 200
  Then the response is JSON
  Then the response data is an array that has "1" items
  Then the response data at 0 count "score" property is "400"

  Given that I want to update a "WeeklyChallengeScore"
  And that its "distance" is "800"
  When I request "/weeklychallenge/score"
  Then the response status code should be 204

  Given that I want to find a "WeeklyChallenge"
  When I request "/weeklychallenge/topscores"
  Then the response status code should be 200
  Then the response is JSON
  Then the response data is an array that has "1" items
  Then the response data at 0 count "score" property is "800"

Scenario: Complete distance challenge and post to wolf defeat time
  Given that I want to update a "WeeklyChallenge"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
  When I request "/weeklychallenge/current/complete"
  Then the response status code should be 204

  Given that I want to find a "Notification"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
  When I request "/notifications"
  Then the response status code should be 200
  Then the response data is an array that has "8" items

  Given that I want to update a "WeeklyChallengeScore"
  And that its "wolfDefeatTime" is "10.5"
  When I request "/weeklychallenge/score"
  Then the response status code should be 204

  Given that I want to find a "WeeklyChallenge"
  When I request "/weeklychallenge/topscores"
  Then the response status code should be 200
  Then the response is JSON
  Then the response data is an array that has "1" items
  Then the response data at 0 count "score" property is "10.5"

  Given that I want to update a "WeeklyChallengeScore"
  And that its "wolfDefeatTime" is "15.7"
  When I request "/weeklychallenge/score"
  Then the response status code should be 204

  Given that I want to find a "WeeklyChallenge"
  When I request "/weeklychallenge/topscores"
  Then the response status code should be 200
  Then the response is JSON
  Then the response data is an array that has "1" items
  Then the response data at 0 count "score" property is "10.5"

  Given that I want to update a "WeeklyChallengeScore"
  And that its "wolfDefeatTime" is "5.2"
  When I request "/weeklychallenge/score"
  Then the response status code should be 204

  Given that I want to find a "WeeklyChallenge"
  When I request "/weeklychallenge/topscores"
  Then the response status code should be 200
  Then the response is JSON
  Then the response data is an array that has "1" items
  Then the response data at 0 count "score" property is "5.2"