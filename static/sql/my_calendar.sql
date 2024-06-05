-- drop database if exists `db_my_calendar`;
-- create database if not exists `db_my_calendar` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- use `db_my_calendar`;

create table `tasks`(
	id INT unsigned not null auto_increment primary key,
	theme VARCHAR(255) not null default '-',
	type_id INT unsigned,
	place VARCHAR(255) not null default '-',
	`datetime` timestamp,
	`duration` time,
	is_completed bool not null default false,
	comment VARCHAR(1000) not null default '-'
);

create table `types`(
	id INT unsigned not null auto_increment primary key,
	name VARCHAR(100),
	label VARCHAR(100)
);

alter table `tasks` add foreign key (`type_id`) references `types`(`id`);

insert into `types`(`name`, `label`) values
	('Встреча', 'meeting'),
	('Звонок', 'call'),
	('Совещание', 'conference'),
	('Дело', 'deal');