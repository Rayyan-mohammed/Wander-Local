<?php
// includes/components/alert.php
function render_alert($type = 'info', $message = '') {
    if (empty($message)) return '';
    $type = htmlspecialchars($type);
    $msg = htmlspecialchars($message);
    return <<<HTML
    <div class="alert alert-{$type} alert-dismissible fade show rounded-3 shadow-sm" role="alert">
        {$msg}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
HTML;
}
