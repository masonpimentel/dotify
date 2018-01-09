function searchSongsNameGo() {
    var songSearch = document.getElementById("songSearch").value;
    clearTable();
    setSongsHeader();
    if (songSearch == "") {
        ajaxRequest("GET", "/songs", "songs", null);
        return;
    }
    var req = {
        song_name: songSearch
    };
    ajaxRequest("POST", "/songs/search/name", "search_song_name", req);
}

function searchSongsGenreGo() {
    var songSearchGenre = document.getElementById("genreSearch").value;
    clearTable();
    setSongsHeader();
    if (songSearchGenre == "") {
        ajaxRequest("GET", "/songs", "songs", null);
        return;
    }
    var req = {
        genre: songSearchGenre
    };
    ajaxRequest("POST", "/songs/search/genre", "search_song_genre", req);
}
