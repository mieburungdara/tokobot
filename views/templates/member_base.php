<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }
        .content-wrapper {
            flex: 1;
            padding: 20px;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            h1, h2 {
                font-size: 1.5rem; /* Smaller font size for extra small screens */
            }
            .card-title {
                font-size: 1rem;
            }
            .card-text.fs-3 {
                font-size: 1.75rem !important;
            }
            .fa-3x {
                font-size: 2em !important;
            }
        }
    </style>
</head>
<body>
    <header class="bg-primary text-white p-3">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                <a class="navbar-brand" href="#"><h3>Member Dashboard</h3></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/member">My Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/dashboard">Dashboard</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <div class="content-wrapper container">
        <?php require_once $contentView; ?>
    </div>

    <footer class="bg-light text-center p-3 mt-auto">
        <div class="container">
            <p class="mb-0">&copy; 2023 Member Area</p>
        </div>
    </footer>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>