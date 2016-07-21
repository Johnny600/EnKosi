# EnKosi
Created Version 3 on 14th July 2016

You can use the script below to help intergrate phantomjs with PHP website to take screenshots
If you wanna change what you can scrape/test, please edit from line 165 to 183 on the /class/darkmeta.php file thanks

New features:
Faster phantomjs scripting
Less logic done on objects
clean pure, simple code blocks

Please edit line 12 of index.php, to specify the website you wanna test, or not and just let the script take pics of google

Email me if you are angry and upset, and i will fix the issue,
Or drop something on the issue list

Email
davidntobeko@gmail.com

<div class=class="highlight highlight-text-html-php">
<pre>
USES:

if you want to inherit the whole class:
$DarkMeta = new DarkMeta();

To take a screenshot of the website:
$DarkMeta->URL_GET('http://www.gmail.com:80');

If you want to know if the website is up:
$DarkMeta->url_heartbeat('http://www.gmail.com:80');
</pre>
</div>

