-- Izveido datubāzi
CREATE DATABASE IF NOT EXISTS instagram_clone
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Izvēlas datubāzi, ar kuru strādāt
USE instagram_clone;

-- Izveido tabulu users
CREATE TABLE IF NOT EXISTS users (
  user_id INT(11) NOT NULL AUTO_INCREMENT,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'user') DEFAULT 'user',
  registered_date DATETIME NOT NULL,
  PRIMARY KEY (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
