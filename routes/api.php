<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/APIinfo',function(){
    return "Welcome to CPSC304 API";
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