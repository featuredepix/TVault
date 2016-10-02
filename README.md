# TVault (Alpha)
The super simple web-based TV show manager!

**Please note that this project is in an early alpha stage, and so there will most likely be many bugs and glitches. Please report them by creating an issue on this project, or by tweeting @imfakeluke!**

## Features
* Track your TV shows!
* Get a calendar view of when your favourite TV shows air!

## In Development Features
* Mobile Notifications of when your favourite TV shows go live!
* TV show posters and episode lists with synopsises!
* Any more ideas? Tweet @imfakeluke or create an issue on this project!

## Requirements
* A web server running PHP. (Apache, Nginx)
* A MySQL server, with a database and user configured. (See Installation below)
* The names of your favourite TV shows!

## Installation
1. Ensure that your web server is working correctly and that PHP is installed successfully. To do this, create a php file (call it whatever you want) in your server's root directory with the following line of code in it: `<?php phpinfo(); ?>`. If you see a nice page with lots of information regarding PHP then you are ready to move onto Step #2.
2. Start your MySQL database (if you haven't already) and create a database. You can name it whatever you want, however we recommend 'tvault' (without the 's). Then, create a user that has access to that database.
3. Download the .zip of TVault from this Github page, and then extract the whole .zip to your web server's root directory.
4. Edit the file called 'config.php' with your MySQL details from Step #2.
5. Navigate to your web server in a browser (we don't officially support Internet Explorer), and then you can start configuring your TV show library!
