<?php
// pages/write_post.php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/pages/auth/login.php');
    exit;
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'host') {
    header('Location: ' . BASE_URL . '/pages/blog.php?error=localists_only');
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = $_GET['id'] ?? null;
$post = null;

$errors = [];
$success = '';

// Allowed categories
$categories = ['Food', 'Adventure', 'Culture', 'Hidden Gems', 'Host Stories', 'Traveler Diaries'];

// If editing, fetch post and verify ownership
if ($post_id) {
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post || $post['author_id'] != $user_id) {
        die("<div class='container py-5 text-center'><h1>Unauthorized or Post Not Found</h1><a href='".BASE_URL."/pages/blog.php' class='btn btn-primary mt-3'>Back to Blog</a></div>");
    }
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $tags = trim($_POST['tags'] ?? '');
    $status = trim($_POST['status'] ?? 'draft');
    $read_time = (int)($_POST['read_time'] ?? 0);
    $content = trim($_POST['content'] ?? '');
    
    // Auto-calculate read time if 0
    if ($read_time <= 0 && !empty($content)) {
        $word_count = str_word_count(strip_tags($content));
        $read_time = max(1, ceil($word_count / 200));
    }

    if (empty($title)) $errors[] = "Title is required.";
    if (empty($content)) $errors[] = "Story content is required.";
    if (empty($category) || !in_array($category, $categories)) $errors[] = "Invalid category.";

    // Handle Cover Image Upload
    $cover_image_path = $post ? $post['cover_image'] : null;
    
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['cover_image']['tmp_name'];
        $name = basename($_FILES['cover_image']['name']);
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        $file_type = mime_content_type($tmp_name);
        
        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "Only JPG, PNG and WebP images are allowed for cover image.";
        } else {
            $upload_dir = __DIR__ . '/../uploads/blog/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $filename = uniqid('post_') . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $name);
            $destination = $upload_dir . $filename;
            
            if (move_uploaded_file($tmp_name, $destination)) {
                // Delete old cover if editing and replacing
                if ($cover_image_path && file_exists(__DIR__ . '/../' . parse_url($cover_image_path, PHP_URL_PATH))) {
                    @unlink(__DIR__ . '/../' . parse_url($cover_image_path, PHP_URL_PATH));
                }
                $cover_image_path = BASE_URL . '/uploads/blog/' . $filename;
            } else {
                $errors[] = "Failed to upload cover image.";
            }
        }
    }

    if (empty($errors)) {
        // Basic slug generation
        $slug_base = preg_replace('/[^a-z0-9]+/', '-', strtolower($title));
        $slug_base = trim($slug_base, '-');
        $slug = $slug_base;
        
        try {
            if ($post_id) {
                $previousStatus = $post['status'] ?? 'draft';

                // Editing existing
                // Check slug uniqueness
                $sStmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts WHERE slug = ? AND id != ?");
                $sStmt->execute([$slug, $post_id]);
                if ($sStmt->fetchColumn() > 0) {
                    $slug .= '-' . uniqid();
                }

                // We allow safe HTML from Quill (strong, em, h2, h3, blockquote, a, img, ul, ol, li, p)
                // Use strip_tags with allowed tags for basic XSS protection.
                $allowed_tags = '<p><br><strong><b><em><i><u><s><h1><h2><h3><h4><h5><h6><a><img><blockquote><ul><ol><li><hr>';
                $safe_content = strip_tags($content, $allowed_tags);

                $updateStmt = $pdo->prepare("
                    UPDATE blog_posts SET 
                    title=?, slug=?, excerpt=?, content=?, cover_image=?, category=?, tags=?, read_time_mins=?, status=?, updated_at=NOW()
                    WHERE id=? AND author_id=?
                ");
                $updateStmt->execute([
                    $title, $slug, $excerpt, $safe_content, $cover_image_path, $category, $tags, $read_time, $status, $post_id, $user_id
                ]);

                if ($previousStatus !== 'published' && $status === 'published') {
                    notifyFollowersOfNewPost($pdo, (int)$user_id, (int)$post_id, $slug, $title);
                }

                $success = "Post updated successfully!";
                
                // Refresh post data
                $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
                $stmt->execute([$post_id]);
                $post = $stmt->fetch(PDO::FETCH_ASSOC);
                
            } else {
                // Creating new
                // Check slug uniqueness
                $sStmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts WHERE slug = ?");
                $sStmt->execute([$slug]);
                if ($sStmt->fetchColumn() > 0) {
                    $slug .= '-' . uniqid();
                }

                $allowed_tags = '<p><br><strong><b><em><i><u><s><h1><h2><h3><h4><h5><h6><a><img><blockquote><ul><ol><li><hr>';
                $safe_content = strip_tags($content, $allowed_tags);

                $insertStmt = $pdo->prepare("
                    INSERT INTO blog_posts (author_id, title, slug, excerpt, content, cover_image, category, tags, status, read_time_mins)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $insertStmt->execute([
                    $user_id, $title, $slug, $excerpt, $safe_content, $cover_image_path, $category, $tags, $status, $read_time
                ]);
                $post_id = $pdo->lastInsertId();

                if ($status === 'published') {
                    notifyFollowersOfNewPost($pdo, (int)$user_id, (int)$post_id, $slug, $title);
                }

                $success = "Post created successfully!";
                
                header("Location: write_post.php?id=$post_id&success=1");
                exit;
            }
        } catch (PDOException $e) {
            error_log('write_post.php: ' . $e->getMessage());
            $errors[] = "Unable to save your story right now. Please try again.";
        }
    }
}

$title_val = htmlspecialchars($_POST['title'] ?? $post['title'] ?? '');
$excerpt_val = htmlspecialchars($_POST['excerpt'] ?? $post['excerpt'] ?? '');
$category_val = htmlspecialchars($_POST['category'] ?? $post['category'] ?? '');
$tags_val = htmlspecialchars($_POST['tags'] ?? $post['tags'] ?? '');
$status_val = htmlspecialchars($_POST['status'] ?? $post['status'] ?? 'draft');
$read_time_val = (int)($_POST['read_time'] ?? $post['read_time_mins'] ?? 0);
$content_val = htmlspecialchars($_POST['content'] ?? $post['content'] ?? '');

$cover_url = $post['cover_image'] ?? '';

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success = "Post created successfully!";
}
?>

<!-- Include Quill stylesheet -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<!-- Override specific Quill defaults to match our theme -->
<style>
    .ql-container.ql-snow {
        border-bottom-left-radius: 0.5rem;
        border-bottom-right-radius: 0.5rem;
        border-color: #dee2e6;
        font-family: inherit;
        font-size: 1.1rem;
        min-height: 400px;
    }
    .ql-toolbar.ql-snow {
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
        border-color: #dee2e6;
        background-color: #f8f9fa;
    }
    .ql-editor { padding: 1.5rem; }
    .cover-preview { height: 250px; background-color: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 0.75rem; background-size: cover; background-position: center; position: relative; overflow: hidden; cursor: pointer; transition: border-color 0.2s; }
    .cover-preview:hover { border-color: var(--primary); }
</style>

<div class="bg-light py-5 min-vh-100">
    <div class="container">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="<?= BASE_URL ?>/pages/blog.php" class="text-decoration-none text-muted mb-2 d-inline-block"><i class="fa-solid fa-arrow-left me-1"></i> Back to stories</a>
                <h2 class="fw-bold font-heading mb-0"><?= $post ? 'Edit Story' : 'Write a Story' ?></h2>
            </div>
            <?php if ($post && $post['status'] == 'published'): ?>
                <a href="<?= BASE_URL ?>/pages/post.php?slug=<?= urlencode($post['slug']) ?>" target="_blank" class="btn btn-outline-primary rounded-pill"><i class="fa-solid fa-eye me-1"></i> View Live</a>
            <?php endif; ?>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4">
                <ul class="mb-0">
                    <?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4 fw-bold">
                <i class="fa-solid fa-check-circle me-2"></i> <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" id="postForm" class="row g-4">
            
            <!-- Main Content Column -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 p-md-5">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Story Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-lg fw-bold" placeholder="Give your story a catchy title..." value="<?= $title_val ?>" required style="font-size: 1.5rem; border-width: 2px;">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Short Excerpt <span class="text-danger">*</span></label>
                            <textarea name="excerpt" class="form-control" rows="2" placeholder="Write a 1-2 sentence summary to hook readers..." required maxlength="255"><?= $excerpt_val ?></textarea>
                            <div class="form-text text-end" id="excerptHelp">Max 255 characters</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Body Content <span class="text-danger">*</span></label>
                            <!-- Hidden input to hold quill html -->
                            <input type="hidden" name="content" id="contentInput" value="<?= $content_val ?>">
                            
                            <!-- Quill Editor Container -->
                            <div id="editor-container" class="bg-white"></div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Sidebar Info Column -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4 sticky-top" style="top: 100px;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                            <h5 class="fw-bold font-heading mb-0">Publishing</h5>
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold px-4 shadow-sm" onclick="return syncEditorContent()">Save</button>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase tracking-wider">Status</label>
                            <select name="status" class="form-select border-2">
                                <option value="draft" <?= $status_val === 'draft' ? 'selected' : '' ?>>Draft - Save for later</option>
                                <option value="published" <?= $status_val === 'published' ? 'selected' : '' ?>>Published - Make live</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase tracking-wider">Cover Image</label>
                            
                            <!-- Clickable Cover Preview -->
                            <div class="cover-preview d-flex flex-column align-items-center justify-content-center text-muted mb-2" id="coverPreview" onclick="document.getElementById('coverInput').click()" style="<?= $cover_url ? "background-image: url('".htmlspecialchars($cover_url)."'); border-style: solid;" : '' ?>">
                                <div id="coverOverlay" class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-dark bg-opacity-25 text-white transition <?= $cover_url ? 'opacity-0 hover-opacity-100' : '' ?>">
                                    <i class="fa-solid fa-camera fa-2x mb-2"></i>
                                    <span class="fw-bold">Upload Cover</span>
                                </div>
                            </div>
                            
                            <input type="file" name="cover_image" id="coverInput" class="d-none" accept="image/jpeg, image/png, image/webp" onchange="previewImage(event)">
                            <div class="form-text small">Recommended: 1200x630px JPG or PNG.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase tracking-wider">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select border-2" required>
                                <option value="">Select Category...</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat) ?>" <?= $category_val === $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase tracking-wider">Tags (Comma Separated)</label>
                            <input type="text" name="tags" class="form-control border-2" placeholder="e.g. coffee, hiking, tips" value="<?= $tags_val ?>">
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold small text-muted text-uppercase tracking-wider">Read Time (Mins)</label>
                            <input type="number" name="read_time" class="form-control border-2" placeholder="Auto-calculated if 0" value="<?= $read_time_val ?>" min="0">
                        </div>

                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

<style>
.tracking-wider { letter-spacing: 0.05em; }
.hover-opacity-100:hover { opacity: 1 !important; }
.opacity-0 { opacity: 0; }
</style>

<!-- Include the Quill library -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
// Initialize Quill editor
var quill = new Quill('#editor-container', {
    modules: {
        toolbar: [
            [{ 'header': [2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['link', 'image'],
            ['clean']
        ]
    },
    placeholder: 'Write your local experiences here...',
    theme: 'snow' // or 'bubble'
});

// Load existing content if any
const contentInput = document.getElementById('contentInput');
if (contentInput.value) {
    quill.root.innerHTML = contentInput.value;
}

// Sync content before submit
function syncEditorContent() {
    // Only parse if editor has text, otherwise allow required validation to fail naturally
    if (quill.getText().trim().length > 0) {
        contentInput.value = quill.root.innerHTML;
    } else {
        contentInput.value = ''; // empty triggers the PHP backend validation fallback
    }
    return true;
}

// Ensure form submission also triggers sync as a fallback
document.getElementById('postForm').addEventListener('submit', function(e) {
    syncEditorContent();
});

// Image Preview for Cover
function previewImage(event) {
    const input = event.target;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('coverPreview');
            const overlay = document.getElementById('coverOverlay');
            preview.style.backgroundImage = 'url(' + e.target.result + ')';
            preview.style.borderStyle = 'solid';
            
            // Auto hide overlay text on successful load
            overlay.classList.add('opacity-0');
            overlay.classList.add('hover-opacity-100');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>