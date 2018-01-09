drop database if exists database_name;
create database database_name;

use database_name;

create table song  (
	sid int not null auto_increment,
	song_name varchar(50) not null,
	track_number int not null,
    song_length float(5,2) not null,
	primary key (sid)
) auto_increment=1000;

create table genre (
	genre_name varchar(30) not null,
    primary key (genre_name)
);

create table user (
	uname varchar(20) not null,
    pass varchar(30) not null,
    first_name varchar(30) null,
    last_name varchar(40) null,
    age int null,
    email varchar(30) null,
    primary key (uname) 
);

create table playlist (
	pid int not null auto_increment,
    playlist_name varchar(40),
    primary key (pid)
) auto_increment=5000;

create table admin (
	uname varchar(20) not null,
    title varchar(20) not null,
    primary key (title, uname),
    foreign key (uname) references user (uname) on delete cascade
);

create table albumcategory (
	album_name varchar(30) not null,
    artist_name varchar(20) not null,
	year int null,
    genre_name varchar(30) not null,
    primary key (album_name, artist_name),
    foreign key (genre_name) references genre (genre_name) on delete no action
);

create table albumcontains (
	album_name varchar(30) not null,
    artist_name varchar(20) not null,
    sid int not null,
    primary key (album_name, artist_name, sid),
    foreign key (sid) references song (sid) on delete cascade on update cascade,
    foreign key (album_name, artist_name) references albumcategory (album_name, artist_name) on delete cascade on update cascade
);

create table privilege (
	description varchar(50) not null,
    task_importance int not null,
    primary key (description)
);

create table adminprivileges (
	title varchar(20) not null,
    description varchar(50) not null,
    primary key (title, description),
    foreign key (title) references admin (title) on delete no action,
    foreign key (description) references privilege (description) on delete no action
);

create table userplaylists (
	uname varchar(20) not null,
    pid int not null,
    primary key (uname, pid),
    foreign key (uname) references user (uname) on delete cascade on update cascade,
    foreign key (pid) references playlist (pid) on delete cascade on update cascade
);

-- Rating should be between 0-5  but MySQL does not support the check constraint. Will have to add in the code
create table playlistsongs (
	added_date char(10) not null,
    sid int not null,
    pid int not null, 
    rating int null,
    primary key (sid, pid),
    foreign key (sid) references song (sid) on delete cascade on update cascade,
    foreign key (pid) references playlist (pid) on delete cascade on update cascade,
    check (rating <= 5 and rating >= 0)
);

insert into genre (genre_name) values ("Hip-Hop");
insert into genre (genre_name) values ("Metal");
insert into genre (genre_name) values ("Rock");
insert into genre (genre_name) values ("Pop");
insert into genre (genre_name) values ("Indie");
insert into genre (genre_name) values ("Future House");

insert into playlist values (5000, "All Songs");
insert into playlist values (5001, "Rock and Roll");
insert into playlist values (5002, "Fave Road Tunes");
insert into playlist values (5003, "Future House");

insert into user values ("FantasyBuddy", 123456, "Marketta", "Timm", 42, null);
insert into user values ("SnXfZ947", "abcd123", "Ayy", "Lmao", 23, null);
insert into user values ("Sailor Bacon", 112200, "Rigoberta", "Nuckles", 25, "rn180@gmail.com");
insert into user values ("Porchwork", 123333, null, null, null, "pw@yahoo.com");
insert into user values ("MafiaPride", "mafia123", "Stewart", "Delucca", 21, "mp124@yahoo.com");

insert into admin values ("SnXfZ947", "Owner");
insert into admin values ("Sailor Bacon", "Administrator");

insert into privilege values ("Remove Users", 4);
insert into privilege values ("Modify Library", 2);
insert into privilege values ("Access Source Code", 1);

insert into adminprivileges values ("Owner", "Remove Users");
insert into adminprivileges values ("Owner", "Modify Library");
insert into adminprivileges values ("Owner", "Access Source Code");
insert into adminprivileges values ("Administrator", "Modify Library");

insert into userplaylists values ("Sailor Bacon", 5000);
insert into userplaylists values ("Porchwork", 5001);
insert into userplaylists values ("MafiaPride", 5002);
insert into userplaylists values ("SnXfZ947", 5003);

insert into albumcategory values ("To Pimp a Butterfly", "Kendrick Lamar", 2015, "Hip-Hop");
insert into song values (1000, "Wesley's Theory", 1, 4.47);
insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1000);
insert into song values (1001, "For Free? (Interlude)", 2, 2.1);
insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1001);
insert into song values (1002, "King Kunta", 3, 3.54);
insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1002);
insert into song values (1003, "Institutionalized", 4, 4.31);
insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1003);
insert into song values (1004, "These Walls", 5, 5.0);
insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1004);
insert into song values (1005, "U", 6, 4.28);
insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1005);
insert into song values (1006, "Alright", 7, 3.39);
insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1006);
insert into song values (1007, "For Sale? (Interlude)", 8, 4.51);
insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1007);
insert into song values (1008, "Momma", 9, 4.43);
insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1008);
insert into song values (1009, "Hood Politics", 10, 4.52);
insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1009);
insert into song values (1010, "How Much a Dollar Cost", 11, 4.21);
insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1010);
insert into song values (1011, "Complexion (A Zulu Love)", 12, 4.23);
insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1011);
insert into song values (1012, "The Blacker the Berry", 13, 5.28);
insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1012);
insert into song values (1013, "You Aint Gotta Lie (Momma Said)", 14, 4.01);
insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1013);
insert into song values (1014, "I", 15, 5.36);
insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1014);
insert into song values (1015, "Mortal Man", 16, 12.07);
insert into albumcontains values ("To Pimp a Butterfly", "Kendrick Lamar", 1015);
insert into playlistsongs values ("2017-03-16", 1000, 5000, 0);
insert into playlistsongs values ("2017-03-16", 1001, 5000, 3);
insert into playlistsongs values ("2017-03-16", 1002, 5000, 1);
insert into playlistsongs values ("2017-03-16", 1003, 5000, 2);
insert into playlistsongs values ("2017-03-16", 1004, 5000, 4);
insert into playlistsongs values ("2017-03-16", 1005, 5000, 4);
insert into playlistsongs values ("2017-03-16", 1006, 5000, 5);
insert into playlistsongs values ("2017-03-16", 1007, 5000, 5);
insert into playlistsongs values ("2017-03-16", 1008, 5000, 1);
insert into playlistsongs values ("2017-03-16", 1009, 5000, 1);
insert into playlistsongs values ("2017-03-16", 1010, 5000, 1);
insert into playlistsongs values ("2017-03-16", 1011, 5000, 2);
insert into playlistsongs values ("2017-03-16", 1012, 5000, 1);
insert into playlistsongs values ("2017-03-16", 1013, 5000, 2);
insert into playlistsongs values ("2017-03-16", 1014, 5000, 1);
insert into playlistsongs values ("2017-03-16", 1015, 5000, 0);

insert into albumcategory values ("Ride the Lightning", "Metallica", 1984, "Metal");
insert into song values (1016, "Fight Fire With Fire", 1, 4.44);
insert into albumcontains values ("Ride the Lightning", "Metallica", 1016);
insert into song values (1017, "Ride the Lightning", 2, 6.36);
insert into albumcontains values ("Ride the Lightning", "Metallica", 1017);
insert into song values (1018, "For Whom the Bell Tolls", 3, 5.1);
insert into albumcontains values ("Ride the Lightning", "Metallica", 1018);
insert into song values (1019, "Fade to Black", 4, 6.56);
insert into albumcontains values ("Ride the Lightning", "Metallica", 1019);
insert into song values (1020, "Trapped Under Ice", 5, 4.03);
insert into albumcontains values ("Ride the Lightning", "Metallica", 1020);
insert into song values (1021, "Escape", 6, 4.23);
insert into albumcontains values ("Ride the Lightning", "Metallica", 1021);
insert into song values (1022, "Creeping Death", 7, 6.36);
insert into albumcontains values ("Ride the Lightning", "Metallica", 1022);
insert into song values (1023, "The Call of Ktulu", 8, 8.52);
insert into albumcontains values ("Ride the Lightning", "Metallica", 1023);
insert into playlistsongs values ("2017-03-16", 1016, 5000, 3);
insert into playlistsongs values ("2017-03-16", 1017, 5000, 4);
insert into playlistsongs values ("2017-03-16", 1018, 5000, 3);
insert into playlistsongs values ("2017-03-16", 1019, 5000, 5);
insert into playlistsongs values ("2017-03-16", 1020, 5000, 3);
insert into playlistsongs values ("2017-03-16", 1021, 5000, 5);
insert into playlistsongs values ("2017-03-16", 1022, 5000, 0);
insert into playlistsongs values ("2017-03-16", 1023, 5000, 0);

insert into albumcategory values ("Dark Side of the Moon", "Pink Floyd", 1973, "Rock");
insert into song values (1024, "Speak to Me", 1, 1.13);
insert into albumcontains values ("Dark Side of the Moon", "Pink Floyd", 1024);
insert into song values (1025, "Breathe", 2, 2.46);
insert into albumcontains values ("Dark Side of the Moon", "Pink Floyd", 1025);
insert into song values (1026, "On the Run", 3, 3.34);
insert into albumcontains values ("Dark Side of the Moon", "Pink Floyd", 1026);
insert into song values (1027, "Time", 4, 7.04);
insert into albumcontains values ("Dark Side of the Moon", "Pink Floyd", 1027);
insert into song values (1028, "The Great Gig in the Sky", 5, 4.44);
insert into albumcontains values ("Dark Side of the Moon", "Pink Floyd", 1028);
insert into song values (1029, "Money", 6, 6.32);
insert into albumcontains values ("Dark Side of the Moon", "Pink Floyd", 1029);
insert into song values (1030, "Us and Them", 7, 7.4);
insert into albumcontains values ("Dark Side of the Moon", "Pink Floyd", 1030);
insert into song values (1031, "Any Colour You Like", 8, 3.25);
insert into albumcontains values ("Dark Side of the Moon", "Pink Floyd", 1031);
insert into song values (1032, "Brain Damage", 9, 3.5);
insert into albumcontains values ("Dark Side of the Moon", "Pink Floyd", 1032);
insert into song values (1033, "Eclipse", 10, 2.02);
insert into albumcontains values ("Dark Side of the Moon", "Pink Floyd", 1033);
insert into playlistsongs values ("2017-03-16", 1024, 5000, 1);
insert into playlistsongs values ("2017-03-16", 1025, 5000, 3);
insert into playlistsongs values ("2017-03-16", 1026, 5000, 3);
insert into playlistsongs values ("2017-03-16", 1027, 5000, 4);
insert into playlistsongs values ("2017-03-16", 1028, 5000, 3);
insert into playlistsongs values ("2017-03-16", 1029, 5000, 3);
insert into playlistsongs values ("2017-03-16", 1030, 5000, 3);
insert into playlistsongs values ("2017-03-16", 1031, 5000, 5);
insert into playlistsongs values ("2017-03-16", 1032, 5000, 3);
insert into playlistsongs values ("2017-03-16", 1033, 5000, 0);

insert into albumcategory values ("1989", "Taylor Swift", 2014, "Pop");
insert into song values (1034, "Welcome to New York", 1, 3.32);
insert into albumcontains values ("1989", "Taylor Swift", 1034);
insert into song values (1035, "Blank Space", 2, 3.51);
insert into albumcontains values ("1989", "Taylor Swift", 1035);
insert into song values (1036, "Style", 3, 3.5);
insert into albumcontains values ("1989", "Taylor Swift", 1036);
insert into song values (1037, "Out of the Woods", 4, 3.55);
insert into albumcontains values ("1989", "Taylor Swift", 1037);
insert into song values (1038, "All You Had to Do Was Stay", 5, 3.13);
insert into albumcontains values ("1989", "Taylor Swift", 1038);
insert into song values (1039, "Shake It Off", 6, 3.39);
insert into albumcontains values ("1989", "Taylor Swift", 1039);
insert into song values (1040, "I Wish You Would", 7, 3.27);
insert into albumcontains values ("1989", "Taylor Swift", 1040);
insert into song values (1041, "Bad Blood", 8, 3.31);
insert into albumcontains values ("1989", "Taylor Swift", 1041);
insert into song values (1042, "Wildest Dreams", 9, 3.4);
insert into albumcontains values ("1989", "Taylor Swift", 1042);
insert into song values (1043, "How You Get the Girl", 10, 4.07);
insert into albumcontains values ("1989", "Taylor Swift", 1043);
insert into song values (1044, "This Love", 11, 4.1);
insert into albumcontains values ("1989", "Taylor Swift", 1044);
insert into song values (1045, "I Know Places", 12, 3.15);
insert into albumcontains values ("1989", "Taylor Swift", 1045);
insert into song values (1046, "Clean", 13, 4.3);
insert into albumcontains values ("1989", "Taylor Swift", 1046);

insert into albumcategory values ("2016 Mix", "Oliver Heldens", 2016, "Future House");
insert into song values (1047, "Waiting", 1, 3.23);
insert into albumcontains values ("2016 Mix", "Oliver Heldens", 1047);
insert into song values (1048, "You Know", 2, 2.37);
insert into albumcontains values ("2016 Mix", "Oliver Heldens", 1048);
insert into song values (1049, "Bunnydance", 3, 4.08);
insert into albumcontains values ("2016 Mix", "Oliver Heldens", 1049);

insert into playlistsongs values ("2017-03-16", 1034, 5000, 1);
insert into playlistsongs values ("2017-03-16", 1035, 5000, 0);
insert into playlistsongs values ("2017-03-16", 1036, 5000, 0);
insert into playlistsongs values ("2017-03-16", 1037, 5000, 5);
insert into playlistsongs values ("2017-03-16", 1038, 5000, 0);
insert into playlistsongs values ("2017-03-16", 1039, 5000, 1);
insert into playlistsongs values ("2017-03-16", 1040, 5000, 5);
insert into playlistsongs values ("2017-03-16", 1041, 5000, 2);
insert into playlistsongs values ("2017-03-16", 1042, 5000, 5);
insert into playlistsongs values ("2017-03-16", 1043, 5000, 2);
insert into playlistsongs values ("2017-03-16", 1044, 5000, 4);
insert into playlistsongs values ("2017-03-16", 1045, 5000, 3);
insert into playlistsongs values ("2017-03-16", 1046, 5000, 3);
insert into playlistsongs values ("2017-03-16", 1047, 5000, 3);
insert into playlistsongs values ("2017-03-16", 1048, 5000, 2);
insert into playlistsongs values ("2017-03-16", 1049, 5000, 3);

insert into playlistsongs values ("2017-03-16", 1024, 5001, 5);
insert into playlistsongs values ("2017-03-16", 1025, 5001, 5);
insert into playlistsongs values ("2017-03-16", 1026, 5001, 5);
insert into playlistsongs values ("2017-03-16", 1027, 5001, 5);
insert into playlistsongs values ("2017-03-16", 1028, 5001, 5);
insert into playlistsongs values ("2017-03-16", 1029, 5001, 5);
insert into playlistsongs values ("2017-03-16", 1030, 5001, 5);
insert into playlistsongs values ("2017-03-16", 1031, 5001, 5);
insert into playlistsongs values ("2017-03-16", 1032, 5001, 5);
insert into playlistsongs values ("2017-03-16", 1033, 5001, 5);

insert into playlistsongs values ("2017-03-16", 1028, 5002, 1);
insert into playlistsongs values ("2017-03-16", 1034, 5002, 3);
insert into playlistsongs values ("2017-03-16", 1035, 5002, 3);
insert into playlistsongs values ("2017-03-16", 1016, 5002, 4);
insert into playlistsongs values ("2017-03-16", 1006, 5002, 3);
insert into playlistsongs values ("2017-03-16", 1004, 5002, 3);   

insert into playlistsongs values ("2017-03-16", 1047, 5003, 5); 
insert into playlistsongs values ("2017-03-16", 1048, 5003, 5); 
insert into playlistsongs values ("2017-03-16", 1049, 5003, 4); 
