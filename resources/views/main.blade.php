<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="js/libraries/jquery.min.js" type="text/javascript"></script>
    <script src="js/libraries/bootstrap.min.js" type="text/javascript"></script>
    <script src="js/libraries/bootstrap-notify.min.js" type="text/javascript"></script>
    <script src="js/libraries/bootstrap-waitingfor.min.js" type="text/javascript"></script>
    <script src="js/libraries/loader.js" type="text/javascript"></script>
    <script src="js/main.js" type="text/javascript"></script>
    <script src="js/modals.js" type="text/javascript"></script>
    <script src="js/login.js" type="text/javascript"></script>
    <script src="js/search.js" type="text/javascript"></script>
    <script src="js/table.js" type="text/javascript"></script>
    <script src="js/request.js" type="text/javascript"></script>
    <script src="js/notification.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="css/libraries/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/libraries/animate.min.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link rel="shortcut icon" type="image/png" href="images/logo.png">
    <title>Dotify</title>
</head>
<body>
<div class="container custom">

    <div class="row">
        <div class="header">
            <img src="images/logo.png" alt="logo" height="75" width="75">
            <h1>Dotify</h1>
        </div>
    </div>

    <div class="row bmargin">
        <div class="col-md-2">
            Search by song name:
        </div>
        <div class="col-md-2">
            <input type="text" id="songSearch" onkeypress="handler(event, 'songsearch')">
        </div>
        <div class="col-md-2">
            <button type="text" class="btn btn-primary" onclick="searchSongsNameGo()">Search</button>
        </div>
    </div>
    <div class="row bmargin">
        <div class="col-md-2">
            Search by genre:
        </div>
        <div class="col-md-2">
            <input type="text" id="genreSearch" onkeypress="handler(event, 'genresearch')">
        </div>
        <div class="col-md-2">
            <button type="text" class="btn btn-primary" onclick="searchSongsGenreGo()">Search</button>
        </div>
    </div>
    <div class="row bmargin">
        <div class="col-md-4">
            Empty string clears searches
        </div>
    </div>


    <div class="row bmargin admin">
        <h4>
            Admin controls:
        </h4>
        <button type="text" class="btn btn-success" onclick="addSong()">Add Song</button>
        <button type="text" class="btn btn-danger" onclick="removeSongs()">Remove Songs by Song</button>
        <button type="text" class="btn btn-danger" onclick="removeSongsAlbum()">Remove Songs by Album</button>
        <h4>
            Library statistics:
        </h4>
        <button type="text" class="btn btn-primary" onclick="albumStats()">Album Stats</button>
        <button type="text" class="btn btn-primary" onclick="playlistStats()">Playlist Stats</button>
    </div>

    <div class="row">
        <div id="playlists" class="col-md-4">
            <table class = "table">
                <thead>
                <!--th>
                    Playlists <button type="text" class="btn btn-primary btn-sm" onclick="addPlaylist()"><span class="glyphicon glyphicon-plus"></span></button>
                </th-->
                <th>
                    Playlists <button type="text" class="btn btn-primary btn-sm" onclick="addPlaylist()"><span class="glyphicon glyphicon-plus"></span></button>
                </th>
                </thead>
                <tbody id="playlist_table">

                </tbody>
            </table>
        </div>
        <div id="songs" class="col-md-8">
            <table class = "table" id="mainTable">
                <thead>
                <th colspan="6" id="songsHeader"><h4><b>Songs</b></h4></th>
                <tr id="songsCol">
                    <th>
                        Song Title
                    </th>
                    <th>
                        Artist
                    </th>
                    <th>
                        Album
                    </th>
                    <th>
                        Genre
                    </th>
                    <th>
                        Song Length
                    </th>
                    <th>
                        Add
                    </th>
                </tr>
                </thead>
                <tbody id="songs_table">

                </tbody>
            </table>
        </div>
    </div>

</div>

<div class="modal fade" id="login">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Login</h4>
            </div>
            <div class="modal-body">
                Username: <input type="text" id="username" onkeypress="handler(event, 'login')">
                Password: <input type="password" id="password" onkeypress="handler(event, 'login')">
            </div>
            <div class="modal-footer">
                <button type="text" class="btn btn-primary" onclick="login()">Login</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="loginWithCredentials">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Login</h4>
            </div>
            <div class="modal-body">
                <button type="text" class="btn btn-primary" onclick="loginWithCredentials('Porchwork', '123333')">Login as standard user</button>
                <button type="text" class="btn btn-success" onclick="loginWithCredentials('SnXfZ947', 'abcd123')">Login as administrator</button>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addSongDisabled">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Restore DB</h4>
            </div>
            <div class="modal-body">
                This used to allow an admin to add an individual song, but... I decided to change this to simply restore the DB to its default values.<br><br>
                Restoring the DB will cause the application to reload.
            </div>
            <div class="modal-footer">
                <button type="text" class="btn btn-success" onclick="restoreDB()">Restore DB</button>
                <button type="text" class="btn btn-danger" onclick="restoreDBClose()">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addSong">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add song</h4>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tbody>
                    <tr>
                        <td class="addModal">
                            Album name:
                        </td>
                        <td>
                            <input type="text" id="albumName" onkeypress="handler(event, 'songs')">
                        </td>
                    </tr>
                    <tr>
                        <td class="addModal">
                            Album genre:
                        </td>
                        <td>
                            <input type="text" id="albumGenre" onkeypress="handler(event, 'songs')">
                        </td>
                    </tr>
                    <tr>
                        <td class="addModal">
                            Song name:
                        </td>
                        <td>
                            <input type="text" id="songName" onkeypress="handler(event, 'songs')">
                        </td>
                    </tr>
                    <!--tr>
                        <td class="addModal">
                            Track number:
                        </td>
                        <td>
                            <input type="text" id="trackNumber" onkeypress="handler(event, 'songs')">
                        </td>
                    </tr-->
                    <tr>
                        <td class="addModal">
                            Song length:
                        </td>
                        <td>
                            <input type="text" id="songLength" onkeypress="handler(event, 'songs')">
                        </td>
                    </tr>
                    <tr>
                        <td class="addModal">
                            Artist name:
                        </td>
                        <td>
                            <input type="text" id="artistName" onkeypress="handler(event, 'songs')">
                        </td>
                    </tr>
                    <tr>
                        <td class="addModal">
                            Album year:
                        </td>
                        <td>
                            <input type="text" id="albumYear" onkeypress="handler(event, 'songs')">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="text" class="btn btn-success" onclick="addSongGo()">Add song</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="removeSongs">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Remove songs by song name</h4>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tbody>
                    <tr>
                        <td class="addModal">
                            Song name:
                        </td>
                        <td>
                            <input type="text" id="songNameDelete" onkeypress="handler(event, 'removeViaSong')">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="text" class="btn btn-danger" onclick="removeSongsGo()">Remove song</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="removeSongsAlbum">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Remove songs by album name and artist</h4>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tbody>
                    <tr>
                        <td class="addModal">
                            Album name:
                        </td>
                        <td>
                            <input type="text" id="albumNameDelete" onkeypress="handler(event, 'removeAlbum')">
                        </td>
                    </tr>
                    <tr>
                        <td class="addModal">
                            Album artist:
                        </td>
                        <td>
                            <input type="text" id="albumArtistDelete" onkeypress="handler(event, 'removeAlbum')">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="text" class="btn btn-danger" onclick="removeSongsAlbumGo()">Remove songs</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addPlaylist">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add playlist</h4>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tbody>
                    <tr>
                        <td class="addModal">
                            Playlist name:
                        </td>
                        <td>
                            <input type="text" id="playlistName" onkeypress="handler(event, 'playlist')">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="text" class="btn btn-primary" onclick="addPlaylistGo()">Create</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addPlaylistDisabled">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add playlist</h4>
            </div>
            <div class="modal-body">
                Disabled, but please feel free to add songs to the current playlist!
            </div>
            <div class="modal-footer">
                <button type="text" class="btn btn-danger" onclick="addPlaylistDisabled()">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="changeRating">
    <div class="modal-dialog">
        <div class="modal-content" id="changeRatingGo">
            <div class="modal-header">
                <h4 class="modal-title">Change rating</h4>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tbody>
                    <tr>
                        <td class="addModal">
                            New rating:
                        </td>
                        <td>
                            <input type="text" id="newRating" onkeypress="handler(event, 'rating', 'rating2Id')">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer" id="oldRatingGo">
                <button type="text" class="btn btn-success" onclick="changeRatingGo('ratingId')">Change rating</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="albumInfo">

</div>

<div class="modal fade" id="playlistStats">

</div>

<div class="modal fade" id="albumStats">

</div>

<div class="modal fade" id="addToPlaylist">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add to playlist</h4>
            </div>
            <div class="modal-body">
                Rating: <input type="text" value="3" id="playlistRating"><br><br>
                <b>Playlist:</b><br><table class="table table-borderless">
                    <tbody id="select_playlist">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>

</body>
</html>