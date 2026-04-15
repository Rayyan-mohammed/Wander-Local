<?php
class DashboardController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
        }
    }

    public function index() {
        $db = new Database();

        $stats = [];
        if($_SESSION['user_role'] == 'host') {
            $db->query("SELECT * FROM experiences WHERE host_id = :id");
            $db->bind(':id', $_SESSION['user_id']);
            $stats['experiences'] = $db->resultSet();

            $db->query("SELECT b.*, e.title as experience_title, u.name as traveler_name FROM bookings b JOIN experiences e ON b.experience_id = e.id JOIN users u ON b.traveler_id = u.id WHERE e.host_id = :id ORDER BY b.created_at DESC");
            $db->bind(':id', $_SESSION['user_id']);
            $stats['bookings'] = $db->resultSet();
        } else {
            $db->query("SELECT b.*, e.title as experience_title, e.image_url as experience_image, u.name as host_name FROM bookings b JOIN experiences e ON b.experience_id = e.id JOIN users u ON e.host_id = u.id WHERE b.traveler_id = :id ORDER BY b.created_at DESC");
            $db->bind(':id', $_SESSION['user_id']);
            $stats['bookings'] = $db->resultSet();
        }

        $data = [
            'name' => $_SESSION['user_name'],
            'role' => $_SESSION['user_role'],
            'stats' => $stats
        ];
        $this->view('dashboard/index', $data);
    }
}