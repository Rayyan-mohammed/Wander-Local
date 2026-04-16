<?php
class ExperiencesController extends Controller {
    private $experienceModel;

    public function __construct() {
        $this->experienceModel = $this->model('Experience');
    }

    public function index() {
        // --- MOCK DATA ---
        $cities = [
            "Tokyo, Japan", "Kyoto, Japan", "Osaka, Japan", "Paris, France", 
            "Rome, Italy", "Milan, Italy", "New York, USA", "Oaxaca, Mexico", 
            "Mexico City, Mexico", "Lisbon, Portugal", "Porto, Portugal", "London, UK", 
            "Barcelona, Spain", "Madrid, Spain", "Berlin, Germany", "Munich, Germany", 
            "Vienna, Austria", "Athens, Greece", "Istanbul, Turkey", "Marrakech, Morocco"
        ];

        $categories = ["Food", "Workshop", "Nature", "Art", "History", "Nightlife", "Adventure"];
        $durations = ["Half-day", "Full-day", "Multi-day"];

        $mock_experiences = [
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

        // Get filters
        $q = isset($_GET['q']) ? $_GET['q'] : '';
        $category = isset($_GET['category']) ? $_GET['category'] : '';
        $duration = isset($_GET['duration']) ? $_GET['duration'] : '';
        $minRating = isset($_GET['minRating']) ? (float)$_GET['minRating'] : 0;
        $maxPrice = isset($_GET['maxPrice']) ? (int)$_GET['maxPrice'] : 500;
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'recommended';

        // Filter the experiences
        $filtered_experiences = array_filter($mock_experiences, function($exp) use ($q, $category, $duration, $minRating, $maxPrice) {
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

        // Fallback for mock experiences that aren't in the database yet
        if(!$experience) {
            $experience = clone (object)[
                'id' => $id,
                'title' => 'Wander Local Experience ' . $id,
                'category' => 'Culture',
                'location' => 'Worldwide',
                'duration' => 'Flexible',
                'price' => 75,
                'host_name' => 'Local Host',
                'description' => 'A unique and memorable local experience for ID ' . $id . '.',
                'is_verified' => true
            ];
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
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'host_id' => trim($_SESSION['user_id']),
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'price' => trim($_POST['price']),
                'duration' => trim($_POST['duration']),
                'category' => trim($_POST['category']),
                'location' => trim($_POST['location']),
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
