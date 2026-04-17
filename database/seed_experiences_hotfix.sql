-- Hotfix seed for localhost instances that still have only 2 experiences.
-- Safe to run multiple times.
USE wander_local;

ALTER TABLE experiences ADD COLUMN IF NOT EXISTS duration_hours DECIMAL(5,2) DEFAULT NULL;
ALTER TABLE experiences ADD COLUMN IF NOT EXISTS city VARCHAR(100) DEFAULT NULL;
ALTER TABLE experiences ADD COLUMN IF NOT EXISTS cover_image VARCHAR(255) DEFAULT NULL;

CREATE TABLE IF NOT EXISTS localist_follows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    follower_id INT NOT NULL,
    localist_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_localist_follows_follower FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_localist_follows_localist FOREIGN KEY (localist_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uq_localist_follows_pair (follower_id, localist_id)
);

CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipient_id INT NOT NULL,
    actor_id INT DEFAULT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(150) NOT NULL,
    message VARCHAR(255) NOT NULL,
    url VARCHAR(255) DEFAULT NULL,
    target_id INT DEFAULT NULL,
    is_read BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notifications_recipient FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_notifications_actor FOREIGN KEY (actor_id) REFERENCES users(id) ON DELETE SET NULL
);


-- Make the two original experiences render with reliable public image links.
UPDATE experiences
SET image_url = 'https://images.pexels.com/photos/1907246/pexels-photo-1907246.jpeg?auto=compress&cs=tinysrgb&w=900',
    cover_image = 'https://images.pexels.com/photos/1907246/pexels-photo-1907246.jpeg?auto=compress&cs=tinysrgb&w=1200',
    category = 'Food',
    duration = 'Half-day',
    duration_hours = COALESCE(duration_hours, 3.0),
    city = COALESCE(city, location),
    status = 'active'
WHERE id = 1;

UPDATE experiences
SET image_url = 'https://images.pexels.com/photos/1036856/pexels-photo-1036856.jpeg?auto=compress&cs=tinysrgb&w=900',
    cover_image = 'https://images.pexels.com/photos/1036856/pexels-photo-1036856.jpeg?auto=compress&cs=tinysrgb&w=1200',
    category = 'History',
    duration = 'Full-day',
    duration_hours = COALESCE(duration_hours, 4.0),
    city = COALESCE(city, location),
    status = 'active'
WHERE id = 2;

INSERT INTO experiences
(host_id, title, slug, description, category, price, duration, duration_hours, max_guests, languages, city, location, country, meeting_point, image_url, cover_image, status, avg_rating, total_bookings)
VALUES
(1, 'Florence Rooftop Sunset Jazz', 'florence-rooftop-sunset-jazz', 'Live rooftop jazz with city skyline and local bites.', 'Nightlife', 74.00, 'Half-day', 3.0, 14, 'English, Italian', 'Florence', 'Florence', 'Italy', 'Piazza della Repubblica', 'https://images.pexels.com/photos/164936/pexels-photo-164936.jpeg?auto=compress&cs=tinysrgb&w=900', 'https://images.pexels.com/photos/164936/pexels-photo-164936.jpeg?auto=compress&cs=tinysrgb&w=1200', 'active', 4.30, 11),
(1, 'Chianti Vineyard Day Trip', 'chianti-vineyard-day-trip', 'Tuscan vineyards, hill roads, and family wine tasting.', 'Nature', 130.00, 'Full-day', 7.5, 8, 'English, Italian', 'Florence', 'Florence', 'Italy', 'Santa Maria Novella Station', 'https://images.pexels.com/photos/1123260/pexels-photo-1123260.jpeg?auto=compress&cs=tinysrgb&w=900', 'https://images.pexels.com/photos/1123260/pexels-photo-1123260.jpeg?auto=compress&cs=tinysrgb&w=1200', 'active', 4.90, 26),
(1, 'Renaissance Secrets Walking Tour', 'renaissance-secrets-walking-tour', 'Hidden symbols and stories behind Florence landmarks.', 'History', 52.00, 'Half-day', 2.7, 15, 'English, Italian', 'Florence', 'Florence', 'Italy', 'Piazza del Duomo', 'https://images.pexels.com/photos/1105766/pexels-photo-1105766.jpeg?auto=compress&cs=tinysrgb&w=900', 'https://images.pexels.com/photos/1105766/pexels-photo-1105766.jpeg?auto=compress&cs=tinysrgb&w=1200', 'active', 4.70, 24),
(1, 'Olive Harvest Countryside Weekend', 'olive-harvest-countryside-weekend', 'Farm stay weekend with olive harvest and fresh pressing.', 'Food', 240.00, 'Multi-day', 16.0, 6, 'English, Italian', 'Florence', 'Florence', 'Italy', 'Firenze Santa Maria Novella', 'https://images.pexels.com/photos/1028599/pexels-photo-1028599.jpeg?auto=compress&cs=tinysrgb&w=900', 'https://images.pexels.com/photos/1028599/pexels-photo-1028599.jpeg?auto=compress&cs=tinysrgb&w=1200', 'active', 4.90, 7),

(2, 'Tea Ceremony and Garden Workshop', 'tea-ceremony-and-garden-workshop', 'Hands-on tea etiquette in a traditional Kyoto garden.', 'Workshop', 69.00, 'Half-day', 2.5, 6, 'English, Japanese', 'Kyoto', 'Kyoto', 'Japan', 'Maruyama Park Main Gate', 'https://images.pexels.com/photos/1417945/pexels-photo-1417945.jpeg?auto=compress&cs=tinysrgb&w=900', 'https://images.pexels.com/photos/1417945/pexels-photo-1417945.jpeg?auto=compress&cs=tinysrgb&w=1200', 'active', 4.60, 13),
(2, 'Kyoto Night Lantern Walk', 'kyoto-night-lantern-walk', 'Lantern-lit backstreets with local folklore and stops.', 'Nightlife', 58.00, 'Half-day', 2.8, 10, 'English, Japanese', 'Kyoto', 'Kyoto', 'Japan', 'Yasaka Shrine Gate', 'https://images.pexels.com/photos/625644/pexels-photo-625644.jpeg?auto=compress&cs=tinysrgb&w=900', 'https://images.pexels.com/photos/625644/pexels-photo-625644.jpeg?auto=compress&cs=tinysrgb&w=1200', 'active', 4.60, 19),
(2, 'Samurai Era Storytelling Route', 'samurai-era-storytelling-route', 'Castle district route with immersive samurai history.', 'History', 88.00, 'Full-day', 6.0, 10, 'English, Japanese', 'Kyoto', 'Kyoto', 'Japan', 'Nijo Castle East Gate', 'https://images.pexels.com/photos/1619317/pexels-photo-1619317.jpeg?auto=compress&cs=tinysrgb&w=900', 'https://images.pexels.com/photos/1619317/pexels-photo-1619317.jpeg?auto=compress&cs=tinysrgb&w=1200', 'active', 4.80, 22),
(2, 'Bamboo Forest Dawn Trek', 'bamboo-forest-dawn-trek', 'Early bamboo trail trek before the crowds arrive.', 'Adventure', 59.00, 'Half-day', 3.0, 12, 'English, Japanese', 'Kyoto', 'Kyoto', 'Japan', 'Arashiyama Station', 'https://images.pexels.com/photos/1366957/pexels-photo-1366957.jpeg?auto=compress&cs=tinysrgb&w=900', 'https://images.pexels.com/photos/1366957/pexels-photo-1366957.jpeg?auto=compress&cs=tinysrgb&w=1200', 'active', 4.50, 16),
(2, 'Kyoto Calligraphy Studio Session', 'kyoto-calligraphy-studio-session', 'Practice Japanese calligraphy with expert instruction.', 'Workshop', 54.00, 'Half-day', 2.0, 10, 'English, Japanese', 'Kyoto', 'Kyoto', 'Japan', 'Kawaramachi OPA Entrance', 'https://images.pexels.com/photos/261763/pexels-photo-261763.jpeg?auto=compress&cs=tinysrgb&w=900', 'https://images.pexels.com/photos/261763/pexels-photo-261763.jpeg?auto=compress&cs=tinysrgb&w=1200', 'active', 4.70, 12),
(2, 'Kyoto Riverside Bike Journey', 'kyoto-riverside-bike-journey', 'Bike route across neighborhoods and riverside cafes.', 'Nature', 63.00, 'Half-day', 4.0, 9, 'English, Japanese', 'Kyoto', 'Kyoto', 'Japan', 'Demachiyanagi Station', 'https://images.pexels.com/photos/100582/pexels-photo-100582.jpeg?auto=compress&cs=tinysrgb&w=900', 'https://images.pexels.com/photos/100582/pexels-photo-100582.jpeg?auto=compress&cs=tinysrgb&w=1200', 'active', 4.40, 10),

(5, 'Old Dhaka Food Trail', 'old-dhaka-food-trail', 'Street food and heritage lanes with a host from Dhaka.', 'Food', 42.00, 'Half-day', 3.0, 12, 'English, Bangla', 'Dhaka', 'Dhaka', 'Bangladesh', 'Ahsan Manzil Gate', 'https://images.pexels.com/photos/70497/pexels-photo-70497.jpeg?auto=compress&cs=tinysrgb&w=900', 'https://images.pexels.com/photos/70497/pexels-photo-70497.jpeg?auto=compress&cs=tinysrgb&w=1200', 'active', 4.50, 9),
(5, 'Sundarbans Explorer Weekend', 'sundarbans-explorer-weekend', 'Boat safari and village stay in the mangrove delta.', 'Adventure', 280.00, 'Multi-day', 22.0, 8, 'English, Bangla', 'Khulna', 'Khulna', 'Bangladesh', 'Khulna Launch Terminal', 'https://images.pexels.com/photos/145939/pexels-photo-145939.jpeg?auto=compress&cs=tinysrgb&w=900', 'https://images.pexels.com/photos/145939/pexels-photo-145939.jpeg?auto=compress&cs=tinysrgb&w=1200', 'active', 4.80, 6),
(5, 'Jamdani Weaving Workshop', 'jamdani-weaving-workshop', 'Hands-on textile workshop with local artisans.', 'Workshop', 61.00, 'Half-day', 3.5, 10, 'English, Bangla', 'Dhaka', 'Dhaka', 'Bangladesh', 'Mirpur Textile Hub', 'https://images.pexels.com/photos/4622427/pexels-photo-4622427.jpeg?auto=compress&cs=tinysrgb&w=900', 'https://images.pexels.com/photos/4622427/pexels-photo-4622427.jpeg?auto=compress&cs=tinysrgb&w=1200', 'active', 4.60, 14),
(5, 'Architectural Heritage in Panam City', 'architectural-heritage-in-panam-city', 'Photowalk in historic streets with restoration stories.', 'Art', 49.00, 'Full-day', 5.5, 14, 'English, Bangla', 'Sonargaon', 'Sonargaon', 'Bangladesh', 'Panam City Main Arch', 'https://images.pexels.com/photos/1704120/pexels-photo-1704120.jpeg?auto=compress&cs=tinysrgb&w=900', 'https://images.pexels.com/photos/1704120/pexels-photo-1704120.jpeg?auto=compress&cs=tinysrgb&w=1200', 'active', 4.40, 8),
(5, 'River Sunset Boat and Folk Music', 'river-sunset-boat-and-folk-music', 'Sunset boat ride with live folk instruments and snacks.', 'Nightlife', 38.00, 'Half-day', 2.5, 20, 'English, Bangla', 'Dhaka', 'Dhaka', 'Bangladesh', 'Sadarghat River Dock', 'https://images.pexels.com/photos/189349/pexels-photo-189349.jpeg?auto=compress&cs=tinysrgb&w=900', 'https://images.pexels.com/photos/189349/pexels-photo-189349.jpeg?auto=compress&cs=tinysrgb&w=1200', 'active', 4.20, 11),
(5, 'Tea Gardens of Sylhet Day Out', 'tea-gardens-of-sylhet-day-out', 'Green tea estate tour with tasting and short hikes.', 'Nature', 97.00, 'Full-day', 8.0, 9, 'English, Bangla', 'Sylhet', 'Sylhet', 'Bangladesh', 'Sylhet Railway Station', 'https://images.pexels.com/photos/39347/tea-plantation-tea-garden-plantation-39347.jpeg?auto=compress&cs=tinysrgb&w=900', 'https://images.pexels.com/photos/39347/tea-plantation-tea-garden-plantation-39347.jpeg?auto=compress&cs=tinysrgb&w=1200', 'active', 4.70, 13)
ON DUPLICATE KEY UPDATE
title = VALUES(title),
description = VALUES(description),
category = VALUES(category),
price = VALUES(price),
duration = VALUES(duration),
duration_hours = VALUES(duration_hours),
max_guests = VALUES(max_guests),
languages = VALUES(languages),
city = VALUES(city),
location = VALUES(location),
country = VALUES(country),
meeting_point = VALUES(meeting_point),
image_url = VALUES(image_url),
cover_image = VALUES(cover_image),
status = VALUES(status),
avg_rating = VALUES(avg_rating),
total_bookings = VALUES(total_bookings);

INSERT INTO localist_follows (follower_id, localist_id)
VALUES
(3, 1),
(3, 2),
(5, 1),
(5, 2)
ON DUPLICATE KEY UPDATE
created_at = created_at;
