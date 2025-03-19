CREATE DATABASE IF NOT EXISTS privacyunsubscribe;
USE privacyunsubscribe;

CREATE TABLE unsubscribe (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    country VARCHAR(50) NOT NULL,
    pub VARCHAR(5) NOT NULL,
    date DATETIME NOT NULL
);
