-- Database schema for Single-Point-of-Entry Authenticated Website
-- Matches the schema created programmatically by createTable() in functions.php.
-- You do not need to run this manually if the app's createTable() function
-- has already been called against your database — it is provided here for
-- reference and for setting up the table directly if preferred.

CREATE TABLE IF NOT EXISTS usersTable (
    userID       INT NOT NULL AUTO_INCREMENT,
    firstName    VARCHAR(45) NOT NULL,
    middleNames  VARCHAR(45),
    surname      VARCHAR(45) NOT NULL,
    email        VARCHAR(45) NOT NULL,
    username     VARCHAR(30) NOT NULL,
    password     VARCHAR(30) NOT NULL,
    PRIMARY KEY (userID),
    UNIQUE KEY (email),
    UNIQUE KEY (username)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
