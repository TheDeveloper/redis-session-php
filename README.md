### Redis-backed PHP session handler
Stores your $_SESSION in Redis, using encoding of your choice. Dependency: [predis](https://github.com/nrk/predis) (included as submodule)

### Installation
Clone:

````
git clone --recursive https://TheDeveloper@github.com/TheDeveloper/redis-session-php.git
````
Or include as a submodule:

    git submodule add https://TheDeveloper@github.com/TheDeveloper/redis-session-php.git
    
### Usage
````
require('redis-session-php/redis-session.php');
RedisSession::start(); // overrides PHP's default session_save_handler and calls session_start()

// use sessions as normal
$_SESSION['barbara'] = 'streisand';
````
    
### Synopsis

I wanted to be able to have sessions that were transferable across apps running on different platforms. My intended use case was to have sessions shared between a PHP and Node.js app. Therefore, a Redis datastore acts as a shared store through which all apps can interact with the session data.

If you don't need to serialize in a universal format like JSON, and/or you only wish to use Redis as a session datastore, you can instead use PHP's native seralize() and unserialize(), which will preserve any data structure. Before you call RedisSession::start(), you define which functions to use like so:

````
define('REDIS_SESSION_SERIALIZER', 'serialize');
define('REDIS_SESSION_UNSERIALIZER, 'unserialize');
````

Otherwise, this module serializes your $_SESSION data using json_encode by default. The benefit of doing so is that the data is in a standardised format and can be read easily by other languages/platform, such as javascript.

**Warning: encoding to JSON gives no bidirectional support for multidimensional PHP arrays and objects**

The downside is that if a PHP array in your $_SESSION has more than one dimension (i.e. key => value nesting), json_encode converts it to an JSON object.

When we come to reading the data from the session store, json_decode only converts these objects to multidimensional PHP arrays, or PHP objects. It can't mix them, or recover the original data structures.

As a result, you can only ever have just arrays or just objects  in your $_SESSION. This module returns arrays by default. To switch between the two, use the 2nd (bool) parameter in json_decode. For only arrays:

    $_SESSION = json_decode(json_encode($_SESSION), true);
For only objects:

    $_SESSION = json_decode(json_encode($_SESSION));
   
    
### Testing
Requires PHP unit. We need to run phpunit with a bootstrap to get around PHP complaining about header output when it tries to start the session:

    phpunit --bootstrap test/bootstrap.php test/suite.php
        
### Session serialization rant:
PHP natively uses a very obfuscated serialisation format (apparently only session_decode() can read it). I don't have time to go digging through PHP source and writing my own adapter.

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

### The other stuff
Inspired by https://github.com/ivanstojic/redis-session-php and http://gonzalo123.wordpress.com/2011/07/25/using-node-js-to-store-php-sessions/

Check out http://petermoulding.com/php_sessions_and_serialisation for more info on PHP session serialisation

I also found this http://www.drupalcoder.com/blog/decoding-raw-php-session-values which only sorta works.

## LICENSE

(The MIT license)

Copyright (c) Geoff Wagstaff

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
of the Software, and to permit persons to whom the Software is furnished to do
so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
