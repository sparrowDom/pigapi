Feature: WeeklyChallenge controller

Scenario: Create a new weekly challenge
  Given that I want to make a new "WeeklyChallenge"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
  And that its "description" is "COLLECT THE MOST COINS ACROSS THE WHOLE WEEK!"
  And that its "type" is "1"
  When I request "/weeklychallenge"
  Then the response status code should be 204

Scenario: Query the last weekly challenge
  Given that I want to find a "WeeklyChallenge"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
  When I request "/weeklychallenge/current"
  Then the response status code should be 200
  Then the response is JSON
  Then the "description" data property equals "COLLECT THE MOST COINS ACROSS THE WHOLE WEEK!" of type "string"

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
  And that its "description" is "RUN THE FURTHEST ACROSS MULTIPLE RUNS!"
  And that its "type" is "2"
  When I request "/weeklychallenge"
  Then the response status code should be 204

  Given that I want to find a "WeeklyChallenge"
  When I request "/weeklychallenge/current"
  Then the response status code should be 200
  Then the response is JSON
  Then the "description" data property equals "COLLECT THE MOST COINS ACROSS THE WHOLE WEEK!" of type "string"

Scenario: List weekly challenges
  Given that I want to find a "WeeklyChallenge"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
  When I request "/weeklychallenges"
  Then the response status code should be 200
  Then the response is JSON
  Then the "success" property equals "true" of type "string"
  Then the response data is an array that has "2" items

Scenario: Post a score to the challenge
  Given that I want to update a "WeeklyChallengeScore"
  And that its "coins" is "15"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
  When I request "/weeklychallenge/score"
  Then the response status code should be 204

  Given that I want to update a "WeeklyChallengeScore"
  And that its "coins" is "15"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
  When I request "/weeklychallenge/score"
  Then the response status code should be 204

  Given that I want to find a "WeeklyChallenge"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
  When I request "/weeklychallenge/topscores"
  Then the response status code should be 200
  Then the response is JSON
  Then the "success" property equals "true" of type "string"
  Then the response data is an array that has "1" items
  Then the response data at 0 count "score" property is "30"

