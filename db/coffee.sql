-- phpMyAdmin SQL Dump
-- version 5.2.1deb1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 09, 2023 at 06:34 PM
-- Server version: 10.11.2-MariaDB-1
-- PHP Version: 8.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `coffee`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `email`, `password`, `profile`) VALUES
('fvx67lgoIXB2gqtyO0zF', 'bahaa', 'bahati@rumble.ru', 'cd0db81ad81e3a32bab943e39946c623fd53e2bd', 'Logo_white.png'),
('G5uyDwzYczETUSp6czkh', 'mungzz', 'paulmungala1@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b\n', 'pexels-reza-kargar-12513230.jpg'),
('rnXDX1l34BDpNb5YakLs', 'baha', 'baha.karanja@gmail.com', '$2y$10$Oz/wAoq61hxr.H/ypZ7eJO1RCOYRymLydHz6mfoRTWHCdnQN02.uO', 'DemonSlayer.png'),
('sPw7sqFsEsaG3A9ozD2Q', 'paul', 'mungalaaa8@gmail.com', '$2y$10$Oz/wAoq61hxr.H/ypZ7eJO1RCOYRymLydHz6mfoRTWHCdnQN02.uO', 'nas.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` varchar(255) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `product_id` varchar(20) NOT NULL,
  `price` varchar(10) NOT NULL,
  `qty` varchar(2) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `price`, `qty`) VALUES
('3w8L5tiQBPQZdYEYOCCO', 'wAdPKELWp11S7ct6mucp', 'yKSJImFNH8zILnMYxnao', '3000', '1'),
('Xdz8FaVkDa3KgwZEqS8G', 'wAdPKELWp11S7ct6mucp', 'VZa2YsWMgl2PAm6Wz4fm', '1500', '1');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` varchar(255) NOT NULL,
  `category_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
('302vBwc9TWIMsgeOTQWG', 'Costumes'),
('aSqwCcNfp5PEDZ1XMNDR', 'Apparel'),
('bVxWZS65ADjxELypBhte', 'Formal wear'),
('DenkSgERYLyhcfiIkOrn', 'Traditional'),
('GHabLhsvVXospoZ2pqoM', 'Accessories'),
('hfkSxM49FowRa2LIcws3', 'Foot wear'),
('w4xKYNi3xfhFFkbZG9Tm', 'Watches &amp; Jewelry');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` varchar(255) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `user_id`, `name`, `email`, `subject`, `message`) VALUES
('EuBBDFyYyKrfg1BWpf34', '69n1MoqgJfy4vCPdQXPA', 'Paul Mungala', 'mungalaaa8@gmail.com', 'woocommerce affected template', 'time time'),
('jdwJGyuImDASvTXoO3eu', '69n1MoqgJfy4vCPdQXPA', 'Paul Mungala', 'mungalaaa8@gmail.com', 'woocommerce affected template', 'mull'),
('SaFEGi9L7JySm1EgFHEw', '69n1MoqgJfy4vCPdQXPA', 'Paul Mungala', 'mungalaaa8@gmail.com', 'woocommerce affected template', 'time time'),
('yoEPKCUffDLiZxH952S3', '69n1MoqgJfy4vCPdQXPA', 'Paul Mungala', 'mungalaaa8@gmail.com', 'BJLLJ', 'minn');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` varchar(255) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `number` varchar(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `address` varchar(200) NOT NULL,
  `address_type` varchar(10) NOT NULL,
  `method` varchar(50) NOT NULL,
  `product_id` varchar(20) NOT NULL,
  `price` varchar(10) NOT NULL,
  `qty` varchar(2) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `status` enum('Completed','In Progress','Cancelled') NOT NULL DEFAULT 'In Progress',
  `order_no` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `name`, `number`, `email`, `address`, `address_type`, `method`, `product_id`, `price`, `qty`, `date`, `status`, `order_no`) VALUES
('dviNSjTpGVtudMT0KYMX', '69n1MoqgJfy4vCPdQXPA', 'paul', '0722254522', 'mungalaaa8@gmail.com', 'PO. BOX 412-01020 Kenol,Kenya, 123, Nairobi, Nairobi, 412-01', 'home', 'cash on delivery', 'rgGI0OHXrDeKkHqf8iO6', '2600', '2', '2023-07-20', 'In Progress', 'pending'),
('dviNSjTpWVtudMT0KKMX', '69n1MoqgJfy4vCPdQXPA', 'paul', '0722254522', 'mungalaaa8@gmail.com', 'PO. BOX 412-01020 Kenol,Kenya, 123, Nairobi, Nairobi, 412-01', 'home', 'cash on delivery', 'zLPDPic1FuTq5N348nvy', '8', '1', '2023-07-16', 'In Progress', 'pending'),
('dviNSjTpWVtudMT0KYMX', '69n1MoqgJfy4vCPdQXPA', 'paul', '0722254522', 'mungalaaa8@gmail.com', 'PO. BOX 412-01020 Kenol,Kenya, 123, Nairobi, Nairobi, 412-01', 'home', 'cash on delivery', 'zLPDPic1FuTq5N348nvy', '2600', '2', '2023-07-20', 'In Progress', 'pending'),
('rXj2CiEqdbRM4YIsRZAr', 'wAdPKELWp11S7ct6mucp', 'paul', '0722254522', 'mungalaaa8@gmail.com', 'PO. BOX 412-01020 Kenol,Kenya, 12235, Nairobi, Kenya, 412-01', 'home', 'cash on delivery', 'pUq566xy7nRJFK1LICRB', '450', '1', '2023-07-17', 'In Progress', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` float DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `product_detail` varchar(1000) NOT NULL,
  `status` varchar(100) NOT NULL,
  `qty` int(11) DEFAULT NULL,
  `category_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image`, `product_detail`, `status`, `qty`, `category_id`) VALUES
('26lPPTjXh9EkNc7WocS5', 'Hoodie Fiti', 600, 'hoodie4.webp', 'This hoodie is made of high-quality materials that will keep you warm and comfortable all season long.\r\n\r\nThe fabric is thick and soft, and the stitching is durable. Plus, the hood is lined with fleece for extra warmth.\r\n\r\nSecond, this hoodie has a stylish design that will look great with any outfit.\r\n\r\nThe color is neutral so that it will go with anything, and the cut is flattering.\r\n\r\nThe Kangaroo pocket is also a nice touch.\r\n\r\nFinally, this hoodie is an excellent value for the price. It&rsquo;s well-made and stylish, but it won&rsquo;t break the bank.\r\n\r\nSo if you&rsquo;re looking for a new hoodie this season, be sure to order this one today', 'active', 1, 'aSqwCcNfp5PEDZ1XMNDR'),
('7ODo29BCRhLdngT8vpqS', 'TULI-CAT EYE SUNGLASSES - BLUE', 1500, 'TULI CAT SUNGLASSES.webp', 'a retro aesthetic for the modern woman with these aviator glasses. Set in tortoiseshell frames with reinforced tips, they&#039;re defined by dual flat-brow frames.\r\n\r\n', 'active', 11, 'GHabLhsvVXospoZ2pqoM'),
('97As7a8v8eOFcq6HJyAG', 'TNF Two Sided  Jacket', 2400, 'TNF Two Sided Jacket.png', 'We&rsquo;ve updated the fan-favorite Men&rsquo;s Aconcagua line to include 100% recycled body fabric on the outside, and a combination of fully recycled 600-fill down and synthetic insulation on the inside. The Men&rsquo;s Aconcagua 3 Hoodie also features zoned sheet insulation in the hood, shoulders and underarm for greater freedom of movement.', 'active', 1, 'aSqwCcNfp5PEDZ1XMNDR'),
('aSBHDzG26iXurm6cfoNv', 'Kimono', 650, 'kimono.webp', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry&#39;s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 'active', 2, 'DenkSgERYLyhcfiIkOrn'),
('bEvIK2PvwOqY4l8nuPTZ', 'Blouse', 450, 'clothe.webp', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry&#39;s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 'active', 3, 'bVxWZS65ADjxELypBhte'),
('BLTtlhOgq1cuz7plh4Ia', 'Casual Shirt', 350, 'IMG_1944_40a21d6b-3c2a-4258-9373-4c7e65b7d9bd_360x.webp', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry&#39;s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 'active', 0, 'bVxWZS65ADjxELypBhte'),
('cWeg7hIUUXvzyHc0XmNV', 'FIKIRA MINIMALIST CHUNKY HEELED SANDAL - WHITE', 5500, 'ZOvUmC9MgS_900x.jpg', 'Give your look a crisp white finish in these chunky block heel sandals!\r\nFeaturing white leather material with a thin ankle strap, slighlty cushioned in sole for extra comfort and an open toe front design. Perfect for styling seamlessly with your existing wardrobe pieces!\r\n\r\nPresented with a white cotton dust bag and box for proper storage.\r\n\r\nAvailable sizes:39, 40, 41\r\n\r\nCare details: Wipe with damp cloth', 'active', 4, 'hfkSxM49FowRa2LIcws3'),
('eBbtkVNYiJJKT9mCgYbk', 'Shati Ngori', 500, 'shirt4.webp', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry&#39;s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ip', 'active', 1, 'bVxWZS65ADjxELypBhte'),
('g5DLcNHmtHvq3DtJYsCb', 'Fany Kimono', 650, 'kimono2.webp', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry&#39;s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 'active', 2, 'DenkSgERYLyhcfiIkOrn'),
('hCtt8vFWYHedBBV4mGJY', 'Under Armour Men&#039;s Charged Assert 10 Running Shoe', 5400, 'Under-Armour.jpg', 'These are running shoes anyone can wear to go faster&mdash;with Charged Cushioning to help protect against impact, a breathable upper, and durability for miles.\r\n\r\n* Lightweight, breathable mesh upper with synthetic overlays for added durability &amp; support\r\n* EVA sockliner provides soft, step-in comfort\r\n* Charged Cushioning midsole uses compression molded foam for ultimate responsiveness &amp; durability\r\n* Solid rubber outsole covers high impact zones for greater durability with less weight\r\n* For runners who need flexibility, cushioning &amp; versatility', 'deactive', 3, 'hfkSxM49FowRa2LIcws3'),
('jo35YMmBWpvbCMB65UdA', 'Stylish Shirt', 400, 'shirt.webp', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry&#39;s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 'active', 3, 'bVxWZS65ADjxELypBhte'),
('kun96OpQed6Eww6M1URo', 'Skirt', 400, 'skirt.webp', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry&#39;s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 'active', 2, 'bVxWZS65ADjxELypBhte'),
('ngbWy4xTfLbSBVP3zyd8', 'ZETU MEN&#039;S CHINO SHORTS - STONE BLUE', 1599, 'IMG_9651_1800x1800.jpg', 'Product Details\r\nMen&#039;s Chino Shorts\r\n\r\nColor : Stone Blue\r\nSize &amp; Fit\r\nModel is wearing : Medium\r\n\r\nBrand\r\nZetu Men&#039;s offers the latest trends in men&#039;s wear at unbeatable prices!\r\n\r\nTake care of me\r\nMachine or hand wash according to instructions on care label\r\n\r\nAbout me\r\n100% Cotton', 'active', 8, 'bVxWZS65ADjxELypBhte'),
('pUq566xy7nRJFK1LICRB', 'Poncho', 450, 'poncho.webp', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry&#39;s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.\r\n\r\n', 'active', 4, 'DenkSgERYLyhcfiIkOrn'),
('qw9r5VO1gZO2Iv30opjN', 'KING&#039;S COLLECTION MEN&#039;S LEATHER BELT - BLACK', 1755, 'kings-collection-mens-leather-belt-black-896912_1800x1800.jpg', '- Leather belt by Kings Collection\r\n- Adjustable length\r\n- Pin buckle fastening\r\n- Do not wash\r\n- Matte finish \r\n- 100% leather\r\n- One Size', 'active', 13, 'GHabLhsvVXospoZ2pqoM'),
('rgGI0OHXrDeKkHqf8iO6', 'Curren Men Silver Auto Date Wrist Watch ', 2999, 'curren-men-silver-auto-date-wrist-watch.jpg', 'Curren Men Silver Auto Date Wrist Watch is a stylish and functional timepiece that is perfect for everyday wear. The watch features a date display, stainless steel band, mineral glass, and 30ATM water resistance. It is powered by a quartz movement, which is accurate and reliable. The Curren Men Silver Auto Date Wrist Watch is an excellent choice for those who are looking for a quality watch that will last for years to come.', 'active', 12, 'w4xKYNi3xfhFFkbZG9Tm'),
('Riq8PuR0v9Zqwva8Srvy', 'Kiss Collection Two Tone Eternity Necklace', 1950, 'KissCollectionTwo-ToneEternityNecklace_1800x1800.jpg', 'Capture the timeless essence of love with the Kiss Collection Two-Tone Eternity Necklace. Delicately crafted with a dazzling array of sparkling stones, this exquisite piece is a stunning symbol of your eternal devotion.\r\n\r\nThe unique two-tone design adds a modern touch to the classic eternity motif, making it a perfect addition to any jewellery collection. Give the gift of everlasting love with this breathtaking necklace, a true expression of affection that will be cherished for a lifetime.', 'active', 4, 'w4xKYNi3xfhFFkbZG9Tm'),
('uOarNNg0n3KD9OvPtItP', 'Ngepa Noma', 250, 'ngepa.webp', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry&#39;s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 'active', 3, 'GHabLhsvVXospoZ2pqoM'),
('VZa2YsWMgl2PAm6Wz4fm', 'Khaki Trouser for Men', 1500, 'Khaki Trouser.png', 'Brown', 'active', 10, 'bVxWZS65ADjxELypBhte'),
('wrJTrzoHsuEwr7hGi3R6', 'Casual Friday', 450, 'casual.webp', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry&#39;s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 'active', 1, 'bVxWZS65ADjxELypBhte'),
('x0UeFmchPUtNMDe7LP9h', 'Lessa Bone Patent', 2800, 'STEVE MADDEN.png', 'The CANDID sandal balances a sleek strap design with a wide block heel, making this a versatile and surprisingly wearable dress sandal.\r\n\r\n - Block heel leather sandal\r\n - Crossed ankle strap with buckle closure\r\n - 4 inch heel height\r\n - Leather upper material\r\n - Synthetic lining\r\n - Synthetic sole\r\n - Imported', 'active', 4, 'hfkSxM49FowRa2LIcws3'),
('X3Qs56n0ZyNZtmPB2Uwi', 'SCRIPT FITTED - BLACK/TIE DYE', 999, 'Trapstar Cap.png', '- Embroidered Irongate T logo on front\r\n\r\n- Embroidered Script Allover Hat\r\n\r\n- Fitted Cap\r\n\r\n- Tie Dye Visor\r\n\r\n', 'active', 2, 'GHabLhsvVXospoZ2pqoM'),
('xEaApYACOPiQRppGRicQ', 'Louis Vuitton Shoulder Bag', 2999, 'Louis Vuitton Shoulder Bag.png', 'Classic bag\r\nPure leather', 'active', 3, 'GHabLhsvVXospoZ2pqoM'),
('xfxroT8dUzcIEk3re4U5', 'Darth Vader Helmet with Voice Changer', 1999, 'il_794xN.4403874833_j6yb.jpg', 'Darth Vader, Darth Vader Helmet with Voice Changer, Star Wars Darth Vader Cosplay, Darth Vader Mask, Wearable Movie Prop Replica', 'active', 7, '302vBwc9TWIMsgeOTQWG'),
('XpOYk6RfWKhq3jQPj21O', 'Jordan 4 Retro Infrared', 4499, 'air-jordan-4-retro-infrared.jpg', 'The Air Jordan 4 Infrared features a tonal grey Durabuck upper with black TPU mesh inserts, tech straps, and heel tabs. Hits of Infrared on the lacing system and woven Jordan Flight tongue label add a sharp contrast to the neutral-toned base. From there, a white sole with visible Air units completes the design.\r\n', 'active', 8, 'hfkSxM49FowRa2LIcws3'),
('yKSJImFNH8zILnMYxnao', 'Custom Air Force 1 ', 3000, 'AF1-dark-Ladies.png', 'clean with a damp cloth', 'active', 3, 'hfkSxM49FowRa2LIcws3'),
('zjx7PCA8Vn9dnOw5jcjt', 'EQ For Men Wallet', 1600, 'EQForMenWalletBrown_1800x1800.webp', 'Introducing the EQ For Men Wallet, a sleek and stylish accessory for the modern man. This wallet is made from high-quality materials, ensuring that it will last for years to come. It comes with a minimalist design making it the perfect choice for the man who values both style and functionality. \r\nWhether you&#039;re heading to the office or running errands, the EQ For Men Wallet is the perfect accessory to keep your essentials organized and stylish. So, why wait? Upgrade your everyday carry today with the EQ For Men Wallet.', 'active', 14, 'GHabLhsvVXospoZ2pqoM'),
('zLPDPic1FuTq5N348nvy', 'for-him-transparent-frames-round-sunglasses-grey', 1300, 'round-sunglasses-grey.jpg', 'Product Details\r\n- Grey transparent frames\r\n- Black polarized lens\r\n\r\nCare Details\r\n- Clean with lens cleaner and microfiber cloth', 'active', 4, 'GHabLhsvVXospoZ2pqoM');

-- --------------------------------------------------------

--
-- Table structure for table `stk_transactions`
--

CREATE TABLE `stk_transactions` (
  `id` int(11) NOT NULL,
  `order_no` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `CheckoutRequestID` varchar(255) NOT NULL,
  `MerchantRequestID` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` varchar(255) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `user_type`) VALUES
('wAdPKELWp11S7ct6mucp', 'paul', 'mungalaaa8@gmail.com', '120d19640e9a53318de88eedd1ac646e61e2dfc0', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` varchar(255) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `product_id` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `price`) VALUES
('FngaVJmk3vBeq3KUmt03', 'UAVjN46f0bvXSKquej8S', 'jo35YMmBWpvbCMB65UdA', '160');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD UNIQUE KEY `category_id` (`category_id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `fk_orders_product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `fk_category` (`category_id`);

--
-- Indexes for table `stk_transactions`
--
ALTER TABLE `stk_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `stk_transactions`
--
ALTER TABLE `stk_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
