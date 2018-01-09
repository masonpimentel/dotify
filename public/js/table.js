var songsHeader = '<th>Song Title</th>' +
    '<th>Artist</th>'+
    '<th>Album</th>' +
    '<th>Genre</th>' +
    '<th>Song Length</th>' +
    '<th>Add</th>';

var playlistHeader = '<th>Song Title</th>' +
    '<th>Artist</th>'+
    '<th>Album</th>' +
    '<th>Genre</th>' +
    '<th>Song Length</th>' +
    '<th>Rating</th>' +
    '<th>Remove</th>';

function setSongsHeader() {
    $("#songsHeader").html("<h4><b>Songs</b></h4>");
    $('#songsCol').html(songsHeader);
    $('#songsHeader').attr("colspan", "6");
}

function setPlaylistHeader() {
    $('#songsHeader').attr("colspan", "7");
    $('#songsCol').html(playlistHeader);
}

function songsView() {
    //clear table
    clearTable();
    //change to songs header
    setSongsHeader();
    ajaxRequest("GET", "/songs", "songs", null);
}

//won't scale well with recursion - would need a better algorithm
function sortTable() {
    var table = document.getElementById("songs_table");
    var rows = table.getElementsByTagName("TR");
    for (var i = 0; i < (rows.length - 1); i++) {
        var tn1 = rows[i].getElementsByTagName("TD")[0];
        var tn2 = rows[i + 1].getElementsByTagName("TD")[0];
        if (tn1.innerHTML.toLowerCase() > tn2.innerHTML.toLowerCase()) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            sortTable();
        }
    }
}

//clears songs table
function clearTable() {
    while (document.getElementById("songs_table").rows.length > 0) {
        document.getElementById("songs_table").deleteRow(0);
    }
}

//clears playlist table
function clearPlaylistTable() {
    while (document.getElementById("playlist_table").rows.length > 0) {
        document.getElementById("playlist_table").deleteRow(0);
    }
}

/* Inserts all songs
 *
 * Required
 * result
 *
 * Optional
 * action: "playlist" for inserting just a playlist
 * playlist: playlist name for playlist being inserted
 */
function insertSongs(result, action, playlist) {
    var element;
    var songsTable = document.getElementById("songs_table");
    for (var i = 0; i < result.length; i++) {
        element = result[i];

        var row = document.createElement("tr");
        var song = document.createElement("td");
        song.innerHTML = element.song_name;
        var artist = document.createElement("td");
        artist.innerHTML = element.artist_name;
        var genre = document.createElement("td");
        genre.innerHTML = element.genre_name;
        var songLength = document.createElement("td");
        songLength.innerHTML = element.song_length;

        var album = document.createElement("td");
        var albumName = element.album_name;
        var alA = document.createElement("a");
        alA.setAttribute("href", "javascript:findAlbumInfo('" + albumName + "')");
        alA.innerHTML = albumName;
        album.appendChild(alA);

        row.appendChild(song);
        row.appendChild(artist);
        row.appendChild(album);
        row.appendChild(genre);
        row.appendChild(songLength);

        var albumNameP = element.album_name;
        var artistP = element.artist_name;
        var songNameP = element.song_name;
        if (action == "playlist") {
            //rating
            var rating = document.createElement("td");
            var pid = element.pid;
            var a = document.createElement("a");
            var songName = element.song_name.replace(/ /g, "");
            a.id = pid + "-" + songName;
            a.setAttribute("href", "javascript:changeRating('" + pid + "-" + songName + "')");
            a.innerHTML = element.rating;
            rating.appendChild(a);
            row.appendChild(rating);
            //remove from playlist
            var remove = document.createElement("td");
            var removeButton= document.createElement("button");
            var args = '"' + playlist + '", "' + albumNameP + '", "' + artistP + '", "' + songNameP + '"';
            removeButton.setAttribute("onclick", "javascript:removeFromPlaylist(" + args + ")");
            removeButton.setAttribute("type", "text");
            removeButton.setAttribute("class", "btn btn-danger btn-sm");
            removeButton.innerHTML = "<span class='glyphicon glyphicon-remove'>";
            remove.appendChild(removeButton);
            row.appendChild(remove);
        }
        else {
            //main songs table - add to playlist
            var addPlaylist = document.createElement("td");
            var addButton= document.createElement("button");
            var argsA = '"' + albumNameP + '", "' + artistP + '", "' + songNameP + '"';
            //(album, artist, song)
            addButton.setAttribute("onclick", "javascript:addToPlaylist(" + argsA + ")");
            addButton.setAttribute("type", "text");
            addButton.setAttribute("class", "btn btn-success btn-sm");
            addButton.innerHTML = "<span class='glyphicon glyphicon-plus'>";
            addPlaylist.appendChild(addButton);
            row.appendChild(addPlaylist);
        }
        songsTable.appendChild(row);
    }
    sortTable();
}

function insertPlaylists(result) {
    var table = document.getElementById("playlist_table");
    for (var i = 0; i < result.length; i++) {
        var name = result[i].playlist_name;
        var row = document.createElement("tr");
        var td = document.createElement("td");
        var a = document.createElement("a");
        a.setAttribute("href", "javascript:displayPlaylist('" + name +"')");
        a.innerHTML = name;
        td.appendChild(a);
        row.appendChild(td);
        table.appendChild(row);
    }
}

function displayPlaylist(playlist) {
    $("#songsHeader").html('<h4><b>' + playlist + '<span>&nbsp</span><a href="javascript:songsView()">×</a></b></h4>');
    //clear table
    clearTable();
    //update to playlist header if it isn't already
    if (document.getElementById("mainTable").rows[1].cells.length <= 6) {
        setPlaylistHeader();
    }
    //insert playlist songs
    var req = {
        playlist: playlist
    };
    ajaxRequest("POST", "/songs/byplaylist", "insert_playlist", req, playlist);
}

/* Insert rating into the playlist table
 *
 * Required
 * ratingId: id of the rating element
 * rating: new rating value
 */
function insertRating(ratingId, rating) {
    document.getElementById(ratingId).innerHTML = rating;
}

/* Insert playlist into the playlist table
 *
 * Required
 * playlist: playlist name
 */
function insertPlaylist(playlist) {
    var table = document.getElementById("playlist_table");
    var row = document.createElement("tr");
    var td = document.createElement("td");
    var a = document.createElement("a");
    a.setAttribute("href", "javascript:displayPlaylist('" + playlist +"')");
    a.innerHTML = playlist;
    td.appendChild(a);
    row.appendChild(td);
    table.appendChild(row);
}

function removeFromPlaylist(playlist, album, artist, song) {
    var req = {
        username: lUname,
        playlist: playlist,
        album_name: album,
        artist: artist,
        song_name: song
    };
    ajaxRequest("POST", "/playlists/remove", "remove_from_playlist", req, playlist);
}