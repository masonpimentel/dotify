drop database if exists heroku_6f66a796c5d0830;
create database heroku_6f66a796c5d0830;

use heroku_6f66a796c5d0830;

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