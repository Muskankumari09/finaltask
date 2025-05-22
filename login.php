<?php
session_start();
require_once 'db_connect.php';

$errors = [];
$username = '';
$password = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize input
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Server-side validation
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    // Proceed with authentication if no validation errors
    if (empty($errors)) {
        // Check if username exists
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            // Verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: index.html");
                exit();
            } else {
                $errors[] = "Invalid password.";
            }
        } else {
            $errors[] = "Username not found.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Club - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom styles for the login page */
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .error-message {
            color: #dc3545;
            font-size: 0.9em;
            margin-top: 5px;
            display: none;
        }
        .form-control.is-invalid {
            border-color: #dc3545;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            width: 100%;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #343a40;
        }
        .server-error {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.html">College Club</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="events.html">Events</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <li class="nav-item"><a class="nav-link active" href="login.php">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Login Form -->
    <section class="py-5">
        <div class="login-container">
            <h2 class="text-center mb-4">Login to College Club</h2>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger server-error">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <form id="login-form" method="POST" action="login.php" novalidate>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                    <div id="username-error" class="error-message"></div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div id="password-error" class="error-message"></div>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            <p class="text-center mt-3">Don't have an account? Contact the club admin.</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-white text-center py-3">
        <p>© 2025 College Club. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('login-form');
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            const usernameError = document.getElementById('username-error');
            const passwordError = document.getElementById('password-error');

            form.addEventListener('submit', (e) => {
                let isValid = true;

                // Reset error messages and styles
                usernameError.style.display = 'none';
                passwordError.style.display = 'none';
                usernameInput.classList.remove('is-invalid');
                passwordInput.classList.remove('is-invalid');

                // Validate username
                const username = usernameInput.value.trim();
                if (!username) {
                    usernameError.textContent = 'Username is required.';
                    usernameError.style.display = 'block';
                    usernameInput.classList.add('is-invalid');
                    isValid = false;
                } else if (username.length < 3) {
                    usernameError.textContent = 'Username must be at least 3 characters long.';
                    usernameError.style.display = 'block';
                    usernameInput.classList.add('is-invalid');
                    isValid = false;
                }

                // Validate password
                const password = passwordInput.value.trim();
                if (!password) {
                    passwordError.textContent = 'Password is required.';
                    passwordError.style.display = 'block';
                    passwordInput.classList.add('is-invalid');
                    isValid = false;
                } else if (password.length < 6) {
                    passwordError.textContent = 'Password must be at least 6 characters long.';
                    passwordError.style.display = 'block';
                    passwordInput.classList.add('is-invalid');
                    isValid = false;
                }

                // If client-side validation passes, allow form submission to PHP
                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>