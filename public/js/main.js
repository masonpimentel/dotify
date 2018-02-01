const MAX_RATING = 5;
var lUname;

//this disables adding songs and playlists
var disableAdd = true;

//button login instead of entering credentials
var buttonLogin = true;

/* Handler for text input
 *
 * Required
 * e: event
 * type: type ex. "login", "songs"
 *
 * Optional:
 * ratingId: ratingId for changing rating
 */
function handler(e, type, ratingId) {
    //if "enter" is pressed
    if (e.keyCode == 13) {
        switch (type) {
            case "login":
                login();
                break;
            case "songs":
                addSongGo();
                break;
            case "playlist":
                addPlaylistGo();
                break;
            case "rating":
                changeRatingGo(ratingId);
                break;
            case "removeAlbum":
                removeSongsAlbumGo();
                break;
            case "removeViaSong":
                removeSongsGo();
                break;
            case "songsearch":
                searchSongsNameGo();
                break;
            case "genresearch":
                searchSongsGenreGo();
                break;
            default:
                console.error("Unknown handler type!");
        }
    }
}

//starting point - this is called when the DOM is ready (according to jQuery)
window.onload = function() {
    if (buttonLogin) {
        //login using buttons
        $("#loginWithCredentials").modal("show");
    } else {
        //login using credentials
        $("#login").modal("show").on('shown.bs.modal', function() {
            $('#username').focus();
        });
    }
};









