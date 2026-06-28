CREATE DATABASE IF NOT EXISTS human_skill_exchange
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE human_skill_exchange;

DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS mentoring_rooms;
DROP TABLE IF EXISTS portfolios;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS exchange_progress;
DROP TABLE IF EXISTS exchange_requests;
DROP TABLE IF EXISTS offers;
DROP TABLE IF EXISTS needs;
DROP TABLE IF EXISTS skills;
DROP TABLE IF EXISTS profiles;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS plans;

CREATE TABLE plans (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    price INT NOT NULL DEFAULT 0,
    max_skills INT NULL,
    max_needs INT NULL,
    max_offers INT NULL,
    max_exchange_requests INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    plan_id BIGINT UNSIGNED NULL DEFAULT 1,
    token VARCHAR(255) NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_plan FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE profiles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,
    bio TEXT NOT NULL,
    location VARCHAR(120) NOT NULL,
    work_mode ENUM('online', 'offline', 'hybrid') NOT NULL DEFAULT 'online',
    available_time VARCHAR(120) NOT NULL,
    portfolio_url VARCHAR(255) NULL,
    social_url VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_profiles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE skills (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(120) NOT NULL,
    category VARCHAR(100) NOT NULL,
    level ENUM('beginner', 'intermediate', 'advanced') NOT NULL DEFAULT 'beginner',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_skills_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE needs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    exchange_offer TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_needs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE offers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    type ENUM('skill', 'time', 'experience', 'mentoring', 'project', 'collaboration') NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    exchange_expectation TEXT NOT NULL,
    available_duration VARCHAR(120) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_offers_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE exchange_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    from_user_id BIGINT UNSIGNED NOT NULL,
    to_user_id BIGINT UNSIGNED NOT NULL,
    offer_id BIGINT UNSIGNED NULL,
    need_id BIGINT UNSIGNED NULL,
    message TEXT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected', 'in_progress', 'completed', 'reviewed', 'cancelled') NOT NULL DEFAULT 'pending',
    completed_by_from_user BOOLEAN NOT NULL DEFAULT FALSE,
    completed_by_to_user BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_exchange_from_user FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_exchange_to_user FOREIGN KEY (to_user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_exchange_offer FOREIGN KEY (offer_id) REFERENCES offers(id) ON DELETE SET NULL,
    CONSTRAINT fk_exchange_need FOREIGN KEY (need_id) REFERENCES needs(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE exchange_progress (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    exchange_request_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    progress_note TEXT NOT NULL,
    file_url VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_progress_exchange FOREIGN KEY (exchange_request_id) REFERENCES exchange_requests(id) ON DELETE CASCADE,
    CONSTRAINT fk_progress_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reviews (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    exchange_request_id BIGINT UNSIGNED NOT NULL,
    reviewer_id BIGINT UNSIGNED NOT NULL,
    reviewed_user_id BIGINT UNSIGNED NOT NULL,
    rating INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_reviews_exchange FOREIGN KEY (exchange_request_id) REFERENCES exchange_requests(id) ON DELETE CASCADE,
    CONSTRAINT fk_reviews_reviewer FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_reviews_reviewed_user FOREIGN KEY (reviewed_user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT unique_exchange_review UNIQUE (exchange_request_id, reviewer_id, reviewed_user_id),
    CONSTRAINT check_rating_range CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE portfolios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(160) NOT NULL,
    description TEXT NOT NULL,
    file_url VARCHAR(255) NULL,
    project_url VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_portfolios_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE mentoring_rooms (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mentor_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    duration_minutes INT NOT NULL,
    price INT NOT NULL DEFAULT 0,
    schedule DATETIME NULL,
    status ENUM('open', 'booked', 'completed', 'cancelled') NOT NULL DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_mentoring_mentor FOREIGN KEY (mentor_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    type ENUM('subscription', 'mentoring', 'paid_project') NOT NULL,
    reference_id BIGINT UNSIGNED NULL,
    amount INT NOT NULL,
    platform_fee INT NOT NULL DEFAULT 0,
    status ENUM('pending', 'paid', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending',
    payment_method VARCHAR(80) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_transactions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO plans (id, name, price, max_skills, max_needs, max_offers, max_exchange_requests)
VALUES
(1, 'Gratis', 0, 3, 3, 2, 5),
(2, 'Pro', 19000, 10, 10, 10, 30),
(3, 'Pro Max', 59000, NULL, NULL, NULL, NULL);

INSERT INTO users (id, name, email, password, role, plan_id, token)
VALUES
(1, 'Fakhri', 'fakhri@example.com', '$2y$10$JoyTJ4DJh20TB5W2jUh2ju7AVDF7j9n1x7/2441miWc8wwzITMTKK', 'user', 1, 'fakhri-token-123'),
(2, 'Raka', 'raka@example.com', '$2y$10$JoyTJ4DJh20TB5W2jUh2ju7AVDF7j9n1x7/2441miWc8wwzITMTKK', 'user', 1, 'raka-token-123'),
(3, 'Admin Human Skill', 'admin@hse.test', '$2y$10$JoyTJ4DJh20TB5W2jUh2ju7AVDF7j9n1x7/2441miWc8wwzITMTKK', 'admin', 3, 'admin-token-123');

INSERT INTO profiles (user_id, bio, location, work_mode, available_time, portfolio_url, social_url)
VALUES
(1, 'Backend learner yang bisa membantu membuat REST API Laravel dan dokumentasi Postman.', 'Purwokerto', 'online', 'Malam dan akhir pekan', 'https://portfolio.example.com/fakhri', 'https://linkedin.com/in/fakhri'),
(2, 'UI designer pemula yang ingin membangun portofolio aplikasi web dan mobile.', 'Purwokerto', 'hybrid', 'Sore hari', 'https://portfolio.example.com/raka', 'https://dribbble.com/raka');

INSERT INTO skills (user_id, name, category, level)
VALUES
(1, 'Laravel REST API', 'Programming', 'intermediate'),
(1, 'Postman Documentation', 'Documentation', 'intermediate'),
(2, 'UI Design', 'Design', 'intermediate'),
(2, 'Figma Prototype', 'Design', 'intermediate');

INSERT INTO needs (user_id, title, category, description, exchange_offer)
VALUES
(1, 'Butuh bantuan desain UI dashboard', 'Design', 'Saya membutuhkan desain dashboard untuk aplikasi REST API.', 'Saya bisa membantu membuat endpoint CRUD dan dokumentasi API.'),
(2, 'Butuh bantuan Laravel REST API', 'Programming', 'Saya membutuhkan backend API untuk project portofolio UI saya.', 'Saya bisa membuat desain UI dan prototype Figma.');

INSERT INTO offers (user_id, title, type, category, description, exchange_expectation, available_duration)
VALUES
(1, 'Saya bisa bantu membuat REST API Laravel', 'skill', 'Programming', 'Saya bisa membantu API login, CRUD, validasi, dan dokumentasi Postman.', 'Saya membutuhkan bantuan desain UI dashboard.', '4 jam per minggu'),
(2, 'Saya bisa bantu desain UI di Figma', 'skill', 'Design', 'Saya bisa membuat wireframe, UI dashboard, dan prototype sederhana.', 'Saya membutuhkan bantuan backend REST API.', '3 jam per minggu');
