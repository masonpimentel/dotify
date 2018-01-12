<?php

use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('main');
});

Route::get('/APIinfo',function(){
    return "Welcome to Dotify API";
});

/*
 * Login routes
 */

Route::post('/login',function(){
    $username = Input::get('username');
    $password = Input::get('password');
    $result = DB::raw("SELECT * FROM users WHERE uname LIKE " . $username . " AND pass LIKE ".$password);
    if(!empty($result)){
        return true;
    }else {
        return false;
    }
});

Route::get('/users',function(){
    try {
        return Response::json(DB::select("SELECT * FROM user;"));
    } catch (Exception $e){
        return $e->getMessage();
    }
});

Route::get('/admins',function(){
    try {
        return Response::json(DB::select("SELECT * FROM admin;"));
    } catch (Exception $e){
        return $e->getMessage();
    }
});

Route::post('/password',function(){
    $username = Input::get('username');
    $password = Input::get('password');
    $result = DB::raw("SELECT * FROM users WHERE uname LIKE " . $username . " AND pass LIKE ".$password);
    if(!empty($result)){
        return true;
    }else {
        return false;
    }
});

/*
 * Song routes
 */

Route::get('/songs',function(){
    return Response::json(DB::select("SELECT * FROM song S, albumcontains A, albumcategory C WHERE S.sid = A.sid AND 
        A.album_name = C.album_name;"));
});

Route::get('/songs/bygenre',function(){
    $genre = Input::get('genre');
    return Response::json(DB::select("SELECT * FROM database_name.song where genre = ".$genre.";"));
});

Route::post('/songs/byplaylist',function(){
    $playlist = Input::get('playlist');
    return Response::json(DB::select("SELECT * FROM playlist P, playlistsongs L, song S, albumcontains A, albumcategory 
        C WHERE P.pid=L.pid AND L.sid=S.sid and S.sid = A.sid AND A.album_name = C.album_name and P.playlist_name LIKE 
        '" . $playlist . "';"));
});

Route::post('/songs/insert', function(){
    $album_name = Input::get('album_name');
    $artist = Input::get('artist');
    $album_year = Input::get('album_year');
    $genre = Input::get('genre');
    $song_name = Input::get('song_name');
    $track = Input::get('track');
    $song_length = Input::get('song_length');
    try{
        DB::select("INSERT INTO albumcategory VALUES ('".$album_name."', '".$artist."', '".$album_year."', '"
            .$genre."') ON duplicate KEY UPDATE album_name=album_name;");
        DB::select("INSERT INTO song (song_name, track_number, song_length) VALUES ('".$song_name."', ".$track.",
         ".$song_length.");");
        DB::select("set @last_sid = last_insert_id();");
        DB::select("INSERT INTO albumcontains VALUES ('".$album_name."', '".$artist."', @last_sid);");
    } catch (Exception $e){
        return $e->getMessage();
    }
    return "Success";
});

Route::post('/songs/delete/name', function(){
    $song_name = Input::get('song_name');
    try{
        DB::select("DELETE FROM song WHERE song_name LIKE '" . $song_name . "';");
    } catch (Exception $e){
        return $e->getMessage();
    }
    return "Success";
});

Route::post('/songs/delete/album', function(){
    $album_name = Input::get('album_name');
    $artist = Input::get('artist');
    try{
        DB::select("DELETE s, ac FROM song s INNER JOIN albumcontains ac ON s.sid=ac.sid WHERE album_name='"
            .$album_name."' AND artist_name='".$artist."';");
    } catch (Exception $e){
        return $e->getMessage();
    }
    return "Success";
});

Route::post('songs/search/name', function(){
    $song_name = Input::get('song_name');
    try{
        return DB::select("SELECT song_name, AC.artist_name, song_length, track_number, AC.album_name, genre_name  
          FROM song S, albumcontains AC, albumcategory A 
          WHERE S.sid = AC.sid AND AC.artist_name = A.artist_name and AC.album_name = A.album_name AND song_name 
          LIKE '%".$song_name."%';");
    } catch (Exception $e){
        return $e->getMessage();
    }
});

Route::post('songs/search/genre', function(){
    $genre = Input::get('genre');
    try{
        return DB::select("SELECT song_name, AC.artist_name, song_length, track_number, AC.album_name, genre_name 
          FROM song S, albumcontains AC, albumcategory A 
          WHERE S.sid = AC.sid AND AC.artist_name = A.artist_name AND AC.album_name = a.album_name AND 
          genre_name ='".$genre."';");
    } catch (Exception $e){
        return $e->getMessage();
    }
});

/*
 * Album routes
 */
Route::post('/album/length', function(){
    $album_name = Input::get('album_name');
    try{
        return DB::select("SELECT album_name, artist_name, SUM(song_length) 
          FROM albumcontains AC, song S 
          WHERE AC.sid = S.sid AND album_name = '". $album_name ."'
          GROUP BY album_name, artist_name;");
    } catch (Exception $e){
        return $e->getMessage();
    }
});

Route::post('/album/avgsonglength', function(){
    $album_name = Input::get('album_name');
    try{
        return DB::select("SELECT album_name, artist_name, AVG(song_length) 
            FROM albumcontains ac, song s 
            WHERE ac.sid = s.sid AND album_name = '". $album_name ."'
            GROUP BY album_name, artist_name;");
    } catch (Exception $e){
        return $e->getMessage();
    }
});

Route::post('/album/maxsonglength', function(){
    $album_name = Input::get('album_name');
    try{
        return DB::select("SELECT album_name, artist_name, MAX(song_length) 
            FROM albumcontains ac, song s 
            WHERE ac.sid = s.sid AND album_name = '". $album_name ."'
            GROUP BY album_name, artist_name;");
    } catch (Exception $e){
        return $e->getMessage();
    }
});

Route::post('/album/minsonglength', function(){
    $album_name = Input::get('album_name');
    try{
        return DB::select("SELECT album_name, artist_name, MIN(song_length) 
            FROM albumcontains ac, song s 
            WHERE ac.sid = s.sid AND album_name = '". $album_name ."'
            GROUP BY album_name, artist_name;");
    } catch (Exception $e){
        return $e->getMessage();
    }
});

Route::post('/album/numsongs', function(){
    $album_name = Input::get('album_name');
    try{
        return DB::select("SELECT album_name, artist_name, COUNT(s.sid) 
            FROM albumcontains ac, song s 
            WHERE ac.sid = s.sid AND album_name = '". $album_name ."'
            GROUP BY album_name, artist_name;");
    } catch (Exception $e){
        return $e->getMessage();
    }
});

Route::get('/album/stats/lownumsongs', function(){
    try{
        return DB::select("SELECT min(x.temp) AS 'Lowest Number of Songs in an Album' FROM
            (SELECT album_name, artist_name, count(s.sid) AS temp
            FROM albumcontains ac, song s 
            WHERE ac.sid = s.sid 
            GROUP BY album_name, artist_name) AS x;");
    } catch (Exception $e){
        return $e->getMessage();
    }
});

Route::get('/album/stats/highnumsongs', function(){
    try{
        return DB::select("SELECT max(x.temp) AS 'Highest Number of Songs in an Album' FROM
            (SELECT album_name, artist_name, count(s.sid) AS temp
            FROM albumcontains ac, song s 
            WHERE ac.sid = s.sid 
            GROUP BY album_name, artist_name) AS x;");
    } catch (Exception $e){
        return $e->getMessage();
    }
});

Route::get('/album/stats/avgnumsongs', function(){
    try{
        return DB::select("SELECT avg(x.temp) AS 'Average Number of Songs in an Album' FROM
            (SELECT album_name, artist_name, count(s.sid) AS temp
            FROM albumcontains ac, song s 
            WHERE ac.sid = s.sid 
            GROUP BY album_name, artist_name) AS x;");
    } catch (Exception $e){
        return $e->getMessage();
    }
});

Route::get('/album/stats/totnumsongs', function(){
    try{
        return DB::select("SELECT sum(x.temp) AS 'Total Number of Songs from all albums' FROM
            (SELECT album_name, artist_name, count(s.sid) AS temp
            FROM albumcontains ac, song s 
            WHERE ac.sid = s.sid 
            GROUP BY album_name, artist_name) AS x;");
    } catch (Exception $e){
        return $e->getMessage();
    }
});

Route::get('/album/stats/totnum', function(){
    try{
        return DB::select("SELECT count(x.temp) AS 'Total Number of albums' FROM
            (SELECT album_name, artist_name, count(s.sid) AS temp
            FROM albumcontains ac, song s 
            WHERE ac.sid = s.sid 
            GROUP BY album_name, artist_name) AS x;");
    } catch (Exception $e){
        return $e->getMessage();
    }
});

/*
 * Rating routes
 */
Route::post('/rating/update', function(){
    $pid = Input::get('pid');
    $song_name = Input::get('song_name');
    $rating = Input::get('rating');
    try{
        DB::select("UPDATE playlistsongs INNER JOIN song ON song.sid = playlistsongs.sid 
        SET rating = '". $rating ."' 
        WHERE song_name= '". $song_name ."' AND pid = '". $pid ."';");
    } catch (Exception $e){
        return $e->getMessage();
    }
    return "Success";
});

/*
 * Playlist routes
 */
Route::post('/playlists', function(){
    $username = Input::get('username');
    try {
        return Response::json(DB::select("SELECT * FROM userplaylists U, playlist P WHERE U.pid = P.pid AND U.uname 
          LIKE '" . $username . "';"));
    } catch (Exception $e){
        return $e->getMessage();
    }
});

Route::get('/playlists/stats', function(){
    try{
        return DB::select("SELECT p.playlist_name
            FROM playlistsongs ps, playlist p
            WHERE p.pid = ps.pid
            GROUP BY p.playlist_name
            HAVING count(ps.sid) = (SELECT count(s.sid) FROM song s);");
    } catch (Exception $e){
        return $e->getMessage();
    }
});

Route::post('/playlists/create', function(){
    $username = Input::get('username');
    $playlist = Input::get('playlist');
    try {
        DB::select("INSERT INTO playlist (playlist_name) VALUES ('" . $playlist . "');");
        DB::select("SET @last_sid = last_insert_id();");
        DB::select("INSERT INTO userplaylists VALUES ('" . $username . "', @last_sid);");
    } catch (Exception $e){
        return $e->getMessage();
    }
    return "Success";
});

Route::post('/playlists/insert', function(){
    $username = Input::get('username');
    $playlist = Input::get('playlist');
    $album_name = Input::get('album_name');
    $artist = Input::get('artist');
    $song_name = Input::get('song_name');
    $rating = Input::get('rating');
    try {
        DB::select("SET @sid_temp = (
            SELECT s.sid 
            FROM song s, albumcontains ac 
            WHERE s.song_name='" . $song_name . "' AND ac.artist_name='" . $artist . "' AND ac.album_name='" . $album_name . "' AND ac.sid=s.sid);");
        DB::select("SET @pid_temp = (
            SELECT p.pid
            FROM playlist p, userplaylists up
            WHERE p.pid=up.pid and p.playlist_name='" . $playlist . "' AND up.uname='". $username ."');");
        DB::select("INSERT INTO playlistsongs VALUES (curdate(), @sid_temp, @pid_temp, '". $rating ."');");
    } catch (Exception $e){
        return $e->getMessage();
    }
    return "Success";
});

Route::post('/playlists/remove', function(){
    $username = Input::get('username');
    $playlist = Input::get('playlist');
    $album_name = Input::get('album_name');
    $artist = Input::get('artist');
    $song_name = Input::get('song_name');
    try {
        DB::select("SET @sid_temp = (
            SELECT s.sid 
            FROM song s, albumcontains ac 
            WHERE s.song_name='" . $song_name . "' AND ac.artist_name='" . $artist . "' AND ac.album_name='" . $album_name . "' AND ac.sid=s.sid);");
        DB::select("SET @pid_temp = (
            SELECT p.pid
            FROM playlist p, userplaylists up
            WHERE p.pid=up.pid and p.playlist_name='" . $playlist . "' AND up.uname='". $username ."');");
        DB::select("DELETE FROM playlistsongs WHERE playlistsongs.pid=@pid_temp AND playlistsongs.sid=@sid_temp;");
    } catch (Exception $e){
        return $e->getMessage();
    }
    return "Success";
});

Route::post('/restore', function(){
    $res = DB::connection()->getPdo()->exec('drop database if exists heroku_6f66a796c5d0830');
    if ($res != 11) {
        return "Error: database not dropped";
    }
    $res = DB::connection()->getPdo()->exec('create database heroku_6f66a796c5d0830');
    if ($res != 1) {
        return "Error: could not create DB";
    }
    $res = DB::connection()->getPdo()->exec('use heroku_6f66a796c5d0830');
    if ($res != 0) {
        return "Error: issue with use statement";
    }
    try {
        DB::select('create table song  ( sid int not null auto_increment, song_name varchar(50) not null, track_number int not null, song_length float(5,2) not null, primary key (sid)) auto_increment=1000');
        DB::select('create table genre ( genre_name varchar(30) not null, primary key (genre_name))');
        DB::select('create table user ( uname varchar(20) not null, pass varchar(30) not null, first_name varchar(30) null, last_name varchar(40) null, age int null, email varchar(30) null, primary key (uname) )');
        DB::select('create table playlist ( pid int not null auto_increment, playlist_name varchar(40), primary key (pid)) auto_increment=5000');
        DB::select('create table admin ( uname varchar(20) not null, title varchar(20) not null, primary key (title, uname), foreign key (uname) references user (uname) on delete cascade)');
        DB::select('create table albumcategory ( album_name varchar(30) not null, artist_name varchar(20) not null, year int null, genre_name varchar(30) not null, primary key (album_name, artist_name), foreign key (genre_name) references genre (genre_name) on delete no action)');
        DB::select('create table albumcontains ( album_name varchar(30) not null, artist_name varchar(20) not null, sid int not null, primary key (album_name, artist_name, sid), foreign key (sid) references song (sid) on delete cascade on update cascade, foreign key (album_name, artist_name) references albumcategory (album_name, artist_name) on delete cascade on update cascade)');
        DB::select('create table privilege ( description varchar(50) not null, task_importance int not null, primary key (description))');
        DB::select('create table adminprivileges ( title varchar(20) not null, description varchar(50) not null, primary key (title, description), foreign key (title) references admin (title) on delete no action, foreign key (description) references privilege (description) on delete no action)');
        DB::select('create table userplaylists ( uname varchar(20) not null, pid int not null, primary key (uname, pid), foreign key (uname) references user (uname) on delete cascade on update cascade, foreign key (pid) references playlist (pid) on delete cascade on update cascade)');
        DB::select('create table playlistsongs ( added_date char(10) not null, sid int not null, pid int not null,  rating int null, primary key (sid, pid), foreign key (sid) references song (sid) on delete cascade on update cascade, foreign key (pid) references playlist (pid) on delete cascade on update cascade, check (rating <= 5 and rating >= 0))');
        DB::select('insert into genre (genre_name) values ("Hip-Hop")');
        DB::select('insert into genre (genre_name) values ("Metal")');
        DB::select('insert into genre (genre_name) values ("Rock")');
        DB::select('insert into genre (genre_name) values ("Pop")');
        DB::select('insert into genre (genre_name) values ("Indie")');
        DB::select('insert into genre (genre_name) values ("Future House")');
        DB::select('insert into playlist values (5000, "All Songs")');
        DB::select('insert into playlist values (5001, "Rock and Roll")');
        DB::select('insert into playlist values (5002, "Fave Road Tunes")');
        DB::select('insert into playlist values (5003, "Future House")');
        DB::select('insert into user values ("FantasyBuddy", 123456, "Marketta", "Timm", 42, null)');
        DB::select('insert into user values ("SnXfZ947", "abcd123", "Ayy", "Lmao", 23, null)');
        DB::select('insert into user values ("Sailor Bacon", 112200, "Rigoberta", "Nuckles", 25, "rn180@gmail.com")');
        DB::select('insert into user values ("Porchwork", 123333, null, null, null, "pw@yahoo.com")');
        DB::select('insert into user values ("MafiaPride", "mafia123", "Stewart", "Delucca", 21, "mp124@yahoo.com")');
        DB::select('insert into admin values ("SnXfZ947", "Owner")');
        DB::select('insert into admin values ("Sailor Bacon", "Administrator")');
        DB::select('insert into privilege values ("Remove Users", 4)');
        DB::select('insert into privilege values ("Modify Library", 2)');
        DB::select('insert into privilege values ("Access Source Code", 1)');
        DB::select('insert into adminprivileges values ("Owner", "Remove Users")');
        DB::select('insert into adminprivileges values ("Owner", "Modify Library")');
        DB::select('insert into adminprivileges values ("Owner", "Access Source Code")');
        DB::select('insert into adminprivileges values ("Administrator", "Modify Library")');
        DB::select('insert into userplaylists values ("Sailor Bacon", 5000)');
        DB::select('insert into userplaylists values ("Porchwork", 5001)');
        DB::select('insert into userplaylists values ("MafiaPride", 5002)');
        DB::select('insert into userplaylists values ("SnXfZ947", 5003)');
        DB::select('insert into albumcategory values ("To Pimp a Butterfly", "Kendrick Lamar", 2015, "Hip-Hop")');
        DB::select('insert into song values (1000, "Wesleys Theory", 1, 4.47)');
        DB::select('insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1000)');
        DB::select('insert into song values (1001, "For Free? (Interlude)", 2, 2.1)');
        DB::select('insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1001)');
        DB::select('insert into song values (1002, "King Kunta", 3, 3.54)');
        DB::select('insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1002)');
        DB::select('insert into song values (1003, "Institutionalized", 4, 4.31)');
        DB::select('insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1003)');
        DB::select('insert into song values (1004, "These Walls", 5, 5.0)');
        DB::select('insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1004)');
        DB::select('insert into song values (1005, "U", 6, 4.28)');
        DB::select('insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1005)');
        DB::select('insert into song values (1006, "Alright", 7, 3.39)');
        DB::select('insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1006)');
        DB::select('insert into song values (1007, "For Sale? (Interlude)", 8, 4.51)');
        DB::select('insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1007)');
        DB::select('insert into song values (1008, "Momma", 9, 4.43)');
        DB::select('insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1008)');
        DB::select('insert into song values (1009, "Hood Politics", 10, 4.52)');
        DB::select('insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1009)');
        DB::select('insert into song values (1010, "How Much a Dollar Cost", 11, 4.21)');
        DB::select('insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1010)');
        DB::select('insert into song values (1011, "Complexion (A Zulu Love)", 12, 4.23)');
        DB::select('insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1011)');
        DB::select('insert into song values (1012, "The Blacker the Berry", 13, 5.28)');
        DB::select('insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1012)');
        DB::select('insert into song values (1013, "You Aint Gotta Lie (Momma Said)", 14, 4.01)');
        DB::select('insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1013)');
        DB::select('insert into song values (1014, "I", 15, 5.36)');
        DB::select('insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1014)');
        DB::select('insert into song values (1015, "Mortal Man", 16, 12.07)');
        DB::select('insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1015)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1000, 5000, 0)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1001, 5000, 3)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1002, 5000, 1)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1003, 5000, 2)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1004, 5000, 4)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1005, 5000, 4)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1006, 5000, 5)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1007, 5000, 5)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1008, 5000, 1)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1009, 5000, 1)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1010, 5000, 1)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1011, 5000, 2)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1012, 5000, 1)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1013, 5000, 2)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1014, 5000, 1)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1015, 5000, 0)');
        DB::select('insert into albumcategory values ("Ride the Lightning", "Metallica", 1984, "Metal")');
        DB::select('insert into song values (1016, "Fight Fire With Fire", 1, 4.44)');
        DB::select('insert into albumcontains values ("Ride the Lightning", "Metallica", 1016)');
        DB::select('insert into song values (1017, "Ride the Lightning", 2, 6.36)');
        DB::select('insert into albumcontains values ("Ride the Lightning", "Metallica", 1017)');
        DB::select('insert into song values (1018, "For Whom the Bell Tolls", 3, 5.1)');
        DB::select('insert into albumcontains values ("Ride the Lightning", "Metallica", 1018)');
        DB::select('insert into song values (1019, "Fade to Black", 4, 6.56)');
        DB::select('insert into albumcontains values ("Ride the Lightning", "Metallica", 1019)');
        DB::select('insert into song values (1020, "Trapped Under Ice", 5, 4.03)');
        DB::select('insert into albumcontains values ("Ride the Lightning", "Metallica", 1020)');
        DB::select('insert into song values (1021, "Escape", 6, 4.23)');
        DB::select('insert into albumcontains values ("Ride the Lightning", "Metallica", 1021)');
        DB::select('insert into song values (1022, "Creeping Death", 7, 6.36)');
        DB::select('insert into albumcontains values ("Ride the Lightning", "Metallica", 1022)');
        DB::select('insert into song values (1023, "The Call of Ktulu", 8, 8.52)');
        DB::select('insert into albumcontains values ("Ride the Lightning", "Metallica", 1023)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1016, 5000, 3)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1017, 5000, 4)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1018, 5000, 3)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1019, 5000, 5)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1020, 5000, 3)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1021, 5000, 5)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1022, 5000, 0)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1023, 5000, 0)');
        DB::select('insert into albumcategory values ("Dark Side of the Moon", "Pink Floyd", 1973, "Rock")');
        DB::select('insert into song values (1024, "Speak to Me", 1, 1.13)');
        DB::select('insert into albumcontains values ("Dark Side of the Moon", "Pink Floyd", 1024)');
        DB::select('insert into song values (1025, "Breathe", 2, 2.46)');
        DB::select('insert into albumcontains values ("Dark Side of the Moon", "Pink Floyd", 1025)');
        DB::select('insert into song values (1026, "On the Run", 3, 3.34)');
        DB::select('insert into albumcontains values ("Dark Side of the Moon", "Pink Floyd", 1026)');
        DB::select('insert into song values (1027, "Time", 4, 7.04)');
        DB::select('insert into albumcontains values ("Dark Side of the Moon", "Pink Floyd", 1027)');
        DB::select('insert into song values (1028, "The Great Gig in the Sky", 5, 4.44)');
        DB::select('insert into albumcontains values ("Dark Side of the Moon", "Pink Floyd", 1028)');
        DB::select('insert into song values (1029, "Money", 6, 6.32)');
        DB::select('insert into albumcontains values ("Dark Side of the Moon", "Pink Floyd", 1029)');
        DB::select('insert into song values (1030, "Us and Them", 7, 7.4)');
        DB::select('insert into albumcontains values ("Dark Side of the Moon", "Pink Floyd", 1030)');
        DB::select('insert into song values (1031, "Any Colour You Like", 8, 3.25)');
        DB::select('insert into albumcontains values ("Dark Side of the Moon", "Pink Floyd", 1031)');
        DB::select('insert into song values (1032, "Brain Damage", 9, 3.5)');
        DB::select('insert into albumcontains values ("Dark Side of the Moon", "Pink Floyd", 1032)');
        DB::select('insert into song values (1033, "Eclipse", 10, 2.02)');
        DB::select('insert into albumcontains values ("Dark Side of the Moon", "Pink Floyd", 1033)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1024, 5000, 1)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1025, 5000, 3)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1026, 5000, 3)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1027, 5000, 4)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1028, 5000, 3)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1029, 5000, 3)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1030, 5000, 3)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1031, 5000, 5)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1032, 5000, 3)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1033, 5000, 0)');
        DB::select('insert into albumcategory values ("1989", "Taylor Swift", 2014, "Pop")');
        DB::select('insert into song values (1034, "Welcome to New York", 1, 3.32)');
        DB::select('insert into albumcontains values ("1989", "Taylor Swift", 1034)');
        DB::select('insert into song values (1035, "Blank Space", 2, 3.51)');
        DB::select('insert into albumcontains values ("1989", "Taylor Swift", 1035)');
        DB::select('insert into song values (1036, "Style", 3, 3.5)');
        DB::select('insert into albumcontains values ("1989", "Taylor Swift", 1036)');
        DB::select('insert into song values (1037, "Out of the Woods", 4, 3.55)');
        DB::select('insert into albumcontains values ("1989", "Taylor Swift", 1037)');
        DB::select('insert into song values (1038, "All You Had to Do Was Stay", 5, 3.13)');
        DB::select('insert into albumcontains values ("1989", "Taylor Swift", 1038)');
        DB::select('insert into song values (1039, "Shake It Off", 6, 3.39)');
        DB::select('insert into albumcontains values ("1989", "Taylor Swift", 1039)');
        DB::select('insert into song values (1040, "I Wish You Would", 7, 3.27)');
        DB::select('insert into albumcontains values ("1989", "Taylor Swift", 1040)');
        DB::select('insert into song values (1041, "Bad Blood", 8, 3.31)');
        DB::select('insert into albumcontains values ("1989", "Taylor Swift", 1041)');
        DB::select('insert into song values (1042, "Wildest Dreams", 9, 3.4)');
        DB::select('insert into albumcontains values ("1989", "Taylor Swift", 1042)');
        DB::select('insert into song values (1043, "How You Get the Girl", 10, 4.07)');
        DB::select('insert into albumcontains values ("1989", "Taylor Swift", 1043)');
        DB::select('insert into song values (1044, "This Love", 11, 4.1)');
        DB::select('insert into albumcontains values ("1989", "Taylor Swift", 1044)');
        DB::select('insert into song values (1045, "I Know Places", 12, 3.15)');
        DB::select('insert into albumcontains values ("1989", "Taylor Swift", 1045)');
        DB::select('insert into song values (1046, "Clean", 13, 4.3)');
        DB::select('insert into albumcontains values ("1989", "Taylor Swift", 1046)');
        DB::select('insert into albumcategory values ("2016 Mix", "Oliver Heldens", 2016, "Future House")');
        DB::select('insert into song values (1047, "Waiting", 1, 3.23)');
        DB::select('insert into albumcontains values ("2016 Mix", "Oliver Heldens", 1047)');
        DB::select('insert into song values (1048, "You Know", 2, 2.37)');
        DB::select('insert into albumcontains values ("2016 Mix", "Oliver Heldens", 1048)');
        DB::select('insert into song values (1049, "Bunnydance", 3, 4.08)');
        DB::select('insert into albumcontains values ("2016 Mix", "Oliver Heldens", 1049)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1034, 5000, 1)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1035, 5000, 0)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1036, 5000, 0)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1037, 5000, 5)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1038, 5000, 0)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1039, 5000, 1)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1040, 5000, 5)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1041, 5000, 2)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1042, 5000, 5)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1043, 5000, 2)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1044, 5000, 4)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1045, 5000, 3)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1046, 5000, 3)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1047, 5000, 3)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1048, 5000, 2)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1049, 5000, 3)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1024, 5001, 5)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1025, 5001, 5)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1026, 5001, 5)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1027, 5001, 5)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1028, 5001, 5)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1029, 5001, 5)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1030, 5001, 5)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1031, 5001, 5)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1032, 5001, 5)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1033, 5001, 5)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1028, 5002, 1)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1034, 5002, 3)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1035, 5002, 3)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1016, 5002, 4)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1006, 5002, 3)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1004, 5002, 3)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1047, 5003, 5)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1048, 5003, 5)');
        DB::select('insert into playlistsongs values ("2017-03-16", 1049, 5003, 4)');
    } catch (Exception $e) {
        return $e->getMessage();
    }
    return "Success";
});
