-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 13, 2026 at 09:19 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rms_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventories`
--

CREATE TABLE `inventories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit` varchar(255) NOT NULL,
  `min_quantity` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cost_per_unit` decimal(8,2) NOT NULL DEFAULT 0.00,
  `supplier` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventories`
--

INSERT INTO `inventories` (`id`, `item_name`, `category`, `quantity`, `unit`, `min_quantity`, `cost_per_unit`, `supplier`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Basmati Rice', 'Rice & Grains', 80.00, 'kg', 20.00, 155.00, 'Karwan Bazar Rice Traders', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(2, 'Chinigura Rice', 'Rice & Grains', 45.00, 'kg', 12.00, 145.00, 'Karwan Bazar Rice Traders', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(3, 'Miniket Rice', 'Rice & Grains', 90.00, 'kg', 25.00, 78.00, 'Karwan Bazar Rice Traders', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(4, 'Chicken', 'Meat', 35.00, 'kg', 10.00, 230.00, 'Kaptan Bazar Poultry', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(5, 'Beef', 'Meat', 28.00, 'kg', 8.00, 780.00, 'Local Meat Supplier', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(6, 'Mutton', 'Meat', 18.00, 'kg', 5.00, 1150.00, 'Local Meat Supplier', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(7, 'Rui Fish', 'Seafood', 24.00, 'kg', 6.00, 360.00, 'Jatrabari Fish Market', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(8, 'Prawns', 'Seafood', 16.00, 'kg', 5.00, 760.00, 'Jatrabari Fish Market', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(9, 'Potatoes', 'Produce', 60.00, 'kg', 15.00, 45.00, 'Shyambazar Produce', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(10, 'Onions', 'Produce', 55.00, 'kg', 15.00, 85.00, 'Shyambazar Produce', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(11, 'Garlic', 'Produce', 18.00, 'kg', 5.00, 190.00, 'Shyambazar Produce', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(12, 'Ginger', 'Produce', 16.00, 'kg', 4.00, 210.00, 'Shyambazar Produce', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(13, 'Tomatoes', 'Produce', 30.00, 'kg', 8.00, 70.00, 'Shyambazar Produce', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(14, 'Green Chili', 'Produce', 10.00, 'kg', 3.00, 130.00, 'Shyambazar Produce', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(15, 'Mixed Vegetables', 'Produce', 35.00, 'kg', 10.00, 80.00, 'Shyambazar Produce', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(16, 'Chickpeas', 'Pantry', 25.00, 'kg', 8.00, 120.00, 'Moulvibazar Grocery', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(17, 'Flour', 'Pantry', 50.00, 'kg', 15.00, 65.00, 'Moulvibazar Grocery', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(18, 'Sugar', 'Pantry', 45.00, 'kg', 12.00, 130.00, 'Moulvibazar Grocery', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(19, 'Salt', 'Pantry', 20.00, 'kg', 5.00, 42.00, 'Moulvibazar Grocery', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(20, 'Cooking Oil', 'Pantry', 70.00, 'L', 18.00, 175.00, 'Moulvibazar Grocery', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(21, 'Mustard Oil', 'Pantry', 24.00, 'L', 6.00, 280.00, 'Moulvibazar Grocery', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(22, 'Ghee', 'Dairy', 12.00, 'kg', 3.00, 950.00, 'Local Dairy Supplier', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(23, 'Milk', 'Dairy', 60.00, 'L', 15.00, 90.00, 'Local Dairy Supplier', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(24, 'Yogurt', 'Dairy', 35.00, 'kg', 8.00, 160.00, 'Local Dairy Supplier', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(25, 'Cream', 'Dairy', 15.00, 'L', 4.00, 420.00, 'Local Dairy Supplier', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(26, 'Paneer', 'Dairy', 12.00, 'kg', 3.00, 520.00, 'Local Dairy Supplier', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(27, 'Biryani Masala', 'Spices', 12.00, 'kg', 3.00, 620.00, 'Moulvibazar Spice House', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(28, 'Garam Masala', 'Spices', 8.00, 'kg', 2.00, 700.00, 'Moulvibazar Spice House', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(29, 'Turmeric Powder', 'Spices', 8.00, 'kg', 2.00, 260.00, 'Moulvibazar Spice House', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(30, 'Chili Powder', 'Spices', 8.00, 'kg', 2.00, 360.00, 'Moulvibazar Spice House', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(31, 'Cumin Powder', 'Spices', 8.00, 'kg', 2.00, 520.00, 'Moulvibazar Spice House', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(32, 'Cardamom', 'Spices', 3.00, 'kg', 1.00, 2200.00, 'Moulvibazar Spice House', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(33, 'Cinnamon', 'Spices', 4.00, 'kg', 1.00, 850.00, 'Moulvibazar Spice House', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(34, 'Bay Leaf', 'Spices', 2.00, 'kg', 1.00, 420.00, 'Moulvibazar Spice House', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(35, 'Tamarind', 'Pantry', 10.00, 'kg', 3.00, 180.00, 'Moulvibazar Grocery', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(36, 'Mint Leaves', 'Produce', 5.00, 'kg', 1.00, 160.00, 'Shyambazar Produce', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(37, 'Lemons', 'Produce', 18.00, 'kg', 5.00, 110.00, 'Shyambazar Produce', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(38, 'Tea Leaves', 'Beverages', 10.00, 'kg', 3.00, 520.00, 'Sylhet Tea Supplier', NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(39, 'vab', NULL, 1.00, '2', 2.00, 190.00, 'ccad', 'dsfasdfads', '2026-09-13 13:16:25', '2026-09-13 13:16:25');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(8,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `name`, `category`, `description`, `price`, `image`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 'Chicken Biryani', 'Rice & Biryani', 'Aromatic basmati rice cooked with chicken, potato, and house biryani spices', 260.00, 'menu-images/chicken-biryani.jpg', 1, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(2, 'Beef Tehari', 'Rice & Biryani', 'Dhaka-style tehari with mustard oil, tender beef, and fragrant rice', 240.00, 'menu-images/beef-tehari.jpg', 1, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(3, 'Kacchi Biryani', 'Rice & Biryani', 'Slow-cooked mutton kacchi with basmati rice, potato, and special masala', 380.00, 'menu-images/kacchi-biryani.jpg', 1, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(4, 'Morog Polao', 'Rice & Biryani', 'Traditional chicken polao with ghee, spices, and soft rice', 300.00, 'menu-images/morog-polao.jpg', 1, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(5, 'Plain Rice', 'Rice & Biryani', 'Steamed rice served fresh for curry meals', 50.00, 'menu-images/plain-rice.jpg', 1, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(6, 'Chicken Curry', 'Curry & Bhuna', 'Home-style chicken curry with onion, garlic, ginger, and warm spices', 180.00, 'menu-images/chicken-curry.jpg', 1, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(7, 'Beef Bhuna', 'Curry & Bhuna', 'Slow-cooked beef bhuna with thick masala gravy', 250.00, 'menu-images/beef-bhuna.jpg', 1, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(8, 'Mutton Rezala', 'Curry & Bhuna', 'Rich mutton rezala cooked with yogurt, ghee, and mild spices', 360.00, 'menu-images/mutton-rezala.jpg', 1, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(9, 'Rui Fish Curry', 'Curry & Bhuna', 'Fresh rui fish curry with mustard oil and Bengali spices', 220.00, 'menu-images/rui-fish-curry.jpg', 1, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(10, 'Prawn Malai Curry', 'Curry & Bhuna', 'Prawns cooked in coconut milk with a mild creamy gravy', 320.00, 'menu-images/prawn-malai-curry.jpg', 1, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(11, 'Mixed Vegetable Bhaji', 'Curry & Bhuna', 'Seasonal vegetables stir-fried with onion, chili, and spices', 120.00, 'menu-images/mixed-vegetable-bhaji.jpg', 1, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(12, 'Chicken Shingara', 'Snacks', 'Crispy pastry filled with spiced chicken and potato', 35.00, 'menu-images/chicken-shingara.jpg', 1, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(13, 'Vegetable Samosa', 'Snacks', 'Golden samosa filled with mixed vegetables and light spices', 25.00, 'menu-images/vegetable-samosa.jpg', 1, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(14, 'Chicken Roll', 'Snacks', 'Paratha wrap with chicken, salad, and house sauce', 120.00, 'menu-images/chicken-roll.jpg', 1, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(15, 'Fuchka Plate', 'Snacks', 'Crispy fuchka served with chickpea filling and tamarind water', 100.00, 'menu-images/fuchka-plate.jpg', 1, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(16, 'Firni', 'Desserts', 'Creamy rice pudding flavored with cardamom and milk', 80.00, 'menu-images/firni.jpg', 1, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(17, 'Mishti Doi', 'Desserts', 'Traditional sweet yogurt served chilled', 70.00, 'menu-images/mishti-doi.jpg', 1, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(18, 'Rasgulla', 'Desserts', 'Soft cheese balls soaked in light sugar syrup', 60.00, 'menu-images/rasgulla.jpg', 1, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(19, 'Borhani', 'Beverages', 'Spiced yogurt drink served chilled with biryani meals', 80.00, 'menu-images/borhani.jpg', 1, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(20, 'Lassi', 'Beverages', 'Sweet yogurt drink blended with milk and sugar', 90.00, 'menu-images/lassi.jpg', 1, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(21, 'Lemon Mint', 'Beverages', 'Fresh lemon drink with mint, sugar, and chilled water', 70.00, 'menu-images/lemon-mint.jpg', 1, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(22, 'Milk Tea', 'Beverages', 'Classic Bangladeshi milk tea', 30.00, 'menu-images/milk-tea.jpg', 1, '2026-09-13 12:42:08', '2026-09-13 12:42:08');

-- --------------------------------------------------------

--
-- Table structure for table `menu_ingredients`
--

CREATE TABLE `menu_ingredients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_id` bigint(20) UNSIGNED NOT NULL,
  `quantity_per_dish` decimal(10,4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_ingredients`
--

INSERT INTO `menu_ingredients` (`id`, `menu_id`, `inventory_id`, `quantity_per_dish`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 0.1800, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(2, 1, 4, 0.2200, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(3, 1, 9, 0.1000, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(4, 1, 10, 0.0500, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(5, 1, 27, 0.0150, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(6, 1, 22, 0.0150, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(7, 2, 2, 0.1800, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(8, 2, 5, 0.1600, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(9, 2, 21, 0.0200, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(10, 2, 10, 0.0600, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(11, 2, 28, 0.0080, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(12, 3, 1, 0.2000, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(13, 3, 6, 0.2200, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(14, 3, 9, 0.1200, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(15, 3, 24, 0.0500, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(16, 3, 27, 0.0180, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(17, 3, 22, 0.0200, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(18, 4, 2, 0.1800, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(19, 4, 4, 0.2500, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(20, 4, 22, 0.0200, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(21, 4, 10, 0.0500, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(22, 4, 28, 0.0080, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(23, 5, 3, 0.1800, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(24, 6, 4, 0.2200, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(25, 6, 10, 0.0600, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(26, 6, 11, 0.0100, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(27, 6, 12, 0.0100, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(28, 6, 20, 0.0250, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(29, 6, 29, 0.0040, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(30, 6, 30, 0.0040, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(31, 7, 5, 0.2000, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(32, 7, 10, 0.0800, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(33, 7, 11, 0.0120, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(34, 7, 12, 0.0120, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(35, 7, 21, 0.0200, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(36, 7, 28, 0.0060, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(37, 8, 6, 0.2200, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(38, 8, 24, 0.0600, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(39, 8, 25, 0.0300, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(40, 8, 22, 0.0180, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(41, 8, 32, 0.0020, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(42, 8, 33, 0.0020, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(43, 9, 7, 0.2500, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(44, 9, 21, 0.0200, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(45, 9, 10, 0.0500, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(46, 9, 13, 0.0600, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(47, 9, 29, 0.0040, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(48, 10, 8, 0.2000, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(49, 10, 25, 0.0600, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(50, 10, 10, 0.0500, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(51, 10, 12, 0.0080, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(52, 10, 28, 0.0050, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(53, 11, 15, 0.2200, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(54, 11, 10, 0.0400, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(55, 11, 14, 0.0100, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(56, 11, 20, 0.0200, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(57, 11, 29, 0.0030, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(58, 12, 17, 0.0600, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(59, 12, 4, 0.0500, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(60, 12, 9, 0.0500, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(61, 12, 20, 0.0350, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(62, 13, 17, 0.0500, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(63, 13, 15, 0.0700, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(64, 13, 9, 0.0400, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(65, 13, 20, 0.0300, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(66, 14, 17, 0.1000, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(67, 14, 4, 0.1200, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(68, 14, 10, 0.0300, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(69, 14, 20, 0.0200, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(70, 15, 17, 0.0700, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(71, 15, 16, 0.1000, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(72, 15, 9, 0.0600, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(73, 15, 35, 0.0400, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(74, 16, 2, 0.0400, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(75, 16, 23, 0.2500, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(76, 16, 18, 0.0400, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(77, 16, 32, 0.0010, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(78, 17, 24, 0.1800, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(79, 17, 18, 0.0400, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(80, 18, 26, 0.0800, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(81, 18, 18, 0.0600, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(82, 19, 24, 0.1800, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(83, 19, 36, 0.0060, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(84, 19, 31, 0.0030, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(85, 19, 19, 0.0020, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(86, 20, 24, 0.1600, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(87, 20, 23, 0.0800, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(88, 20, 18, 0.0300, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(89, 21, 37, 0.0800, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(90, 21, 36, 0.0060, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(91, 21, 18, 0.0250, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(92, 22, 38, 0.0060, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(93, 22, 23, 0.0800, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(94, 22, 18, 0.0150, '2026-09-13 12:42:09', '2026-09-13 12:42:09');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2026_03_31_000001_add_role_to_users_table', 1),
(6, '2026_03_31_000002_create_menus_table', 1),
(7, '2026_03_31_000003_create_tables_table', 1),
(8, '2026_03_31_000004_create_orders_table', 1),
(9, '2026_03_31_000005_create_order_items_table', 1),
(10, '2026_03_31_000006_create_inventories_table', 1),
(11, '2026_03_31_000007_add_payment_fields_to_orders_table', 1),
(12, '2026_04_01_000008_add_inventory_deducted_at_to_orders_table', 1),
(13, '2026_04_01_000009_create_menu_ingredients_table', 1),
(14, '2026_04_09_000010_create_payment_transactions_table', 1),
(15, '2026_04_15_120000_add_customer_order_fields_to_orders_table', 1),
(16, '2026_05_01_225315_create_permission_tables', 1),
(17, '2026_05_01_230500_backfill_spatie_roles_from_user_role_column', 1),
(18, '2026_05_12_111554_update_menu_ingredients_quantity_precision', 1),
(19, '2026_05_12_create_site_settings_table', 1),
(20, '2026_05_29_000002_add_address_to_users_table', 1),
(21, '2026_05_30_000001_add_about_image_to_site_settings_table', 1),
(22, '2026_05_30_000001_add_reserved_requirements_to_orders', 1),
(23, '2026_05_30_000011_add_invoice_number_to_payment_transactions_table', 1),
(24, '2026_05_31_000012_create_ssl_example_orders_table', 1),
(25, '2026_05_31_000013_add_session_key_to_ssl_example_orders_table', 1),
(26, '2026_09_10_200000_create_reservations_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 3),
(4, 'App\\Models\\User', 4),
(5, 'App\\Models\\User', 5),
(5, 'App\\Models\\User', 6),
(5, 'App\\Models\\User', 7);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(255) NOT NULL,
  `table_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_status` varchar(255) NOT NULL DEFAULT 'unpaid',
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_reference` varchar(255) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `paid_at` timestamp NULL DEFAULT NULL,
  `inventory_deducted_at` timestamp NULL DEFAULT NULL,
  `reserved_requirements` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`reserved_requirements`)),
  `reserved_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `order_source` varchar(255) NOT NULL DEFAULT 'staff',
  `is_customer_approved` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `table_id`, `user_id`, `status`, `payment_status`, `payment_method`, `payment_reference`, `total_amount`, `paid_amount`, `paid_at`, `inventory_deducted_at`, `reserved_requirements`, `reserved_at`, `notes`, `order_source`, `is_customer_approved`, `created_at`, `updated_at`) VALUES
(1, 'ORD-20260913-0001', 3, 2, 'completed', 'unpaid', NULL, NULL, 1140.00, 0.00, NULL, NULL, NULL, NULL, 'No onions please', 'staff', 1, '2026-09-11 13:42:08', '2026-09-13 12:42:08'),
(2, 'ORD-20260913-0002', 7, 2, 'completed', 'unpaid', NULL, NULL, 680.00, 0.00, NULL, NULL, NULL, NULL, NULL, 'staff', 1, '2026-09-11 10:42:08', '2026-09-13 12:42:08'),
(3, 'ORD-20260913-0003', 4, 4, 'completed', 'unpaid', NULL, NULL, 1510.00, 0.00, NULL, NULL, NULL, NULL, NULL, 'staff', 1, '2026-09-12 22:42:08', '2026-09-13 12:42:08'),
(4, 'ORD-20260913-0004', 5, 4, 'completed', 'unpaid', NULL, NULL, 800.00, 0.00, NULL, NULL, NULL, NULL, NULL, 'staff', 1, '2026-09-11 02:42:08', '2026-09-13 12:42:08'),
(5, 'ORD-20260913-0005', 4, 4, 'served', 'unpaid', NULL, NULL, 1470.00, 0.00, NULL, NULL, NULL, NULL, NULL, 'staff', 1, '2026-09-11 09:42:08', '2026-09-13 12:42:08'),
(6, 'ORD-20260913-0006', 7, 2, 'preparing', 'unpaid', NULL, NULL, 1810.00, 0.00, NULL, NULL, NULL, NULL, NULL, 'staff', 1, '2026-09-10 20:42:08', '2026-09-13 12:42:08'),
(7, 'ORD-20260913-0007', 6, 2, 'pending', 'unpaid', NULL, NULL, 800.00, 0.00, NULL, NULL, NULL, NULL, NULL, 'staff', 1, '2026-09-12 12:42:08', '2026-09-13 12:42:09'),
(8, 'ORD-20260913-0021', 11, 5, 'preparing', 'paid', 'bkash', NULL, 510.00, 510.00, '2026-09-13 11:42:09', NULL, NULL, NULL, 'Please call on arrival', 'customer', 1, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(9, 'ORD-20260913-0022', 11, 6, 'pending', 'unpaid', 'cash', NULL, 570.00, 0.00, NULL, NULL, NULL, NULL, 'Please call on arrival', 'customer', 0, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(10, 'ORD-20260913-0023', 11, 7, 'completed', 'paid', 'card', NULL, 310.00, 310.00, '2026-09-13 09:42:09', NULL, NULL, NULL, 'Please call on arrival', 'customer', 1, '2026-09-13 12:42:09', '2026-09-13 12:42:09');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(8,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_id`, `quantity`, `unit_price`, `subtotal`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 3, 260.00, 780.00, NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(2, 1, 6, 2, 180.00, 360.00, NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(3, 2, 22, 3, 30.00, 90.00, NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(4, 2, 1, 2, 260.00, 520.00, NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(5, 2, 12, 2, 35.00, 70.00, NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(6, 3, 3, 3, 380.00, 1140.00, NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(7, 3, 7, 1, 250.00, 250.00, NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(8, 3, 18, 2, 60.00, 120.00, NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(9, 4, 21, 2, 70.00, 140.00, NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(10, 4, 11, 1, 120.00, 120.00, NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(11, 4, 10, 1, 320.00, 320.00, NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(12, 4, 9, 1, 220.00, 220.00, NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(13, 5, 10, 2, 320.00, 640.00, NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(14, 5, 17, 1, 70.00, 70.00, NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(15, 5, 20, 1, 90.00, 90.00, NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(16, 5, 1, 2, 260.00, 520.00, NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(17, 5, 5, 3, 50.00, 150.00, NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(18, 6, 16, 2, 80.00, 160.00, NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(19, 6, 8, 2, 360.00, 720.00, NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(20, 6, 20, 1, 90.00, 90.00, NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(21, 6, 2, 3, 240.00, 720.00, NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(22, 6, 18, 2, 60.00, 120.00, NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(23, 7, 14, 2, 120.00, 240.00, NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(24, 7, 17, 2, 70.00, 140.00, NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(25, 7, 18, 2, 60.00, 120.00, NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(26, 7, 15, 3, 100.00, 300.00, NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(27, 8, 1, 1, 260.00, 260.00, NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(28, 8, 7, 1, 250.00, 250.00, NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(29, 9, 3, 1, 380.00, 380.00, NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(30, 9, 11, 1, 120.00, 120.00, NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(31, 9, 21, 1, 70.00, 70.00, NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(32, 10, 2, 1, 240.00, 240.00, NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(33, 10, 17, 1, 70.00, 70.00, NULL, '2026-09-13 12:42:09', '2026-09-13 12:42:09');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `gateway` varchar(255) NOT NULL DEFAULT 'sslcommerz',
  `invoice_number` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'initiated',
  `payment_method` varchar(255) DEFAULT NULL,
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_response`)),
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `table_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `reservation_date` date NOT NULL,
  `time_slot` varchar(10) NOT NULL,
  `party_size` tinyint(3) UNSIGNED NOT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `table_id`, `name`, `phone`, `email`, `reservation_date`, `time_slot`, `party_size`, `notes`, `status`, `created_at`, `updated_at`) VALUES
(1, 5, 2, 'Rahim Uddin', '01819-221100', 'rahim@customer.com', '2026-09-14', '19:00', 4, 'Window table if possible', 'confirmed', '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(2, 6, 9, 'Fatima Khan', '01912-334455', 'fatima@customer.com', '2026-09-15', '20:00', 6, 'Birthday dinner', 'pending', '2026-09-13 12:42:09', '2026-09-13 12:42:09'),
(3, 7, 6, 'Tanvir Hasan', '01617-556677', 'tanvir@customer.com', '2026-09-16', '13:00', 3, NULL, 'confirmed', '2026-09-13 12:42:09', '2026-09-13 12:42:09');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2026-09-13 12:42:07', '2026-09-13 12:42:07'),
(2, 'manager', 'web', '2026-09-13 12:42:07', '2026-09-13 12:42:07'),
(3, 'chef', 'web', '2026-09-13 12:42:07', '2026-09-13 12:42:07'),
(4, 'cashier', 'web', '2026-09-13 12:42:07', '2026-09-13 12:42:07'),
(5, 'customer', 'web', '2026-09-13 12:42:07', '2026-09-13 12:42:07');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `website_name` varchar(255) NOT NULL DEFAULT 'Restaurant Management System',
  `website_tagline` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `hero_badge` varchar(255) NOT NULL DEFAULT 'Open Today',
  `hero_title` varchar(255) NOT NULL DEFAULT 'Fresh Flavors,',
  `hero_accent` varchar(255) NOT NULL DEFAULT 'Fired Daily.',
  `hero_subtitle` text NOT NULL DEFAULT 'Bold dishes crafted from seasonal ingredients. Order online, track your meal, and enjoy a warm dining experience every visit.',
  `hero_background_image` varchar(255) DEFAULT NULL,
  `about_image` varchar(255) DEFAULT NULL,
  `primary_color` varchar(255) NOT NULL DEFAULT '#FF6B35',
  `secondary_color` varchar(255) NOT NULL DEFAULT '#004E89',
  `accent_color` varchar(255) NOT NULL DEFAULT '#F7C59F',
  `phone_number` varchar(255) DEFAULT NULL,
  `email_address` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `opening_hours` varchar(255) DEFAULT NULL,
  `about_us` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `website_name`, `website_tagline`, `logo`, `hero_badge`, `hero_title`, `hero_accent`, `hero_subtitle`, `hero_background_image`, `about_image`, `primary_color`, `secondary_color`, `accent_color`, `phone_number`, `email_address`, `address`, `opening_hours`, `about_us`, `created_at`, `updated_at`) VALUES
(1, 'Spice Garden', 'Home-style Bangladeshi kitchen in Dhaka', NULL, 'Open today · 11:00 AM – 11:00 PM', 'bandhan, rifat', 'Cooked Fresh.', 'Order kacchi, tehari, and home-style curries online, reserve a table, or walk in for a warm meal in Dhanmondi.', NULL, NULL, '#ff6b35', '#004e89', '#f7c59f', '01711-445566', 'hello@spicegarden.test', 'House 27, Road 8, Dhanmondi, Dhaka 1205', 'Sat–Thu 11:00 AM – 11:00 PM · Friday 3:00 PM – 11:00 PM', 'Spice Garden is a neighbourhood kitchen serving Dhaka-style biryani, bhuna, and sweets. We cook in small batches, keep a live inventory for the kitchen, and take both dine-in and online orders.', '2026-09-13 12:42:09', '2026-09-13 13:07:08');

-- --------------------------------------------------------

--
-- Table structure for table `ssl_example_orders`
--

CREATE TABLE `ssl_example_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `main_order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `session_key` varchar(255) DEFAULT NULL,
  `currency` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `table_number` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'available',
  `location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`id`, `table_number`, `capacity`, `status`, `location`, `created_at`, `updated_at`) VALUES
(1, 'T-01', 2, 'available', 'Indoor', '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(2, 'T-02', 4, 'available', 'Indoor', '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(3, 'T-03', 4, 'available', 'Indoor', '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(4, 'T-04', 6, 'available', 'Indoor', '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(5, 'T-05', 4, 'available', 'Indoor', '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(6, 'T-06', 4, 'available', 'Outdoor', '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(7, 'T-07', 6, 'available', 'Outdoor', '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(8, 'T-08', 2, 'reserved', 'Outdoor', '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(9, 'T-09', 8, 'occupied', 'VIP', '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(10, 'T-10', 6, 'maintenance', 'VIP', '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(11, 'ONLINE', 0, 'available', 'Delivery', '2026-09-13 12:42:09', '2026-09-13 12:42:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'waiter',
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `phone`, `address`, `is_active`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@restaurant.com', 'admin', '555-0100', 'Gulshan 2, Dhaka', 1, NULL, '$2y$12$f0TbJL8nDfQc6Z325BRV2uDOduTgv7E/dr0aCohKtTSCwx.y5oH8O', NULL, '2026-09-13 12:42:07', '2026-09-13 13:19:32'),
(2, 'Sarah Ahmed', 'sarah@restaurant.com', 'manager', '01711-100101', 'Banani, Dhaka', 1, NULL, '$2y$12$dr7dTGy.zW4fQEPICI6qGeGUa8Gyyk792NZWC/tEl9EWfHT4UHoqC', NULL, '2026-09-13 12:42:07', '2026-09-13 12:42:07'),
(3, 'Chef Kamal Hossain', 'marco@restaurant.com', 'chef', '01711-100104', 'Mohammadpur, Dhaka', 1, NULL, '$2y$12$ieMfWFGISXHTB5i.x3OsRuSw7t0I1ALByohdqJPnWS5aCgvFjQMpq', NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(4, 'Lamia Chowdhury', 'lisa@restaurant.com', 'cashier', '01711-100105', 'Dhanmondi, Dhaka', 1, NULL, '$2y$12$.hMfyPnYt32rxSr13iCAjO6smOZwPitgNd6lvv8q86mHw.3CRA4li', NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(5, 'Rahim Uddin', 'rahim@customer.com', 'customer', '01819-221100', 'House 12, Road 4, Dhanmondi, Dhaka', 1, NULL, '$2y$12$7g2rhQDEpBB3IVsCXXGnBOc3UwrOlL8SM5FM4L1dnO3xmKW6J2Pgq', NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(6, 'Fatima Khan', 'fatima@customer.com', 'customer', '01912-334455', 'Flat 5B, Banani DOHS, Dhaka', 1, NULL, '$2y$12$rxgjIzF8vOWLuoq2QZWBzO6Pndi7WDMl7NslTjfyA80zWqYgeH90q', NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08'),
(7, 'Tanvir Hasan', 'tanvir@customer.com', 'customer', '01617-556677', 'Block C, Bashundhara R/A, Dhaka', 1, NULL, '$2y$12$pQDOlS9M7GGg5SKLWnWB/.9txbFk1DSzcLFlXu52eNTM.eTt/iHmS', NULL, '2026-09-13 12:42:08', '2026-09-13 12:42:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `inventories`
--
ALTER TABLE `inventories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_ingredients`
--
ALTER TABLE `menu_ingredients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `menu_ingredients_menu_id_inventory_id_unique` (`menu_id`,`inventory_id`),
  ADD KEY `menu_ingredients_inventory_id_foreign` (`inventory_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_table_id_foreign` (`table_id`),
  ADD KEY `orders_user_id_foreign` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_menu_id_foreign` (`menu_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_transactions_transaction_id_unique` (`transaction_id`),
  ADD UNIQUE KEY `payment_transactions_invoice_number_unique` (`invoice_number`),
  ADD KEY `payment_transactions_order_id_status_index` (`order_id`,`status`),
  ADD KEY `payment_transactions_gateway_transaction_id_index` (`gateway`,`transaction_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservations_user_id_foreign` (`user_id`),
  ADD KEY `reservations_table_id_foreign` (`table_id`),
  ADD KEY `reservations_reservation_date_time_slot_status_index` (`reservation_date`,`time_slot`,`status`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ssl_example_orders`
--
ALTER TABLE `ssl_example_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ssl_example_orders_transaction_id_unique` (`transaction_id`),
  ADD KEY `ssl_example_orders_user_id_foreign` (`user_id`),
  ADD KEY `ssl_example_orders_main_order_id_foreign` (`main_order_id`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tables_table_number_unique` (`table_number`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventories`
--
ALTER TABLE `inventories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `menu_ingredients`
--
ALTER TABLE `menu_ingredients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ssl_example_orders`
--
ALTER TABLE `ssl_example_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menu_ingredients`
--
ALTER TABLE `menu_ingredients`
  ADD CONSTRAINT `menu_ingredients_inventory_id_foreign` FOREIGN KEY (`inventory_id`) REFERENCES `inventories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `menu_ingredients_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_table_id_foreign` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD CONSTRAINT `payment_transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_table_id_foreign` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reservations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ssl_example_orders`
--
ALTER TABLE `ssl_example_orders`
  ADD CONSTRAINT `ssl_example_orders_main_order_id_foreign` FOREIGN KEY (`main_order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ssl_example_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
