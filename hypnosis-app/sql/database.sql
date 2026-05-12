-- =============================================================
--  OptisMentis Hypnotherapy – Database Schema & Seed Data
--  Import: mysql -u root -p < sql/database.sql
-- =============================================================

CREATE DATABASE IF NOT EXISTS hypnosis_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hypnosis_app;

-- -----------------------------------------------------------
-- 1. users
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name       VARCHAR(150) NOT NULL,
    email           VARCHAR(180) NOT NULL UNIQUE,
    phone           VARCHAR(30)  DEFAULT NULL,
    password        VARCHAR(255) NOT NULL,
    role            ENUM('client','admin','therapist') NOT NULL DEFAULT 'client',
    profile_photo   VARCHAR(255) DEFAULT NULL,
    status          ENUM('active','inactive','pending') NOT NULL DEFAULT 'pending',
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- 2. intake_forms
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS intake_forms (
    id                          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id                     INT UNSIGNED DEFAULT NULL,
    full_name                   VARCHAR(150) NOT NULL,
    date_of_birth               DATE DEFAULT NULL,
    gender                      VARCHAR(30)  DEFAULT NULL,
    email                       VARCHAR(180) NOT NULL,
    phone                       VARCHAR(30)  DEFAULT NULL,
    address                     TEXT DEFAULT NULL,
    country                     VARCHAR(80)  DEFAULT NULL,
    occupation                  VARCHAR(100) DEFAULT NULL,
    marital_status              VARCHAR(40)  DEFAULT NULL,
    number_of_children          TINYINT UNSIGNED DEFAULT NULL,
    emergency_contact_name      VARCHAR(150) DEFAULT NULL,
    emergency_contact_phone     VARCHAR(30)  DEFAULT NULL,
    main_concern                VARCHAR(150) NOT NULL,
    concern_description         TEXT DEFAULT NULL,
    therapy_goals               TEXT DEFAULT NULL,
    sleep_quality               TINYINT UNSIGNED DEFAULT NULL COMMENT '1-10 scale',
    stress_level                TINYINT UNSIGNED DEFAULT NULL COMMENT '1-10 scale',
    anxiety_level               TINYINT UNSIGNED DEFAULT NULL COMMENT '1-10 scale',
    smoking_status              VARCHAR(30)  DEFAULT NULL,
    alcohol_use                 VARCHAR(30)  DEFAULT NULL,
    current_medications         TEXT DEFAULT NULL,
    medical_conditions          TEXT DEFAULT NULL,
    psychological_history       TEXT DEFAULT NULL,
    history_of_psychosis        TINYINT(1)   NOT NULL DEFAULT 0,
    history_of_epilepsy         TINYINT(1)   NOT NULL DEFAULT 0,
    suicidal_thoughts           TINYINT(1)   NOT NULL DEFAULT 0,
    current_psychiatric_treatment TINYINT(1) NOT NULL DEFAULT 0,
    consent_given               TINYINT(1)   NOT NULL DEFAULT 0,
    data_privacy_agreement      TINYINT(1)   NOT NULL DEFAULT 0,
    status                      ENUM('new','reviewed','accepted','referred','rejected') NOT NULL DEFAULT 'new',
    therapist_notes             TEXT DEFAULT NULL,
    created_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_intake_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- 3. appointments
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS appointments (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id             INT UNSIGNED DEFAULT NULL,
    intake_id           INT UNSIGNED DEFAULT NULL,
    full_name           VARCHAR(150) NOT NULL,
    email               VARCHAR(180) NOT NULL,
    phone               VARCHAR(30)  DEFAULT NULL,
    preferred_date      DATE NOT NULL,
    preferred_time      TIME NOT NULL,
    appointment_type    ENUM('online','in_person') NOT NULL DEFAULT 'online',
    service_type        VARCHAR(150) DEFAULT NULL,
    message             TEXT DEFAULT NULL,
    status              ENUM('requested','confirmed','completed','cancelled') NOT NULL DEFAULT 'requested',
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_appt_user   FOREIGN KEY (user_id)   REFERENCES users(id)        ON DELETE SET NULL,
    CONSTRAINT fk_appt_intake FOREIGN KEY (intake_id) REFERENCES intake_forms(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- 4. services
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS services (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title            VARCHAR(150) NOT NULL,
    slug             VARCHAR(160) NOT NULL UNIQUE,
    description      TEXT DEFAULT NULL,
    icon             VARCHAR(80)  DEFAULT 'bi-star',
    price            DECIMAL(8,2) DEFAULT NULL,
    duration_minutes SMALLINT UNSIGNED DEFAULT 60,
    is_active        TINYINT(1) NOT NULL DEFAULT 1,
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- 5. audio_sessions
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS audio_sessions (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title            VARCHAR(200) NOT NULL,
    category         VARCHAR(80)  DEFAULT NULL,
    description      TEXT DEFAULT NULL,
    audio_file       VARCHAR(255) DEFAULT NULL,
    duration_minutes SMALLINT UNSIGNED DEFAULT NULL,
    access_type      ENUM('free','premium','assigned') NOT NULL DEFAULT 'free',
    is_active        TINYINT(1) NOT NULL DEFAULT 1,
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- 6. client_audio_assignments
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS client_audio_assignments (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id    INT UNSIGNED NOT NULL,
    audio_id     INT UNSIGNED NOT NULL,
    assigned_by  INT UNSIGNED DEFAULT NULL,
    assigned_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_caa_client    FOREIGN KEY (client_id)   REFERENCES users(id)          ON DELETE CASCADE,
    CONSTRAINT fk_caa_audio     FOREIGN KEY (audio_id)    REFERENCES audio_sessions(id) ON DELETE CASCADE,
    CONSTRAINT fk_caa_therapist FOREIGN KEY (assigned_by) REFERENCES users(id)          ON DELETE SET NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- 7. session_notes
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS session_notes (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id       INT UNSIGNED NOT NULL,
    therapist_id    INT UNSIGNED DEFAULT NULL,
    appointment_id  INT UNSIGNED DEFAULT NULL,
    session_date    DATE NOT NULL,
    notes           TEXT DEFAULT NULL,
    recommendations TEXT DEFAULT NULL,
    next_steps      TEXT DEFAULT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sn_client      FOREIGN KEY (client_id)      REFERENCES users(id)        ON DELETE CASCADE,
    CONSTRAINT fk_sn_therapist   FOREIGN KEY (therapist_id)   REFERENCES users(id)        ON DELETE SET NULL,
    CONSTRAINT fk_sn_appointment FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- 8. progress_logs
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS progress_logs (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id     INT UNSIGNED NOT NULL,
    mood_score    TINYINT UNSIGNED DEFAULT NULL,
    stress_score  TINYINT UNSIGNED DEFAULT NULL,
    sleep_score   TINYINT UNSIGNED DEFAULT NULL,
    craving_score TINYINT UNSIGNED DEFAULT NULL,
    journal_note  TEXT DEFAULT NULL,
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pl_client FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- 9. messages
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS messages (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sender_id   INT UNSIGNED NOT NULL,
    receiver_id INT UNSIGNED NOT NULL,
    message     TEXT NOT NULL,
    is_read     TINYINT(1) NOT NULL DEFAULT 0,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_msg_sender   FOREIGN KEY (sender_id)   REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_msg_receiver FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- 10. blog_posts
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS blog_posts (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(255) NOT NULL,
    slug            VARCHAR(270) NOT NULL UNIQUE,
    content         LONGTEXT NOT NULL,
    featured_image  VARCHAR(255) DEFAULT NULL,
    author_id       INT UNSIGNED DEFAULT NULL,
    status          ENUM('draft','published') NOT NULL DEFAULT 'draft',
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_bp_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- 11. consent_logs
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS consent_logs (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id       INT UNSIGNED DEFAULT NULL,
    intake_id     INT UNSIGNED DEFAULT NULL,
    consent_type  VARCHAR(80) NOT NULL,
    consent_text  TEXT NOT NULL,
    accepted      TINYINT(1) NOT NULL DEFAULT 0,
    ip_address    VARCHAR(45) DEFAULT NULL,
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cl_user   FOREIGN KEY (user_id)   REFERENCES users(id)        ON DELETE SET NULL,
    CONSTRAINT fk_cl_intake FOREIGN KEY (intake_id) REFERENCES intake_forms(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =============================================================
--  SEED DATA
-- =============================================================

-- Default admin account
-- password: Admin@12345
INSERT INTO users (full_name, email, phone, password, role, status)
VALUES (
    'Admin User',
    'admin@example.com',
    NULL,
    '$2y$12$KykSL3NNmIjxXr7BlbEXl.CPbYKvY7tfRJW.dqntfcCUEdFyhmcRC',
    'admin',
    'active'
);

-- Default services
INSERT INTO services (title, slug, description, icon, price, duration_minutes) VALUES
('Stress and Anxiety Support',   'stress-anxiety-support',   'Calm the nervous system and build resilience through focused hypnotherapy sessions designed to reduce stress and anxiety.',         'bi-wind',             90.00, 60),
('Smoking Cessation Support',    'smoking-cessation',        'Supportive behavioral hypnotherapy to help you address smoking habits and strengthen your motivation to remain smoke-free.',       'bi-x-circle',         90.00, 75),
('Weight Management Support',    'weight-management',        'Behavioral and mindset support sessions to complement healthy lifestyle changes related to nutrition and body confidence.',         'bi-heart-pulse',      90.00, 60),
('Sleep Improvement',            'sleep-improvement',        'Relaxation and suggestion-based hypnotherapy to support healthier sleep patterns and reduce insomnia-related tension.',            'bi-moon-stars',       85.00, 60),
('Confidence and Self-Esteem',   'confidence-self-esteem',   'Targeted sessions to help you reshape limiting beliefs and strengthen a positive, grounded self-image.',                          'bi-person-check',     85.00, 60),
('Phobia and Fear Support',      'phobia-fear-support',      'Gentle, evidence-informed techniques to address specific fears and phobic responses at a pace that feels right for you.',         'bi-shield-check',     90.00, 60),
('Performance and Focus',        'performance-focus',        'Mental conditioning sessions supporting concentration, goal-setting, and peak performance in professional or personal contexts.',  'bi-lightning-charge', 85.00, 60),
('Guided Relaxation Sessions',   'guided-relaxation',        'Pure relaxation audio and live sessions designed to promote deep calm, body awareness, and mental restoration.',                  'bi-peace',            70.00, 45);

-- Sample free audio sessions
INSERT INTO audio_sessions (title, category, description, audio_file, duration_minutes, access_type) VALUES
('Deep Calm Relaxation',       'Relaxation', 'A gentle guided journey into deep physical and mental relaxation.',                 'placeholder.mp3', 20, 'free'),
('Restful Sleep Induction',    'Sleep',      'A soothing session designed to quieten the mind and prepare you for restful sleep.', 'placeholder.mp3', 25, 'free'),
('Confidence Reset',           'Confidence', 'Positive suggestion session to reinforce your sense of personal strength.',         'placeholder.mp3', 18, 'premium'),
('Anxiety Release',            'Anxiety',    'Breathing and visualization techniques to release stored tension and anxiety.',     'placeholder.mp3', 22, 'premium');

-- Sample blog post
INSERT INTO blog_posts (title, slug, content, author_id, status) VALUES
(
    'What Is Hypnotherapy and How Can It Help You?',
    'what-is-hypnotherapy',
    '<p>Hypnotherapy is a form of complementary therapy that uses guided relaxation, focused attention, and positive suggestion to help individuals address a range of habits and emotional patterns.</p>
<p>During a hypnotherapy session you remain fully conscious and in control at all times. The therapist guides you into a state of relaxed focus—often described as similar to daydreaming or deep meditation—where the mind becomes more receptive to positive ideas and perspectives.</p>
<p>Common areas where hypnotherapy may offer support include stress management, sleep, confidence, habit change, and emotional wellness. It is not a replacement for medical or psychiatric care, but many people find it a valuable complement to their overall wellbeing journey.</p>',
    1,
    'published'
);
