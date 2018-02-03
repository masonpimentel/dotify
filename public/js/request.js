/* Sends requests using XMLHttpRequest
 *
 * Required
 * type: ex. "POST", "GET" etc.
 * url: ex. /songs
 *
 * Optional
 * req: request body for POST requests
 * arg1: optional argument
 */
function ajaxRequest(type, url, action, req, arg1, arg2, arg3, arg4) {
    var request = new XMLHttpRequest();
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    request.open(type, url);
    if (action === "restore") {
        request.timeout = 60000;
        waitingDialog.show('Restoring database... please wait');
    }
    else {
        request.timeout = 5000;
    }
    request.setRequestHeader('Content-Type', 'application/json');
    request.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    request.onload = function () {
        if (action != "update_rating" && action != "delete_song" && action != "insert_song" && action != "delete_song_album" && action !=
            "add_playlist" && action != "remove_from_playlist" && action != "insert_into_playlist" && action != "restore") {
            try {
                var result = JSON.parse(this.responseText);
            }
            catch(e) {
                if (this.responseText.includes("table or view not found") || this.responseText.includes("Unknown database")) {
                    warningMessage("Last database restore may have been unsuccessful - attempting to fix now");
                    ajaxRequest("POST", "/restore", "restore");
                }
                else {
                    errorMessage("There was an error communicating with the server - please try again");
                    return;
                }
            }
        }
        if (this.status == 200) {
            var arg;
            var res;
            if (action == "users") {
                checkUser(result, arg1, arg2);
            }
            else if (action == "songs") {
                insertSongs(result);
            }
            else if (action == "playlists") {
                insertPlaylists(result);
            }
            else if (action == "admins") {
                checkAdmin(result, arg1);
            }
            else if(action == "insert_playlist") {
                insertSongs(result, "playlist", arg1);
            }
            else if(action == "update_rating") {
                if (this.responseText.includes("Success")) {
                    insertRating(arg1, arg2);
                    successMessage("Rating updated")
                }
                else {
                    errorMessage("There was an error updating the rating - please try again");
                }
            }
            else if(action == "delete_song") {
                res = checkSuccess(this.responseText);
                if (res.length > 0) {
                    successMessage("Song successfully deleted");
                    songsView();
                }
                else {
                    errorMessage("Not found in library - no delete performed");
                }
            }
            else if (action == "insert_into_playlist") {
                if (this.responseText.includes("Duplicate entry")) {
                    errorMessage("Already in playlist - not added");
                }
                else if(this.responseText.includes("[]")) {
                    successMessage("Successfully added to " + arg1);
                }
                else {
                    errorMessage("There was an error communicating with the server - please try again");
                }

            }
            else if(action == "insert_song") {
                clearTable();
                ajaxRequest("GET", "/songs", "songs", null);

            }
            else if(action == "delete_song_album") {
                res = checkSuccess(this.responseText);
                if (res.length > 0) {
                    successMessage("Album successfully deleted");
                    songsView();
                }
                else {
                    errorMessage("No matching name and artist - not deleted");
                }
            }
            else if(action == "search_song_genre" || action == "search_song_name") {
                clearTable();
                insertSongs(result);
            }
            else if(action == "playlist_stats") {
                playlistStatsSet(result);
            }
            else if(action == "album_stats_1") {
                arg = result[0]["Lowest Number of Songs in an Album"];
                ajaxRequest("GET", "/album/stats/highnumsongs", "album_stats_2", "dummy", arg);
            }
            else if(action == "album_stats_2") {
                arg = result[0]["Highest Number of Songs in an Album"];
                ajaxRequest("GET", "/album/stats/avgnumsongs", "album_stats_3", "dummy", arg1, arg);
            }
            else if(action == "album_stats_3") {
                arg = result[0]["Average Number of Songs in an Album"];
                ajaxRequest("GET", "/album/stats/totnumsongs", "album_stats_4", "dummy", arg1, arg2, arg);
            }
            else if(action == "album_stats_4") {
                arg = result[0]["Total Number of Songs from all albums"];
                ajaxRequest("GET", "/album/stats/totnum", "album_stats_5", "dummy", arg1, arg2, arg3, arg);
            }
            else if(action == "album_stats_5") {
                arg = result[0]["Total Number of albums"];
                albumStatsSet(arg1, arg2, arg3, arg4, arg);
            }
            else if(action == "album_1") {
                arg = result[0]["SUM(song_length)"];
                ajaxRequest("POST", "/album/avgsonglength", "album_2", req, arg);
            }
            else if(action == "album_2") {
                arg = result[0]["AVG(song_length)"];
                ajaxRequest("POST", "/album/maxsonglength", "album_3", req, arg1, arg);
            }
            else if(action == "album_3") {
                arg = result[0]["MAX(song_length)"];
                ajaxRequest("POST", "/album/minsonglength", "album_4", req, arg1, arg2, arg);
            }
            else if(action == "album_4") {
                arg = result[0]["MIN(song_length)"];
                ajaxRequest("POST", "/album/numsongs", "album_5", req, arg1, arg2, arg3, arg);
            }
            else if(action == "album_5") {
                arg = result[0]["COUNT(s.sid)"];
                findAlbumInfoSet(arg1, arg2, arg3, arg4, arg, req.album_name);
            }
            else if(action == "add_playlist") {
                clearPlaylistTable();
                ajaxRequest("POST", "/playlists", "playlists", req, lUname);
            }
            else if(action == "remove_from_playlist") {
                if (this.responseText.includes("Success")) {
                    successMessage("Successfully removed from playlist");
                    clearTable();
                    displayPlaylist(arg1);
                }
                else {
                    errorMessage("There was an error removing from this playlist - please try again");
                }

            }
            else if(action == "playlist_modal") {
                createAddToPlaylistModal(result, arg1, arg2, arg3);
            }
            else if(action == "restore") {
                window.location.reload(true);
            }
        }
        else {
            errorMessage("Error " + this.status);
        }
        if (action === "songs" || action === "restore") {
            waitingDialog.hide();
        }
    };
    request.onerror = function() {
        errorMessage("There was an error communicating with the server - please try again");
    };
    request.ontimeout = function() {
        errorMessage("Timeout: please check your internet connection");
    };
    if (action == "playlists" || action == "insert_song" || action == "delete_song" || action == "insert_playlist"
        || action == "update_rating" || action == "delete_song_album" || action == "search_song_name"
        || action == "search_song_genre" || action == "album_1" || action == "album_2" || action == "album_3"
        || action == "album_4" || action == "album_5" || action == "add_playlist" || action == "remove_from_playlist"
        || action == "playlist_modal" || action == "insert_into_playlist") {
        request.send(JSON.stringify(req));
    }
    else {
        request.send();
    }

}

function checkSuccess(res) {
    try {
        var parsed_res = JSON.parse(res);
    }
    catch(e) {
        if (res.includes("table or view not found")) {
            warningMessage("Last database restore may have been unsuccessful - attempting to fix now");
            ajaxRequest("POST", "/restore", "restore");
        }
        else {
            errorMessage("There was an error communicating with the server - please try again");
            throw "Error communicating with the server";
        }
    }
    return parsed_res;
}