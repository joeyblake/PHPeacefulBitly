PHPeacefulBitly
===============

An *almost* fully featured, bit.ly API wrapper for PHP. Including oAuth endpoint.
Rolled up into a Simple Bitly object, minimal configuration.

Why peaceful? Because you don't have to fight with it.

Enjoy, ask questions, add to it, thanks. All available api functions are in order appearing [here](http://dev.bitly.com/api.html)

Requirements:
-------------
CURL

Todo:
---
* Finished stubbed api calls
* Tests

Example:
---

```

<?
  require('../bitly.php');
  
  /*
    Instantiate bitly class
   */
  $bitly = new Bitly(array(
    'apiKey' => '__YOUR_BITLY_API_KEY__',
    'apiSecret' => '__YOUR_BITLY_API_SECRET__',
    'apiCallback' => 'http://__YOUR ENDPOINT FOR CATCHING AND STORING YOUR AUTH TOKEN__/'
  ));
  
  /*
    Get auth link for oAuth
   */
  
  $bitly->getAuthUrl();
  //catch the $_GET['code'] at your apiCallback set above and stash it somewhere
  
  /*
    Once you've done the oAuth dance and have your access token
   */
  
  $bitly = $bitly->setAccessToken('__YOUR_STASHED_ACCESS_TOKEN__');
  
  /*
    Make api calls by bitly methods
   */
  
  $shortened = $bitly->shorten('http://codenimbus.com');
  $expanded = $bitly->expand('http://bit.ly/1oLvWHt');
  
```


License
---
The MIT License (MIT)
Copyright © 2014 Joey Blake, http://codenimbus.com

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the “Software”), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.