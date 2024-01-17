CREATE TABLE `users` (
  `user_id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(100) UNIQUE NOT NULL,
  `email` varchar(100) UNIQUE NOT NULL,
  `bcrypt_password` varchar(255) NOT NULL,
  `is_admin` boolean NOT NULL DEFAULT false,
  `is_editor` boolean NOT NULL DEFAULT false,
  `is_author` boolean NOT NULL DEFAULT false
);

CREATE TABLE `sessions` (
  `session_id` int PRIMARY KEY AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token` binary(255) NOT NULL,
  `expiry` timestamp
);

CREATE TABLE `invite_tokens` (
  `token_id` int PRIMARY KEY AUTO_INCREMENT,
  `token` binary(255) NOT NULL,
  `author` boolean NOT NULL DEFAULT false,
  `editor` boolean NOT NULL DEFAULT false,
  `admin` boolean NOT NULL DEFAULT false
);

ALTER TABLE `sessions` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

