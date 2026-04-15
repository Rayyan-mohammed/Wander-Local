<?php
class BookingsController extends Controller {
    private $bookingModel;

    public function __construct() {
        $this->bookingModel = $this->model('Booking');
    }

    public function create() {
        if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] === 'host') {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'experience_id' => trim($_POST['experience_id']),
                'traveler_id' => trim($_SESSION['user_id']),
                'booking_date' => trim($_POST['booking_date']),
                'message' => trim($_POST['message'])
            ];

            if($this->bookingModel->createBooking($data)) {
                // Should redirect to dashboard with success message
                header('Location: ' . URLROOT . '/dashboard?success=booking_requested');
            } else {
                die('Something went wrong');
            }
        } else {
            header('Location: ' . URLROOT . '/experiences');
        }
    }
}