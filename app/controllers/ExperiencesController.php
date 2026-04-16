<?php
class ExperiencesController extends Controller {
    private $experienceModel;

    public function __construct() {
        $this->experienceModel = $this->model('Experience');
    }

    private function isDevelopment() {
        return defined('APP_ENV') && APP_ENV === 'development';
    }

    private function normalizeExperienceForListing($exp) {
        $location = trim((string)($exp->location ?? ''));
        if ($location === '') {
            $city = trim((string)($exp->city ?? ''));
            $country = trim((string)($exp->country ?? ''));
            $location = trim($city . ($country !== '' ? ', ' . $country : ''));
        }

        return (object) [
            'id' => (int)($exp->id ?? 0),
            'title' => (string)($exp->title ?? ''),
            'host' => (string)($exp->host_name ?? 'Local Host'),
            'hostAvatar' => !empty($exp->avatar_url)
                ? (string)$exp->avatar_url
                : 'https://ui-avatars.com/api/?name=' . urlencode((string)($exp->host_name ?? 'Host')),
            'verified' => (bool)($exp->is_verified ?? false),
            'location' => $location,
            'category' => (string)($exp->category ?? 'Experience'),
            'duration' => (string)($exp->duration ?? 'Half-day'),
            'price' => (float)($exp->price ?? 0),
            'rating' => (float)($exp->avg_rating ?? 0),
            'reviews' => (int)($exp->total_bookings ?? 0),
            'image' => !empty($exp->image_url)
                ? (string)$exp->image_url
                : 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&q=80&w=600&h=400'
        ];
    }

    private function getBaseExperiences() {
        $dbExperiences = $this->experienceModel->getExperiences();
        if (!empty($dbExperiences)) {
            return array_map(function ($exp) {
                return $this->normalizeExperienceForListing($exp);
            }, $dbExperiences);
        }

        if ($this->isDevelopment()) {
            return $this->getMockExperiences();
        }

        return [];
    }

    private function getMockExperiences() {
        return [
            (object)["id" => 1, "title" => "Secret Ramen Alleys", "host" => "Kenji", "hostAvatar" => "https://i.pravatar.cc/150?u=kenji", "verified" => true, "location" => "Tokyo, Japan", "category" => "Food", "duration" => "Half-day", "price" => 45, "rating" => 4.9, "reviews" => 124, "image" => "https://images.pexels.com/photos/1907246/pexels-photo-1907246.jpeg?auto=compress&cs=tinysrgb&w=600"],
            (object)["id" => 2, "title" => "Traditional Kintsugi Workshop", "host" => "Yuki", "hostAvatar" => "https://i.pravatar.cc/150?u=yuki", "verified" => true, "location" => "Kyoto, Japan", "category" => "Workshop", "duration" => "Half-day", "price" => 85, "rating" => 5.0, "reviews" => 89, "image" => "https://images.pexels.com/photos/1036856/pexels-photo-1036856.jpeg?auto=compress&cs=tinysrgb&w=600"],
            (object)["id" => 3, "title" => "Hidden Catacombs Tour", "host" => "Marco", "hostAvatar" => "https://i.pravatar.cc/150?u=marco", "verified" => false, "location" => "Rome, Italy", "category" => "History", "duration" => "Half-day", "price" => 35, "rating" => 4.7, "reviews" => 312, "image" => "https://images.pexels.com/photos/1105786/pexels-photo-1105786.jpeg?auto=compress&cs=tinysrgb&w=600"],
            (object)["id" => 4, "title" => "Oaxacan Mole Masterclass", "host" => "Elena", "hostAvatar" => "https://i.pravatar.cc/150?u=elena", "verified" => true, "location" => "Oaxaca, Mexico", "category" => "Food", "duration" => "Full-day", "price" => 110, "rating" => 5.0, "reviews" => 45, "image" => "https://images.pexels.com/photos/2098085/pexels-photo-2098085.jpeg?auto=compress&cs=tinysrgb&w=600"],
            (object)["id" => 5, "title" => "Street Art & Tapas", "host" => "Carlos", "hostAvatar" => "https://i.pravatar.cc/150?u=carlos", "verified" => true, "location" => "Barcelona, Spain", "category" => "Art", "duration" => "Half-day", "price" => 65, "rating" => 4.8, "reviews" => 210, "image" => "https://images.pexels.com/photos/1239291/pexels-photo-1239291.jpeg?auto=compress&cs=tinysrgb&w=600"],
            (object)["id" => 6, "title" => "Atlas Mountains Trek", "host" => "Youssef", "hostAvatar" => "https://i.pravatar.cc/150?u=youssef", "verified" => true, "location" => "Marrakech, Morocco", "category" => "Adventure", "duration" => "Multi-day", "price" => 250, "rating" => 4.9, "reviews" => 78, "image" => "https://images.pexels.com/photos/1366919/pexels-photo-1366919.jpeg?auto=compress&cs=tinysrgb&w=600"],
            (object)["id" => 7, "title" => "Midnight Jazz & Cocktails", "host" => "Sarah", "hostAvatar" => "https://i.pravatar.cc/150?u=sarah", "verified" => true, "location" => "New York, USA", "category" => "Nightlife", "duration" => "Half-day", "price" => 95, "rating" => 4.6, "reviews" => 156, "image" => "https://images.pexels.com/photos/164936/pexels-photo-164936.jpeg?auto=compress&cs=tinysrgb&w=600"],
            (object)["id" => 8, "title" => "Fado Music Experience", "host" => "Joao", "hostAvatar" => "https://i.pravatar.cc/150?u=joao", "verified" => false, "location" => "Lisbon, Portugal", "category" => "Nightlife", "duration" => "Half-day", "price" => 40, "rating" => 4.5, "reviews" => 92, "image" => "https://images.pexels.com/photos/625644/pexels-photo-625644.jpeg?auto=compress&cs=tinysrgb&w=600"],
            (object)["id" => 9, "title" => "Alpine Foraging", "host" => "Klaus", "hostAvatar" => "https://i.pravatar.cc/150?u=klaus", "verified" => true, "location" => "Munich, Germany", "category" => "Nature", "duration" => "Full-day", "price" => 120, "rating" => 4.8, "reviews" => 34, "image" => "https://images.pexels.com/photos/287240/pexels-photo-287240.jpeg?auto=compress&cs=tinysrgb&w=600"],
            (object)["id" => 10, "title" => "Bosphorus Sunset Sail", "host" => "Emre", "hostAvatar" => "https://i.pravatar.cc/150?u=emre", "verified" => true, "location" => "Istanbul, Turkey", "category" => "Nature", "duration" => "Half-day", "price" => 55, "rating" => 4.9, "reviews" => 201, "image" => "https://images.pexels.com/photos/1482193/pexels-photo-1482193.jpeg?auto=compress&cs=tinysrgb&w=600"],
            (object)["id" => 11, "title" => "Underground Techno Scene", "host" => "Anna", "hostAvatar" => "https://i.pravatar.cc/150?u=anna", "verified" => true, "location" => "Berlin, Germany", "category" => "Nightlife", "duration" => "Full-day", "price" => 75, "rating" => 4.7, "reviews" => 420, "image" => "https://images.pexels.com/photos/1190297/pexels-photo-1190297.jpeg?auto=compress&cs=tinysrgb&w=600"],
            (object)["id" => 12, "title" => "Ancient Mythology Walk", "host" => "Dimitris", "hostAvatar" => "https://i.pravatar.cc/150?u=dimitris", "verified" => true, "location" => "Athens, Greece", "category" => "History", "duration" => "Half-day", "price" => 30, "rating" => 4.8, "reviews" => 175, "image" => "https://images.pexels.com/photos/1645855/pexels-photo-1645855.jpeg?auto=compress&cs=tinysrgb&w=600"],
            (object)["id" => 13, "title" => "Pastry & Baking Class", "host" => "Chloe", "hostAvatar" => "https://i.pravatar.cc/150?u=chloe", "verified" => true, "location" => "Paris, France", "category" => "Food", "duration" => "Half-day", "price" => 90, "rating" => 4.9, "reviews" => 88, "image" => "https://images.pexels.com/photos/2056247/pexels-photo-2056247.jpeg?auto=compress&cs=tinysrgb&w=600"],
            (object)["id" => 14, "title" => "Azulejo Tile Painting", "host" => "Maria", "hostAvatar" => "https://i.pravatar.cc/150?u=maria", "verified" => false, "location" => "Porto, Portugal", "category" => "Workshop", "duration" => "Half-day", "price" => 45, "rating" => 4.6, "reviews" => 56, "image" => "https://images.pexels.com/photos/1328891/pexels-photo-1328891.jpeg?auto=compress&cs=tinysrgb&w=600"],
            (object)["id" => 15, "title" => "Volcano Hiking", "host" => "Alejandro", "hostAvatar" => "https://i.pravatar.cc/150?u=alejandro", "verified" => true, "location" => "Mexico City, Mexico", "category" => "Adventure", "duration" => "Full-day", "price" => 130, "rating" => 4.9, "reviews" => 110, "image" => "https://images.pexels.com/photos/933054/pexels-photo-933054.jpeg?auto=compress&cs=tinysrgb&w=600"]
        ];
    }

    private function mapMockExperienceToDetail($mock) {
        return (object)[
            'id' => $mock->id,
            'title' => $mock->title,
            'description' => 'Discover a handcrafted local experience hosted by passionate locals.',
            'price' => $mock->price,
            'duration' => $mock->duration,
            'category' => $mock->category,
            'location' => $mock->location,
            'image_url' => $mock->image,
            'host_name' => $mock->host,
            'host_bio' => 'Local host sharing authentic stories, culture, and practical tips from the city.',
            'host_languages' => 'English',
            'is_verified' => $mock->verified,
            'avatar_url' => $mock->hostAvatar
        ];
    }

    public function index() {
        $baseExperiences = $this->getBaseExperiences();
        $cities = array_values(array_unique(array_map(function($exp) {
            return $exp->location;
        }, $baseExperiences)));

        $categories = ["Food", "Workshop", "Nature", "Art", "History", "Nightlife", "Adventure"];
        $durations = ["Half-day", "Full-day", "Multi-day"];

        // Get filters
        $q = isset($_GET['q']) ? $_GET['q'] : '';
        $category = isset($_GET['category']) ? $_GET['category'] : '';
        $duration = isset($_GET['duration']) ? $_GET['duration'] : '';
        $minRating = isset($_GET['minRating']) ? (float)$_GET['minRating'] : 0;
        $maxPrice = isset($_GET['maxPrice']) ? (int)$_GET['maxPrice'] : 500;
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'recommended';

        // Filter the experiences
        $filtered_experiences = array_filter($baseExperiences, function($exp) use ($q, $category, $duration, $minRating, $maxPrice) {
            $matchQuery = empty($q) || stripos($exp->location, $q) !== false || stripos($exp->title, $q) !== false;
            $matchCat = empty($category) || $exp->category === $category;
            $matchDur = empty($duration) || $exp->duration === $duration;
            $matchRating = $exp->rating >= $minRating;
            $matchPrice = $exp->price <= $maxPrice;
            
            return $matchQuery && $matchCat && $matchDur && $matchRating && $matchPrice;
        });

        // Sort the experiences
        usort($filtered_experiences, function($a, $b) use ($sort) {
            switch($sort) {
                case 'price_asc': return $a->price <=> $b->price;
                case 'price_desc': return $b->price <=> $a->price;
                case 'rating': return $b->rating <=> $a->rating; // desc
                default: return 0; // recommended (original order)
            }
        });

        $data = [
            'experiences' => $filtered_experiences,
            'cities' => $cities,
            'categories' => $categories,
            'durations' => $durations,
            'filters' => [
                'q' => $q,
                'category' => $category,
                'duration' => $duration,
                'minRating' => $minRating,
                'maxPrice' => $maxPrice,
                'sort' => $sort
            ]
        ];

        $this->view('experiences/index', $data);
    }

    public function show($id) {
        $experience = $this->experienceModel->getExperienceById($id);

        if(!$experience && $this->isDevelopment()) {
            foreach($this->getMockExperiences() as $mockExperience) {
                if((int)$mockExperience->id === (int)$id) {
                    $experience = $this->mapMockExperienceToDetail($mockExperience);
                    break;
                }
            }
        }

        if(!$experience) {
            header('Location: ' . URLROOT . '/experiences');
            exit;
        }

        $data = [
            'experience' => $experience
        ];
        $this->view('experiences/show', $data);
    }

    public function create() {
        if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'host') {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize
            $_POST = array_map(function ($value) {
                return is_string($value) ? trim($value) : $value;
            }, $_POST);

            $data = [
                'host_id' => trim($_SESSION['user_id']),
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? '',
                'price' => $_POST['price'] ?? '',
                'duration' => $_POST['duration'] ?? '',
                'category' => $_POST['category'] ?? '',
                'location' => $_POST['location'] ?? '',
                'image_url' => '', // placeholder
                
                'title_err' => '',
                'description_err' => '',
            ];

            // Image Upload Handling (Simple)
            if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $target_dir = "../public/images/";
                $image_name = time() . '_' . basename($_FILES["image"]["name"]);
                $target_file = $target_dir . $image_name;
                
                if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $data['image_url'] = $image_name;
                }
            } else {
                $data['image_url'] = 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&q=80&w=600&h=400'; // placeholder default image 
            }

            // Submit
            if(empty($data['title_err']) && empty($data['description_err'])) {
                if($this->experienceModel->addExperience($data)) {
                    header('Location: ' . URLROOT . '/experiences');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('experiences/create', $data);
            }
        } else {
            $data = [
                'title' => '',
                'description' => '',
                'price' => '',
                'duration' => '',
                'category' => '',
                'location' => ''
            ];
            $this->view('experiences/create', $data);
        }
    }
}
