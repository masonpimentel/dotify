function createAlbumInfoModal(arg1, arg2, arg3, arg4, arg5, name) {
    return '<div class="modal-dialog"><div class="modal-content"><div class="modal-header">' +
        '<h4 class="modal-title">' + name + '</h4></div><div class="modal-body"><table class="table table-borderless">' +
        '<tbody> Album length: ' + arg1 + '<br></tbody><tbody> Average song length: ' + arg2 + '<br></tbody><tbody> Max song length: ' +
        arg3 + '<br></tbody><tbody> Min song length: ' + arg4 + '<br></tbody><tbody> Total songs: ' + arg5 + '</tbody></table>' +
        '</div><div class="modal-footer"><button type="text" class="btn btn-success" onclick="findAlbumInfoGo()">Close' +
        '</button></div></div></div>'
}

function createAlbumStatsModal(arg1, arg2, arg3, arg4, arg5) {
    return '<div class="modal-dialog"><div class="modal-content"><div class="modal-header">' +
        '<h4 class="modal-title"> Album stats: </h4></div><div class="modal-body"><table class="table table-borderless">' +
        '<tbody> Lowest number of songs in an album: ' + arg1 + '<br></tbody><tbody> Highest number of songs in an album: ' + arg2 + '<br></tbody><tbody> Average number of songs in an album: ' +
        arg3 + '<br></tbody><tbody> Total number of songs from all albums: ' + arg4 + '<br></tbody><tbody> Total number of albums: ' + arg5 + '</tbody></table>' +
        '</div><div class="modal-footer"><button type="text" class="btn btn-success" onclick="albumStatsGo()">Close' +
        '</button></div></div></div>'
}

function createPlaylistModal(value) {
    return '<div class="modal-dialog"><div class="modal-content"><div class="modal-header">' +
        '<h4 class="modal-title">Playlist stats</h4></div><div class="modal-body"><table class="table table-borderless">' +
        '<tbody>Playlists that contain all songs in the library: ' + value + '</tbody>' +
        '</table></div><div class="modal-footer"><button type="text" class="btn btn-success" onclick="playlistStatsGo()">' +
        'Close</button></div></div></div>';
}

function createAddToPlaylistModal(result, album, artist, song) {
    var links = "";
    var args;
    var playlist;
    for (var i = 0; i < result.length; i++) {
        playlist = result[i].playlist_name;
        args = "'" + playlist + "', '" + album + "', '" + artist + "', '" + song + "'";
        links = links + '<tr><td class="playlistAdd"><a href="javascript:addToPlaylistGo(' + args + ')">' + playlist + '</a></td></tr>';
    }
    $('#addToPlaylist').removeData().on('shown.bs.modal', function() {
        var input = $('#playlistRating');
        input.focus();
        input[0].setSelectionRange(1,1);
        $('#select_playlist').html(links);
    }).modal("show");
}

function addSong() {
    if (disableAdd) {
        $("#addSongDisabled").modal("show");
    }
    else {
        $("#addSong").modal("show");
    }

}

function addSongGo() {
    $("#addSong").modal("hide");
    var songName = document.getElementById("songName").value;
    var albumName = document.getElementById("albumName").value;
    var albumGenre = document.getElementById("albumGenre").value;
    var trackNumber = 1;
    var songLength = document.getElementById("songLength").value;
    var artistName = document.getElementById("artistName").value;
    var albumYear = document.getElementById("albumYear").value;
    var req = {
        album_name: albumName,
        artist: artistName,
        album_year: albumYear,
        genre: albumGenre,
        song_name: songName,
        track: trackNumber,
        song_length: songLength
    };
    document.getElementById("songName").value = "";
    document.getElementById("albumName").value = "";
    document.getElementById("albumGenre").value = "";
    document.getElementById("songLength").value = "";
    document.getElementById("artistName").value = "";
    document.getElementById("albumYear").value = "";
    ajaxRequest("POST", "/songs/insert", "insert_song", req);
}

function restoreDB() {
    $("#addSongDisabled").modal("hide");
    ajaxRequest("POST", "/restore", "restore");
}

function restoreDBClose() {
    $("#addSongDisabled").modal("hide");
}

function removeSongs() {
    $("#removeSongs").modal("show").on('shown.bs.modal', function() {
        $('#songNameDelete').focus();
    });
}

function removeSongsGo() {
    var songNameDelete = document.getElementById("songNameDelete");
    var req = {
        song_name: songNameDelete.value
    };
    songNameDelete.value = '';
    $("#removeSongs").modal("hide");
    ajaxRequest("POST", "/songs/delete/name", "delete_song", req);
}

function removeSongsAlbum() {
    $("#removeSongsAlbum").modal("show").on('shown.bs.modal', function() {
        $('#albumNameDelete').focus();
    });
}

function removeSongsAlbumGo() {
    var albumNameDelete = document.getElementById("albumNameDelete");
    var albumArtistDelete = document.getElementById("albumArtistDelete");
    var req = {
        album_name: albumNameDelete.value,
        artist: albumArtistDelete.value
    };
    albumNameDelete.value = '';
    albumArtistDelete.value = '';
    $("#removeSongsAlbum").modal("hide");
    ajaxRequest("POST", "/songs/delete/album", "delete_song_album", req);
}

/* Change rating
 *
 * Required
 * ratingId: id of rating element
 */
function changeRating(ratingId) {
    var changeRating = document.getElementById("changeRating").innerHTML;
    var oldRating = changeRating.split("'")[5];
    var oldRating2 = changeRating.split("'")[3];
    var oldElement = document.getElementById("changeRating").innerHTML;
    var newElement = oldElement.replace(oldRating, ratingId);
    newElement = newElement.replace(oldRating2, ratingId);
    document.getElementById("changeRating").innerHTML = newElement;
    console.log("newRating: " + newElement);
    $("#changeRating").modal("show").on('shown.bs.modal', function() {
        $('#newRating').focus();
    });
}

/* Insert new rating
 *
 * Required
 * ratingId: "pid"-"song_name"
 */
function changeRatingGo(ratingId) {
    var newRating = document.getElementById("newRating").value;
    if (newRating > MAX_RATING || newRating < 0) {
        $("#changeRating").modal("hide");
        errorMessage("Please enter a rating > 0 and <= " + MAX_RATING);
        return;
    }
    document.getElementById("newRating").value = "";
    var pid = ratingId.split("-")[0];
    var songName = ratingId.split("-")[1];
    console.log("ratingId: " + ratingId);
    var req = {
        rating: newRating,
        song_name: songName,
        pid: pid
    };
    $("#changeRating").modal("hide");
    ajaxRequest("POST", "/rating/update", "update_rating", req, ratingId, newRating);
}

function addPlaylist() {
    if (disableAdd) {
        $("#addPlaylistDisabled").modal("show");
    }
    else {
        $("#addPlaylist").modal("show").on('shown.bs.modal', function() {
            $('#playlistName').focus();
        });
    }
}

function addPlaylistGo() {
    var playlist = document.getElementById("playlistName").value;
    document.getElementById("playlistName").value = "";
    var req = {
        playlist: playlist,
        username: lUname
    };
    $("#addPlaylist").modal("hide");
    ajaxRequest("POST", "/playlists/create", "add_playlist", req, playlist);
}

function addPlaylistDisabled() {
    $("#addPlaylistDisabled").modal("hide");
}

function addToPlaylist(album, artist, song) {
    var req = {
        username: lUname
    };
    $('#select_playlist').html(loader);
    ajaxRequest("POST", "/playlists", "playlist_modal", req, album, artist, song);
}

function addToPlaylistGo(playlist, album, artist, song) {
    var rating = document.getElementById("playlistRating").value;
    var req = {
        username: lUname,
        playlist: playlist,
        album_name: album,
        artist: artist,
        song_name: song,
        rating: rating
    };
    $("#addToPlaylist").modal("hide");
    ajaxRequest("POST", "/playlists/insert", "insert_into_playlist", req, playlist);
}

function playlistStats() {
    ajaxRequest("GET", "/playlists/stats", "playlist_stats", "dummy");
}

function playlistStatsSet(result) {
    var newValue = "";
    if (!result[0]) {
        newValue = "none";
    }
    else {
        newValue = result[0].playlist_name;
    }
    for (var i = 1; i < result.length; i++) {
        newValue = newValue + ", " + result[i].playlist_name;
    }
    var modalValue = createPlaylistModal(newValue);
    $('#playlistStats').on('show.bs.modal', function(){
        $('#playlistStats').html(modalValue);
    }).modal("show");
}

function playlistStatsGo() {
    $("#playlistStats").modal("hide");
}

function albumStats() {
    ajaxRequest("GET", "/album/stats/lownumsongs", "album_stats_1", "dummy");
}

function albumStatsSet(arg1, arg2, arg3, arg4, arg5) {
    var modalValue = createAlbumStatsModal(arg1.toFixed(2), arg2.toFixed(2), parseInt(arg3).toFixed(2), parseInt(arg4).toFixed(2), arg5.toFixed(2));
    $('#albumStats').on('show.bs.modal', function(){
        $('#albumStats').html(modalValue);
    }).modal("show");
}

function albumStatsGo() {
    $("#albumStats").modal("hide");
}

function findAlbumInfo(albumId) {
    var req = {
        album_name: albumId
    };
    ajaxRequest("POST", "/album/length", "album_1", req);
}

function findAlbumInfoSet(arg1, arg2, arg3, arg4, arg5, name) {
    var modalValue = createAlbumInfoModal(arg1.toFixed(2), arg2.toFixed(2), arg3.toFixed(2), arg4.toFixed(2), arg5.toFixed(2), name);
    $('#albumInfo').on('show.bs.modal', function(){
        $('#albumInfo').html(modalValue);
    }).modal("show");
}

function findAlbumInfoGo() {
    $("#albumInfo").modal("hide");
}


