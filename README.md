# EnKosi
This application helps you take screenshots of websites and their current state. It is an engine that can be run in the background.

Kindly note that its in its alpha state and you(besides me) will help publicly push for a stable v3 :)

David 'cronus'
davidntobeko@gmail.com

Install:
Please change define('project_name', 'en_kosi_v3_alph'); to define('project_name', '');

USES:

$en_kosi_core = new en_kosi_core();

initialises the entire class

There after, depending on your resource, you access as an object through a method:
$en_kosi_core->URL_GET($prod);

Runs a screengrab and saves to /cache folder


