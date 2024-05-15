 CREATE TABLE users (
     id INT AUTO_INCREMENT PRIMARY KEY,
     username VARCHAR(50) NOT NULL UNIQUE,
     password VARCHAR(255) NOT NULL,
     role ENUM('admin', 'guard', 'cook') NOT NULL
 );
 INSERT INTO users (username, password, role)
 VALUES ('admin', 'password', 'admin');

 ALTER TABLE users ADD COLUMN dob DATE;
 ALTER TABLE users ADD COLUMN joining_date DATE;
 ALTER TABLE users ADD COLUMN gender ENUM('male', 'female');




 CREATE TABLE prisons (
     id INT AUTO_INCREMENT PRIMARY KEY,
     cell_number INT NOT NULL UNIQUE,
     criminal_name VARCHAR(100) NOT NULL,
     crime VARCHAR(255) NOT NULL,
     gender ENUM('male', 'female') NOT NULL
 );
        
 CREATE TABLE inmates (
     id INT AUTO_INCREMENT PRIMARY KEY,
     criminal_name VARCHAR(100) NOT NULL,
     crime ENUM('Theft', 'Murder', 'Assault', 'Drug Trafficking', 'Other') NOT NULL,
     adhar_number VARCHAR(12) NOT NULL, -- Assuming Aadhar numbers are 12 characters long
     belongings VARCHAR(255) NOT NULL,
     gender ENUM('male', 'female') NOT NULL,
     age INT NOT NULL,
     cell_block INT NOT NULL, -- Assuming cell_block corresponds to cell_number in prisons table
     joining_date DATE NOT NULL,
     sentence_duration INT NOT NULL,
     leaving_date DATE NOT NULL,
     court_name VARCHAR(100) NOT NULL,
     crime_history ENUM('Yes', 'No') NOT NULL,
     previous_crime VARCHAR(255) NOT NULL,
     CHECK (age >= 18), -- Ensuring age is above 18
     CHECK (joining_date != leaving_date) -- Ensuring joining_date is not the same as leaving_date
 );







ALTER TABLE inmates
ADD release_status ENUM('Released', 'Not Released') DEFAULT 'Not Released' AFTER previous_crime;




 CREATE TABLE visitors (
     id INT AUTO_INCREMENT PRIMARY KEY,
     visitor_name VARCHAR(100) NOT NULL,
     id_proof_type VARCHAR(50) NOT NULL,
     id_proof_number VARCHAR(50) NOT NULL,
     criminal_name VARCHAR(100) NOT NULL,
     relation VARCHAR(100) NOT NULL,
     visit_date DATE NOT NULL,
     visit_time TIME NOT NULL
 );



 ALTER TABLE inmates ADD COLUMN adhar_number VARCHAR(20) NOT NULL;
 ALTER TABLE inmates ADD COLUMN belongings VARCHAR(255) NOT NULL;
 ALTER TABLE inmates ADD COLUMN gender ENUM('male', 'female') NOT NULL;
 ALTER TABLE inmates ADD COLUMN age INT NOT NULL;
 ALTER TABLE inmates ADD COLUMN cell_block VARCHAR(255) NOT NULL;
 ALTER TABLE inmates ADD COLUMN joining_date DATE;
 ALTER TABLE inmates ADD COLUMN sentence_duration INT;
 ALTER TABLE inmates ADD COLUMN leaving_date DATE;
 ALTER TABLE inmates ADD COLUMN court_name VARCHAR(255) NOT NULL;
 ALTER TABLE inmates ADD COLUMN crime_history ENUM('Yes', 'No') NOT NULL DEFAULT 'No';
 ALTER TABLE inmates ADD COLUMN previous_crime VARCHAR(255);

 CREATE TABLE jail_info (
     id INT AUTO_INCREMENT PRIMARY KEY,
     total_cells INT UNSIGNED NOT NULL CHECK (total_cells > 0),
     total_guards INT UNSIGNED NOT NULL CHECK (total_guards > 0),
     total_cooks INT UNSIGNED NOT NULL CHECK (total_cooks > 0),
     jail_name VARCHAR(100) NOT NULL,
     jail_address VARCHAR(255) NOT NULL,
     UNIQUE(jail_name, jail_address)
 );
