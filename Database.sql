SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dht`
--

DELIMITER $$
--
-- 存储过程
--
CREATE DEFINER=`root`@`%` PROCEDURE `addMetadata`(IN `p1` CHAR(40), IN `p2` INT(11), IN `p3` TINYTEXT, IN `p4` TINYTEXT, IN `p5` BIGINT(20), IN `p6` INT(11), IN `p7` MEDIUMTEXT, IN `p8` TEXT)
BEGIN
  insert into metadata (`hash`,`time`,name,created,size,files_num,files_name,files_size) values (p1,p2,p3,p4,p5,p6,p7,p8);
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `addSearch`(IN `p1` char(64),IN `p2` char(40))
BEGIN
  insert into search (keyword,hashs) values (p1,p2) on duplicate key update hashs=CONCAT(hashs,'|',p2);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- 表的结构 `metadata`
--

CREATE TABLE IF NOT EXISTS `metadata` (
  `id` int(11) NOT NULL,
  `hash` char(40) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `name` text,
  `created` tinytext,
  `size` bigint(20) DEFAULT NULL,
  `files_num` int(11) DEFAULT NULL,
  `files_name` mediumtext,
  `files_size` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `search`
--

CREATE TABLE IF NOT EXISTS `search` (
  `keyword` char(64) NOT NULL,
  `hashs` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `metadata`
--
ALTER TABLE `metadata`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hash` (`hash`);

--
-- Indexes for table `search`
--
ALTER TABLE `search`
  ADD UNIQUE KEY `keyword` (`keyword`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `metadata`
--
ALTER TABLE `metadata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
