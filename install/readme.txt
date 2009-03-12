Installation of the Signlink CMS

Server requirements
	mySQL 5+
	PHP 5 with GD library
	Mail server (for password reminder)

Installation

1. Unzip folder into a web-accessible directory on the server.

2. Set permissions for the /uploads directory, and its sub-directories, to be 777 (readable, writeable, and executable by all)

3. Create the configuration file by copying /install/config_template.php to /include/config.inc.php and open it in an editor. Fill in the values for your server's mySQL connection and database.

4. Run the mySQL script /install/signlink_schema.sql to set up the necessary tables for your site's database.

5. Edit the admin password and email address by logging into the admin area (add /admin to the end of your site's URL) with login "admin" and password "admin". Go to the settings area and change the admin password to something more secure, and update the contact email.