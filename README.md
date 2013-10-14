Punchcard
=========

Punchcard is an online report portfolio. It supports auto completion and digtial signed reports.

Introduction
------------

This app, named Punchcard, is used to create an online report portfolio for an apprenticeship.
It is based on the open source framework CakePHP 2.3 and other libraries listed below.

Currently this app supports only weekly report portfolios.
At least two users are needed to use this app properly:
- An Instructor: He has sub-users and can review and accept reports that are submitted by users that belong to him/her. He/She can also upload a digital signatur (image and certificate) to sign reports digital so that belonging users can donwload & print their reports.
- Normal users: A normal user does always belong to an instructor. Users can create and edit not accepted reports and submit them for reviewing. They also can download them for printing.
 
This app is at a rudimental state. It is a beta version, because I had only a short amount of time to code it.

The code is well documented in commentaries. Please contact me if there are further questions.

I am currently very short on time and I hope that this project grows with it being open source.


Requirements
------------

I used a bunch of external tools and libraries to make the coding as easy and fast as possible.
I included all of them in the repository to make sure this app is working "out of the box".

Plugins like BlackBeard and Twitter Bootstrap are currently under heavy development and can break backwards compatibility with every commit. So please be careful in case you update them.

- CakePHP 2.3+ (< 3.0 (untested))
- BlackBeard plugin (https://github.com/bbcrew/Bb) (included as submodule)
- Twitter Bootstrap plugin (https://github.com/bbcrew/Twb) (included as submodule)
- FPDI and FPDF_TPL (http://www.setasign.de/products/pdf-php-solutions/fpdi/downloads/)
- TCPDF (http://www.tcpdf.org/)
 

License
-------

Punchcard is licensed under GPLv2.

Every tool or library included in it may be licensed under its own license.


Warranty
--------

Absolutly no warranty for this app or its used tools and libraries is given by me.


Installation
------------

Note: This is a very early state of this project. There is nothing like an installer or something.

1. Upload working copy
2. Change the path to the cakephp instance in webroot/index.php if needed. (Default is the [parentFolderOfTheAppRoot/cakephp2.3])
2. Create a database and insert schema.sql into it
3. Change the database credentials in Config/database.php
4. Change the "Security.salt" and the "Security.cipherSeed" in Config/core.php for security reasons
5. In the AppController line ~435 add "admin_add" to the allowed actions so it looks like this:
      `$aA = array('login', 'logout', 'redirect_by_role', 'admin_add');`
6. Visit http://example.com/admin/user/users/add and create a new user with admin privilegs
7. Remove "admin_add" from allowed actions again to prevent security breaches.
8. Set permissions of "tmp/" directory and its subfolders to 777 or at least as high that the webserver user can write into it.
9. Start using the app :)
 
