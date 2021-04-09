# PHP Security
Make users input more secure

## Installation
1. Download or clone
2. Open `phpsecurity.php`
3. Copy the class
4. Look at example.php for usage and options


## Results
```php
$test->TextScript("<script>alert('You would never see this')</script>Hello there person");
Hello there person
```
```php
$test->SQLSelect(
    "test", 
    array("name", "email", "extraData"), // could also be "*" 
    (object)array( // Where data
        "name" => (object)array(
            "or" => false, // if TRUE then ... OR ... else ... AND ...
            "value" => "Tazio" // Value is required
        ),
    )
);
SELECT (name, email, extraData) FROM test WHERE name = 'Tazio'
```
```php
$test->SQLInsert(
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
INSERT INTO test (`name`, `password`, `email`, `extraData`) VALUES ('{"firstname":"Tazio","middlename":"de","lastname":"Bruin"}', 'thisPasswordIsNotSafe', 'contact@tazio.nl', '["test","value"]')
```