CREATE TABLE cities
(
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE address_book
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    last_name  VARCHAR(255) NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    email      VARCHAR(255) NOT NULL,
    street     VARCHAR(255) NOT NULL,
    zip_code   VARCHAR(20)  NOT NULL,
    city_id    INT,
    FOREIGN KEY (city_id) REFERENCES cities (id)
);