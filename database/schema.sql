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
  `duration_hours` DECIMAL(5,2) DEFAULT NULL,
  `max_guests` INT DEFAULT 10,
  `languages` VARCHAR(255) DEFAULT 'English',
  `city` VARCHAR(100) DEFAULT NULL,
  `location` VARCHAR(100) NOT NULL,
  `country` VARCHAR(100) DEFAULT 'Unknown',
  `meeting_point` VARCHAR(255) DEFAULT '',
  `image_url` VARCHAR(255) DEFAULT NULL,
  `cover_image` VARCHAR(255) DEFAULT NULL,
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
  `updated_at` DATETIME DEFAULT NULL,
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

CREATE TABLE `password_resets` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `token` VARCHAR(255) NOT NULL UNIQUE,
  `expires_at` DATETIME NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- Indexes for performance
CREATE INDEX idx_exp_host ON experiences(host_id);
CREATE INDEX idx_exp_city_country ON experiences(city, country);
CREATE INDEX idx_exp_status ON experiences(status);
CREATE INDEX idx_exp_duration_hours ON experiences(duration_hours);
CREATE INDEX idx_bookings_traveler ON bookings(traveler_id);
CREATE INDEX idx_bookings_date ON bookings(booking_date);
CREATE INDEX idx_bookings_status ON bookings(status);
CREATE INDEX idx_bookings_created_at ON bookings(created_at);
CREATE INDEX idx_blog_slug ON blog_posts(slug);
CREATE INDEX idx_blog_author ON blog_posts(author_id);
CREATE INDEX idx_blog_status ON blog_posts(status);
CREATE INDEX idx_reviews_reviewer ON reviews(reviewer_id);

-- INSERT SEED DATA

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `avatar`, `bio`, `nationality`, `languages`, `is_verified`, `is_active`) VALUES
(1, 'Elena Rossi', 'elena@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'host', 'https://i.pravatar.cc/150?u=elena', 'Florence-born food expert and pasta instructor.', 'Italy', 'English, Italian', 1, 1),
(2, 'Kenji Tanaka', 'kenji@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'host', 'https://i.pravatar.cc/150?u=kenji', 'Local historian focused on Kyoto temples and tea culture.', 'Japan', 'English, Japanese', 1, 1),
(3, 'David Chen', 'david@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'traveler', 'https://i.pravatar.cc/150?u=david', 'Photographer who travels for local food stories.', 'Canada', 'English, Mandarin', 1, 1),
(4, 'Sofia Alvarez', 'sofia@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'host', 'https://i.pravatar.cc/150?u=sofia', 'Art and architecture host based in Barcelona.', 'Spain', 'English, Spanish', 1, 1),
(5, 'Maya Patel', 'maya@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'traveler', 'https://i.pravatar.cc/150?u=maya', 'Remote worker exploring cities through local guides.', 'India', 'English, Hindi', 0, 1),
(6, 'Lucas Meyer', 'lucas@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'traveler', 'https://i.pravatar.cc/150?u=lucas', 'Weekend traveler and street-food enthusiast.', 'Germany', 'English, German', 0, 1);

INSERT INTO `host_profiles` (`id`, `user_id`, `city`, `country`, `neighborhood`, `speciality_tags`, `response_rate`, `total_reviews`, `cover_photo`) VALUES
(1, 1, 'Florence', 'Italy', 'Oltrarno', 'Culinary, Wine, Handmade Pasta', 98, 44, 'https://images.unsplash.com/photo-1491553895911-0055eca6402d?auto=format&fit=crop&q=80&w=1200'),
(2, 2, 'Kyoto', 'Japan', 'Gion', 'Culture, History, Tea Ceremony', 95, 39, 'https://images.unsplash.com/photo-1492571350019-22de08371fd3?auto=format&fit=crop&q=80&w=1200'),
(3, 4, 'Barcelona', 'Spain', 'El Born', 'Art, Architecture, Local Markets', 93, 27, 'https://images.unsplash.com/photo-1539037116277-4db20889f2d4?auto=format&fit=crop&q=80&w=1200');

INSERT INTO `experiences` (`id`, `host_id`, `title`, `slug`, `description`, `category`, `price`, `duration`, `duration_hours`, `max_guests`, `languages`, `city`, `location`, `country`, `meeting_point`, `image_url`, `cover_image`, `status`, `avg_rating`, `total_bookings`) VALUES
(1, 1, 'Authentic Tuscan Pasta Making', 'authentic-tuscan-pasta-making', 'Learn the secrets of perfect handmade pasta from a Florentine host.', 'Food', 85.00, 'Half-day', 3.0, 6, 'English, Italian', 'Florence', 'Florence', 'Italy', 'Piazza del Carmine 14', 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?auto=format&fit=crop&q=80&w=900', 'https://images.unsplash.com/photo-1551183053-bf91a1d81141?auto=format&fit=crop&q=80&w=1200', 'active', 4.90, 21),
(2, 2, 'Hidden Temples of Kyoto', 'hidden-temples-of-kyoto', 'Explore quiet temples and local stories beyond the main tourist routes.', 'History', 65.00, 'Full-day', 4.0, 8, 'English, Japanese', 'Kyoto', 'Kyoto', 'Japan', 'Kyoto Station Central Gate', 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?auto=format&fit=crop&q=80&w=900', 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?auto=format&fit=crop&q=80&w=1200', 'active', 4.80, 17),
(3, 4, 'Gaudi Walk and Local Tapas', 'gaudi-walk-and-local-tapas', 'A design-focused walking route ending with neighborhood tapas.', 'Art', 72.00, 'Half-day', 3.5, 10, 'English, Spanish', 'Barcelona', 'Barcelona', 'Spain', 'Arc de Triomf Entrance', 'https://images.unsplash.com/photo-1509840841025-9088ba78a826?auto=format&fit=crop&q=80&w=900', 'https://images.unsplash.com/photo-1509840841025-9088ba78a826?auto=format&fit=crop&q=80&w=1200', 'active', 4.70, 12),
(4, 1, 'Sunrise Market and Espresso Crawl', 'sunrise-market-and-espresso-crawl', 'Visit early-morning local markets and hidden espresso bars.', 'Food', 48.00, 'Half-day', 2.5, 8, 'English, Italian', 'Florence', 'Florence', 'Italy', 'Mercato Centrale Main Entrance', 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&q=80&w=900', 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&q=80&w=1200', 'active', 4.50, 8),
(5, 4, 'Street Art and Hidden Courtyards', 'street-art-and-hidden-courtyards', 'Explore murals, alley galleries, and hidden courtyards with a local artist.', 'Art', 55.00, 'Half-day', 3.0, 12, 'English, Spanish', 'Barcelona', 'Barcelona', 'Spain', 'El Born Cultural Center', 'https://images.unsplash.com/photo-1470229538611-16ba8c7ffbd7?auto=format&fit=crop&q=80&w=900', 'https://images.unsplash.com/photo-1470229538611-16ba8c7ffbd7?auto=format&fit=crop&q=80&w=1200', 'active', 4.40, 15),
(6, 2, 'Kyoto Night Lantern Walk', 'kyoto-night-lantern-walk', 'Walk historic lanes after sunset and learn local legends.', 'Nightlife', 58.00, 'Half-day', 2.8, 10, 'English, Japanese', 'Kyoto', 'Kyoto', 'Japan', 'Yasaka Shrine Gate', 'https://images.unsplash.com/photo-1503899036084-c55cdd92da26?auto=format&fit=crop&q=80&w=900', 'https://images.unsplash.com/photo-1503899036084-c55cdd92da26?auto=format&fit=crop&q=80&w=1200', 'active', 4.60, 19),
(7, 1, 'Chianti Vineyard Day Trip', 'chianti-vineyard-day-trip', 'Drive through Tuscan hills, visit family vineyards, and taste regional wines.', 'Nature', 130.00, 'Full-day', 7.5, 8, 'English, Italian', 'Florence', 'Florence', 'Italy', 'Santa Maria Novella Station', 'https://images.unsplash.com/photo-1506377247377-2a5b3b417ebb?auto=format&fit=crop&q=80&w=900', 'https://images.unsplash.com/photo-1506377247377-2a5b3b417ebb?auto=format&fit=crop&q=80&w=1200', 'active', 4.90, 26),
(8, 4, 'Montserrat Hiking Escape', 'montserrat-hiking-escape', 'A full-day hiking escape with panoramic mountain views and monastery history.', 'Adventure', 98.00, 'Full-day', 8.0, 9, 'English, Spanish', 'Barcelona', 'Barcelona', 'Spain', 'Plaça Espanya Metro Exit A', 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&q=80&w=900', 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&q=80&w=1200', 'active', 4.70, 18),
(9, 2, 'Samurai Era Storytelling Route', 'samurai-era-storytelling-route', 'Visit castles and backstreets while hearing stories from the samurai era.', 'History', 88.00, 'Full-day', 6.0, 10, 'English, Japanese', 'Kyoto', 'Kyoto', 'Japan', 'Nijo Castle East Gate', 'https://images.unsplash.com/photo-1518544866330-4e8df64f50f8?auto=format&fit=crop&q=80&w=900', 'https://images.unsplash.com/photo-1518544866330-4e8df64f50f8?auto=format&fit=crop&q=80&w=1200', 'active', 4.80, 22),
(10, 1, 'Florence Rooftop Sunset Jazz', 'florence-rooftop-sunset-jazz', 'Enjoy live jazz on a rooftop with sunset skyline views and local bites.', 'Nightlife', 74.00, 'Half-day', 3.0, 14, 'English, Italian', 'Florence', 'Florence', 'Italy', 'Piazza della Repubblica', 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?auto=format&fit=crop&q=80&w=900', 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?auto=format&fit=crop&q=80&w=1200', 'active', 4.30, 11),
(11, 4, 'Catalan Cooking Masterclass', 'catalan-cooking-masterclass', 'Hands-on cooking workshop with seasonal ingredients and tapas pairing.', 'Workshop', 82.00, 'Half-day', 3.5, 8, 'English, Spanish', 'Barcelona', 'Barcelona', 'Spain', 'Mercat de Sant Antoni Entrance', 'https://images.unsplash.com/photo-1466978913421-dad2ebd01d17?auto=format&fit=crop&q=80&w=900', 'https://images.unsplash.com/photo-1466978913421-dad2ebd01d17?auto=format&fit=crop&q=80&w=1200', 'active', 4.90, 30),
(12, 2, 'Tea Ceremony and Garden Workshop', 'tea-ceremony-and-garden-workshop', 'Learn tea etiquette and seasonal garden aesthetics from a local expert.', 'Workshop', 69.00, 'Half-day', 2.5, 6, 'English, Japanese', 'Kyoto', 'Kyoto', 'Japan', 'Maruyama Park Main Gate', 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&q=80&w=900', 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&q=80&w=1200', 'active', 4.60, 13),
(13, 1, 'Cinque Terre Multi-Day Escape', 'cinque-terre-multi-day-escape', 'Two-day coastal escape with village trails, local seafood, and sea views.', 'Nature', 220.00, 'Multi-day', 18.0, 8, 'English, Italian', 'Florence', 'Florence', 'Italy', 'Campo di Marte Station', 'https://images.unsplash.com/photo-1533105079780-92b9be482077?auto=format&fit=crop&q=80&w=900', 'https://images.unsplash.com/photo-1533105079780-92b9be482077?auto=format&fit=crop&q=80&w=1200', 'active', 4.80, 9),
(14, 4, 'Pyrenees Weekend Adventure', 'pyrenees-weekend-adventure', 'A multi-day mountain trip with trekking, cabin stay, and stargazing.', 'Adventure', 260.00, 'Multi-day', 20.0, 7, 'English, Spanish', 'Barcelona', 'Barcelona', 'Spain', 'Barcelona Sants Station', 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&q=80&w=1000', 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&q=80&w=1400', 'active', 4.90, 14),
(15, 2, 'Bamboo Forest Dawn Trek', 'bamboo-forest-dawn-trek', 'Early morning trek through bamboo paths before the crowds arrive.', 'Adventure', 59.00, 'Half-day', 3.0, 12, 'English, Japanese', 'Kyoto', 'Kyoto', 'Japan', 'Arashiyama Station', 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&q=80&w=900', 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&q=80&w=1200', 'active', 4.50, 16),
(16, 1, 'Renaissance Secrets Walking Tour', 'renaissance-secrets-walking-tour', 'Uncover hidden symbols and stories behind Florence''s iconic landmarks.', 'History', 52.00, 'Half-day', 2.7, 15, 'English, Italian', 'Florence', 'Florence', 'Italy', 'Piazza del Duomo', 'https://images.unsplash.com/photo-1467269204594-9661b134dd2b?auto=format&fit=crop&q=80&w=900', 'https://images.unsplash.com/photo-1467269204594-9661b134dd2b?auto=format&fit=crop&q=80&w=1200', 'active', 4.70, 24),
(17, 4, 'Coastal Kayak and Cliff Picnic', 'coastal-kayak-and-cliff-picnic', 'Kayak along the Mediterranean coast and stop for a scenic picnic.', 'Nature', 115.00, 'Full-day', 6.5, 10, 'English, Spanish', 'Barcelona', 'Barcelona', 'Spain', 'Barceloneta Beach Lifeguard Tower', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&q=80&w=900', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&q=80&w=1200', 'active', 4.60, 20),
(18, 2, 'Kyoto Riverside Bike Journey', 'kyoto-riverside-bike-journey', 'Cycle across local neighborhoods, shrines, and riverside cafes.', 'Nature', 63.00, 'Half-day', 4.0, 9, 'English, Japanese', 'Kyoto', 'Kyoto', 'Japan', 'Demachiyanagi Station', 'https://images.unsplash.com/photo-1473448912268-2022ce9509d8?auto=format&fit=crop&q=80&w=900', 'https://images.unsplash.com/photo-1473448912268-2022ce9509d8?auto=format&fit=crop&q=80&w=1200', 'active', 4.40, 10),
(19, 1, 'Olive Harvest Countryside Weekend', 'olive-harvest-countryside-weekend', 'Join an olive harvest, press fresh oil, and stay overnight in a farmhouse.', 'Food', 240.00, 'Multi-day', 16.0, 6, 'English, Italian', 'Florence', 'Florence', 'Italy', 'Firenze Santa Maria Novella', 'https://images.unsplash.com/photo-1478145046317-39f10e56b5e9?auto=format&fit=crop&q=80&w=900', 'https://images.unsplash.com/photo-1478145046317-39f10e56b5e9?auto=format&fit=crop&q=80&w=1200', 'active', 4.90, 7),
(20, 4, 'Barcelona Gothic Night Tales', 'barcelona-gothic-night-tales', 'A storytelling route through Gothic Quarter streets and hidden squares.', 'Nightlife', 47.00, 'Half-day', 2.4, 18, 'English, Spanish', 'Barcelona', 'Barcelona', 'Spain', 'Plaça Reial Fountain', 'https://images.unsplash.com/photo-1518991791750-7499f2cbff1c?auto=format&fit=crop&q=80&w=900', 'https://images.unsplash.com/photo-1518991791750-7499f2cbff1c?auto=format&fit=crop&q=80&w=1200', 'active', 4.20, 6),
(21, 2, 'Kyoto Calligraphy Studio Session', 'kyoto-calligraphy-studio-session', 'Practice Japanese calligraphy in a traditional studio with expert guidance.', 'Workshop', 54.00, 'Half-day', 2.0, 10, 'English, Japanese', 'Kyoto', 'Kyoto', 'Japan', 'Kawaramachi OPA Entrance', 'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&q=80&w=900', 'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&q=80&w=1200', 'active', 4.70, 12),
(22, 1, 'Truffle Hunting in Tuscan Hills', 'truffle-hunting-in-tuscan-hills', 'Join trained dogs and local experts to hunt seasonal truffles.', 'Adventure', 145.00, 'Full-day', 7.0, 8, 'English, Italian', 'Florence', 'Florence', 'Italy', 'Piazzale Montelungo Bus Point', 'https://images.unsplash.com/photo-1472396961693-142e6e269027?auto=format&fit=crop&q=80&w=900', 'https://images.unsplash.com/photo-1472396961693-142e6e269027?auto=format&fit=crop&q=80&w=1200', 'active', 4.80, 9);

INSERT INTO `experience_images` (`id`, `experience_id`, `image_path`, `sort_order`) VALUES
(1, 1, 'https://images.unsplash.com/photo-1546549032-9571cd6b27df?auto=format&fit=crop&q=80&w=1000', 1),
(2, 1, 'https://images.unsplash.com/photo-1512058564366-c9e2d31714af?auto=format&fit=crop&q=80&w=1000', 2),
(3, 2, 'https://images.unsplash.com/photo-1503899036084-c55cdd92da26?auto=format&fit=crop&q=80&w=1000', 1),
(4, 3, 'https://images.unsplash.com/photo-1533106418989-88406c7cc8ca?auto=format&fit=crop&q=80&w=1000', 1);

INSERT INTO `experience_itinerary` (`id`, `experience_id`, `step_number`, `title`, `description`) VALUES
(1, 1, 1, 'Welcome and Ingredients Intro', 'Meet your host, review ingredients, and prepare fresh dough.'),
(2, 1, 2, 'Hands-on Pasta Crafting', 'Shape tagliatelle and ravioli with regional techniques.'),
(3, 1, 3, 'Cook and Taste', 'Prepare sauces and enjoy the meal together.'),
(4, 2, 1, 'Old District Briefing', 'Introduction to Kyoto''s temple neighborhoods and etiquette.'),
(5, 2, 2, 'Temple Walk', 'Visit two lesser-known temples and a zen garden.'),
(6, 3, 1, 'Modernist Architecture Route', 'Guided walk through iconic facades and hidden details.');

INSERT INTO `bookings` (`id`, `experience_id`, `traveler_id`, `booking_date`, `guest_count`, `total_price`, `status`, `special_requests`, `booking_ref`) VALUES
(1, 1, 3, '2026-05-10', 2, 170.00, 'confirmed', 'Vegetarian option preferred.', 'WL-728193'),
(2, 2, 5, '2026-05-18', 1, 68.25, 'pending', 'Can we include a tea house stop?', 'WL-731002'),
(3, 3, 6, '2026-05-21', 3, 226.80, 'confirmed', 'One guest is gluten-sensitive.', 'WL-731438'),
(4, 1, 5, '2026-04-06', 2, 170.00, 'completed', 'Anniversary trip.', 'WL-719551');

INSERT INTO `reviews` (`id`, `experience_id`, `booking_id`, `reviewer_id`, `rating`, `comment`) VALUES
(1, 1, 1, 3, 5, 'Absolutely incredible experience! The ravioli was life-changing.'),
(2, 1, 4, 5, 4, 'Great host and very practical tips for making pasta at home.'),
(3, 3, 3, 6, 5, 'Loved the route and local tapas recommendations.');

INSERT INTO `blog_posts` (`id`, `author_id`, `title`, `slug`, `excerpt`, `content`, `cover_image`, `category`, `tags`, `status`, `read_time_mins`, `views`, `updated_at`) VALUES
(1, 1, 'Top 5 Local Wines to Try in Florence', 'top-5-local-wines-florence', 'A quick guide to local Tuscan varieties and where to taste them.', 'Here are the best wines and tasting rooms to explore in Florence neighborhoods...', 'https://images.unsplash.com/photo-1516594915697-87eb3b1c14ea?auto=format&fit=crop&q=80&w=1200', 'Food & Drink', 'wine,florence,tuscany', 'published', 6, 245, '2026-04-10 11:30:00'),
(2, 2, 'Quiet Corners of Kyoto at Dawn', 'quiet-corners-of-kyoto-at-dawn', 'Start your morning in Kyoto with less-crowded spots.', 'These early routes are ideal for travelers who want calm, photos, and local rhythm...', 'https://images.unsplash.com/photo-1545569341-9eb8b30979d9?auto=format&fit=crop&q=80&w=1200', 'Culture', 'kyoto,morning,temples', 'published', 5, 132, '2026-04-12 09:10:00'),
(3, 4, 'Barcelona Through Shapes and Stories', 'barcelona-through-shapes-and-stories', 'An architecture-first walk for design lovers.', 'From modernist balconies to quiet plazas, here is a route worth saving...', 'https://images.unsplash.com/photo-1464790719320-516ecd75af6c?auto=format&fit=crop&q=80&w=1200', 'Art & Design', 'barcelona,gaudi,architecture', 'draft', 7, 18, NULL);

INSERT INTO `blog_likes` (`id`, `post_id`, `user_id`) VALUES
(1, 1, 3),
(2, 1, 5),
(3, 2, 3),
(4, 2, 6),
(5, 3, 5);

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `experience_id`, `message_text`, `is_read`) VALUES
(1, 3, 1, 1, 'Hi Elena, is this suitable for complete beginners?', 1),
(2, 1, 3, 1, 'Yes, absolutely. I guide every step and provide recipes too.', 1),
(3, 5, 2, 2, 'Can I join if I only speak English?', 0),
(4, 6, 4, 3, 'Is there a vegetarian tapas option on this route?', 0);

INSERT INTO `wishlists` (`id`, `user_id`, `experience_id`) VALUES
(1, 3, 2),
(2, 3, 3),
(3, 5, 1),
(4, 5, 3),
(5, 6, 2);

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expires_at`) VALUES
(1, 5, 'reset_token_maya_20260416', '2026-04-16 23:59:59'),
(2, 6, 'reset_token_lucas_20260416', '2026-04-16 22:30:00');
