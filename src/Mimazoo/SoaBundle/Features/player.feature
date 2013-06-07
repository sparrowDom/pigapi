Feature: Player entity
    In order to manage players
    As a rest api user
    I need to be able to CRUD and list players
 
Scenario: List players
    Given that I want to find a "Player"
    When I request "/players"
    Then the response is JSON
    Then the response status code should be 200
    Then the response has a "_embedded" property
    Then the response has a "_links" property
    
Scenario: Get player's details
    Given that I want to find a "Player"
    When I request "/players/1"
    Then the response is JSON
    Then the response status code should be 200
    Then the response has a "_links" property
    Then the response has a "id" property
    Then the "id" property equals "1" of type "int"
    Then the response has a "fbId" property
    Then the response has a "fbAccessToken" property
    Then the response has a "applePushToken" property
    Then the response has a "name" property
    Then the response has a "slug" property
    Then the response has a "challengesCounter" property
    Then the "challengesCounter" property equals "15" of type "int"
    Then the response has a "distanceBest" property
    Then the "distanceBest" property equals "1000" of type "int"
    
Scenario: Trying to get player's details for a not existing player
    Given that I want to find a "Player"
    When I request "/players/99999"
    Then the response is JSON
    Then the response status code should be 404
    Then the "status" property equals "error" of type "string"
    
Scenario: Add new player
    Given that I want to make a new "Player"
    And that its "fbId" is "620310212"
    And that its "fbAccessToken" is "CAACEdEose0cBABzQcWYmKRhOUI26qalQQ7R4ijLrnpB5ZAv8LdpJ5a2ElWXraoAywn5KRJLD4N6eaHwpmWzou0ZCvpoN0HauCwJUA8QsXIsjlH56ZABJA3nT5Rn4o58Ja60QheHq54m9dnuW3QykWEkBn5ajTrhZBUDbGYVSTgZDZD"
    And that its "applePushToken" is "TRALALA"
    And that its "name" is "Domen Grabec"
    And that its "distanceBest" is "100"
    And that its "challengesCounter" is "1"
    When I request "/players"
    Then the response status code should be 201

Scenario: Trying to add a new player without required fields set
    Given that I want to make a new "Player"
    And that its "name" is "test"
    When I request "/players"
    Then the response is JSON
    And the response status code should be 400    
    
Scenario: Update player
    Given that I want to update a "Player"
    And that its "fbId" is "620310212"
    And that its "fbAccessToken" is "CAACEdEose0cBABzQcWYmKRhOUI26qalQQ7R4ijLrnpB5ZAv8LdpJ5a2ElWXraoAywn5KRJLD4N6eaHwpmWzou0ZCvpoN0HauCwJUA8QsXIsjlH56ZABJA3nT5Rn4o58Ja60QheHq54m9dnuW3QykWEkBn5ajTrhZBUDbGYVSTgZDZD"
    And that its "applePushToken" is "TRALALA"
    And that its "name" is "Domen Grabec"
    And that its "distanceBest" is "105"
    And that its "challengesCounter" is "2"
    When I request "/players/1"
    Then the response status code should be 204  

Scenario: Trying to update a player without required fields set
    Given that I want to update a "Player"
    And that its "distanceBest" is "106"
    When I request "/players/1"
    Then the response status code should be 400
    
Scenario: Trying to update a player that does not exist
    Given that I want to update a "Player"
    When I request "/players/99999"
    Then the response status code should be 404   
    
Scenario: Patch update slug for player
    Given that I want to patch update a "Player"
    And that its "slug" is "krneki"
    When I request "/players/1"
    Then the response status code should be 204   
    
Scenario: Trying to patch update a not whitelisted field
    Given that I want to patch update a "Player"
    And that its "foo" is "bar"
    When I request "/players/1"
    Then the response status code should be 400       
    
Scenario: Trying to patch update a not existing player
    Given that I want to patch update a "Player"
    And that its "foo" is "bar"
    When I request "/players/9999"
    Then the response status code should be 404          

Scenario: Delete player
    Given that I want to delete a "Player"
    When I request "/players/1"
    Then the response status code should be 204    
    
Scenario: Trying to delete not existing player
    Given that I want to delete a "Player"
    When I request "/players/9999"    
    Then the response status code should be 404    
    