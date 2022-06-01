# Undergrad Databases Project: Dotify

This is an application I built in my undergrad with 3 other team members: Jonathan Hansen, Scott Pidzarko and Alex Lin. The purpose of this project was to demonstrate a fully functional web application utilizing a MySQL database. We decided to use the [Laravel](https://laravel.com/) PHP framework to build the application.

Dotify is meant to provide a subset of the services provided by [Spotify](https://www.spotify.com). It is a small music 
management system in which users are able to create playlists from a library of albums and songs. 

There are two classes of users - administrators and users. Standard users have access to their own playlists, and all the songs in the Dotify library. Administrators have access to a console that allows them to modify the songs users have access to, and complete library statistics.  

### Standard users

The following credentials can be used - username: Porchwork, password: 123333

<img src="https://github.com/snxfz947/dotify/blob/master/screens/1.png"><br>_Logging in as a 
standard user_

<img src="https://github.com/snxfz947/dotify/blob/master/screens/2.png"><br>_User interface for standard
users_

Users are able to search for songs by song name or genre:

<img src="https://github.com/snxfz947/dotify/blob/master/screens/3.png"><br>_Searching by song name_

They are also able to add their own playlists:

<img src="https://github.com/snxfz947/dotify/blob/master/screens/7.png"><br>_Adding a playlist_

<img src="https://github.com/snxfz947/dotify/blob/master/screens/11.png"><br>_Adding a song to a playlist_

They can also update the rating assigned to the songs in their playlists:

<img src="https://github.com/snxfz947/dotify/blob/master/screens/8.png"><br>

<img src="https://github.com/snxfz947/dotify/blob/master/screens/10.png"><br>_Updating a rating_

### Administrators

Administrators have access to the administrator console. The following credentials can be used - username: SnXfZ947, 
password: abcd123

<img src="https://github.com/snxfz947/dotify/blob/master/screens/4.png"><br>_Administrator's console shown_

They have access to statistics for the entire library, such as for albums and playlists:

<img src="https://github.com/snxfz947/dotify/blob/master/screens/5.png"><br>_Statistics for all albums
in library_

Administrators can also add songs to the library:

<img src="https://github.com/snxfz947/dotify/blob/master/screens/6.png"><br>_Adding songs to the library_

## Heroku deployment instructions

See full instructions here: https://devcenter.heroku.com/articles/getting-started-with-laravel

The `Procfile` is already set up - just need to set up the key for the Heroku app.

Instructions for setting up ClearDB can be found here: https://devcenter.heroku.com/articles/cleardb#provisioning-the-dedicated-mysql-add-on

In short, provisioning ClearDB will provide the `host`, `database`, `username` and `password` values needed to complete the MySQL configuration in `config/database.php`.

After filling in the necessary details in the `database.php` file, you can simply push to Heroku and it should take care of deploying the app! Note that of course, you wouldn't commit the changes to `database.php` to any public repository (like Github) but you'll have to include it in the Heroku build so the correct database configuration will be used.


 