<?php

include './phpsecurity.php';

$test = new PHPSecure();

echo $test->TextScript("<script>alert('You would never see this')</script>Hello there person");

echo "<br>";

echo $test->SQLSelect(
    "test", 
    array("name", "email", "extraData"), // could also be "*" 
    (object)array( // Where data
        "name" => (object)array(
            "or" => false, // if TRUE then ... OR ... else ... AND ...
            "value" => "Tazio" // Value is required
        ),
    )
);

echo "<br>";

echo $test->SQLInsert(
    "test",
    array("name", "password", "email", "extraData"), // Table keys like (ID, firstname etc.)
    array(
        (object)array( // This will be seen as JSON
            "firstname" => "Tazio",
            "middlename" => "de",
            "lastname" => "Bruin"
        ),
        "thisPasswordIsNotSafe",
        "contact@tazio.nl",
        array("test","value")
    )
);


// * RESULTS
// Hello there person
// SELECT (name, email, extraData) FROM test WHERE name = 'Tazio'
// INSERT INTO test (`name`, `password`, `email`, `extraData`) VALUES ('{"firstname":"Tazio","middlename":"de","lastname":"Bruin"}', 'thisPasswordIsNotSafe', 'contact@tazio.nl', '["test","value"]')