CREATE DATABASE IF NOT EXISTS user_form_db_zasim;
USE user_form_db_zasim;

CREATE TABLE IF NOT EXISTS satisfaction_survey (
    id INT AUTO_INCREMENT PRIMARY KEY,
    respondent_name VARCHAR(255) NOT NULL,
    service_rating INT NOT NULL CHECK (service_rating >= 1 AND service_rating <= 5),
    recommendation_likelihood INT NOT NULL CHECK (recommendation_likelihood >= 1 AND recommendation_likelihood <= 10),
    suggestions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
