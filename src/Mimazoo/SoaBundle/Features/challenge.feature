Feature: Challenge controller
  In order to manage challenges
  As a rest api user
  I need to be able to CRUD and list of challenges

Scenario: Create a challenge wrong challenger id
  Given that I want to make a new "Challenge"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAGn2ZBK3jFlyW2iE28FKvKTdgAer9QvuGt6LEtGrDxcYPIt2I8OsHATVqZADN58X2dA78mRre6NWtSGtEzk3yd8o4d9d75JbiiB0I5zyCTEuPeFIZAFSHwRrugxFtYtO9agpJirD3TPebxZApbpAH4GSOO7gUF0JOYYZANE56"
  And that its "type" is "0"
  And that its "value" is "122"
  And that its "challenger_fbid" is "608899282xxx"
  And that its "challenged_fbid" is "100006480546572"
  When I request "/challenge/new"
  Then the response status code should be 400

Scenario: Create a challenge wrong challenged id
  Given that I want to make a new "Challenge"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAGn2ZBK3jFlyW2iE28FKvKTdgAer9QvuGt6LEtGrDxcYPIt2I8OsHATVqZADN58X2dA78mRre6NWtSGtEzk3yd8o4d9d75JbiiB0I5zyCTEuPeFIZAFSHwRrugxFtYtO9agpJirD3TPebxZApbpAH4GSOO7gUF0JOYYZANE56"
  And that its "type" is "0"
  And that its "value" is "122"
  And that its "challenger_fbid" is "608899282"
  And that its "challenged_fbid" is "100006480546572xxx"
  When I request "/challenge/new"
  Then the response status code should be 400

Scenario: Create a challenge correct id but not the challenger or challengee
  Given that I want to make a new "Challenge"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAGn2ZBK3jFlyW2iE28FKvKTdgAer9QvuGt6LEtGrDxcYPIt2I8OsHATVqZADN58X2dA78mRre6NWtSGtEzk3yd8o4d9d75JbiiB0I5zyCTEuPeFIZAFSHwRrugxFtYtO9agpJirD3TPebxZApbpAH4GSOO7gUF0JOYYZANE56"
  And that its "type" is "0"
  And that its "value" is "122"
  And that its "challenger_fbid" is "620310212"
  And that its "challenged_fbid" is "100006480546572xxx"
  When I request "/challenge/new"
  Then the response status code should be 400


Scenario: Create a challenge, challenged reads it, wins and challenger reads it
  Given that I want to make a new "Challenge"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
  And that its "type" is "0"
  And that its "value" is "122"
  And that its "challenger_fbid" is "608899282"
  And that its "challenged_fbid" is "100006480546572"
  When I request "/challenge/new"
  Then the response status code should be 204

  Given that I want to find a "Challenge"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBALDUv0CgFoUGbrZBenZCblI7A8W06ab6dmk7sZAZCGvnC0dOtS4hx515HbWUYvvmIzkoZAkxNh5mFJc6ZCQy2xsKvriXKCzxQS0BQW8OP9ISE5mrBWtnzPO2xtmY4ohwIhoIs566w96b6p5XT8GYXxI3plWIXcRbtfuAte50FZC"
  When I request "/challenges/1"
  Then the response status code should be 200
  Then the response is JSON
  Then the "success" property equals "true" of type "string"
  Then the "id" data property equals "1" of type "int"
  Then the "state" data property equals "1" of type "int"
  Then the "type" data property equals "0" of type "int"
  Then the "value" data property equals "122" of type "int"

  Given that I want to update a "Challenge"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBALDUv0CgFoUGbrZBenZCblI7A8W06ab6dmk7sZAZCGvnC0dOtS4hx515HbWUYvvmIzkoZAkxNh5mFJc6ZCQy2xsKvriXKCzxQS0BQW8OP9ISE5mrBWtnzPO2xtmY4ohwIhoIs566w96b6p5XT8GYXxI3plWIXcRbtfuAte50FZC"
  And that its "state" is "2"
  When I request "/challenges/1"
  Then the response status code should be 204

  Given that I want to find a "Challenge"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBALDUv0CgFoUGbrZBenZCblI7A8W06ab6dmk7sZAZCGvnC0dOtS4hx515HbWUYvvmIzkoZAkxNh5mFJc6ZCQy2xsKvriXKCzxQS0BQW8OP9ISE5mrBWtnzPO2xtmY4ohwIhoIs566w96b6p5XT8GYXxI3plWIXcRbtfuAte50FZC"
  When I request "/challenges/1"
  Then the response status code should be 200
  Then the response is JSON
  Then the "state" data property equals "2" of type "int"

  Given that I want to find a "Challenge"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
  When I request "/challenges/1"
  Then the response status code should be 200
  Then the response is JSON
  Then the "state" data property equals "4" of type "int"

Scenario: Create a challenge
  Given that I want to make a new "Challenge"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
  And that its "type" is "0"
  And that its "value" is "122"
  And that its "challenger_fbid" is "608899282"
  And that its "challenged_fbid" is "620310212"
  When I request "/challenge/new"
  Then the response status code should be 204

Scenario: Create a challenge
  Given that I want to make a new "Challenge"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
  And that its "type" is "0"
  And that its "value" is "122"
  And that its "challenger_fbid" is "608899282"
  And that its "challenged_fbid" is "100006480546572"
  When I request "/challenge/new"
  Then the response status code should be 204

Scenario: Create a challenge
  Given that I want to make a new "Challenge"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
  And that its "type" is "0"
  And that its "value" is "122"
  And that its "challenger_fbid" is "608899282"
  And that its "challenged_fbid" is "100006462098682"
  When I request "/challenge/new"
  Then the response status code should be 204

Scenario: List challenges
  Given that I want to find a "Challenge"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
  When I request "/challenges"
  Then the response status code should be 200
  Then the response is JSON
  Then the "success" property equals "true" of type "string"
  Then the response data is an array that has "3" items




