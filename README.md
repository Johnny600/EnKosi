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

<pre>
<b>Stored images are found on 'Cache' folder thats automatically generated.</b>
<br>
USES:
<span style="color:red">
if you want to inherit the whole class:
</span>
$DarkMeta = new DarkMeta();

<span style="color:red">
To take a screenshot of the website:
</span>
$DarkMeta->URL_GET('http://www.gmail.com:80');

<span style="color:red">
If you want to know if the website is up:
</span>
$DarkMeta->url_heartbeat('http://www.gmail.com:80');
</pre>
</div>

