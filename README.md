### Redis-backed PHP session handler
Stores your $_SESSION in Redis, json encoded. Dependency: [predis](https://github.com/nrk/predis)

I wanted to be able to have sessions that were ubiquitous across apps running on different platforms. My intended use case was to have sessions shared between a PHP and Node.js app.

PHP session data is serialized using json_encode by default, because PHP uses a criminally indescipherable format by default (apparently only session_decode() can read it). I don't have time to go digging through PHP source and writing my own adapter. See below for rant.

**Warning: encoding to JSON gives no bidirectional support for 2+D PHP arrays and objects**

If a PHP array has more than one dimension (i.e. key => value nesting), json_encode converts it to an JSON object.

You can only ever have just arrays or just objects for these data structures in the decoded response. To switch between the two, use the 2nd (bool) parameter in json_decode

    json_decode(json_encode($_SESSION), true);
    
### Testing
Requires PHP unit. We need to run a fairly funky command to get around PHP complaining about header output when it tries to start the session:

    phpunit --bootstrap test/bootstrap.php test/suite.php

### Session serialization rant:
````
/**
     * RANT: It's seemingly impossible to parse the value in $data.
     * Example:
     *
     * PHP Serialises the following:
     * $_SESSION['test'] = "ohai";
     * $_SESSION['md'] = array('test2' => array('multidimensional' => 'array'));
     * $_SESSION['more'] = new stdClass;
     *
     * Gives:
     *
     * test|s:4:"ohai";md|a:1:{s:5:"test2";a:1:{s:16:"multidimensional";s:5:"array";}}more|O:8:"stdClass":0:{}
     *
     * Where are the delimeters between keys? I'm testing this on PHP 5.3.8 with
     * Suhosin patch, and session_decode() gives false.
     *
     * This is why, on write, we have to access $_SESSION and encode that into
     * a format which is more generic and world readable
     */
     
````