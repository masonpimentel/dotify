use database_name;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE song;
DROP TABLE genre;
DROP TABLE user;
DROP TABLE playlist;
DROP TABLE admin;
DROP TABLE albumcategory;
DROP TABLE albumcontains;
DROP TABLE privilege;
DROP TABLE adminprivileges;
DROP TABLE userplaylists;
DROP TABLE playlistsongs;

SET FOREIGN_KEY_CHECKS = 1;