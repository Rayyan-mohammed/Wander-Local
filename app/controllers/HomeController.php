<?php
class HomeController extends Controller {
    public function index() {
        $data = [
            'title' => 'Wander Local - Authentic Experiences'
        ];
        
        $this->view('home/index', $data);
    }
}