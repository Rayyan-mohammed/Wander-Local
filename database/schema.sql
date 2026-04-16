-- Wander_Local Database Schema
CREATE DATABASE IF NOT EXISTS wander_local;
USE wander_local;

CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('traveler', 'host') NOT NULL DEFAULT 'traveler',
  `avatar` VARCHAR(255) DEFAULT NULL,
  `bio` TEXT DEFAULT NULL,
  `nationality` VARCHAR(100) DEFAULT NULL,
  `languages` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `is_verified` BOOLEAN DEFAULT 0,
  `is_active` BOOLEAN DEFAULT 1
);

CREATE TABLE `host_profiles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `country` VARCHAR(100) NOT NULL,
  `neighborhood` VARCHAR(100) DEFAULT NULL,
  `speciality_tags` VARCHAR(255) DEFAULT NULL,
  `response_rate` INT DEFAULT 100,
  `total_reviews` INT DEFAULT 0,
  `cover_photo` VARCHAR(255) DEFAULT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE `experiences` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `host_id` INT NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(200) DEFAULT NULL UNIQUE,
  `description` TEXT NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `duration` VARCHAR(100) NOT NULL,
  `max_guests` INT DEFAULT 10,
  `languages` VARCHAR(255) DEFAULT 'English',
  `location` VARCHAR(100) NOT NULL,
  `country` VARCHAR(100) DEFAULT 'Unknown',
  `meeting_point` VARCHAR(255) DEFAULT '',
  `image_url` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('draft', 'active', 'paused') NOT NULL DEFAULT 'draft',
  `avg_rating` DECIMAL(3,2) DEFAULT 0.00,
  `total_bookings` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`host_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE `experience_images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `experience_id` INT NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `sort_order` INT DEFAULT 0,
  FOREIGN KEY (`experience_id`) REFERENCES `experiences`(`id`) ON DELETE CASCADE
);

CREATE TABLE `experience_itinerary` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `experience_id` INT NOT NULL,
  `step_number` INT NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT NOT NULL,
  FOREIGN KEY (`experience_id`) REFERENCES `experiences`(`id`) ON DELETE CASCADE
);

CREATE TABLE `bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `experience_id` INT NOT NULL,
  `traveler_id` INT NOT NULL,
  `booking_date` DATE NOT NULL,
  `guest_count` INT NOT NULL,
  `total_price` DECIMAL(10,2) NOT NULL,
  `status` ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
  `special_requests` TEXT DEFAULT NULL,
  `booking_ref` VARCHAR(50) NOT NULL UNIQUE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`experience_id`) REFERENCES `experiences`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`traveler_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE `reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `experience_id` INT NOT NULL,
  `booking_id` INT NOT NULL,
  `reviewer_id` INT NOT NULL,
  `rating` INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
  `comment` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`experience_id`) REFERENCES `experiences`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`reviewer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE `blog_posts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `author_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `excerpt` TEXT DEFAULT NULL,
  `content` LONGTEXT NOT NULL,
  `cover_image` VARCHAR(255) DEFAULT NULL,
  `category` VARCHAR(100) DEFAULT NULL,
  `tags` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('draft', 'published') DEFAULT 'draft',
  `read_time_mins` INT DEFAULT 5,
  `views` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

CREATE TABLE `blog_likes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `post_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`post_id`) REFERENCES `blog_posts`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  UNIQUE(`post_id`, `user_id`)
);

CREATE TABLE `messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `sender_id` INT NOT NULL,
  `receiver_id` INT NOT NULL,
  `experience_id` INT DEFAULT NULL,
  `message_text` TEXT NOT NULL,
  `is_read` BOOLEAN DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`receiver_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`experience_id`) REFERENCES `experiences`(`id`) ON DELETE SET NULL
);

CREATE TABLE `wishlists` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `experience_id` INT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`experience_id`) REFERENCES `experiences`(`id`) ON DELETE CASCADE,
  UNIQUE(`user_id`, `experience_id`)
);

-- Indexes for performance
CREATE INDEX idx_exp_host ON experiences(host_id);
CREATE INDEX idx_exp_city_country ON experiences(city, country);
CREATE INDEX idx_bookings_traveler ON bookings(traveler_id);
CREATE INDEX idx_bookings_date ON bookings(booking_date);
CREATE INDEX idx_blog_slug ON blog_posts(slug);

-- INSERT SEED DATA

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `nationality`, `is_verified`) VALUES
(1, 'Elena Rossi', 'elena@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'host', 'Italy', 1),
(2, 'Kenji Tanaka', 'kenji@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'host', 'Japan', 1),
(3, 'David Chen', 'david@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'traveler', 'Canada', 1);

INSERT INTO `host_profiles` (`user_id`, `city`, `country`, `speciality_tags`) VALUES
(1, 'Florence', 'Italy', 'Culinary, Wine'),
(2, 'Kyoto', 'Japan', 'Culture, History');

INSERT INTO `experiences` (`id`, `host_id`, `title`, `slug`, `description`, `category`, `price`, `duration_hours`, `max_guests`, `languages`, `city`, `country`, `meeting_point`, `status`) VALUES
(1, 1, 'Authentic Tuscan Pasta Making', 'authentic-tuscan-pasta-making', 'Learn the secrets of perfect handmade pasta.', 'Culinary', 85.00, 3.0, 6, 'English, Italian', 'Florence', 'Italy', 'Piazza del Carmine 14', 'active'),
(2, 2, 'Hidden Temples of Kyoto', 'hidden-temples-of-kyoto', 'Explore the secret zen gardens and temples of ancient Kyoto.', 'Culture', 65.00, 4.0, 8, 'English, Japanese', 'Kyoto', 'Japan', 'Kyoto Station Central Gate', 'active');

INSERT INTO `bookings` (`id`, `experience_id`, `traveler_id`, `booking_date`, `guest_count`, `total_price`, `status`, `booking_ref`) VALUES
(1, 1, 3, '2026-05-10', 2, 170.00, 'confirmed', 'WL-728193');

INSERT INTO `reviews` (`id`, `experience_id`, `booking_id`, `reviewer_id`, `rating`, `comment`) VALUES
(1, 1, 1, 3, 5, 'Absolutely incredible experience! The ravioli was life-changing.');

INSERT INTO `blog_posts` (`author_id`, `title`, `slug`, `content`, `category`, `status`) VALUES
(1, 'Top 5 Local Wines to Try in Florence', 'top-5-local-wines-florence', 'Here are the best wines...', 'Food & Drink', 'published');
