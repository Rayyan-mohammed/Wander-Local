<?php
class Booking {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function createBooking($data) {
        $this->db->query("INSERT INTO bookings (experience_id, traveler_id, booking_date, message_to_host, status) VALUES (:experience_id, :traveler_id, :booking_date, :message_to_host, 'pending')");

        $this->db->bind(':experience_id', $data['experience_id']);
        $this->db->bind(':traveler_id', $data['traveler_id']);
        $this->db->bind(':booking_date', $data['booking_date']);
        $this->db->bind(':message_to_host', $data['message']);

        return $this->db->execute();
    }
}