DROP TABLE IF EXISTS cart;
DROP TABLE IF EXISTS order_detail;
DROP TABLE IF EXISTS user_votes;
DROP TABLE IF EXISTS user_order;
DROP TABLE IF EXISTS user_review_food;
DROP TABLE IF EXISTS menu_item;
DROP TABLE IF EXISTS user;

CREATE TABLE user (
	user_id INT AUTO_INCREMENT NOT NULL,
	first_name VARCHAR(50) NOT NULL,
	last_name VARCHAR(50) NOT NULL,
	email VARCHAR(50) NOT NULL,
	password VARCHAR(255) NOT NULL,
	is_admin TINYINT(1) DEFAULT 0,
	PRIMARY KEY (user_id)
) ENGINE=InnoDB;

CREATE TABLE menu_item (
	item_id INT AUTO_INCREMENT NOT NULL,
	item_name VARCHAR(50) NOT NULL,
	item_description VARCHAR(255) NOT NULL,
	item_category VARCHAR(15) NOT NULL,
	unit_price DECIMAL(10, 2) NOT NULL,
	item_photo VARCHAR(200),
	thumbs_up INT DEFAULT 0,
	thumbs_down INT DEFAULT 0,
	PRIMARY KEY (item_id)
) ENGINE=InnoDB;

CREATE TABLE user_order (
	order_id INT AUTO_INCREMENT NOT NULL,
	user_id INT NOT NULL,
	order_datetime DATETIME,
	total_price DECIMAL(10,2),
	PRIMARY KEY (order_id),
	FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE user_votes (
	user_id INT NOT NULL,
	item_id INT NOT NULL,
	vote_type ENUM('up', 'down') NOT NULL,
	PRIMARY KEY (user_id, item_id),
	FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE,
	FOREIGN KEY (item_id) REFERENCES menu_item(item_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE order_detail (
	detail_id INT AUTO_INCREMENT NOT NULL,
	order_id INT NOT NULL,
	item_id INT NOT NULL,
	quantity INT NOT NULL,
	PRIMARY KEY (detail_id),
	FOREIGN KEY (order_id) REFERENCES user_order(order_id) ON DELETE CASCADE,
	FOREIGN KEY (item_id) REFERENCES menu_item(item_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE cart (
	user_id INT NOT NULL,
	item_id INT NOT NULL,
	quantity INT NOT NULL,
	PRIMARY KEY (user_id, item_id),
	FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE,
	FOREIGN KEY (item_id) REFERENCES menu_item(item_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE user_review_food (
	review_id INT AUTO_INCREMENT NOT NULL,
	user_id INT NOT NULL,
	item_id INT NOT NULL,
	review TEXT,
	PRIMARY KEY (review_id),
	FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE,
	FOREIGN KEY (item_id) REFERENCES menu_item(item_id) ON DELETE CASCADE
) ENGINE=InnoDB;
