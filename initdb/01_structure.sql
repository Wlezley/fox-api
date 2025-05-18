-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Počítač: database:3306
-- Vytvořeno: Ned 18. kvě 2025, 16:36
-- Verze serveru: 10.6.21-MariaDB-ubu2004
-- Verze PHP: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `fox-api`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `product`
--

CREATE TABLE `product` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` float UNSIGNED NOT NULL,
  `stock` int(11) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` int(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `product`
--

INSERT INTO `product` (`id`, `name`, `price`, `stock`, `created_at`, `updated_at`, `deleted`) VALUES
(1, 'žaludy', 1.2, 150, '2025-05-18 16:11:52', '2025-05-18 16:11:52', 0),
(2, 'houby', 24.9, 20, '2025-05-18 16:12:30', '2025-05-18 16:12:30', 0),
(3, 'jahody', 42.8, 416, '2025-05-18 16:13:57', '2025-05-18 16:18:12', 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `product_history`
--

CREATE TABLE `product_history` (
  `id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `price` float UNSIGNED NOT NULL,
  `stock` int(11) UNSIGNED NOT NULL,
  `changed_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `product_history`
--

INSERT INTO `product_history` (`id`, `product_id`, `name`, `price`, `stock`, `changed_at`) VALUES
(1, 1, 'žaludy', 1.2, 150, '2025-05-18 16:11:53'),
(2, 2, 'houby', 24.9, 20, '2025-05-18 16:12:30'),
(3, 3, 'jahody', 39.9, 416, '2025-05-18 16:13:57'),
(4, 3, 'jahody', 20, 416, '2025-05-18 16:17:17'),
(5, 3, 'jahody', 80, 416, '2025-05-18 16:17:25'),
(6, 3, 'jahody', 49.9, 416, '2025-05-18 16:17:52'),
(7, 3, 'jahody', 33.5, 416, '2025-05-18 16:18:01'),
(8, 3, 'jahody', 42.8, 416, '2025-05-18 16:18:12');

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `product_history`
--
ALTER TABLE `product_history`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `fk_product_product_history` (`product_id`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pro tabulku `product_history`
--
ALTER TABLE `product_history`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `product_history`
--
ALTER TABLE `product_history`
  ADD CONSTRAINT `fk_product_product_history` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
