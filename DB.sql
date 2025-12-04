-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Vært: mariadb
-- Genereringstid: 04. 12 2025 kl. 09:18:00
-- Serverversion: 10.6.20-MariaDB-ubu2004
-- PHP-version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `company`
--

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `comments`
--

CREATE TABLE `comments` (
  `comment_pk` varchar(50) NOT NULL,
  `comment_post_fk` varchar(50) NOT NULL,
  `comment_user_fk` varchar(50) NOT NULL,
  `comment_message` text NOT NULL,
  `comment_created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Data dump for tabellen `comments`
--

INSERT INTO `comments` (`comment_pk`, `comment_post_fk`, `comment_user_fk`, `comment_message`, `comment_created_at`, `updated_at`, `deleted_at`) VALUES
('257a289c66c68b9aed2953ae68a361248c882d7b2d1060520a', '03db19c699667926a214eb06280ca360cc5015e610ee583195', '1', 'HEJEHEJEJHEH', '2025-11-22 16:44:59', NULL, NULL),
('2658f9eae0e8605f310deeb928fb01c288dddc04283f2c386a', '03db19c699667926a214eb06280ca360cc5015e610ee583195', '5afc9c4fe287446fa12cad78655727d2', 'sdfdsf', '2025-11-23 16:29:12', NULL, NULL),
('33c78c20280689f57a13b53237a0bedc0459a5e0e8542e86d6', 'a7b0b388995525ccaeb6674f2752bedc02e13e682db2a71631', '1', 'Nej tak', '2025-11-22 16:47:04', NULL, NULL),
('4b0697ec7ceac6fb7d1f41f5e07fa3187a1f90ef5ac5afee99', '1', '5afc9c4fe287446fa12cad78655727d2', 'asddsasad', '2025-11-23 16:29:19', NULL, NULL),
('4c7ab7667be9d90af099b6d2648c9cd510a5d15d2b4b447e93', '03db19c699667926a214eb06280ca360cc5015e610ee583195', '5afc9c4fe287446fa12cad78655727d2', 'asdasdsad', '2025-11-23 16:29:36', NULL, NULL),
('6b9d0eb419e7571c48b1dde456d6fd24a5aabf1cbad9a60304', '42098f86770b40120733b6e76f73af6b84f1ea4ecc04517c98', '1', 'dsfsd', '2025-11-22 17:37:43', '2025-11-22 18:13:11', NULL),
('80f850dabc0138c61b1278f4613de0dc6ad6fea63a81f0b024', 'a7b0b388995525ccaeb6674f2752bedc02e13e682db2a71631', '5afc9c4fe287446fa12cad78655727d2', 'dsdfsd', '2025-11-22 18:27:30', '2025-11-23 16:28:31', NULL),
('8da1ad5b48f800ec31d6cb9684d4e34e74ac9167714b3bda62', '03db19c699667926a214eb06280ca360cc5015e610ee583195', '5afc9c4fe287446fa12cad78655727d2', 'asdsda', '2025-11-23 16:29:42', NULL, NULL),
('d39f341a92b0fb77b893cbd8dbf3b8069c5b3fca973cb58ab2', '1', '1', 'HEj', '2025-11-22 16:48:28', NULL, NULL),
('d884187ec47a05f3658650456d94c647ff491e133c462f9cd5', '1', '5afc9c4fe287446fa12cad78655727d2', 'Hej hej', '2025-11-22 18:18:29', NULL, NULL),
('d8deeb79d81b4cb16025f621d6bb770b513caa43b67886a3bc', '03db19c699667926a214eb06280ca360cc5015e610ee583195', '5afc9c4fe287446fa12cad78655727d2', 'sadasd', '2025-11-23 16:29:28', NULL, NULL),
('f58f1b648411cdeadae6726f957863c346f30f3bcdff7c678f', '1', '1', 'trytrrtyrty', '2025-11-22 17:35:36', '2025-11-22 17:57:31', NULL),
('fc8d3293b3846d0ea91afb88b1c39e6489af5a4ccae789ddc7', '03db19c699667926a214eb06280ca360cc5015e610ee583195', '1', 'HEj', '2025-11-22 16:44:48', NULL, NULL),
('fed13080cf14564f5619017ef8f4dc5e9fa4c191e5690a4617', '42098f86770b40120733b6e76f73af6b84f1ea4ecc04517c98', '1', 'Hej', '2025-11-24 12:48:49', '2025-11-24 12:49:27', '2025-11-24 12:49:27'),
('a066ef351f1d3d89b3a9d90d6c91c8f8e8e20d596236bd4e7f', 'a7b0b388995525ccaeb6674f2752bedc02e13e682db2a71631', 'c9085d628ea7b2b8e316bd5f333635eec26789fa268b5c3b69', 'hwehol', '2025-12-03 17:37:54', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `follows`
--

CREATE TABLE `follows` (
  `follower_user_fk` char(50) NOT NULL,
  `follow_user_fk` char(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Data dump for tabellen `follows`
--

INSERT INTO `follows` (`follower_user_fk`, `follow_user_fk`) VALUES
('1', '4feb86ecb46c473e0f6c01e97bc26086e7953b5f621c08b5a1'),
('1', '5afc9c4fe287446fa12cad78655727d2'),
('1', '8f13e38e3e400c285a6b7308251c4829bfa88bc440f07aca7f'),
('1', 'c9085d628ea7b2b8e316bd5f333635eec26789fa268b5c3b69'),
('1a3add65aab9406698beb1de16227e12', '1'),
('1a3add65aab9406698beb1de16227e12', '4feb86ecb46c473e0f6c01e97bc26086e7953b5f621c08b5a1'),
('1a3add65aab9406698beb1de16227e12', '5afc9c4fe287446fa12cad78655727d2'),
('8f13e38e3e400c285a6b7308251c4829bfa88bc440f07aca7f', '1'),
('8f13e38e3e400c285a6b7308251c4829bfa88bc440f07aca7f', 'c9085d628ea7b2b8e316bd5f333635eec26789fa268b5c3b69'),
('c9085d628ea7b2b8e316bd5f333635eec26789fa268b5c3b69', '1'),
('c9085d628ea7b2b8e316bd5f333635eec26789fa268b5c3b69', '8f13e38e3e400c285a6b7308251c4829bfa88bc440f07aca7f');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `likes`
--

CREATE TABLE `likes` (
  `like_user_fk` char(50) NOT NULL,
  `like_post_fk` char(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Data dump for tabellen `likes`
--

INSERT INTO `likes` (`like_user_fk`, `like_post_fk`) VALUES
('1', '03db19c699667926a214eb06280ca360cc5015e610ee583195'),
('1a3add65aab9406698beb1de16227e12', '03db19c699667926a214eb06280ca360cc5015e610ee583195'),
('1a3add65aab9406698beb1de16227e12', '42098f86770b40120733b6e76f73af6b84f1ea4ecc04517c98'),
('5afc9c4fe287446fa12cad78655727d2', '03db19c699667926a214eb06280ca360cc5015e610ee583195');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `notifications`
--

CREATE TABLE `notifications` (
  `notification_pk` varchar(100) NOT NULL,
  `notification_user_fk` varchar(100) NOT NULL,
  `notification_actor_fk` varchar(100) NOT NULL,
  `notification_post_fk` varchar(100) DEFAULT NULL,
  `notification_message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Data dump for tabellen `notifications`
--

INSERT INTO `notifications` (`notification_pk`, `notification_user_fk`, `notification_actor_fk`, `notification_post_fk`, `notification_message`, `is_read`, `created_at`, `deleted_at`) VALUES
('06c9d9a7ccf0b60c0a8b5e579bdc7873ed7657e7960fca78d703f9052c5599cab6ecd72325dd1c5a6cd8675ce30911ca617e', '1a3add65aab9406698beb1de16227e12', '1', '8b5f44c06506829f602d838378a3c7b3544a21814ae8f98573', 'not', 0, '2025-12-03 19:15:35', NULL),
('1c400d0e17a10c5f730354f340dc27c5dd43de4e5f4a0a6043388dd51e24e1887bdaf461fd932beb5f75247a4fa1f0e5d9eb', '8f13e38e3e400c285a6b7308251c4829bfa88bc440f07aca7f', 'c9085d628ea7b2b8e316bd5f333635eec26789fa268b5c3b69', 'b17eacf5a9866638f6542d162be2e8b94238613e86d3a1c5e2', 'test notifikation', 0, '2025-12-03 18:27:18', NULL),
('2e5ce47048ec1bd03f846d50f29c946bc00496dbb9de2dbb9aa1bd62c7a656507c13f2d09935f4e96940692eed250b5af3ba', '8f13e38e3e400c285a6b7308251c4829bfa88bc440f07aca7f', '1', '8b5f44c06506829f602d838378a3c7b3544a21814ae8f98573', 'not', 0, '2025-12-03 19:15:35', NULL),
('43121a9f611a902e992e907df2632fd1f01a4f8d7f1f875254318cb4f0f062cb6581bda6bd673bcc8c462d243f755a9ab4a4', 'c9085d628ea7b2b8e316bd5f333635eec26789fa268b5c3b69', '1', '1300d6a43d79c95f51be5080f673220304e469b1f77ec73d09', 'test notifikation', 0, '2025-12-03 19:05:56', NULL),
('4590ed9b4da9ecf996ed5d0c6d13b4b04e0ac01c77477210ca1079308ef6965e3ad3eef82005c8777d2506cad6dd9fe3b5c5', '8f13e38e3e400c285a6b7308251c4829bfa88bc440f07aca7f', 'c9085d628ea7b2b8e316bd5f333635eec26789fa268b5c3b69', 'dabbd59814eefc7427d8ae8244be2d65bad1072e663a3e13a9', 'hej notifikation', 0, '2025-12-03 18:22:28', NULL),
('4a449a357a9c8c0a685541d02c61912f6494a78fef91a305fd8f3c362892094ff339c361e610c77332031b131db4b0827911', 'c9085d628ea7b2b8e316bd5f333635eec26789fa268b5c3b69', '1', 'fe0ad43c5dfb05062f5d722c98113b9278f3ada3288a894560', 'hej sof', 1, '2025-12-03 19:05:47', NULL),
('4e7939e30e222d57e12d1572257401a96f5bc55f28fded15aa61c9bff4afc643530c7e85e5fec7468f4ee1ec64e09b50de4a', '8f13e38e3e400c285a6b7308251c4829bfa88bc440f07aca7f', '1', 'f6abdbdc09d8685cee07f3dd64232810f5df5e7990fe191e69', 'notifikation test', 0, '2025-12-03 18:04:14', NULL),
('4f42d1282cb53b1666a386dc83d9b2bfc43a952e6368257509506fecbe624731977adb4dcca68fc82e1d5a9b0b0effdf98e6', '1a3add65aab9406698beb1de16227e12', '1', 'f6abdbdc09d8685cee07f3dd64232810f5df5e7990fe191e69', 'notifikation test', 0, '2025-12-03 18:04:14', NULL),
('7fced05129bb09326b40176a7d3b818ce01605cb4e6f4704e0dbc986342ef338f8898751a3eef2a4aa7899349ddccec428b0', '8f13e38e3e400c285a6b7308251c4829bfa88bc440f07aca7f', '1', 'fe0ad43c5dfb05062f5d722c98113b9278f3ada3288a894560', 'hej sof', 0, '2025-12-03 19:05:47', NULL),
('8c1ddc6c7de7fc395ee95f71291aaeaed11632c5f8c0e1e2b60904829a654f993ffde703ca1bfc43786ffb71ba26f3496b79', '1a3add65aab9406698beb1de16227e12', '1', '1300d6a43d79c95f51be5080f673220304e469b1f77ec73d09', 'test notifikation', 0, '2025-12-03 19:05:56', NULL),
('9e70214109acae7149574a29e7bd4d2a54c73b7d2e90b4a716722b82475a2d862bd968dc0d7c3c8a5850010a9f246321f832', '8f13e38e3e400c285a6b7308251c4829bfa88bc440f07aca7f', '1', '1300d6a43d79c95f51be5080f673220304e469b1f77ec73d09', 'test notifikation', 0, '2025-12-03 19:05:56', NULL),
('e3e6d7dced709791220956788fb1a8a2eb46c6dc006ccba64d239e1f6d1c60c4f041c705883657525e6e03eb5887cf0ca005', '1', 'c9085d628ea7b2b8e316bd5f333635eec26789fa268b5c3b69', 'dabbd59814eefc7427d8ae8244be2d65bad1072e663a3e13a9', 'hej notifikation', 1, '2025-12-03 18:22:28', NULL),
('f2a22700a1311007800db10d042dc50dcaeec5fd4680c8e362fe43db9488c49149521256ab3722125c979a9affdbc231057b', '1a3add65aab9406698beb1de16227e12', '1', 'fe0ad43c5dfb05062f5d722c98113b9278f3ada3288a894560', 'hej sof', 0, '2025-12-03 19:05:47', NULL);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `people`
--

CREATE TABLE `people` (
  `person_pk` bigint(20) UNSIGNED NOT NULL,
  `person_username` varchar(20) NOT NULL,
  `person_first_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Data dump for tabellen `people`
--

INSERT INTO `people` (`person_pk`, `person_username`, `person_first_name`) VALUES
(1, 'andreahauberg', 'Andrea'),
(5, '', '');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `person`
--

CREATE TABLE `person` (
  `person_pk` bigint(20) UNSIGNED NOT NULL,
  `person_username` varchar(20) NOT NULL,
  `person_first_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Data dump for tabellen `person`
--

INSERT INTO `person` (`person_pk`, `person_username`, `person_first_name`) VALUES
(1, 'andreahauberg', 'Andrea'),
(5, '', '');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `posts`
--

CREATE TABLE `posts` (
  `post_pk` char(50) NOT NULL,
  `post_message` varchar(200) NOT NULL,
  `post_image_path` varchar(100) NOT NULL,
  `post_user_fk` char(50) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Data dump for tabellen `posts`
--

INSERT INTO `posts` (`post_pk`, `post_message`, `post_image_path`, `post_user_fk`, `deleted_at`, `created_at`, `updated_at`) VALUES
('03db19c699667926a214eb06280ca360cc5015e610ee583195', 'dsffdsdsfsdfdsfsdfdsf', 'https://picsum.photos/400/250', '1', '2025-12-03 17:46:20', NULL, NULL),
('1', 'Post one', 'https://picsum.photos/400/250', '1', '2025-12-03 17:46:26', NULL, NULL),
('1300d6a43d79c95f51be5080f673220304e469b1f77ec73d09', 'test notifikation', 'https://picsum.photos/400/250', '1', NULL, '2025-12-03 19:05:56', NULL),
('2', '<script>alert()</stript>', 'https://picsum.photos/400/250', '1', '2025-12-03 17:46:30', NULL, NULL),
('42098f86770b40120733b6e76f73af6b84f1ea4ecc04517c98', 'Hej med jer her er mit første post', 'https://picsum.photos/400/250', '1a3add65aab9406698beb1de16227e12', NULL, NULL, NULL),
('89fa9d9b7ef2257677cc177eafbcb8d047283fe2e498a0fd90', '# news', 'https://picsum.photos/400/250', '1', '2025-12-03 17:36:36', '2025-12-03 17:36:13', NULL),
('8b5f44c06506829f602d838378a3c7b3544a21814ae8f98573', 'not', 'https://picsum.photos/400/250', '1', NULL, '2025-12-03 19:15:35', NULL),
('8e994a7fbff34b118e6604cebb0f1379dc94e95b86aa7f6530', '#like', 'https://picsum.photos/400/250', '1', NULL, '2025-12-03 17:36:48', NULL),
('a3c0a09879aa6209bdb58574dd18a8954b0f2d1f56f06bb813', 'new post', 'https://picsum.photos/400/250', '1', '2025-12-03 17:46:37', NULL, NULL),
('a7b0b388995525ccaeb6674f2752bedc02e13e682db2a71631', 'assdasadsdadsa', 'https://picsum.photos/400/250', '5afc9c4fe287446fa12cad78655727d2', NULL, NULL, NULL),
('b17eacf5a9866638f6542d162be2e8b94238613e86d3a1c5e2', 'test notifikation', 'https://picsum.photos/400/250', 'c9085d628ea7b2b8e316bd5f333635eec26789fa268b5c3b69', NULL, '2025-12-03 18:27:18', NULL),
('c5a7919a32cc432d6304f8a1c3af44709d0f44584a6e163648', 'notifikation', 'https://picsum.photos/400/250', '1', NULL, '2025-12-03 18:02:44', NULL),
('cf92e37c3faeaae0663851cef64d80ff625872bd98d6887a66', '#news', 'https://picsum.photos/400/250', '1', NULL, '2025-12-03 17:36:27', NULL),
('dabbd59814eefc7427d8ae8244be2d65bad1072e663a3e13a9', 'hej notifikation', 'https://picsum.photos/400/250', 'c9085d628ea7b2b8e316bd5f333635eec26789fa268b5c3b69', NULL, '2025-12-03 18:22:28', NULL),
('db517c70eae4766a085fe030638734d286203fc9bbc85becd5', 'fdsdsfdsfdfsdfs', 'https://picsum.photos/400/250', '1', '2025-12-03 17:46:42', NULL, NULL),
('ea9a680f2ca31d68950d90b96fb30839574031664cd62a7a76', 'dsfsdfkkgfdlkjfgdsjklsfdgjksgflk', 'https://picsum.photos/400/250', '1', '2025-12-03 17:46:48', NULL, NULL),
('eef379190c798ee647cdae0101a2810f0f22aacf48e9ac71af', 'nyt post test', 'https://picsum.photos/400/250', '1', NULL, '2025-12-03 17:35:40', NULL),
('f6abdbdc09d8685cee07f3dd64232810f5df5e7990fe191e69', 'notifikation test', 'https://picsum.photos/400/250', '1', NULL, '2025-12-03 18:04:14', NULL),
('fe0ad43c5dfb05062f5d722c98113b9278f3ada3288a894560', 'hej sof', 'https://picsum.photos/400/250', '1', NULL, '2025-12-03 19:05:47', NULL);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `users`
--

CREATE TABLE `users` (
  `user_pk` char(50) NOT NULL,
  `user_username` varchar(20) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_full_name` varchar(20) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Data dump for tabellen `users`
--

INSERT INTO `users` (`user_pk`, `user_username`, `user_email`, `user_password`, `user_full_name`, `deleted_at`, `created_at`) VALUES
('1', 'andreahauberg', 'a@a.com', '$2y$10$FFupw1g6iLrsAG1rraCqjeS38je8PgdOxml9YVQdV8ln/hSgkm.4.', 'Andrea Hauberg', NULL, NULL),
('5afc9c4fe287446fa12cad78655727d2', 'user1', 'b@b.com', '$2y$10$GheYC/X6jez8yeWkVpqgKuJ5q/miOZcAdWBB858OFojdL0JLZzyba', 'Bobo Bob', NULL, NULL),
('1a3add65aab9406698beb1de16227e12', 'user2', 'c@c.com', '$2y$10$5Rt6kJtegTIh3mAfJk059OgsLIdLES8n/ldWgvRegunURAUCK.T0m', 'Clem Clemsen', NULL, NULL),
('4feb86ecb46c473e0f6c01e97bc26086e7953b5f621c08b5a1', 'user4', 'j@j.com', '$2y$10$YRUnPlxARN5G7em/xySvRuJV/hhjpATtSGMFbQ.zUsdeWJhmKaPrm', 'Jean Jean', NULL, NULL),
('8f13e38e3e400c285a6b7308251c4829bfa88bc440f07aca7f', 'not', 'not@not.com', '$2y$10$YkY5ZdkaDcSkIy2AaBRPBOfrGbnjkzT1nUv5WEQOjm1XwjluorO/e', 'not', NULL, '2025-12-03 17:47:21'),
('c9085d628ea7b2b8e316bd5f333635eec26789fa268b5c3b69', 'sof', 's@s.com', '$2y$10$QTnkvX1YidjhEFFosjHSMul4tofJO2bla1K8h1BHtxc1bB6uRzwTy', 'sof', NULL, '2025-12-03 17:37:39');

--
-- Begrænsninger for dumpede tabeller
--

--
-- Indeks for tabel `follows`
--
ALTER TABLE `follows`
  ADD PRIMARY KEY (`follower_user_fk`,`follow_user_fk`),
  ADD KEY `follow_user_fk` (`follow_user_fk`);

--
-- Indeks for tabel `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`like_user_fk`,`like_post_fk`),
  ADD KEY `like_post_fk` (`like_post_fk`);

--
-- Indeks for tabel `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_pk`),
  ADD KEY `idx_notifications_user_fk` (`notification_user_fk`),
  ADD KEY `idx_notifications_actor_fk` (`notification_actor_fk`),
  ADD KEY `idx_notifications_post_fk` (`notification_post_fk`);

--
-- Indeks for tabel `people`
--
ALTER TABLE `people`
  ADD PRIMARY KEY (`person_pk`),
  ADD UNIQUE KEY `person_pk` (`person_pk`),
  ADD UNIQUE KEY `person_username` (`person_username`);

--
-- Indeks for tabel `person`
--
ALTER TABLE `person`
  ADD PRIMARY KEY (`person_pk`),
  ADD UNIQUE KEY `person_pk` (`person_pk`),
  ADD UNIQUE KEY `person_username` (`person_username`);

--
-- Indeks for tabel `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_pk`);

--
-- Indeks for tabel `users`
--
ALTER TABLE `users`
  ADD UNIQUE KEY `user_email` (`user_email`),
  ADD UNIQUE KEY `user_pk` (`user_pk`);

--
-- Brug ikke AUTO_INCREMENT for slettede tabeller
--

--
-- Tilføj AUTO_INCREMENT i tabel `people`
--
ALTER TABLE `people`
  MODIFY `person_pk` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tilføj AUTO_INCREMENT i tabel `person`
--
ALTER TABLE `person`
  MODIFY `person_pk` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Begrænsninger for dumpede tabeller
--

--
-- Begrænsninger for tabel `follows`
--
ALTER TABLE `follows`
  ADD CONSTRAINT `follows_ibfk_1` FOREIGN KEY (`follower_user_fk`) REFERENCES `users` (`user_pk`) ON DELETE CASCADE,
  ADD CONSTRAINT `follows_ibfk_2` FOREIGN KEY (`follow_user_fk`) REFERENCES `users` (`user_pk`) ON DELETE CASCADE;

--
-- Begrænsninger for tabel `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`like_post_fk`) REFERENCES `posts` (`post_pk`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`like_user_fk`) REFERENCES `users` (`user_pk`) ON DELETE CASCADE;

--
-- Begrænsninger for tabel `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_actor` FOREIGN KEY (`notification_actor_fk`) REFERENCES `users` (`user_pk`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_notifications_post` FOREIGN KEY (`notification_post_fk`) REFERENCES `posts` (`post_pk`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`notification_user_fk`) REFERENCES `users` (`user_pk`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;