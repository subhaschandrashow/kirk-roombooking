CREATE TABLE IF NOT EXISTS `#__kirk_rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_name` VARCHAR(100),
  `room_description` text,
  `status` tinyint(1),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__kirk_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11),
  `booking_date` date,
  `checkin_time` char(5) NOT NULL,
  `checkout_time` char(5) NOT NULL,
  `customer_name` VARCHAR(100),
  `customer_phone` VARCHAR(100),
  `customer_email` VARCHAR(100),
  `customer_address` text,
  `booking_reason` text,
  `admin_note` text,
  `payment_gateway` char(50),
  `amount` float(10,2),
  `booking_master_id` int(11),
  `holidays` text,
  `business_name` VARCHAR(100),
  `add_info` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__kirk_booking_enquiries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11),
  `booking_date` date,
  `checkin_time` char(5) NOT NULL,
  `checkout_time` char(5) NOT NULL,
  `customer_name` VARCHAR(100),
  `customer_phone` VARCHAR(100),
  `customer_email` VARCHAR(100),
  `customer_address` text,
  `booking_reason` text,
  `admin_note` text,
  `event_required` tinyint(1)  NOT NULL DEFAULT '0',
  `payment_gateway` char(50),
  `amount` float(10,2),
  `business_name` VARCHAR(100),
  `add_info` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__kirk_booking_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11),
  `event_title` VARCHAR(100),
  `event_description` text,
  `regular` tinyint(1) default 0,
  `event_period` int(11),
  `event_enddate` date,
  `promotion_starts` int(11),
  `public` tinyint(1) default 0,
  `images` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__kirk_addons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `addon_title` VARCHAR(100),
  `addon_description` text,
  `price` float(10,2),
  `status` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__kirk_enquiry_addons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enquiry_id` int(11),
  `addon_id` int(11),
  `price` float(10,2),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__kirk_booking_addons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11),
  `addon_id` int(11),
  `price` float(10,2),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__kirk_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11),
  `payment_method` CHAR(50),
  `transaction_id` VARCHAR(255),
  `amt`	float(10,2),
  `transaction_date` date,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__kirk_refunds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11),
  `booking_date` date,
  `checkin_time` char(5) NOT NULL,
  `checkout_time` char(5) NOT NULL,
  `customer_name` VARCHAR(100),
  `customer_phone` VARCHAR(100),
  `customer_email` VARCHAR(100),
  `customer_address` text,
  `booking_reason` text,
  `admin_note` text,
  `payment_gateway` char(50),
  `amount` float(10,2),
  `refund_trx_id` VARCHAR(100),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

