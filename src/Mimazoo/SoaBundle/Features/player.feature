Feature: Player entity
    In order to manage players
    As a rest api user
    I need to be able to CRUD and list players

Scenario: List one player
    Given that I want to find a "Player"
    And that query parameter's "token" value is "CAAFM6NnZBvQoBAGn2ZBK3jFlyW2iE28FKvKTdgAer9QvuGt6LEtGrDxcYPIt2I8OsHATVqZADN58X2dA78mRre6NWtSGtEzk3yd8o4d9d75JbiiB0I5zyCTEuPeFIZAFSHwRrugxFtYtO9agpJirD3TPebxZApbpAH4GSOO7gUF0JOYYZANE56"
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
    Then the response data has a "present_id" property
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
    And that its "present_id" is "2"
    And that query parameter's "token" value is "CAAFM6NnZBvQoBAGn2ZBK3jFlyW2iE28FKvKTdgAer9QvuGt6LEtGrDxcYPIt2I8OsHATVqZADN58X2dA78mRre6NWtSGtEzk3yd8o4d9d75JbiiB0I5zyCTEuPeFIZAFSHwRrugxFtYtO9agpJirD3TPebxZApbpAH4GSOO7gUF0JOYYZANE56"
    When I request "/players/2"
    Then the response status code should be 204

    Given that I want to find a "Player"
    And that query parameter's "token" value is "CAAFM6NnZBvQoBAGn2ZBK3jFlyW2iE28FKvKTdgAer9QvuGt6LEtGrDxcYPIt2I8OsHATVqZADN58X2dA78mRre6NWtSGtEzk3yd8o4d9d75JbiiB0I5zyCTEuPeFIZAFSHwRrugxFtYtO9agpJirD3TPebxZApbpAH4GSOO7gUF0JOYYZANE56"
    When I request "/players/2"
    Then the response status code should be 200
    Then the "present_id" data property equals "2" of type "int"
    Then the "distance" data property equals "100" of type "int"

Scenario: Update with smaller distance
    Given that I want to update a "Player"
    And that its "distance" is "100"
    And that query parameter's "token" value is "CAAFM6NnZBvQoBAGn2ZBK3jFlyW2iE28FKvKTdgAer9QvuGt6LEtGrDxcYPIt2I8OsHATVqZADN58X2dA78mRre6NWtSGtEzk3yd8o4d9d75JbiiB0I5zyCTEuPeFIZAFSHwRrugxFtYtO9agpJirD3TPebxZApbpAH4GSOO7gUF0JOYYZANE56"
    When I request "/players/2"
    Then the response status code should be 204

    Given that I want to update a "Player"
    And that its "distance" is "50"
    And that query parameter's "token" value is "CAAFM6NnZBvQoBAGn2ZBK3jFlyW2iE28FKvKTdgAer9QvuGt6LEtGrDxcYPIt2I8OsHATVqZADN58X2dA78mRre6NWtSGtEzk3yd8o4d9d75JbiiB0I5zyCTEuPeFIZAFSHwRrugxFtYtO9agpJirD3TPebxZApbpAH4GSOO7gUF0JOYYZANE56"
    When I request "/players/2"
    Then the response status code should be 204

    Given that I want to find a "Player"
    And that query parameter's "token" value is "CAAFM6NnZBvQoBAGn2ZBK3jFlyW2iE28FKvKTdgAer9QvuGt6LEtGrDxcYPIt2I8OsHATVqZADN58X2dA78mRre6NWtSGtEzk3yd8o4d9d75JbiiB0I5zyCTEuPeFIZAFSHwRrugxFtYtO9agpJirD3TPebxZApbpAH4GSOO7gUF0JOYYZANE56"
    When I request "/players/2"
    Then the response status code should be 200
    Then the "distance" data property equals "100" of type "int"

Scenario: Update with too big present_id
    Given that I want to update a "Player"
    And that its "present_id" is "99"
    And that query parameter's "token" value is "CAAFM6NnZBvQoBAGn2ZBK3jFlyW2iE28FKvKTdgAer9QvuGt6LEtGrDxcYPIt2I8OsHATVqZADN58X2dA78mRre6NWtSGtEzk3yd8o4d9d75JbiiB0I5zyCTEuPeFIZAFSHwRrugxFtYtO9agpJirD3TPebxZApbpAH4GSOO7gUF0JOYYZANE56"
    When I request "/players/2"
    Then the response status code should be 400

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
    And that query parameter's "token" value is "CAAFM6NnZBvQoBAGn2ZBK3jFlyW2iE28FKvKTdgAer9QvuGt6LEtGrDxcYPIt2I8OsHATVqZADN58X2dA78mRre6NWtSGtEzk3yd8o4d9d75JbiiB0I5zyCTEuPeFIZAFSHwRrugxFtYtO9agpJirD3TPebxZApbpAH4GSOO7gUF0JOYYZANE56"
    When I request "/players/2"
    Then the response status code should be 204

Scenario: Log in one user
  Given that I want to find a "Player"
  #Ta token potece cez okrog 1.10.2013
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAMQbNbVCs2wL9JGW4INciqOV1n6gqcuZBBJ8XZCN6x3oLhZBrZBFpWtSZA6hpETV5d25LWpsYZCMZCSraGRUm4gPivLIR5q0st2PuZBlFndCvsQrzlTchGuoum36VHkoaQsNoXdSbMSG0BtXlhCs05ub0oF37Ju1vNmd9sqXMI6exBFMYt3JDKAZD"
  When I request "/players/login"
  Then the response is JSON
  Then the response status code should be 200
  Then the "success" property equals "true" of type "string"
  Then the "msg" property equals "New User" of type "string"
  Then store the response "access_token" property as new token

  Given that I want to find a "Player"
  When I request "/players/5"
  Then the response status code should be 200
  Then the "success" property equals "true" of type "string"
  Then the "id" data property equals "5" of type "int"
  Then the "name" data property equals "Tom Amfdeaeaahec Yangsky" of type "string"
  Then the "firstName" data property equals "Tom" of type "string"
  Then the "lastName" data property equals "Yangsky" of type "string"
  Then the "fb_id" data property equals "100006451511853" of type "string"
  Then the response data has a "distance" property
  Then the response data has a "present_id" property
  Then the response data has a "friends" property
  Then the response data has an array property "friends" of length "1"

  #Ta token potece cez okrog 1.10.2013
  And that query parameter's "token" value is "CAAFM6NnZBvQoBAEtKBLeh28T6ZCJkdsDBTgcIEClcSlZACIb06GQj3ZCOB4ZAg6ffp0r4FZBBzJbWAKm4MB6uAwIiZAqPMB6RCkvPPF5L7WCKc0ZA1SGVuAdtS0STH8GkyZAclKbSel3PA4JooQzmQZCIuG24XTQtjkVkZD"
  When I request "/players/login"
  Then the response is JSON
  Then the response status code should be 200
  Then the "success" property equals "true" of type "string"
  Then the "msg" property equals "New User" of type "string"
  Then store the response "access_token" property as new token

  Given that I want to find a "Player"
  When I request "/players/6"
  Then the response status code should be 200
  Then the "success" property equals "true" of type "string"
  Then the "id" data property equals "6" of type "int"
  Then the "name" data property equals "Karen Amfdbdjfcdej Letuchystein" of type "string"
  Then the "firstName" data property equals "Karen" of type "string"
  Then the "lastName" data property equals "Letuchystein" of type "string"
  Then the "fb_id" data property equals "100006424063450" of type "string"
  Then the response data has a "distance" property
  Then the response data has a "present_id" property
  Then the response data has a "friends" property
  Then the response data has an array property "friends" of length "2"

#  Given that I want to find a "Player"
#  And that query parameter's "token" value is "CAAFM6NnZBvQoBAMQbNbVCs2wL9JGW4INciqOV1n6gqcuZBBJ8XZCN6x3oLhZBrZBFpWtSZA6hpETV5d25LWpsYZCMZCSraGRUm4gPivLIR5q0st2PuZBlFndCvsQrzlTchGuoum36VHkoaQsNoXdSbMSG0BtXlhCs05ub0oF37Ju1vNmd9sqXMI6exBFMYt3JDKAZD"
 # When I request "/players/login"
#  Then store the response "access_token" property as new token
#
#  When I request "/players/3"
#  Then the response status code should be 200
#  Then the response data has an array property "friends" of length "1"

  #SAM ŠE PREVER DA SO SE FRENDI REGISTRIRAL V BAZI