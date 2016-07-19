***********************************************************************************************
***********************************************************************************************
** Plugin Name: KORA Database Display                                                                  **
** Plugin URI: TBD                                                                                              **
** Description: Plugin for displaying information from a KORA digital repository platform.   **
** Author: MATRIX: The Center for Digital Humanities and Social Sciences
                  **
** Version: 1.0                                                                                                   **
** Author URI: TBD                                                                                             **
***********************************************************************************************
***********************************************************************************************


Intro:

This is a plugin for displaying information from a KORA digitial repositiory platform on your Wordpress site.
You can display your data in galleries or libraries.


Installation and Configuration.:

To install, get the source code for the KORA plugin. Place it in the wordpress plugins directory which should be something like:
\wordpress\wp-content\plugins
From there, edit the file dbconfig.php.dist. Only fill out the information where the Xs indicate for Server Authentication Settings, as shown below:

//Server Authentication Settings
//server username
define("kordat_dbuser", 'XXXX');
//server password
define("kordat_dbpass", "XXXXXX");

The User Database Settings have been filled out for you and don't need to be touched.
Then, save this file as dbconfig.php.


Activation:
On your wordpress dashboard, go to the plugins menu and activate the kora plugin.
You should now have a 'Kora' menu of your own on the left side of your dashboard.


Kora Settings:
In your Kora menu, go to the Kora option. This will open a page for general settings.
First, you must enter the URL of the KORA installation. Unless you have a private KORA server, this will be what you enter:

http://kora.matrix.msu.edu/


Adding Project and Schemes:

Adding projects and associated schemes are done on the Kora General Settings page as well.
To add a project, click the green '+' button next to the title 'Project ID and Token'. Then, you must enter your project ID (which is labeled as a 'pid' on KORA) as well as
the associated search token (also can be found in KORA -- When in your project, 'search tokens' should be an option on the left menu bar).
After this has been verified, you can click the drop down menu next to 'Scheme ID' and pick which schemes you want to be able to work with.
You will simply check the box next to whatever Scheme ID (known as SID on KORA) in the drop down menu you want to use.

You now have the ability to use the data from these schemes to build up your galleries and library.


Galleries:
To create a new gallery or add to an existing one, you will choose the 'Galleries' option in the KORA menu on the left menu bar of your dashboard.

To create a new gallery, you will click the 'Add New Gallery' button at the top of the page.
A window will pop up and you will have to choose what data you want to use.
First, you will choose what scheme you will be using. You may pick from the drop down menu next to 'Scheme ID'.
Next, you will choose what controls you want to be able to add. To add multiple controls, click the green '+' button next to the drop down menu. 
After that, options of those controls should appear below and you can choose to add all of them, or you can pick and choose to add which ones you want. 
If there are many options to choose from, you can use the search bar to search for a specific object. 
Then, give your gallery a name and click 'Create Gallery' at the bottom of the page. It should then appear in the gallery display.

To add objects to an existing gallery, click the 'Add to Existing Gallery' button.
From there, you will choose a scheme and controls, and objects the same way you did when creating a new gallery.
After you've chosen your objects, you will pick the current gallery you want to add your's to from the drop down menu
that will appear at the bottom of the page and then click 'Add to Existing Gallery'. They should then appear in the gallery display.

To delete an object from a gallery or delete the gallery itsself, simply click the red 'x' button next to the object or on the upper right corner of the gallery you want to delete.


Libraries:
You can add your objects to your Kora Library as well. To do this, first pick what scheme you want to use from the drop down menu next to 'Scheme ID'.
Next, choose the controls you want to use. To add more controls, click the green '+' button next to the dropdown menu.
After that, options of those controls should appear below and you can choose to add all of them, or you can pick and choose to add which ones you want. 
If there are many options to choose from, you can use the search bar to search for a specific object. 
After that, simply hit the 'Insert New Object' button and it should be displayed in your Library display.

To delete an object from your library, click the red 'x' button next to the object you want to delete.



Legal
---------------------

1) KORA Database Display and all other likenesses to KORA: The Digital Repository and 
   Publishing Platform are property of Matrix: Center for Digital Humanities & Social Sciences 
   and Michigan State University.

2) This plugin holds the same legal standings as KORA: The Digital Repository and Publishing 
   Platform. This includes, but not limited to, copyrights, licensing, and open-source usage.
   
   
   
   
   
*** This is the old readme file. I found I didn't use some of the instructions and didn't know if they were
useful for the general user. I didn't want to simply delete them if you wanted part of them added to my 
readme file, so I just put them on the bottom of this page for you do with as you please. ***
   
Installation & Use
---------------------

1) Complete the options form in the admin settings section, labeled "KORA Database Display". 
   Graphs cannot display properly until all information is filled out.

2) Imbed KORA graph using html with a URL pointing to the kora_execute.php file. Iframes are 
   recommened as they have been tested and are easily modifiable.

3) You may imbed a graph in any area of wordpress that allows php processing using the function
   kordat_getrecords().

4) IMPORTANT! For security purposes, please change the passwords in the files 
   kora_database_admin.php (2 places) and kora_execute.php (1 place). Make sure all three 
   passwords match exactly and are as random as possible. 