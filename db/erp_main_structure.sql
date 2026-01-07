-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Jan 06, 2026 at 04:36 AM
-- Server version: 8.0.44
-- PHP Version: 8.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `erp_main`
--

-- --------------------------------------------------------

--
-- Table structure for table `a_public_holiday`
--

CREATE TABLE `a_public_holiday` (
  `id` int NOT NULL,
  `FisYear` int NOT NULL,
  `PublicHoliday` date NOT NULL,
  `Descripiton` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `bs_adjust_amount`
--

CREATE TABLE `bs_adjust_amount` (
  `id` int NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `date` date DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `usd` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_adjust_cost`
--

CREATE TABLE `bs_adjust_cost` (
  `id` int NOT NULL,
  `date_adjust` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `value_amount` decimal(19,4) DEFAULT NULL,
  `value_buy` decimal(19,4) DEFAULT NULL,
  `value_sell` decimal(19,4) DEFAULT NULL,
  `value_new` decimal(19,4) DEFAULT NULL,
  `value_profit` decimal(19,4) DEFAULT NULL,
  `value_adjust_cost` decimal(19,4) DEFAULT NULL,
  `value_adjust_discount` decimal(19,4) DEFAULT NULL,
  `value_net` decimal(19,4) DEFAULT NULL,
  `user` int DEFAULT NULL,
  `supplier_id` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_adjust_defer`
--

CREATE TABLE `bs_adjust_defer` (
  `id` int NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `date` date DEFAULT NULL,
  `value_adjust_type` decimal(19,4) DEFAULT NULL,
  `product_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_adjust_physical_adjust`
--

CREATE TABLE `bs_adjust_physical_adjust` (
  `id` int NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `date` date DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `usd` decimal(19,4) DEFAULT NULL,
  `thb` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_adjust_purchase`
--

CREATE TABLE `bs_adjust_purchase` (
  `id` int NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `date` date DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `usd` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_adjust_thb`
--

CREATE TABLE `bs_adjust_thb` (
  `id` int NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `date` date DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `usd` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_announce_silver`
--

CREATE TABLE `bs_announce_silver` (
  `id` int NOT NULL,
  `no` int NOT NULL,
  `created` datetime NOT NULL,
  `date` date NOT NULL,
  `rate_spot` decimal(19,4) NOT NULL,
  `rate_exchange` decimal(19,4) NOT NULL,
  `rate_pmdc` decimal(19,4) NOT NULL,
  `sell` decimal(19,4) NOT NULL,
  `buy` decimal(19,4) NOT NULL,
  `status` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_banks`
--

CREATE TABLE `bs_banks` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `number` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `branch` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `detail` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_bank_statement`
--

CREATE TABLE `bs_bank_statement` (
  `id` int NOT NULL,
  `bank_id` int DEFAULT NULL,
  `date` date DEFAULT NULL,
  `type` int DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `balance` decimal(19,4) DEFAULT NULL,
  `narrator` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `payment_id` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `approved` datetime DEFAULT NULL,
  `transfer_to` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_bwd_pack_items`
--

CREATE TABLE `bs_bwd_pack_items` (
  `id` int NOT NULL,
  `delivery_id` int DEFAULT NULL,
  `item_type` int DEFAULT NULL,
  `item_id` int DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `mapping` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_claims`
--

CREATE TABLE `bs_claims` (
  `id` int NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `date_claim` date DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `issue` varchar(1023) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `pack_problem` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pack_claim` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `detail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` int DEFAULT NULL,
  `submitted` datetime DEFAULT NULL,
  `approved` datetime DEFAULT NULL,
  `approver_id` int DEFAULT NULL,
  `rejected` datetime DEFAULT NULL,
  `solved` datetime DEFAULT NULL,
  `solver_id` int DEFAULT NULL,
  `closed` datetime DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `org_name` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_issuer` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_sender` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_sales` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `solutions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `solutions_user` int DEFAULT NULL,
  `imgs` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_coa_run`
--

CREATE TABLE `bs_coa_run` (
  `id` int NOT NULL,
  `number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `number_coc` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `order_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` date DEFAULT NULL,
  `status` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_coupons`
--

CREATE TABLE `bs_coupons` (
  `id` int NOT NULL,
  `number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` date DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `status` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_crucible_items`
--

CREATE TABLE `bs_crucible_items` (
  `id` int NOT NULL,
  `crucible_id` int NOT NULL,
  `round` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_currencies`
--

CREATE TABLE `bs_currencies` (
  `id` int NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `value` decimal(19,4) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_customers`
--

CREATE TABLE `bs_customers` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `gid` int NOT NULL,
  `contact` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fax` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `shipping_address` varchar(1023) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `billing_address` varchar(1023) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `comment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `default_sales` int DEFAULT NULL,
  `default_payment` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `default_bank` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `default_vat_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `default_pack` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `imgs` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `org_name` varchar(512) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `org_taxid` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `org_branch` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `org_address` varchar(1023) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `new_cus` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `date_newcus` varchar(4) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `coa` int DEFAULT '0',
  `po` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `date_po` date DEFAULT NULL,
  `contact_coc` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `org_name_coc` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `address_coc` varchar(1023) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `certificate_number` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `certificate_coc` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `export` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `signature` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `silvernow_no` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_customers_bwd`
--

CREATE TABLE `bs_customers_bwd` (
  `id` int NOT NULL,
  `customer_name` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `username` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `shipping_address` varchar(1023) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `billing_address` varchar(1023) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_customer_groups`
--

CREATE TABLE `bs_customer_groups` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `product_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_defer_cost`
--

CREATE TABLE `bs_defer_cost` (
  `id` int NOT NULL,
  `date_defer` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `value_defer_spot` decimal(19,4) DEFAULT NULL,
  `value_net` decimal(19,4) DEFAULT NULL,
  `defer` decimal(19,4) DEFAULT NULL,
  `user` int DEFAULT NULL,
  `supplier_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_deliveries`
--

CREATE TABLE `bs_deliveries` (
  `id` int NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `type` int DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `user` int DEFAULT NULL,
  `comment` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `billing_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `payment_note` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `delivery_time` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `delivery_method` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `emp_safekeeper` int DEFAULT NULL,
  `emp_requester` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_deliveries_bwd`
--

CREATE TABLE `bs_deliveries_bwd` (
  `id` int NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `type` int DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `user` int DEFAULT NULL,
  `comment` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `billing_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `payment_note` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `delivery_time` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `delivery_method` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `emp_safekeeper` int DEFAULT NULL,
  `emp_requester` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_deliveries_drivers`
--

CREATE TABLE `bs_deliveries_drivers` (
  `id` int NOT NULL,
  `delivery_id` int DEFAULT NULL,
  `emp_driver` int DEFAULT NULL,
  `truck_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `truck_license` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `time_departure` time DEFAULT NULL,
  `time_arrive` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_delivery_detail`
--

CREATE TABLE `bs_delivery_detail` (
  `id` int NOT NULL,
  `delivery_id` int DEFAULT NULL,
  `delivery_time_arrive` time DEFAULT NULL,
  `delivery_time_departure` time DEFAULT NULL,
  `actor_driver` int DEFAULT NULL,
  `actor_opener` int DEFAULT NULL,
  `actor_requester` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_delivery_items`
--

CREATE TABLE `bs_delivery_items` (
  `id` int NOT NULL,
  `delivery_id` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `size` decimal(19,4) DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `status` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_delivery_pack_items`
--

CREATE TABLE `bs_delivery_pack_items` (
  `id` int NOT NULL,
  `delivery_id` int DEFAULT NULL,
  `item_type` int DEFAULT NULL,
  `item_id` int DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `mapping` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_departments`
--

CREATE TABLE `bs_departments` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_employees`
--

CREATE TABLE `bs_employees` (
  `id` int NOT NULL,
  `fullname` varchar(511) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `nickname` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `signature` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `status` int DEFAULT NULL,
  `user` int DEFAULT NULL,
  `department` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_finance_static_values`
--

CREATE TABLE `bs_finance_static_values` (
  `id` int NOT NULL,
  `type` int DEFAULT NULL,
  `start` date DEFAULT NULL,
  `end` date DEFAULT NULL,
  `title` varchar(1023) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `customer_name` varchar(1023) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_fonts_bwd`
--

CREATE TABLE `bs_fonts_bwd` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_holiday`
--

CREATE TABLE `bs_holiday` (
  `id` int NOT NULL,
  `day_name` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_imports`
--

CREATE TABLE `bs_imports` (
  `id` int NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `delivery_by` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `user` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `submited` datetime DEFAULT NULL,
  `production_id` int DEFAULT NULL,
  `delivery_time` time DEFAULT NULL,
  `delivery_note` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `weight_in` decimal(19,4) DEFAULT NULL,
  `weight_actual` decimal(19,4) DEFAULT NULL,
  `weight_margin` decimal(19,4) DEFAULT NULL,
  `bar` int DEFAULT NULL,
  `weight_bar` decimal(19,4) DEFAULT NULL,
  `info_coa` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `info_coa_files` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `parent` int DEFAULT NULL,
  `transfer_id` int DEFAULT NULL,
  `combine_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_import_combine`
--

CREATE TABLE `bs_import_combine` (
  `id` int NOT NULL,
  `transfer_id` int DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_import_usd_splited`
--

CREATE TABLE `bs_import_usd_splited` (
  `id` int NOT NULL,
  `import_id` int DEFAULT NULL,
  `transfer_id` int DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_incoming_plans`
--

CREATE TABLE `bs_incoming_plans` (
  `id` int NOT NULL,
  `import_id` int DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `import_date` date DEFAULT NULL,
  `import_brand` varchar(1023) DEFAULT NULL,
  `import_lot` varchar(255) DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `rate_pmdc` decimal(19,4) DEFAULT NULL,
  `factory` varchar(255) DEFAULT NULL,
  `product_type_id` int DEFAULT NULL,
  `coa` varchar(255) DEFAULT NULL,
  `coc` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `remark` int DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `parent` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `bank_date` date DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `usd` decimal(19,4) DEFAULT NULL,
  `defer_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `bs_mapping_profit`
--

CREATE TABLE `bs_mapping_profit` (
  `id` int NOT NULL,
  `mapped` datetime DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `total` decimal(19,4) DEFAULT NULL,
  `remark` varchar(1023) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `is_checked` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_mapping_profit_bwd`
--

CREATE TABLE `bs_mapping_profit_bwd` (
  `id` int NOT NULL,
  `mapped` datetime DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `total` decimal(19,4) DEFAULT NULL,
  `remark` varchar(1023) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `is_checked` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_mapping_profit_orders`
--

CREATE TABLE `bs_mapping_profit_orders` (
  `id` int NOT NULL,
  `mapping_id` int DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `total` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_mapping_profit_orders_bwd`
--

CREATE TABLE `bs_mapping_profit_orders_bwd` (
  `id` int NOT NULL,
  `mapping_id` int DEFAULT NULL,
  `order_id` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `total` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_mapping_profit_orders_usd`
--

CREATE TABLE `bs_mapping_profit_orders_usd` (
  `id` int NOT NULL,
  `mapping_id` int DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `total` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_mapping_profit_orders_usd_bwd`
--

CREATE TABLE `bs_mapping_profit_orders_usd_bwd` (
  `id` int NOT NULL,
  `mapping_id` int DEFAULT NULL,
  `order_id` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `total` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_mapping_profit_sumusd`
--

CREATE TABLE `bs_mapping_profit_sumusd` (
  `id` int NOT NULL,
  `mapped` datetime DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `total` decimal(19,4) DEFAULT NULL,
  `remark` varchar(1023) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `is_checked` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_mapping_profit_sumusd_bwd`
--

CREATE TABLE `bs_mapping_profit_sumusd_bwd` (
  `id` int NOT NULL,
  `mapped` datetime DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `total` decimal(19,4) DEFAULT NULL,
  `remark` varchar(1023) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `is_checked` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_mapping_silvers`
--

CREATE TABLE `bs_mapping_silvers` (
  `id` int NOT NULL,
  `mapped` datetime DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `remark` varchar(1023) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_mapping_silver_orders`
--

CREATE TABLE `bs_mapping_silver_orders` (
  `id` int NOT NULL,
  `mapping_id` int DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_mapping_silver_purchases`
--

CREATE TABLE `bs_mapping_silver_purchases` (
  `id` int NOT NULL,
  `mapping_id` int DEFAULT NULL,
  `purchase_id` int DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_mapping_usd`
--

CREATE TABLE `bs_mapping_usd` (
  `id` int NOT NULL,
  `mapped` datetime DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `remark` varchar(1023) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_mapping_usd_purchases`
--

CREATE TABLE `bs_mapping_usd_purchases` (
  `id` int NOT NULL,
  `mapping_id` int DEFAULT NULL,
  `purchase_id` int DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_mapping_usd_spots`
--

CREATE TABLE `bs_mapping_usd_spots` (
  `id` int NOT NULL,
  `mapping_id` int DEFAULT NULL,
  `purchase_id` int DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `silver_item_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_match`
--

CREATE TABLE `bs_match` (
  `id` int NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `usd` decimal(19,4) DEFAULT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_match_bank`
--

CREATE TABLE `bs_match_bank` (
  `id` int NOT NULL,
  `bank` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `value` decimal(19,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_match_data`
--

CREATE TABLE `bs_match_data` (
  `id` int NOT NULL,
  `date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `data` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `bs_match_deposit`
--

CREATE TABLE `bs_match_deposit` (
  `id` int NOT NULL,
  `supplier_id` int NOT NULL,
  `usd` decimal(19,4) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_match_fx`
--

CREATE TABLE `bs_match_fx` (
  `id` int NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `usd` decimal(19,4) DEFAULT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_match_stx`
--

CREATE TABLE `bs_match_stx` (
  `id` int NOT NULL,
  `supplier_id` int NOT NULL,
  `usd` decimal(19,4) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_match_stx_add`
--

CREATE TABLE `bs_match_stx_add` (
  `id` int NOT NULL,
  `supplier_id` int NOT NULL,
  `usd` decimal(19,4) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_match_tr`
--

CREATE TABLE `bs_match_tr` (
  `id` int NOT NULL,
  `transfer_id` int NOT NULL,
  `paid` decimal(19,4) NOT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_match_usd`
--

CREATE TABLE `bs_match_usd` (
  `id` int NOT NULL,
  `bank` int NOT NULL,
  `usd` decimal(19,4) NOT NULL,
  `date` date NOT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_orders`
--

CREATE TABLE `bs_orders` (
  `id` int NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `customer_name` varchar(1023) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `sales` int DEFAULT NULL,
  `user` int DEFAULT NULL,
  `type` int DEFAULT NULL,
  `parent` int DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `price` decimal(19,4) DEFAULT NULL,
  `vat_type` int NOT NULL,
  `vat` decimal(19,4) DEFAULT NULL,
  `total` decimal(19,4) NOT NULL,
  `net` decimal(19,4) DEFAULT NULL,
  `usd` decimal(19,4) DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `delivery_time` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `lock_status` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `shipping_address` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `billing_address` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `rate_spot` decimal(19,4) DEFAULT NULL,
  `rate_exchange` decimal(19,4) DEFAULT NULL,
  `billing_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `currency` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `info_payment` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `info_contact` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `delivery_id` int DEFAULT NULL,
  `remove_reason` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `keep_silver` int DEFAULT NULL,
  `flag_hide` int NOT NULL DEFAULT '0',
  `store` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `Tracking` varchar(400) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `orderable_type` enum('delivered_by_company','post_office','receive_at_company','receive_at_luckgems','delivered_by_transport') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_orders_back_bwd`
--

CREATE TABLE `bs_orders_back_bwd` (
  `id` int NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `customer_name` varchar(1023) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `platform` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `sales` int DEFAULT NULL,
  `user` int DEFAULT NULL,
  `type` int DEFAULT NULL,
  `parent` int DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `price` decimal(19,4) DEFAULT NULL,
  `vat_type` int DEFAULT NULL,
  `vat` decimal(19,4) DEFAULT NULL,
  `total` decimal(19,4) DEFAULT NULL,
  `net` decimal(19,4) DEFAULT NULL,
  `status` int DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `engrave` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `remove_reason` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `product_type` int DEFAULT NULL,
  `product_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_orders_buy`
--

CREATE TABLE `bs_orders_buy` (
  `id` int NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `price` decimal(19,4) DEFAULT NULL,
  `vat_type` int NOT NULL,
  `vat` decimal(19,4) NOT NULL,
  `total` decimal(19,4) NOT NULL,
  `net` decimal(19,4) NOT NULL,
  `rate_spot` decimal(19,4) DEFAULT NULL,
  `rate_exchange` decimal(19,4) DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `status` int DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `sales` int DEFAULT NULL,
  `product_id` int DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_orders_bwd`
--

CREATE TABLE `bs_orders_bwd` (
  `id` int NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `customer_name` varchar(1023) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `platform` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `order_platform` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `sales` int DEFAULT NULL,
  `user` int DEFAULT NULL,
  `type` int DEFAULT NULL,
  `parent` int DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `price` decimal(19,4) DEFAULT NULL,
  `vat_type` int NOT NULL DEFAULT '2',
  `discount_type` int DEFAULT NULL,
  `discount` decimal(19,4) DEFAULT NULL,
  `fee` decimal(19,4) DEFAULT NULL,
  `total` decimal(19,4) DEFAULT NULL,
  `net` decimal(19,4) DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `delivery_time` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `lock_status` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `shipping_address` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `billing_address` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `shipping` int DEFAULT NULL,
  `shipping_base` decimal(10,2) DEFAULT '0.00' COMMENT 'ค่าส่งพื้นฐาน',
  `shipping_box_fee` decimal(10,2) DEFAULT '0.00' COMMENT 'ค่ากล่องพิเศษ',
  `shipping_remote_fee` decimal(10,2) DEFAULT '0.00' COMMENT 'ค่าพื้นที่ห่างไกล',
  `shipping_total` decimal(10,2) DEFAULT '0.00' COMMENT 'ค่าส่งรวม',
  `engrave` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ai` int DEFAULT NULL,
  `font` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `carving` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `billing_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `default_bank` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `info_payment` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `info_contact` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `delivery_id` int DEFAULT NULL,
  `remove_reason` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `product_type` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `Tracking` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `delivery_pack` int DEFAULT '0',
  `box_number` int DEFAULT '0' COMMENT 'หมายเลขกล่องสำหรับการจัดส่ง (0=กล่องที่1, 1=กล่องที่2)',
  `delivery_pack_updated` datetime DEFAULT NULL,
  `orderable_type` enum('delivered_by_company','post_office','receive_at_company','receive_at_luckgems') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `accept` int DEFAULT NULL,
  `accept_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_orders_profit`
--

CREATE TABLE `bs_orders_profit` (
  `id` int NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `customer_name` varchar(1023) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `sales` int DEFAULT NULL,
  `user` int DEFAULT NULL,
  `type` int DEFAULT NULL,
  `parent` int DEFAULT NULL,
  `is_split` tinyint(1) DEFAULT '0',
  `split_sequence` int DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `price` decimal(19,4) DEFAULT NULL,
  `vat_type` int NOT NULL,
  `vat` decimal(19,4) DEFAULT NULL,
  `total` decimal(19,4) NOT NULL,
  `net` decimal(19,4) DEFAULT NULL,
  `usd` decimal(19,4) DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `delivery_time` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `lock_status` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `shipping_address` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `billing_address` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `rate_spot` decimal(19,4) DEFAULT NULL,
  `rate_exchange` decimal(19,4) DEFAULT NULL,
  `billing_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `currency` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `info_payment` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `info_contact` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `delivery_id` int DEFAULT NULL,
  `remove_reason` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `keep_silver` int DEFAULT NULL,
  `flag_hide` int NOT NULL DEFAULT '0',
  `store` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `orderable_type` enum('delivered_by_company','post_office','receive_at_company','receive_at_luckgems','delivered_by_transport') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `order_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_orders_split_bwd`
--

CREATE TABLE `bs_orders_split_bwd` (
  `id` int NOT NULL,
  `parent_order_id` int NOT NULL COMMENT 'ID ของ order ต้นทาง',
  `split_amount` decimal(18,4) NOT NULL COMMENT 'calculated_amount ที่แยกออกมา',
  `split_total` decimal(18,2) NOT NULL COMMENT 'total ที่แยกออกมา (คำนวณจาก total/amount × split_amount)',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=active, 0=deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='เก็บข้อมูล Split Orders';

-- --------------------------------------------------------

--
-- Table structure for table `bs_order_postpone`
--

CREATE TABLE `bs_order_postpone` (
  `id` int DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `delivery_date_old` date DEFAULT NULL,
  `delivery_date_new` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `reason_customer` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `reason_company` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_order_postpone_bwd`
--

CREATE TABLE `bs_order_postpone_bwd` (
  `id` int DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `delivery_date_old` date DEFAULT NULL,
  `delivery_date_new` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `reason_customer` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `reason_company` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_order_postpone_bwd_2`
--

CREATE TABLE `bs_order_postpone_bwd_2` (
  `id` int DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `delivery_date_old` date DEFAULT NULL,
  `delivery_date_new` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `reason_customer` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `reason_company` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_over_adjust_types`
--

CREATE TABLE `bs_over_adjust_types` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `type` int DEFAULT NULL,
  `sort_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_packings`
--

CREATE TABLE `bs_packings` (
  `id` int NOT NULL,
  `production_id` int DEFAULT NULL,
  `round` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `weight_peritem` decimal(19,4) DEFAULT NULL,
  `total_item` int DEFAULT NULL,
  `total_weight` decimal(19,4) DEFAULT NULL,
  `size` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `approver_weight` int NOT NULL,
  `approver_general` int DEFAULT NULL,
  `status` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_packing_items`
--

CREATE TABLE `bs_packing_items` (
  `id` int NOT NULL,
  `production_id` int DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `pack_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `pack_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `weight_actual` decimal(19,4) DEFAULT NULL,
  `weight_expected` decimal(19,4) DEFAULT NULL,
  `parent` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `delivery_id` int DEFAULT NULL,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_payments`
--

CREATE TABLE `bs_payments` (
  `id` int NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `date_active` date DEFAULT NULL,
  `payment` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `customer_bank` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `bank_id` int DEFAULT NULL,
  `ref` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `approved` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  `approver` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_payment_deposits`
--

CREATE TABLE `bs_payment_deposits` (
  `id` int NOT NULL,
  `customer_id` int DEFAULT NULL,
  `payment_id` int DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `status` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_payment_deposit_use`
--

CREATE TABLE `bs_payment_deposit_use` (
  `id` int NOT NULL,
  `customer_id` int DEFAULT NULL,
  `payment_id` int DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `deposit_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_payment_items`
--

CREATE TABLE `bs_payment_items` (
  `id` int NOT NULL,
  `type_id` int NOT NULL,
  `payment_id` int DEFAULT NULL,
  `amount` decimal(19,2) DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `ref_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_payment_orders`
--

CREATE TABLE `bs_payment_orders` (
  `id` int NOT NULL,
  `payment_id` int DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_payment_types`
--

CREATE TABLE `bs_payment_types` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `negative` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_pmr_pack_items`
--

CREATE TABLE `bs_pmr_pack_items` (
  `id` int NOT NULL,
  `pmr_id` int DEFAULT NULL,
  `item_type` int DEFAULT NULL,
  `item_id` int DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `mapping` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_price`
--

CREATE TABLE `bs_price` (
  `id` int NOT NULL,
  `created` datetime NOT NULL,
  `date` date NOT NULL,
  `rate_spot` decimal(19,4) NOT NULL,
  `rate_exchange` decimal(19,4) NOT NULL,
  `price1` decimal(19,4) NOT NULL,
  `user` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_processing_summary`
--

CREATE TABLE `bs_processing_summary` (
  `id` int NOT NULL,
  `weight_expected` decimal(19,4) DEFAULT NULL,
  `submited` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_productions`
--

CREATE TABLE `bs_productions` (
  `id` int NOT NULL,
  `round` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `user` int DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `supplier` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `weight_in_safe` decimal(19,4) DEFAULT NULL,
  `weight_in_plate` decimal(19,4) DEFAULT NULL,
  `weight_in_nugget` decimal(19,4) DEFAULT NULL,
  `weight_in_blacknugget` decimal(19,4) DEFAULT NULL,
  `weight_in_whitedust` decimal(19,4) DEFAULT NULL,
  `weight_in_blackdust` decimal(19,4) DEFAULT NULL,
  `weight_in_refine` decimal(19,4) DEFAULT NULL,
  `weight_in_1` decimal(19,4) DEFAULT NULL,
  `weight_in_2` decimal(19,4) DEFAULT NULL,
  `weight_in_3` decimal(19,4) DEFAULT NULL,
  `weight_in_4` decimal(19,4) DEFAULT NULL,
  `weight_in_total` decimal(19,4) DEFAULT NULL,
  `weight_out_safe` decimal(19,4) DEFAULT NULL,
  `weight_out_plate` decimal(19,4) DEFAULT NULL,
  `weight_out_nugget` decimal(19,4) DEFAULT NULL,
  `weight_out_blacknugget` decimal(19,4) DEFAULT NULL,
  `weight_out_whitedust` decimal(19,4) DEFAULT NULL,
  `weight_out_blackdust` decimal(19,4) DEFAULT NULL,
  `weight_out_refine` decimal(19,4) DEFAULT NULL,
  `weight_out_packing` decimal(19,4) DEFAULT NULL,
  `weight_out_total` decimal(19,4) DEFAULT NULL,
  `weight_margin` decimal(19,4) DEFAULT NULL,
  `submited` date DEFAULT NULL,
  `delivery_license` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `delivery_driver` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `delivery_time` time DEFAULT NULL,
  `approver_appointment` time DEFAULT NULL,
  `approver_weight` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `approver_general` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `type_material` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `type_work` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `type_thaicustoms_method` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `status` int DEFAULT NULL,
  `import_date` date DEFAULT NULL,
  `import_weight_in` decimal(19,4) DEFAULT NULL,
  `import_weight_actual` decimal(19,4) DEFAULT NULL,
  `import_weight_margin` decimal(19,4) DEFAULT NULL,
  `import_bar` int DEFAULT NULL,
  `import_bar_weight` decimal(19,4) DEFAULT NULL,
  `imgs` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `data` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `product_id` int NOT NULL DEFAULT '1',
  `round_summary` int DEFAULT NULL,
  `PMR` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `product_id_out` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_productions_crucible`
--

CREATE TABLE `bs_productions_crucible` (
  `id` int NOT NULL,
  `round` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `date` date DEFAULT NULL,
  `user` int DEFAULT NULL,
  `submited` datetime DEFAULT NULL,
  `status` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_productions_furnace`
--

CREATE TABLE `bs_productions_furnace` (
  `id` int NOT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `round` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `furnace` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `crucible` int DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time_start` time DEFAULT NULL,
  `time_end` time DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submited` date DEFAULT NULL,
  `status` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_productions_in`
--

CREATE TABLE `bs_productions_in` (
  `id` int NOT NULL,
  `round` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `user` int DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `weight_out_packing` decimal(19,4) DEFAULT NULL,
  `weight_out_total` decimal(19,4) DEFAULT NULL,
  `submited` date DEFAULT NULL,
  `status` int DEFAULT NULL,
  `product_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_productions_oven`
--

CREATE TABLE `bs_productions_oven` (
  `id` int NOT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `round` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `oven` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time_start` time DEFAULT NULL,
  `time_end` time DEFAULT NULL,
  `temp` decimal(19,4) DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submited` date DEFAULT NULL,
  `status` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_productions_pmr`
--

CREATE TABLE `bs_productions_pmr` (
  `id` int NOT NULL,
  `round` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `user` int DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `weight_out_packing` decimal(19,4) DEFAULT NULL,
  `weight_out_total` decimal(19,4) DEFAULT NULL,
  `submited` date DEFAULT NULL,
  `status` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `export_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_productions_round`
--

CREATE TABLE `bs_productions_round` (
  `id` int NOT NULL,
  `import_id` int DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `import_date` date DEFAULT NULL,
  `import_brand` varchar(1023) DEFAULT NULL,
  `import_lot` varchar(255) DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `amount_balance` decimal(19,4) NOT NULL,
  `rate_pmdc` decimal(19,4) DEFAULT NULL,
  `factory` varchar(255) DEFAULT NULL,
  `product_type_id` int DEFAULT NULL,
  `coa` varchar(255) DEFAULT NULL,
  `remark` int DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `parent` int DEFAULT NULL,
  `status` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `bs_productions_scale`
--

CREATE TABLE `bs_productions_scale` (
  `id` int NOT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `round` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `approve_scale` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approve_packing` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approve_check` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `submited` date DEFAULT NULL,
  `status` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_productions_silver_save`
--

CREATE TABLE `bs_productions_silver_save` (
  `id` int NOT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `round` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bar` decimal(19,4) DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_productions_switch`
--

CREATE TABLE `bs_productions_switch` (
  `id` int NOT NULL,
  `round` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `round_turn` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `user` int DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `weight_out_packing` decimal(19,4) DEFAULT NULL,
  `weight_out_total` decimal(19,4) DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `submited` date DEFAULT NULL,
  `date_back` date DEFAULT NULL,
  `status` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `product_id_turn` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_products`
--

CREATE TABLE `bs_products` (
  `id` int NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `detail` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  `imgs` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_products_bwd`
--

CREATE TABLE `bs_products_bwd` (
  `id` int NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `detail` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `status` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_products_export`
--

CREATE TABLE `bs_products_export` (
  `id` int NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `detail` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  `imgs` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_products_import`
--

CREATE TABLE `bs_products_import` (
  `id` int NOT NULL,
  `code` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_products_turn`
--

CREATE TABLE `bs_products_turn` (
  `id` int NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `detail` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  `imgs` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_products_type`
--

CREATE TABLE `bs_products_type` (
  `id` int NOT NULL,
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `user` int DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `status` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_profit_daily`
--

CREATE TABLE `bs_profit_daily` (
  `id` int NOT NULL,
  `date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `user` int DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_purchase_buy`
--

CREATE TABLE `bs_purchase_buy` (
  `id` int NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `Type` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `ounces` decimal(19,4) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `method` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `img` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `purchase_spot` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_purchase_buyfix`
--

CREATE TABLE `bs_purchase_buyfix` (
  `id` int NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `Type` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `ounces` decimal(19,4) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `method` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `img` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `sales_spot` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_purchase_spot`
--

CREATE TABLE `bs_purchase_spot` (
  `id` int NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `rate_spot` decimal(19,4) DEFAULT NULL,
  `rate_pmdc` decimal(19,4) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `value_date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `method` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ref` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user` int DEFAULT NULL,
  `status` int DEFAULT NULL COMMENT '\r\n\r\n1-Normal\r\n2- Used\r\n',
  `comment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `confirm` datetime DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `trade_id` int DEFAULT NULL,
  `import_id` int DEFAULT NULL,
  `parent` int DEFAULT NULL,
  `transfer_id` int DEFAULT NULL,
  `adjust_id` int DEFAULT NULL,
  `adjust_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `THBValue` decimal(19,2) DEFAULT NULL,
  `flag_hide` int NOT NULL DEFAULT '0',
  `adj_supplier` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `claim` int DEFAULT NULL,
  `noted` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `defer_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_purchase_spot_profit`
--

CREATE TABLE `bs_purchase_spot_profit` (
  `id` int NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `rate_spot` decimal(19,4) DEFAULT NULL,
  `rate_pmdc` decimal(19,4) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `value_date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `method` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ref` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user` int DEFAULT NULL,
  `status` int DEFAULT NULL COMMENT '\r\n\r\n1-Normal\r\n2- Used\r\n',
  `comment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `confirm` datetime DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `trade_id` int DEFAULT NULL,
  `import_id` int DEFAULT NULL,
  `parent` int DEFAULT NULL,
  `transfer_id` int DEFAULT NULL,
  `adjust_id` int DEFAULT NULL,
  `adjust_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `THBValue` decimal(19,2) DEFAULT NULL,
  `flag_hide` int NOT NULL DEFAULT '0',
  `adj_supplier` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `claim` int DEFAULT NULL,
  `noted` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `defer_id` int DEFAULT NULL,
  `purchase` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_purchase_spot_profit_bwd`
--

CREATE TABLE `bs_purchase_spot_profit_bwd` (
  `id` int NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `rate_spot` decimal(19,4) DEFAULT NULL,
  `rate_pmdc` decimal(19,4) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `value_date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `method` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ref` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user` int DEFAULT NULL,
  `status` int DEFAULT NULL COMMENT '\r\n\r\n1-Normal\r\n2- Used\r\n',
  `comment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `confirm` datetime DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `trade_id` int DEFAULT NULL,
  `import_id` int DEFAULT NULL,
  `parent` int DEFAULT NULL,
  `transfer_id` int DEFAULT NULL,
  `adjust_id` int DEFAULT NULL,
  `adjust_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `THBValue` decimal(19,2) DEFAULT NULL,
  `flag_hide` int NOT NULL DEFAULT '0',
  `adj_supplier` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `claim` int DEFAULT NULL,
  `noted` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `defer_id` int DEFAULT NULL,
  `purchase` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_purchase_usd`
--

CREATE TABLE `bs_purchase_usd` (
  `id` int NOT NULL,
  `bank` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `rate_exchange` decimal(19,6) DEFAULT NULL,
  `rate_finance` decimal(19,6) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `method` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ref` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `confirm` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `parent` int DEFAULT NULL,
  `bank_date` date DEFAULT NULL,
  `premium_start` date DEFAULT NULL,
  `premium` decimal(19,4) DEFAULT NULL,
  `transfer_id` int DEFAULT NULL,
  `fw_contract_no` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `unpaid` decimal(19,4) DEFAULT NULL,
  `date_transfer` date DEFAULT NULL,
  `finance` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_purchase_usd_profit`
--

CREATE TABLE `bs_purchase_usd_profit` (
  `id` int NOT NULL,
  `bank` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `rate_exchange` decimal(19,6) DEFAULT NULL,
  `rate_finance` decimal(19,6) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `value_date` date DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `method` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ref` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `confirm` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `parent` int DEFAULT NULL,
  `bank_date` date DEFAULT NULL,
  `premium_start` date DEFAULT NULL,
  `premium` decimal(19,4) DEFAULT NULL,
  `transfer_id` int DEFAULT NULL,
  `fw_contract_no` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `unpaid` decimal(19,4) DEFAULT NULL,
  `date_transfer` date DEFAULT NULL,
  `finance` int DEFAULT NULL,
  `purchase` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_purchase_usd_profit_bwd`
--

CREATE TABLE `bs_purchase_usd_profit_bwd` (
  `id` int NOT NULL,
  `bank` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `rate_exchange` decimal(19,6) DEFAULT NULL,
  `rate_finance` decimal(19,6) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `value_date` date DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `method` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ref` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `confirm` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `parent` int DEFAULT NULL,
  `bank_date` date DEFAULT NULL,
  `premium_start` date DEFAULT NULL,
  `premium` decimal(19,4) DEFAULT NULL,
  `transfer_id` int DEFAULT NULL,
  `fw_contract_no` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `unpaid` decimal(19,4) DEFAULT NULL,
  `date_transfer` date DEFAULT NULL,
  `finance` int DEFAULT NULL,
  `purchase` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_quick_orders`
--

CREATE TABLE `bs_quick_orders` (
  `id` int NOT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `price` decimal(19,4) DEFAULT NULL,
  `total` decimal(19,4) DEFAULT NULL,
  `usd` decimal(19,4) DEFAULT NULL,
  `store` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `orderable_type` enum('delivered_by_company','post_office','receive_at_company','receive_at_luckgems') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `rate_spot` decimal(19,4) DEFAULT NULL,
  `rate_exchange` decimal(19,4) DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `status` int DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `vat_type` int NOT NULL,
  `sales` int DEFAULT NULL,
  `product_id` int DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_reserve_silver`
--

CREATE TABLE `bs_reserve_silver` (
  `id` int NOT NULL,
  `lock_date` date DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `discount` decimal(19,4) DEFAULT NULL,
  `weight_lock` decimal(19,4) DEFAULT NULL,
  `weight_actual` decimal(19,4) DEFAULT NULL,
  `weight_fixed` decimal(19,4) DEFAULT NULL,
  `weight_pending` decimal(19,4) DEFAULT NULL,
  `defer` decimal(19,2) DEFAULT NULL,
  `bar` int DEFAULT NULL,
  `type` int DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `user` int DEFAULT NULL,
  `import_id` int DEFAULT NULL,
  `brand` varchar(511) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_sales_spot`
--

CREATE TABLE `bs_sales_spot` (
  `id` int NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `rate_spot` decimal(19,4) DEFAULT NULL,
  `rate_pmdc` decimal(19,4) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `value_date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `method` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `maturity` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ref` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `trade_id` int DEFAULT NULL,
  `bank` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `adjust_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_scrap_items`
--

CREATE TABLE `bs_scrap_items` (
  `id` int NOT NULL,
  `production_id` int DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `pack_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `pack_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `weight_actual` decimal(19,4) DEFAULT NULL,
  `weight_expected` decimal(19,4) DEFAULT NULL,
  `parent` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `delivery_id` int DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `datecancel` datetime DEFAULT NULL,
  `submited` date DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_scrap_total`
--

CREATE TABLE `bs_scrap_total` (
  `id` int NOT NULL,
  `production_id` int DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pack_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pack_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight_expected` decimal(19,4) DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `submited` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_shipping_bwd`
--

CREATE TABLE `bs_shipping_bwd` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(19,4) DEFAULT NULL,
  `status` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_cash`
--

CREATE TABLE `bs_smg_cash` (
  `id` int NOT NULL,
  `date` date DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_claim`
--

CREATE TABLE `bs_smg_claim` (
  `id` int NOT NULL,
  `date` date DEFAULT NULL,
  `purchase_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `rate_spot` decimal(19,4) DEFAULT NULL,
  `rate_pmdc` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_daily`
--

CREATE TABLE `bs_smg_daily` (
  `id` int NOT NULL,
  `date` date DEFAULT NULL,
  `rollover` decimal(19,5) DEFAULT NULL,
  `spot_sell` decimal(19,5) DEFAULT NULL,
  `spot_buy` decimal(19,5) DEFAULT NULL,
  `cash` decimal(19,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_initial`
--

CREATE TABLE `bs_smg_initial` (
  `id` int NOT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `margin` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_interest`
--

CREATE TABLE `bs_smg_interest` (
  `id` int NOT NULL,
  `date` date DEFAULT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `interest` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_other`
--

CREATE TABLE `bs_smg_other` (
  `id` int NOT NULL,
  `date` date DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `usd_debit` decimal(19,4) DEFAULT NULL,
  `usd_credit` decimal(19,4) DEFAULT NULL,
  `amount_debit` decimal(19,4) DEFAULT NULL,
  `amount_credit` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_payment`
--

CREATE TABLE `bs_smg_payment` (
  `id` int NOT NULL,
  `date` date DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount_usd` decimal(19,4) DEFAULT NULL,
  `rate_pmdc` decimal(19,4) DEFAULT NULL,
  `amount_total` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_rate`
--

CREATE TABLE `bs_smg_rate` (
  `id` int NOT NULL,
  `date` date NOT NULL,
  `rate_short` decimal(19,2) DEFAULT NULL,
  `rate` decimal(19,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_rate_rollover`
--

CREATE TABLE `bs_smg_rate_rollover` (
  `id` int NOT NULL,
  `date` date NOT NULL,
  `rate_short` decimal(19,2) DEFAULT NULL,
  `rate` decimal(19,2) DEFAULT NULL,
  `interest` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_receiving`
--

CREATE TABLE `bs_smg_receiving` (
  `id` int NOT NULL,
  `date` date DEFAULT NULL,
  `amount` decimal(19,5) DEFAULT NULL,
  `rate_pmdc` decimal(19,4) DEFAULT NULL,
  `transfer` decimal(19,2) NOT NULL DEFAULT '0.00',
  `import_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_rollover`
--

CREATE TABLE `bs_smg_rollover` (
  `id` int NOT NULL,
  `trade` date DEFAULT NULL,
  `date` date DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount` decimal(19,6) DEFAULT NULL,
  `rate_spot` decimal(19,6) DEFAULT NULL,
  `entry` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'credit'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_rollover_type`
--

CREATE TABLE `bs_smg_rollover_type` (
  `id` int NOT NULL,
  `type` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `type2` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_stx_cash`
--

CREATE TABLE `bs_smg_stx_cash` (
  `id` int NOT NULL,
  `date` date DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_stx_claim`
--

CREATE TABLE `bs_smg_stx_claim` (
  `id` int NOT NULL,
  `date` date DEFAULT NULL,
  `purchase_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `rate_spot` decimal(19,4) DEFAULT NULL,
  `rate_pmdc` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_stx_daily`
--

CREATE TABLE `bs_smg_stx_daily` (
  `id` int NOT NULL,
  `date` date DEFAULT NULL,
  `rollover` decimal(19,5) DEFAULT NULL,
  `spot_sell` decimal(19,5) DEFAULT NULL,
  `spot_buy` decimal(19,5) DEFAULT NULL,
  `cash` decimal(19,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_stx_initial`
--

CREATE TABLE `bs_smg_stx_initial` (
  `id` int NOT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `margin` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_stx_interest`
--

CREATE TABLE `bs_smg_stx_interest` (
  `id` int NOT NULL,
  `date` date DEFAULT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `interest` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_stx_other`
--

CREATE TABLE `bs_smg_stx_other` (
  `id` int NOT NULL,
  `date` date DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `usd_debit` decimal(19,4) DEFAULT NULL,
  `usd_credit` decimal(19,4) DEFAULT NULL,
  `amount_debit` decimal(19,4) DEFAULT NULL,
  `amount_credit` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_stx_payment`
--

CREATE TABLE `bs_smg_stx_payment` (
  `id` int NOT NULL,
  `date` date DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount_usd` decimal(19,4) DEFAULT NULL,
  `rate_pmdc` decimal(19,4) DEFAULT NULL,
  `amount_total` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_stx_rate`
--

CREATE TABLE `bs_smg_stx_rate` (
  `id` int NOT NULL,
  `date` date NOT NULL,
  `rate_short` decimal(19,2) DEFAULT NULL,
  `rate` decimal(19,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_stx_rate_rollover`
--

CREATE TABLE `bs_smg_stx_rate_rollover` (
  `id` int NOT NULL,
  `date` date NOT NULL,
  `rate_short` decimal(19,2) DEFAULT NULL,
  `rate` decimal(19,2) DEFAULT NULL,
  `interest` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_stx_receiving`
--

CREATE TABLE `bs_smg_stx_receiving` (
  `id` int NOT NULL,
  `date` date DEFAULT NULL,
  `amount` decimal(19,5) DEFAULT NULL,
  `rate_pmdc` decimal(19,4) DEFAULT NULL,
  `transfer` decimal(19,2) NOT NULL DEFAULT '0.00',
  `import_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_stx_rollover`
--

CREATE TABLE `bs_smg_stx_rollover` (
  `id` int NOT NULL,
  `trade` date DEFAULT NULL,
  `date` date DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount` decimal(19,6) DEFAULT NULL,
  `rate_spot` decimal(19,6) DEFAULT NULL,
  `entry` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT 'credit'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_stx_trade`
--

CREATE TABLE `bs_smg_stx_trade` (
  `id` int NOT NULL,
  `date` date DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `purchase_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount` decimal(19,6) DEFAULT NULL,
  `rate_spot` decimal(19,5) DEFAULT NULL,
  `rate_pmdc` decimal(19,4) DEFAULT NULL,
  `purchase_spot` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_smg_trade`
--

CREATE TABLE `bs_smg_trade` (
  `id` int NOT NULL,
  `date` date DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `purchase_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount` decimal(19,6) DEFAULT NULL,
  `rate_spot` decimal(19,5) DEFAULT NULL,
  `rate_pmdc` decimal(19,4) DEFAULT NULL,
  `purchase_spot` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_spot_profit_daily`
--

CREATE TABLE `bs_spot_profit_daily` (
  `id` int NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `rate_spot` decimal(19,4) DEFAULT NULL,
  `rate_pmdc` decimal(19,4) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `value_date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `method` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ref` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user` int DEFAULT NULL,
  `status` int DEFAULT NULL COMMENT '\r\n\r\n1-Normal\r\n2- Used\r\n',
  `comment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `confirm` datetime DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `trade_id` int DEFAULT NULL,
  `import_id` int DEFAULT NULL,
  `parent` int DEFAULT NULL,
  `transfer_id` int DEFAULT NULL,
  `adjust_id` int DEFAULT NULL,
  `adjust_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `THBValue` decimal(19,2) DEFAULT NULL,
  `flag_hide` int NOT NULL DEFAULT '0',
  `adj_supplier` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `claim` int DEFAULT NULL,
  `noted` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `defer_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_spot_usd_splited`
--

CREATE TABLE `bs_spot_usd_splited` (
  `id` int NOT NULL,
  `purchase_id` int DEFAULT NULL,
  `transfer_id` int DEFAULT NULL,
  `amount` decimal(19,2) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_stock_adjuest_types`
--

CREATE TABLE `bs_stock_adjuest_types` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `type` int DEFAULT NULL,
  `sort_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_stock_adjusted`
--

CREATE TABLE `bs_stock_adjusted` (
  `id` int NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `amount` decimal(19,4) DEFAULT NULL,
  `amount2` decimal(19,4) DEFAULT NULL,
  `amount3` decimal(19,4) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `ref` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `type_id` int DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_stock_adjusted_bwd`
--

CREATE TABLE `bs_stock_adjusted_bwd` (
  `id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `weight_expected` decimal(19,4) DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `date` date DEFAULT NULL,
  `dateend` date DEFAULT NULL,
  `type_id` int DEFAULT '1',
  `product_type` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_stock_adjusted_over`
--

CREATE TABLE `bs_stock_adjusted_over` (
  `id` int NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `code_no` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `ref` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `type_id` int DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_stock_adjust_type_bwd`
--

CREATE TABLE `bs_stock_adjust_type_bwd` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` int NOT NULL,
  `sort_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_stock_bwd`
--

CREATE TABLE `bs_stock_bwd` (
  `id` int NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pack_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pack_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight_actual` decimal(19,4) DEFAULT NULL,
  `weight_expected` decimal(19,4) DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `status` int DEFAULT NULL,
  `product_type` int DEFAULT NULL,
  `submited` date DEFAULT NULL,
  `product_id` int NOT NULL,
  `created` datetime DEFAULT NULL,
  `customer_po` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_stock_items`
--

CREATE TABLE `bs_stock_items` (
  `id` int NOT NULL,
  `prepare_id` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `size` decimal(19,4) DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `status` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_stock_prepare`
--

CREATE TABLE `bs_stock_prepare` (
  `id` int NOT NULL,
  `user` int DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `prepare_date` date DEFAULT NULL,
  `status` int DEFAULT NULL,
  `approved` int DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `info_amount` decimal(19,4) DEFAULT NULL,
  `info_mine` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `status_show` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_stock_silver`
--

CREATE TABLE `bs_stock_silver` (
  `id` int NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_po` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pack_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pack_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight_actual` decimal(19,4) DEFAULT NULL,
  `weight_expected` decimal(19,4) DEFAULT NULL,
  `status` int DEFAULT NULL,
  `stock` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submited` date DEFAULT NULL,
  `swapdate` date DEFAULT NULL,
  `product_id` int NOT NULL,
  `created` datetime DEFAULT NULL,
  `supplier_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_suppliers`
--

CREATE TABLE `bs_suppliers` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `type` int DEFAULT NULL,
  `gid` int DEFAULT NULL,
  `status` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_suppliers_jinsung`
--

CREATE TABLE `bs_suppliers_jinsung` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `usd` decimal(19,4) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `supplier_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_suppliers_mapping`
--

CREATE TABLE `bs_suppliers_mapping` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `usd` decimal(19,4) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_suppliers_mapping_1`
--

CREATE TABLE `bs_suppliers_mapping_1` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `usd` decimal(19,4) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_suppliers_standard`
--

CREATE TABLE `bs_suppliers_standard` (
  `id` int NOT NULL,
  `code` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `usd` decimal(19,4) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_supplier_groups`
--

CREATE TABLE `bs_supplier_groups` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_switch_pack_items`
--

CREATE TABLE `bs_switch_pack_items` (
  `id` int NOT NULL,
  `switch_id` int DEFAULT NULL,
  `item_type` int DEFAULT NULL,
  `item_id` int DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `mapping` datetime DEFAULT NULL,
  `status` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_trade_spot`
--

CREATE TABLE `bs_trade_spot` (
  `id` int NOT NULL,
  `date` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `user` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_transfers`
--

CREATE TABLE `bs_transfers` (
  `id` int NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `value_usd_goods` decimal(19,4) DEFAULT NULL,
  `value_usd_deposit` decimal(19,4) DEFAULT NULL,
  `value_usd_paid` decimal(19,4) DEFAULT NULL,
  `value_usd_adjusted` decimal(19,4) DEFAULT '0.0000',
  `value_usd_total` decimal(19,4) DEFAULT NULL,
  `value_usd_fixed` decimal(19,4) DEFAULT NULL,
  `value_usd_nonfixed` decimal(19,4) DEFAULT NULL,
  `rate_counter` decimal(19,4) DEFAULT NULL,
  `value_thb_fixed` decimal(19,4) DEFAULT NULL,
  `value_thb_premium` decimal(19,4) DEFAULT NULL,
  `value_thb_net` decimal(19,4) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `value_thb_transaction` decimal(19,4) DEFAULT NULL,
  `paid_thb` decimal(19,4) DEFAULT NULL,
  `paid_usd` decimal(19,4) DEFAULT NULL,
  `status` int DEFAULT NULL,
  `source` int DEFAULT NULL,
  `rate_interest` decimal(5,2) DEFAULT '0.00',
  `due_date` date DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `value_adjust_trade` decimal(19,4) DEFAULT NULL,
  `value_edit_trade` decimal(19,4) DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `interest_match` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_transfer_adjusted`
--

CREATE TABLE `bs_transfer_adjusted` (
  `id` int NOT NULL,
  `transfer_id` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_transfer_paid`
--

CREATE TABLE `bs_transfer_paid` (
  `id` int NOT NULL,
  `date` date DEFAULT NULL,
  `bank` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `paid` decimal(19,2) DEFAULT NULL,
  `principle` decimal(19,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_transfer_payments`
--

CREATE TABLE `bs_transfer_payments` (
  `id` int NOT NULL,
  `date` date DEFAULT NULL,
  `transfer_id` int DEFAULT NULL,
  `currency` char(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `principle` decimal(19,2) DEFAULT NULL,
  `interest` decimal(19,2) DEFAULT NULL,
  `paid` decimal(19,2) DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `rate_interest` decimal(5,2) DEFAULT NULL,
  `rate_counter` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_transfer_report`
--

CREATE TABLE `bs_transfer_report` (
  `id` int NOT NULL,
  `bank` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `balance` decimal(19,4) DEFAULT NULL,
  `credit` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_transfer_usd`
--

CREATE TABLE `bs_transfer_usd` (
  `purchase_id` int NOT NULL,
  `transfer_id` int DEFAULT NULL,
  `premium_type` int DEFAULT NULL,
  `date` date DEFAULT NULL,
  `premium_start` date DEFAULT NULL,
  `premium_end` date DEFAULT NULL,
  `premium_day` int DEFAULT NULL,
  `rate_premium` decimal(19,2) DEFAULT NULL,
  `rate_counter` decimal(19,2) DEFAULT NULL,
  `premium` decimal(19,2) DEFAULT NULL,
  `fw_contract_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_usd_payment`
--

CREATE TABLE `bs_usd_payment` (
  `id` int NOT NULL,
  `date` datetime DEFAULT NULL,
  `transfer_id` int DEFAULT NULL,
  `purchase_id` int DEFAULT NULL,
  `rate_interest` decimal(5,2) DEFAULT NULL,
  `interest` decimal(19,4) DEFAULT NULL,
  `interest_day` int DEFAULT NULL,
  `piad_usd` decimal(19,4) DEFAULT NULL,
  `paid_thb` decimal(19,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bs_usd_profit_daily`
--

CREATE TABLE `bs_usd_profit_daily` (
  `id` int NOT NULL,
  `bank` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `amount` decimal(19,4) DEFAULT NULL,
  `rate_exchange` decimal(19,6) DEFAULT NULL,
  `rate_finance` decimal(19,6) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `method` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ref` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `confirm` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `parent` int DEFAULT NULL,
  `bank_date` date DEFAULT NULL,
  `premium_start` date DEFAULT NULL,
  `premium` decimal(19,4) DEFAULT NULL,
  `transfer_id` int DEFAULT NULL,
  `fw_contract_no` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `unpaid` decimal(19,4) DEFAULT NULL,
  `date_transfer` date DEFAULT NULL,
  `finance` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_cities`
--

CREATE TABLE `db_cities` (
  `id` int NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `region` int DEFAULT NULL,
  `country` int DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `db_countries`
--

CREATE TABLE `db_countries` (
  `id` int NOT NULL,
  `name` varchar(80) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `local_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `region` int NOT NULL,
  `iso` char(2) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `iso3` char(3) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `numcode` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `phonecode` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `db_districts`
--

CREATE TABLE `db_districts` (
  `id` int NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `city` int NOT NULL,
  `region` int NOT NULL,
  `country` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `db_regions`
--

CREATE TABLE `db_regions` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `country` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `db_subdistricts`
--

CREATE TABLE `db_subdistricts` (
  `id` int NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `district` int NOT NULL,
  `city` int NOT NULL,
  `region` int NOT NULL,
  `country` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `db_zipcode_thailand`
--

CREATE TABLE `db_zipcode_thailand` (
  `ZIPCODE_ID` int NOT NULL,
  `DISTRICT_CODE` varchar(100) NOT NULL,
  `PROVINCE_ID` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `AMPHUR_ID` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `DISTRICT_ID` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `ZIPCODE` varchar(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `order_id_seq`
--

CREATE TABLE `order_id_seq` (
  `id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `os_accounts`
--

CREATE TABLE `os_accounts` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  `validated` datetime DEFAULT NULL,
  `org_id` int NOT NULL,
  `data` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `os_address`
--

CREATE TABLE `os_address` (
  `id` int NOT NULL,
  `address` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fulladdress` varchar(2047) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `country` int NOT NULL,
  `city` int NOT NULL,
  `district` int NOT NULL,
  `subdistrict` int NOT NULL,
  `postal` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `contact` int DEFAULT NULL,
  `organization` int DEFAULT NULL,
  `priority` int DEFAULT NULL,
  `remark` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `os_concurrents`
--

CREATE TABLE `os_concurrents` (
  `id` int NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `package` int DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `device` varchar(255) DEFAULT NULL,
  `login` datetime DEFAULT NULL,
  `connected` datetime DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `connect_counter` int DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `os_contacts`
--

CREATE TABLE `os_contacts` (
  `id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `surname` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `status` int DEFAULT NULL,
  `skype` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `facebook` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `google` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `line` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `citizen_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `nickname` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `data` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `org_id` int DEFAULT NULL,
  `position` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `os_groups`
--

CREATE TABLE `os_groups` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `account` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `os_logs`
--

CREATE TABLE `os_logs` (
  `id` int NOT NULL,
  `datetime` datetime DEFAULT NULL,
  `user_type` int DEFAULT NULL,
  `user` int DEFAULT NULL,
  `action` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `value` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `location` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `data` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `os_messages`
--

CREATE TABLE `os_messages` (
  `id` int NOT NULL,
  `source` int DEFAULT NULL,
  `destination` int DEFAULT NULL,
  `type` int DEFAULT NULL,
  `msg` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `opened` datetime DEFAULT NULL,
  `acknowledge` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `os_notifications`
--

CREATE TABLE `os_notifications` (
  `id` int NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `topic` varchar(512) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `detail` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `user` int DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `acknowledge` datetime DEFAULT NULL,
  `data` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `os_organizations`
--

CREATE TABLE `os_organizations` (
  `id` int NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fax` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `website` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `parent` int DEFAULT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tax_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `branch_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `branch` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `coordinator` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `os_permissions`
--

CREATE TABLE `os_permissions` (
  `id` int NOT NULL,
  `gid` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `action` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `os_users`
--

CREATE TABLE `os_users` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `display` varchar(1023) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `status` int DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `validated` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `gid` int NOT NULL,
  `contact` int DEFAULT NULL,
  `setting` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `data` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `os_variable`
--

CREATE TABLE `os_variable` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `value` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `updated` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_orders_summary`
-- (See below for the actual view)
--
CREATE TABLE `v_orders_summary` (
`amount` decimal(19,4)
,`customer_id` int
,`customer_name` varchar(1023)
,`delivery_date` date
,`order_code` varchar(255)
,`order_date` datetime
,`price_thb` decimal(19,4)
,`price_usd` decimal(24,4)
,`product_name` varchar(255)
,`rate_exchange` decimal(19,4)
,`rate_spot` decimal(19,4)
,`sales_name` varchar(511)
,`total_amount` decimal(35,4)
,`total_vat_included` decimal(19,4)
);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `a_public_holiday`
--
ALTER TABLE `a_public_holiday`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_fisyear_publicholiday` (`FisYear`,`PublicHoliday`);

--
-- Indexes for table `bs_adjust_amount`
--
ALTER TABLE `bs_adjust_amount`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_adjust_cost`
--
ALTER TABLE `bs_adjust_cost`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_adjust_defer`
--
ALTER TABLE `bs_adjust_defer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_adjust_physical_adjust`
--
ALTER TABLE `bs_adjust_physical_adjust`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_adjust_purchase`
--
ALTER TABLE `bs_adjust_purchase`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_adjust_thb`
--
ALTER TABLE `bs_adjust_thb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_announce_silver`
--
ALTER TABLE `bs_announce_silver`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_banks`
--
ALTER TABLE `bs_banks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_bank_statement`
--
ALTER TABLE `bs_bank_statement`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_bwd_pack_items`
--
ALTER TABLE `bs_bwd_pack_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_claims`
--
ALTER TABLE `bs_claims`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_coa_run`
--
ALTER TABLE `bs_coa_run`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_coupons`
--
ALTER TABLE `bs_coupons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_crucible_items`
--
ALTER TABLE `bs_crucible_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_currencies`
--
ALTER TABLE `bs_currencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_customers`
--
ALTER TABLE `bs_customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bs_customers_phone` (`phone`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `bs_customers_bwd`
--
ALTER TABLE `bs_customers_bwd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_customer_groups`
--
ALTER TABLE `bs_customer_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_defer_cost`
--
ALTER TABLE `bs_defer_cost`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_deliveries`
--
ALTER TABLE `bs_deliveries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_deliveries_bwd`
--
ALTER TABLE `bs_deliveries_bwd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_deliveries_drivers`
--
ALTER TABLE `bs_deliveries_drivers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_delivery_detail`
--
ALTER TABLE `bs_delivery_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_delivery_items`
--
ALTER TABLE `bs_delivery_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_delivery_pack_items`
--
ALTER TABLE `bs_delivery_pack_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_departments`
--
ALTER TABLE `bs_departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_employees`
--
ALTER TABLE `bs_employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_finance_static_values`
--
ALTER TABLE `bs_finance_static_values`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_fonts_bwd`
--
ALTER TABLE `bs_fonts_bwd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_holiday`
--
ALTER TABLE `bs_holiday`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_imports`
--
ALTER TABLE `bs_imports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_import_combine`
--
ALTER TABLE `bs_import_combine`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_import_usd_splited`
--
ALTER TABLE `bs_import_usd_splited`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_incoming_plans`
--
ALTER TABLE `bs_incoming_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_mapping_profit`
--
ALTER TABLE `bs_mapping_profit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bmp_mapped` (`mapped`),
  ADD KEY `idx_bmp_checked_mapped` (`is_checked`,`mapped`);

--
-- Indexes for table `bs_mapping_profit_bwd`
--
ALTER TABLE `bs_mapping_profit_bwd`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bmp_mapped` (`mapped`),
  ADD KEY `idx_bmp_checked_mapped` (`is_checked`,`mapped`);

--
-- Indexes for table `bs_mapping_profit_orders`
--
ALTER TABLE `bs_mapping_profit_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mpo_order` (`order_id`),
  ADD KEY `idx_mpo_mapping` (`mapping_id`),
  ADD KEY `idx_mpo_mapping_order` (`mapping_id`,`order_id`);

--
-- Indexes for table `bs_mapping_profit_orders_bwd`
--
ALTER TABLE `bs_mapping_profit_orders_bwd`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mpo_order` (`order_id`),
  ADD KEY `idx_mpo_mapping` (`mapping_id`),
  ADD KEY `idx_mpo_mapping_order` (`mapping_id`,`order_id`);

--
-- Indexes for table `bs_mapping_profit_orders_usd`
--
ALTER TABLE `bs_mapping_profit_orders_usd`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mpou_order` (`order_id`),
  ADD KEY `idx_mpou_mapping` (`mapping_id`),
  ADD KEY `idx_mpou_mapping_order` (`mapping_id`,`order_id`);

--
-- Indexes for table `bs_mapping_profit_orders_usd_bwd`
--
ALTER TABLE `bs_mapping_profit_orders_usd_bwd`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mpou_order` (`order_id`),
  ADD KEY `idx_mpou_mapping` (`mapping_id`),
  ADD KEY `idx_mpou_mapping_order` (`mapping_id`,`order_id`);

--
-- Indexes for table `bs_mapping_profit_sumusd`
--
ALTER TABLE `bs_mapping_profit_sumusd`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bms_mapped` (`mapped`),
  ADD KEY `idx_bms_checked_mapped` (`is_checked`,`mapped`);

--
-- Indexes for table `bs_mapping_profit_sumusd_bwd`
--
ALTER TABLE `bs_mapping_profit_sumusd_bwd`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bms_mapped` (`mapped`),
  ADD KEY `idx_bms_checked_mapped` (`is_checked`,`mapped`);

--
-- Indexes for table `bs_mapping_silvers`
--
ALTER TABLE `bs_mapping_silvers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_mapping_silver_orders`
--
ALTER TABLE `bs_mapping_silver_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_mapping_silver_purchases`
--
ALTER TABLE `bs_mapping_silver_purchases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_mapping_usd`
--
ALTER TABLE `bs_mapping_usd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_mapping_usd_purchases`
--
ALTER TABLE `bs_mapping_usd_purchases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_mapping_usd_spots`
--
ALTER TABLE `bs_mapping_usd_spots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_match`
--
ALTER TABLE `bs_match`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_match_bank`
--
ALTER TABLE `bs_match_bank`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_match_data`
--
ALTER TABLE `bs_match_data`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_bs_match_data_date` (`date`);

--
-- Indexes for table `bs_match_deposit`
--
ALTER TABLE `bs_match_deposit`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_match_fx`
--
ALTER TABLE `bs_match_fx`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_match_stx`
--
ALTER TABLE `bs_match_stx`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_match_stx_add`
--
ALTER TABLE `bs_match_stx_add`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_match_tr`
--
ALTER TABLE `bs_match_tr`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_match_usd`
--
ALTER TABLE `bs_match_usd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_orders`
--
ALTER TABLE `bs_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bs_orders_pid_date_status` (`product_id`,`delivery_date`,`status`),
  ADD KEY `idx_bs_orders_common` (`status`,`product_id`,`delivery_date`),
  ADD KEY `idx_bs_orders_tracking` (`Tracking`),
  ADD KEY `idx_bs_orders_customer_id` (`customer_id`),
  ADD KEY `idx_orders_customer` (`customer_id`);

--
-- Indexes for table `bs_orders_back_bwd`
--
ALTER TABLE `bs_orders_back_bwd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_orders_buy`
--
ALTER TABLE `bs_orders_buy`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_orders_bwd`
--
ALTER TABLE `bs_orders_bwd`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bwd_phone` (`phone`),
  ADD KEY `idx_bwd_status_parent` (`status`,`parent`),
  ADD KEY `idx_bwd_product` (`product_id`,`product_type`),
  ADD KEY `idx_bwd_platform` (`platform`),
  ADD KEY `idx_bwd_parent_status_delv_date` (`parent`,`status`,`delivery_date`,`date`,`id`),
  ADD KEY `idx_bwd_parent_status_customer` (`parent`,`status`,`customer_id`),
  ADD KEY `idx_bwd_parent_status` (`parent`,`status`),
  ADD KEY `idx_bwd_delivery` (`delivery_id`),
  ADD KEY `idx_bwd_code` (`code`),
  ADD KEY `idx_date_safe` (`delivery_date`,`date`),
  ADD KEY `idx_box_number` (`box_number`),
  ADD KEY `idx_parent_box` (`parent`,`box_number`),
  ADD KEY `order_platform` (`order_platform`);

--
-- Indexes for table `bs_orders_profit`
--
ALTER TABLE `bs_orders_profit`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_order_id` (`order_id`),
  ADD KEY `idx_orders_profit_prod_status_deldate` (`customer_id`,`code`,`product_id`,`status`,`delivery_date`),
  ADD KEY `idx_profit_status_hide_date` (`status`,`flag_hide`,`date`),
  ADD KEY `idx_profit_order` (`order_id`),
  ADD KEY `idx_parent_split` (`parent`,`is_split`,`split_sequence`);

--
-- Indexes for table `bs_orders_split_bwd`
--
ALTER TABLE `bs_orders_split_bwd`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_order_id` (`parent_order_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `bs_over_adjust_types`
--
ALTER TABLE `bs_over_adjust_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_packings`
--
ALTER TABLE `bs_packings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_packing_items`
--
ALTER TABLE `bs_packing_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_payments`
--
ALTER TABLE `bs_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_payment_deposits`
--
ALTER TABLE `bs_payment_deposits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_payment_deposit_use`
--
ALTER TABLE `bs_payment_deposit_use`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_payment_items`
--
ALTER TABLE `bs_payment_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_payment_orders`
--
ALTER TABLE `bs_payment_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_payment_types`
--
ALTER TABLE `bs_payment_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_pmr_pack_items`
--
ALTER TABLE `bs_pmr_pack_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_price`
--
ALTER TABLE `bs_price`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_processing_summary`
--
ALTER TABLE `bs_processing_summary`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_productions`
--
ALTER TABLE `bs_productions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bs_productions_pid_time` (`product_id`,`submited`);

--
-- Indexes for table `bs_productions_crucible`
--
ALTER TABLE `bs_productions_crucible`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_productions_furnace`
--
ALTER TABLE `bs_productions_furnace`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_productions_in`
--
ALTER TABLE `bs_productions_in`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bs_productions_in_pid_time` (`product_id`,`submited`);

--
-- Indexes for table `bs_productions_oven`
--
ALTER TABLE `bs_productions_oven`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_productions_pmr`
--
ALTER TABLE `bs_productions_pmr`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bs_productions_pmr_pid_time` (`product_id`,`submited`);

--
-- Indexes for table `bs_productions_round`
--
ALTER TABLE `bs_productions_round`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_productions_scale`
--
ALTER TABLE `bs_productions_scale`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_productions_silver_save`
--
ALTER TABLE `bs_productions_silver_save`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_productions_switch`
--
ALTER TABLE `bs_productions_switch`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_products`
--
ALTER TABLE `bs_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_products_bwd`
--
ALTER TABLE `bs_products_bwd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_products_export`
--
ALTER TABLE `bs_products_export`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_products_import`
--
ALTER TABLE `bs_products_import`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_products_turn`
--
ALTER TABLE `bs_products_turn`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_products_type`
--
ALTER TABLE `bs_products_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_profit_daily`
--
ALTER TABLE `bs_profit_daily`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_purchase_buy`
--
ALTER TABLE `bs_purchase_buy`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_purchase_buyfix`
--
ALTER TABLE `bs_purchase_buyfix`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_purchase_spot`
--
ALTER TABLE `bs_purchase_spot`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_date` (`date`),
  ADD KEY `idx_parent` (`parent`),
  ADD KEY `idx_type_rate_status_confirm` (`type`,`rate_spot`,`status`,`confirm`);

--
-- Indexes for table `bs_purchase_spot_profit`
--
ALTER TABLE `bs_purchase_spot_profit`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_purchase_spot_profit_bwd`
--
ALTER TABLE `bs_purchase_spot_profit_bwd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_purchase_usd`
--
ALTER TABLE `bs_purchase_usd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_purchase_usd_profit`
--
ALTER TABLE `bs_purchase_usd_profit`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_purchase_usd_profit_bwd`
--
ALTER TABLE `bs_purchase_usd_profit_bwd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_quick_orders`
--
ALTER TABLE `bs_quick_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_reserve_silver`
--
ALTER TABLE `bs_reserve_silver`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_sales_spot`
--
ALTER TABLE `bs_sales_spot`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_scrap_items`
--
ALTER TABLE `bs_scrap_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_scrap_total`
--
ALTER TABLE `bs_scrap_total`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_shipping_bwd`
--
ALTER TABLE `bs_shipping_bwd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_cash`
--
ALTER TABLE `bs_smg_cash`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_claim`
--
ALTER TABLE `bs_smg_claim`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_daily`
--
ALTER TABLE `bs_smg_daily`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_initial`
--
ALTER TABLE `bs_smg_initial`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_interest`
--
ALTER TABLE `bs_smg_interest`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_other`
--
ALTER TABLE `bs_smg_other`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_payment`
--
ALTER TABLE `bs_smg_payment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_rate`
--
ALTER TABLE `bs_smg_rate`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_rate_rollover`
--
ALTER TABLE `bs_smg_rate_rollover`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_receiving`
--
ALTER TABLE `bs_smg_receiving`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_rollover`
--
ALTER TABLE `bs_smg_rollover`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_rollover_type`
--
ALTER TABLE `bs_smg_rollover_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_stx_cash`
--
ALTER TABLE `bs_smg_stx_cash`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_stx_claim`
--
ALTER TABLE `bs_smg_stx_claim`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_stx_daily`
--
ALTER TABLE `bs_smg_stx_daily`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_stx_initial`
--
ALTER TABLE `bs_smg_stx_initial`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_stx_interest`
--
ALTER TABLE `bs_smg_stx_interest`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_stx_other`
--
ALTER TABLE `bs_smg_stx_other`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_stx_payment`
--
ALTER TABLE `bs_smg_stx_payment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_stx_rate`
--
ALTER TABLE `bs_smg_stx_rate`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_stx_rate_rollover`
--
ALTER TABLE `bs_smg_stx_rate_rollover`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_stx_receiving`
--
ALTER TABLE `bs_smg_stx_receiving`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_stx_rollover`
--
ALTER TABLE `bs_smg_stx_rollover`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_stx_trade`
--
ALTER TABLE `bs_smg_stx_trade`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_smg_trade`
--
ALTER TABLE `bs_smg_trade`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_spot_profit_daily`
--
ALTER TABLE `bs_spot_profit_daily`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_spot_usd_splited`
--
ALTER TABLE `bs_spot_usd_splited`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_stock_adjuest_types`
--
ALTER TABLE `bs_stock_adjuest_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bs_stock_adjuest_types_type` (`type`);

--
-- Indexes for table `bs_stock_adjusted`
--
ALTER TABLE `bs_stock_adjusted`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bs_stock_adjusted_pid_type_date` (`product_id`,`type_id`,`date`);

--
-- Indexes for table `bs_stock_adjusted_bwd`
--
ALTER TABLE `bs_stock_adjusted_bwd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_stock_adjusted_over`
--
ALTER TABLE `bs_stock_adjusted_over`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_stock_adjust_type_bwd`
--
ALTER TABLE `bs_stock_adjust_type_bwd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_stock_bwd`
--
ALTER TABLE `bs_stock_bwd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_stock_items`
--
ALTER TABLE `bs_stock_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_stock_prepare`
--
ALTER TABLE `bs_stock_prepare`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_stock_silver`
--
ALTER TABLE `bs_stock_silver`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bs_stock_silver_pid_time` (`product_id`,`submited`);

--
-- Indexes for table `bs_suppliers`
--
ALTER TABLE `bs_suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_suppliers_jinsung`
--
ALTER TABLE `bs_suppliers_jinsung`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_suppliers_mapping`
--
ALTER TABLE `bs_suppliers_mapping`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_suppliers_mapping_1`
--
ALTER TABLE `bs_suppliers_mapping_1`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_suppliers_standard`
--
ALTER TABLE `bs_suppliers_standard`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_supplier_groups`
--
ALTER TABLE `bs_supplier_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_switch_pack_items`
--
ALTER TABLE `bs_switch_pack_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_trade_spot`
--
ALTER TABLE `bs_trade_spot`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_transfers`
--
ALTER TABLE `bs_transfers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_transfer_adjusted`
--
ALTER TABLE `bs_transfer_adjusted`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_transfer_paid`
--
ALTER TABLE `bs_transfer_paid`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_transfer_payments`
--
ALTER TABLE `bs_transfer_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_transfer_report`
--
ALTER TABLE `bs_transfer_report`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_transfer_usd`
--
ALTER TABLE `bs_transfer_usd`
  ADD PRIMARY KEY (`purchase_id`);

--
-- Indexes for table `bs_usd_payment`
--
ALTER TABLE `bs_usd_payment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bs_usd_profit_daily`
--
ALTER TABLE `bs_usd_profit_daily`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `db_cities`
--
ALTER TABLE `db_cities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `db_countries`
--
ALTER TABLE `db_countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `db_districts`
--
ALTER TABLE `db_districts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `db_regions`
--
ALTER TABLE `db_regions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `db_subdistricts`
--
ALTER TABLE `db_subdistricts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `db_zipcode_thailand`
--
ALTER TABLE `db_zipcode_thailand`
  ADD PRIMARY KEY (`ZIPCODE_ID`);

--
-- Indexes for table `order_id_seq`
--
ALTER TABLE `order_id_seq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `os_accounts`
--
ALTER TABLE `os_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `os_address`
--
ALTER TABLE `os_address`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `os_concurrents`
--
ALTER TABLE `os_concurrents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `os_contacts`
--
ALTER TABLE `os_contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `os_groups`
--
ALTER TABLE `os_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `os_logs`
--
ALTER TABLE `os_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `os_messages`
--
ALTER TABLE `os_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `os_notifications`
--
ALTER TABLE `os_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `os_organizations`
--
ALTER TABLE `os_organizations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `os_permissions`
--
ALTER TABLE `os_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FKpermission650610` (`gid`);

--
-- Indexes for table `os_users`
--
ALTER TABLE `os_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `os_variable`
--
ALTER TABLE `os_variable`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_os_variable_name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `a_public_holiday`
--
ALTER TABLE `a_public_holiday`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_adjust_amount`
--
ALTER TABLE `bs_adjust_amount`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_adjust_cost`
--
ALTER TABLE `bs_adjust_cost`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_adjust_defer`
--
ALTER TABLE `bs_adjust_defer`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_adjust_physical_adjust`
--
ALTER TABLE `bs_adjust_physical_adjust`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_adjust_purchase`
--
ALTER TABLE `bs_adjust_purchase`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_adjust_thb`
--
ALTER TABLE `bs_adjust_thb`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_announce_silver`
--
ALTER TABLE `bs_announce_silver`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_banks`
--
ALTER TABLE `bs_banks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_bank_statement`
--
ALTER TABLE `bs_bank_statement`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_bwd_pack_items`
--
ALTER TABLE `bs_bwd_pack_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_claims`
--
ALTER TABLE `bs_claims`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_coa_run`
--
ALTER TABLE `bs_coa_run`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_coupons`
--
ALTER TABLE `bs_coupons`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_crucible_items`
--
ALTER TABLE `bs_crucible_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_currencies`
--
ALTER TABLE `bs_currencies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_customers`
--
ALTER TABLE `bs_customers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_customers_bwd`
--
ALTER TABLE `bs_customers_bwd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_customer_groups`
--
ALTER TABLE `bs_customer_groups`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_defer_cost`
--
ALTER TABLE `bs_defer_cost`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_deliveries`
--
ALTER TABLE `bs_deliveries`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_deliveries_bwd`
--
ALTER TABLE `bs_deliveries_bwd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_deliveries_drivers`
--
ALTER TABLE `bs_deliveries_drivers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_delivery_detail`
--
ALTER TABLE `bs_delivery_detail`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_delivery_items`
--
ALTER TABLE `bs_delivery_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_delivery_pack_items`
--
ALTER TABLE `bs_delivery_pack_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_departments`
--
ALTER TABLE `bs_departments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_employees`
--
ALTER TABLE `bs_employees`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_finance_static_values`
--
ALTER TABLE `bs_finance_static_values`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_fonts_bwd`
--
ALTER TABLE `bs_fonts_bwd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_holiday`
--
ALTER TABLE `bs_holiday`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_imports`
--
ALTER TABLE `bs_imports`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_import_combine`
--
ALTER TABLE `bs_import_combine`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_import_usd_splited`
--
ALTER TABLE `bs_import_usd_splited`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_incoming_plans`
--
ALTER TABLE `bs_incoming_plans`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_mapping_profit`
--
ALTER TABLE `bs_mapping_profit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_mapping_profit_bwd`
--
ALTER TABLE `bs_mapping_profit_bwd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_mapping_profit_orders`
--
ALTER TABLE `bs_mapping_profit_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_mapping_profit_orders_bwd`
--
ALTER TABLE `bs_mapping_profit_orders_bwd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_mapping_profit_orders_usd`
--
ALTER TABLE `bs_mapping_profit_orders_usd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_mapping_profit_orders_usd_bwd`
--
ALTER TABLE `bs_mapping_profit_orders_usd_bwd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_mapping_profit_sumusd`
--
ALTER TABLE `bs_mapping_profit_sumusd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_mapping_profit_sumusd_bwd`
--
ALTER TABLE `bs_mapping_profit_sumusd_bwd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_mapping_silvers`
--
ALTER TABLE `bs_mapping_silvers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_mapping_silver_orders`
--
ALTER TABLE `bs_mapping_silver_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_mapping_silver_purchases`
--
ALTER TABLE `bs_mapping_silver_purchases`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_mapping_usd`
--
ALTER TABLE `bs_mapping_usd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_mapping_usd_purchases`
--
ALTER TABLE `bs_mapping_usd_purchases`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_mapping_usd_spots`
--
ALTER TABLE `bs_mapping_usd_spots`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_match`
--
ALTER TABLE `bs_match`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_match_bank`
--
ALTER TABLE `bs_match_bank`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_match_data`
--
ALTER TABLE `bs_match_data`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_match_deposit`
--
ALTER TABLE `bs_match_deposit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_match_fx`
--
ALTER TABLE `bs_match_fx`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_match_stx`
--
ALTER TABLE `bs_match_stx`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_match_stx_add`
--
ALTER TABLE `bs_match_stx_add`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_match_tr`
--
ALTER TABLE `bs_match_tr`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_match_usd`
--
ALTER TABLE `bs_match_usd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_orders`
--
ALTER TABLE `bs_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_orders_back_bwd`
--
ALTER TABLE `bs_orders_back_bwd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_orders_buy`
--
ALTER TABLE `bs_orders_buy`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_orders_bwd`
--
ALTER TABLE `bs_orders_bwd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_orders_profit`
--
ALTER TABLE `bs_orders_profit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_orders_split_bwd`
--
ALTER TABLE `bs_orders_split_bwd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_over_adjust_types`
--
ALTER TABLE `bs_over_adjust_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_packings`
--
ALTER TABLE `bs_packings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_packing_items`
--
ALTER TABLE `bs_packing_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_payments`
--
ALTER TABLE `bs_payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_payment_deposits`
--
ALTER TABLE `bs_payment_deposits`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_payment_deposit_use`
--
ALTER TABLE `bs_payment_deposit_use`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_payment_items`
--
ALTER TABLE `bs_payment_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_payment_orders`
--
ALTER TABLE `bs_payment_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_payment_types`
--
ALTER TABLE `bs_payment_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_pmr_pack_items`
--
ALTER TABLE `bs_pmr_pack_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_price`
--
ALTER TABLE `bs_price`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_processing_summary`
--
ALTER TABLE `bs_processing_summary`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_productions`
--
ALTER TABLE `bs_productions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_productions_crucible`
--
ALTER TABLE `bs_productions_crucible`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_productions_furnace`
--
ALTER TABLE `bs_productions_furnace`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_productions_in`
--
ALTER TABLE `bs_productions_in`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_productions_oven`
--
ALTER TABLE `bs_productions_oven`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_productions_pmr`
--
ALTER TABLE `bs_productions_pmr`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_productions_round`
--
ALTER TABLE `bs_productions_round`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_productions_scale`
--
ALTER TABLE `bs_productions_scale`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_productions_silver_save`
--
ALTER TABLE `bs_productions_silver_save`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_productions_switch`
--
ALTER TABLE `bs_productions_switch`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_products`
--
ALTER TABLE `bs_products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_products_bwd`
--
ALTER TABLE `bs_products_bwd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_products_export`
--
ALTER TABLE `bs_products_export`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_products_import`
--
ALTER TABLE `bs_products_import`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_products_turn`
--
ALTER TABLE `bs_products_turn`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_products_type`
--
ALTER TABLE `bs_products_type`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_profit_daily`
--
ALTER TABLE `bs_profit_daily`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_purchase_buy`
--
ALTER TABLE `bs_purchase_buy`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_purchase_buyfix`
--
ALTER TABLE `bs_purchase_buyfix`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_purchase_spot`
--
ALTER TABLE `bs_purchase_spot`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_purchase_spot_profit`
--
ALTER TABLE `bs_purchase_spot_profit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_purchase_spot_profit_bwd`
--
ALTER TABLE `bs_purchase_spot_profit_bwd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_purchase_usd`
--
ALTER TABLE `bs_purchase_usd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_purchase_usd_profit`
--
ALTER TABLE `bs_purchase_usd_profit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_purchase_usd_profit_bwd`
--
ALTER TABLE `bs_purchase_usd_profit_bwd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_quick_orders`
--
ALTER TABLE `bs_quick_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_reserve_silver`
--
ALTER TABLE `bs_reserve_silver`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_sales_spot`
--
ALTER TABLE `bs_sales_spot`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_scrap_items`
--
ALTER TABLE `bs_scrap_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_scrap_total`
--
ALTER TABLE `bs_scrap_total`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_shipping_bwd`
--
ALTER TABLE `bs_shipping_bwd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_cash`
--
ALTER TABLE `bs_smg_cash`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_claim`
--
ALTER TABLE `bs_smg_claim`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_daily`
--
ALTER TABLE `bs_smg_daily`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_initial`
--
ALTER TABLE `bs_smg_initial`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_interest`
--
ALTER TABLE `bs_smg_interest`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_other`
--
ALTER TABLE `bs_smg_other`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_payment`
--
ALTER TABLE `bs_smg_payment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_rate`
--
ALTER TABLE `bs_smg_rate`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_rate_rollover`
--
ALTER TABLE `bs_smg_rate_rollover`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_receiving`
--
ALTER TABLE `bs_smg_receiving`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_rollover`
--
ALTER TABLE `bs_smg_rollover`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_rollover_type`
--
ALTER TABLE `bs_smg_rollover_type`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_stx_cash`
--
ALTER TABLE `bs_smg_stx_cash`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_stx_claim`
--
ALTER TABLE `bs_smg_stx_claim`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_stx_daily`
--
ALTER TABLE `bs_smg_stx_daily`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_stx_initial`
--
ALTER TABLE `bs_smg_stx_initial`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_stx_interest`
--
ALTER TABLE `bs_smg_stx_interest`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_stx_other`
--
ALTER TABLE `bs_smg_stx_other`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_stx_payment`
--
ALTER TABLE `bs_smg_stx_payment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_stx_rate`
--
ALTER TABLE `bs_smg_stx_rate`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_stx_rate_rollover`
--
ALTER TABLE `bs_smg_stx_rate_rollover`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_stx_receiving`
--
ALTER TABLE `bs_smg_stx_receiving`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_stx_rollover`
--
ALTER TABLE `bs_smg_stx_rollover`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_stx_trade`
--
ALTER TABLE `bs_smg_stx_trade`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_smg_trade`
--
ALTER TABLE `bs_smg_trade`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_spot_profit_daily`
--
ALTER TABLE `bs_spot_profit_daily`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_spot_usd_splited`
--
ALTER TABLE `bs_spot_usd_splited`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_stock_adjuest_types`
--
ALTER TABLE `bs_stock_adjuest_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_stock_adjusted`
--
ALTER TABLE `bs_stock_adjusted`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_stock_adjusted_bwd`
--
ALTER TABLE `bs_stock_adjusted_bwd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_stock_adjusted_over`
--
ALTER TABLE `bs_stock_adjusted_over`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_stock_adjust_type_bwd`
--
ALTER TABLE `bs_stock_adjust_type_bwd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_stock_bwd`
--
ALTER TABLE `bs_stock_bwd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_stock_items`
--
ALTER TABLE `bs_stock_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_stock_prepare`
--
ALTER TABLE `bs_stock_prepare`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_stock_silver`
--
ALTER TABLE `bs_stock_silver`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_suppliers`
--
ALTER TABLE `bs_suppliers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_suppliers_jinsung`
--
ALTER TABLE `bs_suppliers_jinsung`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_suppliers_mapping`
--
ALTER TABLE `bs_suppliers_mapping`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_suppliers_mapping_1`
--
ALTER TABLE `bs_suppliers_mapping_1`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_suppliers_standard`
--
ALTER TABLE `bs_suppliers_standard`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_supplier_groups`
--
ALTER TABLE `bs_supplier_groups`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_switch_pack_items`
--
ALTER TABLE `bs_switch_pack_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_trade_spot`
--
ALTER TABLE `bs_trade_spot`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_transfers`
--
ALTER TABLE `bs_transfers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_transfer_adjusted`
--
ALTER TABLE `bs_transfer_adjusted`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_transfer_paid`
--
ALTER TABLE `bs_transfer_paid`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_transfer_payments`
--
ALTER TABLE `bs_transfer_payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_transfer_report`
--
ALTER TABLE `bs_transfer_report`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_usd_payment`
--
ALTER TABLE `bs_usd_payment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bs_usd_profit_daily`
--
ALTER TABLE `bs_usd_profit_daily`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_cities`
--
ALTER TABLE `db_cities`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_countries`
--
ALTER TABLE `db_countries`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_districts`
--
ALTER TABLE `db_districts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_regions`
--
ALTER TABLE `db_regions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_subdistricts`
--
ALTER TABLE `db_subdistricts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_zipcode_thailand`
--
ALTER TABLE `db_zipcode_thailand`
  MODIFY `ZIPCODE_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_id_seq`
--
ALTER TABLE `order_id_seq`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `os_accounts`
--
ALTER TABLE `os_accounts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `os_address`
--
ALTER TABLE `os_address`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `os_concurrents`
--
ALTER TABLE `os_concurrents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `os_contacts`
--
ALTER TABLE `os_contacts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `os_groups`
--
ALTER TABLE `os_groups`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `os_logs`
--
ALTER TABLE `os_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `os_messages`
--
ALTER TABLE `os_messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `os_notifications`
--
ALTER TABLE `os_notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `os_organizations`
--
ALTER TABLE `os_organizations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `os_permissions`
--
ALTER TABLE `os_permissions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `os_users`
--
ALTER TABLE `os_users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `os_variable`
--
ALTER TABLE `os_variable`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Structure for view `v_orders_summary`
--
DROP TABLE IF EXISTS `v_orders_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_orders_summary`  AS SELECT `o`.`customer_id` AS `customer_id`, `o`.`code` AS `order_code`, `o`.`customer_name` AS `customer_name`, `o`.`date` AS `order_date`, `e`.`fullname` AS `sales_name`, round(`o`.`amount`,4) AS `amount`, round(`o`.`price`,4) AS `price_thb`, round(coalesce(nullif(`o`.`usd`,0),(case when ((`o`.`rate_exchange` is not null) and (`o`.`rate_exchange` <> 0)) then (`o`.`price` / `o`.`rate_exchange`) end)),4) AS `price_usd`, round(coalesce(nullif(`o`.`total`,0),(ifnull(`o`.`amount`,0) * ifnull(`o`.`price`,0))),4) AS `total_amount`, round(`o`.`net`,4) AS `total_vat_included`, `o`.`delivery_date` AS `delivery_date`, `o`.`rate_spot` AS `rate_spot`, `o`.`rate_exchange` AS `rate_exchange`, `p`.`name` AS `product_name` FROM ((`bs_orders` `o` left join `bs_products` `p` on((`p`.`id` = `o`.`product_id`))) left join `bs_employees` `e` on((`e`.`id` = `o`.`sales`))) WHERE (((`o`.`parent` is null) OR (`o`.`parent` = 0)) AND (`o`.`status` > -(1))) ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
