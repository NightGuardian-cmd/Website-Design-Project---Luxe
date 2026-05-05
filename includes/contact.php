<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'Contact Us';

$success = false;
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name)    $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (!$message) $errors[] = 'Message is required.';

    if (empty($errors)) {
        $db   = getDB();
        $stmt = $db->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $subject, $message]);
        $success = true;
    }
}

include __DIR__ . '/header.php';
?>

<div class="page-hero">
    <h1 class="reveal">Get in Touch</h1>
    <p class="reveal">We'd love to hear from you</p>
    <div class="breadcrumb"><a href="<?= SITE_URL ?>">Home</a> / Contact</div>
</div>

<div class="contact-layout">
    <!-- Info -->
    <div class="contact-info">
        <h2 class="reveal">Let's Talk</h2>
        <p class="reveal">Whether you have a question about an order, a product, or just want to say hello — our team is here for you, Monday to Friday, 9am–6pm.</p>

        <div class="contact-item reveal">
            <div class="contact-icon"><i class="fa-solid fa-location-dot"></i></div>
            <div>
                <h4>Visit Us</h4>
                <p>14 Rue de la Paix, Paris, France<br>75002</p>
            </div>
        </div>
        <div class="contact-item reveal">
            <div class="contact-icon"><i class="fa-solid fa-phone"></i></div>
            <div>
                <h4>Call Us</h4>
                <p>+33 1 23 45 67 89</p>
            </div>
        </div>
        <div class="contact-item reveal">
            <div class="contact-icon"><i class="fa-regular fa-envelope"></i></div>
            <div>
                <h4>Email Us</h4>
                <p>hello@luxepremium.com</p>
            </div>
        </div>
        <div class="contact-item reveal">
            <div class="contact-icon"><i class="fa-regular fa-clock"></i></div>
            <div>
                <h4>Working Hours</h4>
                <p>Monday – Friday: 9:00 AM – 6:00 PM<br>Saturday: 10:00 AM – 4:00 PM</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="contact-form reveal">
        <h3>Send a Message</h3>

        <?php if ($success): ?>
            <div class="flash flash-success" style="border-radius:4px;margin-bottom:24px;">
                <i class="fa-solid fa-circle-check"></i> Thanks! We'll get back to you within 24 hours.
            </div>
        <?php endif; ?>

        <?php if ($errors): ?>
            <div class="flash flash-error" style="border-radius:4px;margin-bottom:24px;display:block;">
                <?php foreach ($errors as $e): ?><div><?= sanitize($e) ?></div><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="form-row">
                <div class="form-group">
                    <label>Your Name</label>
                    <input type="text" name="name" placeholder="Jane Doe" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="jane@example.com" required>
                </div>
            </div>
            <div class="form-group">
                <label>Subject</label>
                <select name="subject">
                    <option value="">Select a topic…</option>
                    <option>Order Enquiry</option>
                    <option>Returns & Exchanges</option>
                    <option>Product Question</option>
                    <option>Partnership</option>
                    <option>Other</option>
                </select>
            </div>
            <div class="form-group">
                <label>Message</label>
                <textarea name="message" placeholder="Tell us how we can help…" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Send Message <i class="fa-solid fa-paper-plane"></i></button>
        </form>
    </div>
</div>

<script>const SITE_URL = '<?= SITE_URL ?>';</script>
<?php include __DIR__ . '/footer.php'; ?>