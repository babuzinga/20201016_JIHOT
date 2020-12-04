-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               5.5.62 - MySQL Community Server (GPL)
-- Операционная система:         Win64
-- HeidiSQL Версия:              10.1.0.5464
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Дамп структуры для таблица project20201016.accounts
DROP TABLE IF EXISTS `accounts`;
CREATE TABLE IF NOT EXISTS `accounts` (
  `uuid` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nid` int(2) NOT NULL,
  `tag_ids` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `last_parse` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uuid`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `account_network_id` (`login`,`nid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица project20201016.medias
DROP TABLE IF EXISTS `medias`;
CREATE TABLE IF NOT EXISTS `medias` (
  `uuid` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uuid_post` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` int(1) NOT NULL COMMENT '1 - image / 2 - video',
  `views` int(1) DEFAULT '0',
  `video_view_count` int(1) DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '0 - moder / 1 - ok / 2 - remove',
  PRIMARY KEY (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица project20201016.medias_tmp
DROP TABLE IF EXISTS `medias_tmp`;
CREATE TABLE IF NOT EXISTS `medias_tmp` (
  `uuid` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uuid_post` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` int(1) DEFAULT NULL COMMENT '1 - image / 2 - video',
  `url` text COLLATE utf8mb4_unicode_ci,
  `preview` text COLLATE utf8mb4_unicode_ci,
  `video_view_count` int(11) DEFAULT NULL,
  PRIMARY KEY (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица project20201016.networks
DROP TABLE IF EXISTS `networks`;
CREATE TABLE IF NOT EXISTS `networks` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Список социальных сетей';

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица project20201016.posts
DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `uuid` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uuid_account` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pid` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nid` int(2) NOT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `shortcode` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `comments` int(11) NOT NULL,
  `likes` int(11) NOT NULL,
  `media_count` int(2) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '0 - moder / 1 - ok / 2 - remove',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uuid`),
  UNIQUE KEY `uuid_account_pid` (`uuid_account`,`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Экспортируемые данные не выделены.
-- Дамп структуры для таблица project20201016.tags_account
DROP TABLE IF EXISTS `tags_account`;
CREATE TABLE IF NOT EXISTS `tags_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Экспортируемые данные не выделены.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
