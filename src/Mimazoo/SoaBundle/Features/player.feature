Feature: Player entity
    In order to manage players
    As a rest api user
    I need to be able to CRUD and list players

Scenario: List one player
    Given that I want to find a "Player"
    And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
    When I request "/players/2"
    Then the response status code should be 200
    Then the response is JSON
    Then the "success" property equals "true" of type "string"
    Then the "id" data property equals "2" of type "int"
    Then the "name" data property equals "Domen Grabec" of type "string"
    Then the "firstName" data property equals "Domen" of type "string"
    Then the "lastName" data property equals "Grabec" of type "string"
    Then the "fb_id" data property equals "608899282" of type "string"
    Then the response data has a "distance" property
    Then the response data has a "friends" property

Scenario: Trying to get player's details for a not existing player
    Given that I want to find a "Player"
    When I request "/players/99999"
    Then the response is JSON
    Then the response status code should be 404
    Then the "status" property equals "error" of type "string"

#Scenario: Add new player
#    Given that I want to make a new "Player"
#    And that its "fbId" is "620310212"
#    And that its "fbAccessToken" is "CAACEdEose0cBABzQcWYmKRhOUI26qalQQ7R4ijLrnpB5ZAv8LdpJ5a2ElWXraoAywn5KRJLD4N6eaHwpmWzou0ZCvpoN0HauCwJUA8QsXIsjlH56ZABJA3nT5Rn4o58Ja60QheHq54m9dnuW3QykWEkBn5ajTrhZBUDbGYVSTgZDZD"
#    And that its "applePushToken" is "TRALALA"
#    And that its "name" is "Domen Grabec"
#    And that its "distanceBest" is "100"
#    And that its "challengesCounter" is "1"
#    When I request "/players"
#    Then the response status code should be 201

Scenario: Update player
    Given that I want to update a "Player"
    And that its "distance" is "100"
    And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
    When I request "/players/2"
    Then the response status code should be 204

    Given that I want to find a "Player"
    And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
    When I request "/players/2"
    Then the response status code should be 200
    Then the "distance" data property equals "100" of type "int"


Scenario: Update apple push token
    Given that I want to update a "Player"
    And that its "apple_push_token" is "XXXXX-XXXXX-XXXXX-XXXXX"
    And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
    When I request "/players/2"
    Then the response status code should be 204

    Given that I want to find a "Player"
    And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
    When I request "/players/2"
    Then the response status code should be 200
    Then the "apple_push_token" data property equals "XXXXX-XXXXX-XXXXX-XXXXX" of type "string"

Scenario: Update with smaller distance
    Given that I want to update a "Player"
    And that its "distance" is "100"
    And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
    When I request "/players/2"
    Then the response status code should be 204

    Given that I want to update a "Player"
    And that its "distance" is "50"
    And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
    When I request "/players/2"
    Then the response status code should be 204

    Given that I want to find a "Player"
    And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
    When I request "/players/2"
    Then the response status code should be 200
    Then the "distance" data property equals "100" of type "int"

Scenario: Trying to update a player without a token. Should be a forbidden resource
    Given that I want to update a "Player"
    And that its "distanceBest" is "106"
    When I request "/players/2"
    Then the response status code should be 403

Scenario: Trying to update a player that does not exist
    Given that I want to update a "Player"
    When I request "/players/99999"
    Then the response status code should be 404

Scenario: Trying to patch update a not whitelisted field
    Given that I want to update a "Player"
    And that its "foo" is "bar"
    And that query parameter's "token" value is "CAAFM6NnZBvQoBAJ9JyfTlzJurAueMa1CZC19xhRBp4sKCHs6YSrKHSZBGTiBfsoIIWu0neCoQs5mqonAATsYuqDVZBArGkHuZBeeB9qKBPNP7k73Qg9UuQ6SIC70QdZBZCiG2IKNywHhxMl08MdZCs4A6ZAQOvW4fdUTYMByKwmW0RDQvEKp6jZCFY"
    When I request "/players/2"
    Then the response status code should be 204

Scenario: Log in one user
  Given that I want to find a "Player"
  And that query parameter's "deviceToken" value is "deviceToken123456"
  #Ta token potece cez okrog 1.10.2013
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAHApQ6brcFiwKM0Pdx2JojUgBqpCbQpw7bNa9gMJmcSFeeDZC1Ncmbq4ZCrb32hEIFjSB5ZBEyL8TxJnfRNWRNbj2ZBSsRCSwcnGfsh1pGzdwUHy6iDz1kF3J2VyskRyjsPrQ76Mx48kXBbMjJ7ZASoAGMvbPnigDS84W8rZCFUSIjYxLOZAZBkZD"
  When I request "/players/login"
  Then the response is JSON
  Then the response status code should be 200
  Then the "success" property equals "true" of type "string"
  Then the "msg" property equals "New User" of type "string"
  Then store the response "access_token" property as new token
  Then the "deviceToken" property equals "deviceToken123456" of type "string"

  Given that I want to find a "Player"
  When I request "/players/7"
  Then the response status code should be 200
  Then the "success" property equals "true" of type "string"
  Then the "id" data property equals "7" of type "int"
  Then the "name" data property equals "Tom Amfdeaeaahec Yangsky" of type "string"
  Then the "firstName" data property equals "Tom" of type "string"
  Then the "lastName" data property equals "Yangsky" of type "string"
  Then the "fb_id" data property equals "100006451511853" of type "string"
  Then the response data has a "distance" property
  Then the response data has a "friends" property
  Then the response data has an array property "friends" of length "1"
  Then the "msg" property is not present

  #Ta token potece cez okrog 1.12.2013
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAHKwtAfGxTO0UwcnJAvud83GE3giuzVzmnoa8zznKk1336ADnHZBXraxzmaZBYmrc3AFn8J3q5zZCWddNXA8j1cjhcI8dqFCfuxgvKLuDCiZBnzip8MxNmUaGxM9ooorxeuZCR0RNRY0XR45LUQpE69DyPfPpaAZCZAxnw4xXJd"
  When I request "/players/login"
  Then the response is JSON
  Then the response status code should be 200
  Then the "success" property equals "true" of type "string"
  Then the "msg" property equals "New User" of type "string"
  Then store the response "access_token" property as new token

  Given that I want to find a "Player"
  When I request "/players/8"
  Then the response status code should be 200
  Then the "success" property equals "true" of type "string"
  Then the "id" data property equals "8" of type "int"
  Then the "name" data property equals "Karen Amfdbdjfcdej Letuchystein" of type "string"
  Then the "firstName" data property equals "Karen" of type "string"
  Then the "lastName" data property equals "Letuchystein" of type "string"
  Then the "fb_id" data property equals "100006424063450" of type "string"
  Then the response data has a "distance" property
  Then the response data has a "friends" property
  Then the response data has an array property "friends" of length "2"
  Then the "msg" property is not present

Scenario: Log in one user using device token
  Given that I want to find a "Player"
  And that query parameter's "deviceToken" value is "deviceToken123456"
  When I request "/players/login"
  Then the response is JSON
  Then the response status code should be 200
  Then the "success" property equals "true" of type "string"
  Then the "msg" property equals "New Non Facebook User" of type "string"

#login again should work
  Given that I want to find a "Player"
  And that query parameter's "deviceToken" value is "deviceToken123456"
  When I request "/players/login"
  Then the response is JSON
  Then the response status code should be 200
  Then the "success" property equals "true" of type "string"
  Then the "msg" property is not present
  Then the "deviceToken" property equals "deviceToken123456" of type "string"

Scenario: Browse highscores
  Given that I want to find a "Player"
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAE9Y2qydlfTo6pMfoVpDLkglq8gQwZBREnBPXBXMGXgQcXLKTmAv5Fc1cnaI1crFO8qdJy73DnG1ZCImv2aNH0waaKx2Ch4Vihl6DZCTqtoAJEKXFHDYlxZCaJskOwrKUwwbN1z0atmXFkXX1MKRFqM3ZAovg0RBzt3qNurgO"
  When I request "/players/highscores/alltime"
  Then the response is JSON
  Then the "success" property equals "true" of type "string"
  Then the response data is an array that has "9" items


#  Given that I want to find a "Player"
#  And that query parameter's "token" value is "CAAFM6NnZBvQoBAMQbNbVCs2wL9JGW4INciqOV1n6gqcuZBBJ8XZCN6x3oLhZBrZBFpWtSZA6hpETV5d25LWpsYZCMZCSraGRUm4gPivLIR5q0st2PuZBlFndCvsQrzlTchGuoum36VHkoaQsNoXdSbMSG0BtXlhCs05ub0oF37Ju1vNmd9sqXMI6exBFMYt3JDKAZD"
 # When I request "/players/login"
#  Then store the response "access_token" property as new token
#
#  When I request "/players/3"
#  Then the response status code should be 200
#  Then the response data has an array property "friends" of length "1"

  #SAM ŠE PREVER DA SO SE FRENDI REGISTRIRAL V BAZI