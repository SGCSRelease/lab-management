#데이터베이스와 사용자 생성및 권한설정.

create database taeguk DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
create user 'taeguk'@'localhost' identified by 'password_is_secret';
grant all privileges on taeguk.* to taeguk@localhost;
flush privileges;

USE taeguk;

#테이블 생성.
create table user_list (
	ul_id INT not null auto_increment primary key,
	user_name VARCHAR(30) not null,
	user_pw CHAR(128),
	pw_salt CHAR(128),
	is_exist_pw BOOLEAN not null,
	stage INT not null
) engine=innodb;

create table `login_attempts` (
	`ul_id` INT not null,
	`time` VARCHAR(30) not null,
	foreign key(ul_id) references user_list(ul_id) on delete cascade
) engine=InnoDB;

create table mng_list (
	mng_id INT not null auto_increment primary key,
	mng_day ENUM('mon','tue','wed','thu',"fri") not null,
	mng_period ENUM('2','3','4','5','6','7') not null   # 7 -> night
)engine=innodb;

create table apply_list (
	apply_id INT not null auto_increment primary key,
	ul_id INT not null,
	mng_id INT not null,
	is_can_mng BOOLEAN not null,
	prefer_order INT,
	apply_time TIMESTAMP default CURRENT_TIMESTAMP,
	foreign key(ul_id) references user_list(ul_id) on delete cascade,
	foreign key(mng_id) references mng_list(mng_id) on delete cascade
)engine=innodb;
#insert into apply_list (ul_id,mng_id) VALUES (32,29);

create table mng_apply_info (
	mng_apply_id INT not null auto_increment primary key,
	start_time TIMESTAMP not null,
	end_time TIMESTAMP not null
)engine=innodb;

#create table admin_list (
#al_id INT not null auto_increment primary key,
#admin_id VARCHAR(30) not null,
#admin_pw VARCHAR(40) not null
#)engine=innodb;

#예시 데이터 삽입. 57600
#insert into admin_list VALUES (1,"",SHA('wlalszlTja12'));

insert into mng_apply_info VALUES (1,FROM_UNIXTIME(UNIX_TIMESTAMP('2014-09-12 23:00:00')),FROM_UNIXTIME(UNIX_TIMESTAMP('2016-09-13 14:00:00')));
#update mng_apply_info set start_time=FROM_UNIXTIME(UNIX_TIMESTAMP('2015-02-10 22:00:00')-57600);
#update mng_apply_info set start_time=FROM_UNIXTIME(UNIX_TIMESTAMP('2015-02-16 23:37:00')-61140), end_time=FROM_UNIXTIME(UNIX_TIMESTAMP('2015-02-25 23:38:00')-61140);
#update mng_apply_info set start_time=FROM_UNIXTIME(UNIX_TIMESTAMP('2015-03-16 23:04:00')-54), end_time=FROM_UNIXTIME(UNIX_TIMESTAMP('2015-03-16 23:05:00')-54);

# insert into user_list VALUES (증가하는 숫자, "학번+이름",비번존재여부,0);
insert into user_list (ul_id,user_name,is_exist_pw,stage) VALUES (1,"14권태국",false,0);
# ...


# insert into mng_list VALUES (증가하는 숫자, 'mon, tue, wed, thu, fri 각각', 1~6,11각각, 2 or 1, 0, 2);
insert into mng_list VALUES (2, 'mon', '2');
insert into mng_list VALUES (3, 'mon', '3');
insert into mng_list VALUES (4, 'mon', '4');
insert into mng_list VALUES (5, 'mon', '5');
insert into mng_list VALUES (6, 'mon', '6');
insert into mng_list VALUES (7, 'mon', '7');
insert into mng_list VALUES (9, 'tue', '2');
insert into mng_list VALUES (10, 'tue', '3');
insert into mng_list VALUES (11, 'tue', '4');
insert into mng_list VALUES (12, 'tue', '5');
insert into mng_list VALUES (13, 'tue', '6');
insert into mng_list VALUES (14, 'tue', '7');
insert into mng_list VALUES (16, 'wed', '2');
insert into mng_list VALUES (17, 'wed', '3');
insert into mng_list VALUES (18, 'wed', '4');
insert into mng_list VALUES (19, 'wed', '5');
insert into mng_list VALUES (20, 'wed', '6');
insert into mng_list VALUES (21, 'wed', '7');
insert into mng_list VALUES (23, 'thu', '2');
insert into mng_list VALUES (24, 'thu', '3');
insert into mng_list VALUES (25, 'thu', '4');
insert into mng_list VALUES (26, 'thu', '5');
insert into mng_list VALUES (27, 'thu', '6');
insert into mng_list VALUES (28, 'thu', '7');
insert into mng_list VALUES (30, 'fri', '2');
insert into mng_list VALUES (31, 'fri', '3');
insert into mng_list VALUES (32, 'fri', '4');
insert into mng_list VALUES (33, 'fri', '5');
insert into mng_list VALUES (34, 'fri', '6');
insert into mng_list VALUES (35, 'fri', '7');


#SET @tables = NULL; SELECT GROUP_CONCAT(table_schema, '.', table_name) INTO @tables FROM information_schema.tables  WHERE table_schema = 'teamCTFdb'; SET @tables = CONCAT('DROP TABLE ', @tables); PREPARE stmt FROM @tables; EXECUTE stmt; DEALLOCATE PREPARE stmt;
#SET FOREIGN_KEY_CHECKS=0; -- to disable them
#SET FOREIGN_KEY_CHECKS=1; -- to re-enable them