-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Мар 10 2025 г., 05:52
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `crm`
--

-- --------------------------------------------------------

--
-- Структура таблицы `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `birthday` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `clients`
--

INSERT INTO `clients` (`id`, `name`, `email`, `phone`, `birthday`, `created_at`) VALUES
(1, 'Ivan Ivanovich', 'ivan.ivanov@example.com', '+79991234567', '1985-05-15', '2025-01-13 09:21:57'),
(2, 'Maria Petrovna', 'maria.petrova@example.com', '+79991234568', '1990-07-20', '2025-01-13 09:21:57'),
(3, 'Sergei Sidorov', 'sergey.sidorov@example.com', '+79991234569', '1988-03-30', '2025-01-13 09:21:57'),
(6, 'Pedik Petr', 'dmitry.dmitriev@mail.ru', '13895714124', '2025-01-31', '2025-02-03 03:04:50'),
(1738895546, 'не указано', 'lox@mail.ru', 'не указано', '0000-00-00', '2025-02-07 02:32:26'),
(1738895547, 'кака', 'Aaa@gmail.com', '+7 (996) 378-55-71', '2025-02-25', '2025-02-26 08:15:52'),
(1738895548, 'Аль Абдул', 'jojexa9796@youke1.com', '89943775883', '2001-09-11', '2025-02-26 08:22:26');

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total` decimal(10,2) DEFAULT NULL,
  `status` enum('0','1') DEFAULT '1',
  `admin` int(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `client_id`, `order_date`, `total`, `status`, `admin`) VALUES
(3, 1, '2025-01-13 09:25:36', 200.00, '0', 1),
(5, 2, '2025-01-14 03:15:45', 300.00, '0', 1),
(1738732062, 6, '2025-02-05 05:07:42', 1600.00, '1', 1),
(1738732563, 3, '2025-02-05 05:16:03', 250.50, '0', 1),
(1738732643, 2, '2025-02-05 05:17:23', 500.00, '0', 1),
(1738732738, 6, '2025-02-05 05:18:58', 500.00, '0', 1),
(1738818886, 3, '2025-02-06 05:14:46', 623.00, '1', 1),
(1738818896, 2, '2025-02-06 05:14:56', 1750.50, '1', 1),
(1738895546, 1738895546, '2025-02-07 02:32:26', 250.50, '1', 1),
(1739250762, 2, '2025-02-11 05:12:42', 1750.50, '1', 1),
(1739423871, 2, '2025-02-13 05:17:51', 2123.00, '0', 1),
(1739424654, 1738895546, '2025-02-13 05:30:54', 2123.00, '0', 1),
(1739424731, 1738895546, '2025-02-13 05:32:11', 250.50, '0', 1),
(1739424751, 6, '2025-02-13 05:32:31', 2123.00, '0', 1),
(1739425148, 6, '2025-02-13 05:39:08', 1500.00, '1', 1),
(1739426074, 6, '2025-02-13 05:54:34', 623.00, '1', 1),
(1739426459, 6, '2025-02-13 06:00:59', 1500.00, '1', 1),
(1739426499, 1738895546, '2025-02-13 06:01:39', 2123.00, '1', 1),
(1739426533, 3, '2025-02-13 06:02:13', 1750.50, '1', 1),
(1740541712, 6, '2025-02-26 03:48:32', 2123.00, '1', 1),
(1740541788, 6, '2025-02-26 03:49:48', 2273.50, '1', 1),
(1740541796, 6, '2025-02-26 03:49:56', 2273.50, '1', 1),
(1740542648, 3, '2025-02-26 04:04:08', 1623.00, '0', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(23, 3, 6, 12, 450.00),
(26, 5, 8, 13, 500.00),
(36, 1738732062, 1, 1, 100.00),
(37, 1738732062, 6, 1, 1500.00),
(38, 1738732563, 1, 1, 100.00),
(39, 1738732563, 2, 1, 150.50),
(40, 1738732643, 9, 1, 500.00),
(41, 1738732738, 9, 1, 500.00),
(42, 1738818886, 8, 1, 123.00),
(43, 1738818886, 9, 1, 500.00),
(44, 1738818896, 1, 1, 100.00),
(45, 1738818896, 2, 1, 150.50),
(46, 1738818896, 6, 1, 1500.00),
(47, 1738895546, 1, 1, 100.00),
(48, 1738895546, 2, 1, 150.50),
(49, 1739250762, 1, 1, 100.00),
(50, 1739250762, 2, 1, 150.50),
(51, 1739250762, 6, 1, 1500.00),
(52, 1739423871, 6, 1, 1500.00),
(53, 1739423871, 8, 1, 123.00),
(54, 1739423871, 9, 1, 500.00),
(55, 1739424654, 6, 1, 1500.00),
(56, 1739424654, 8, 1, 123.00),
(57, 1739424654, 9, 1, 500.00),
(58, 1739424731, 1, 1, 100.00),
(59, 1739424731, 2, 1, 150.50),
(60, 1739424751, 6, 1, 1500.00),
(61, 1739424751, 8, 1, 123.00),
(62, 1739424751, 9, 1, 500.00),
(63, 1739425148, 6, 1, 1500.00),
(64, 1739426074, 8, 1, 123.00),
(65, 1739426074, 9, 1, 500.00),
(66, 1739426459, 6, 1, 1500.00),
(67, 1739426499, 6, 1, 1500.00),
(68, 1739426499, 8, 1, 123.00),
(69, 1739426499, 9, 1, 500.00),
(70, 1739426533, 1, 1, 100.00),
(71, 1739426533, 2, 1, 150.50),
(72, 1739426533, 6, 1, 1500.00),
(73, 1740541712, 6, 1, 1500.00),
(74, 1740541712, 8, 1, 123.00),
(75, 1740541712, 9, 1, 500.00),
(76, 1740541788, 2, 1, 150.50),
(77, 1740541788, 6, 1, 1500.00),
(78, 1740541788, 8, 1, 123.00),
(79, 1740541788, 9, 1, 500.00),
(80, 1740541796, 2, 1, 150.50),
(81, 1740541796, 6, 1, 1500.00),
(82, 1740541796, 8, 1, 123.00),
(83, 1740541796, 9, 1, 500.00),
(84, 1740542648, 6, 1, 1500.00),
(85, 1740542648, 8, 1, 123.00);

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`) VALUES
(1, 'Говно', 'Opisanie tovara 1', 100.00, 50),
(2, 'Tovar 2', 'Opisanie tovara 2', 150.50, 30),
(6, 'какашки', 'очень вкусные', 1500.00, 75),
(8, 'товарик3', 'не будет', 123.00, 15),
(9, 'стул', 'кожанный', 500.00, 30),
(10, 'чмо', 'Пидар', 15000.00, 5);

-- --------------------------------------------------------

--
-- Структура таблицы `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `type` enum('tech','crm') NOT NULL,
  `message` varchar(256) DEFAULT NULL,
  `clients` int(11) NOT NULL,
  `admin` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `tickets`
--

INSERT INTO `tickets` (`id`, `type`, `message`, `clients`, `admin`, `created_at`) VALUES
(1, 'tech', 'Чмо', 1, 1, '2025-03-10 04:03:14'),
(2, 'crm', 'Иди нахуй', 6, 1, '2025-03-09 04:03:14'),
(3, 'crm', 'Гей', 1, 1, '2025-03-10 04:50:17'),
(4, 'crm', 'Гей', 1, 1, '2025-03-10 04:51:22');

-- --------------------------------------------------------

--
-- Структура таблицы `ticket_messages`
--

CREATE TABLE `ticket_messages` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(256) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `ticket_messages`
--

INSERT INTO `ticket_messages` (`id`, `ticket_id`, `user_id`, `message`, `created_at`) VALUES
(1, 1, 1, 'Гей', '2025-03-03 04:10:43');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(256) NOT NULL,
  `token` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `name`, `surname`, `token`) VALUES
(1, 'admin', 'admin123', 'Administrator', 'kitchen', 'bG9naW49YWRtaW4mcGFzc3dvcmQ9YWRtaW4xMjMmdW5pcXVlPTE3Mzk0OTgxOTk='),
(2, 'manager', 'manager456', 'Manager', '', ''),
(3, 'sales', 'sales789', 'Sales Representative', '', '');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `orders_ibfk_1` (`admin`);

--
-- Индексы таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `ticket_messages`
--
ALTER TABLE `ticket_messages`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1738895549;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1740542649;

--
-- AUTO_INCREMENT для таблицы `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `ticket_messages`
--
ALTER TABLE `ticket_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`admin`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
