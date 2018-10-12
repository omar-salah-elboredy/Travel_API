DROP DATABASE IF EXISTS `testapi`;
CREATE DATABASE `testapi`;
USE `testapi`;

CREATE TABLE `trips` ( 
	`id` BIGINT NOT NULL AUTO_INCREMENT , 
	`trip_source` VARCHAR(300) NOT NULL , 
	`trip_destination` VARCHAR(300) NOT NULL , 
	`trip_start_date` DATE NOT NULL , 
	`trip_end_date` DATE NOT NULL , 
	`trip_user_id` BIGINT NOT NULL , 
	PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE `users` ( 
	`id` BIGINT NOT NULL AUTO_INCREMENT , 
	`username` VARCHAR(20) NOT NULL , 
	`first_name` VARCHAR(50) , 
	`last_name` VARCHAR(50) , 
	`dob` DATE , 
	`email_address` VARCHAR(254) , 
	`password` VARCHAR(45) NOT NULL ,
	`salt` VARCHAR(10) NOT NULL , 
	`access_level` INT(2) NOT NULL , 
	PRIMARY KEY (`id`), 
	UNIQUE (`username`)
) ENGINE = InnoDB;

ALTER TABLE `trips` 
ADD CONSTRAINT `user_id_foreign_constraint` 
FOREIGN KEY (`trip_user_id`) 
REFERENCES `users`(`id`) 
ON DELETE CASCADE ON UPDATE CASCADE;