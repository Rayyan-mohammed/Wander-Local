<?php
class Booking {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function createBooking($data) {
        $experiencePrice = $this->getExperiencePrice((int)$data['experience_id']);
        if ($experiencePrice === null) {
            return false;
        }

        $guestCount = max(1, (int)($data['guest_count'] ?? 1));
        $totalPrice = $experiencePrice * $guestCount;
        $bookingRef = $this->generateBookingRef();

        $this->db->query("INSERT INTO bookings (experience_id, traveler_id, booking_date, guest_count, total_price, status, special_requests, booking_ref) VALUES (:experience_id, :traveler_id, :booking_date, :guest_count, :total_price, 'pending', :special_requests, :booking_ref)");

        $this->db->bind(':experience_id', $data['experience_id']);
        $this->db->bind(':traveler_id', $data['traveler_id']);
        $this->db->bind(':booking_date', $data['booking_date']);
        $this->db->bind(':guest_count', $guestCount);
        $this->db->bind(':total_price', $totalPrice);
        $this->db->bind(':special_requests', $data['message'] ?? null);
        $this->db->bind(':booking_ref', $bookingRef);

        return $this->db->execute();
    }

    private function getExperiencePrice($experienceId) {
        $this->db->query('SELECT price FROM experiences WHERE id = :experience_id LIMIT 1');
        $this->db->bind(':experience_id', (int)$experienceId);
        $row = $this->db->single();

        if (!$row || !isset($row->price)) {
            return null;
        }

        return (float)$row->price;
    }

    private function generateBookingRef() {
        return 'WL-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
    }
}