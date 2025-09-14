<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
    
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <meta name="keywords" content="hotel, booking, accommodation, sweetlife">
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : 'SweetLife Hotel - Your perfect accommodation destination'; ?>">
    <meta name="author" content="SweetLife Hotel">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <!-- Favicon -->
    <link rel="icon" href="images/fevicon.png" type="image/gif" />
    <!-- Scrollbar Custom CSS -->
    <link rel="stylesheet" href="css/jquery.mCustomScrollbar.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css" media="screen">
    
    <!-- Custom Hotel CSS -->
    <style>
        .booking-form {
            background: rgba(0,0,0,0.8);
            padding: 30px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .booking-form .form-control {
            margin-bottom: 15px;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .room-card {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            transition: transform 0.3s ease;
        }
        .room-card:hover {
            transform: translateY(-5px);
        }
        .room-price {
            color: #fe0000;
            font-size: 24px;
            font-weight: bold;
        }
        .availability-calendar {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .unavailable-date {
            background-color: #ff6b6b !important;
            color: white !important;
            cursor: not-allowed !important;
        }
        .modal-content {
            border-radius: 10px;
        }
        .alert {
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .btn-book {
            background: #fe0000;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-book:hover {
            background: #d40000;
            color: white;
            transform: translateY(-2px);
        }
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #fe0000;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body class="main-layout <?php echo isset($body_class) ? $body_class : ''; ?>">
    <!-- Loader -->
    <div class="loader_bg">
        <div class="loader"><img src="images/loading.gif" alt="Loading"/></div>
    </div>
    
    <!-- Header -->
    <header>
        <div class="header">
            <div class="container">
                <div class="row">
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col logo_section">
                        <div class="full">
                            <div class="center-desk">
                                <div class="logo">
                                    <a href="index.php"><img src="images/logo.png" alt="SweetLife Hotel" /></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <nav class="navigation navbar navbar-expand-md navbar-dark">
                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample04" aria-controls="navbarsExample04" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="navbarsExample04">
                                <ul class="navbar-nav mr-auto">
                                    <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                                        <a class="nav-link" href="index.php">Home</a>
                                    </li>
                                    <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'active' : ''; ?>">
                                        <a class="nav-link" href="about.php">About</a>
                                    </li>
                                    <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'rooms.php') ? 'active' : ''; ?>">
                                        <a class="nav-link" href="rooms.php">Hotel Rooms</a>
                                    </li>
                                    <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'apartments.php') ? 'active' : ''; ?>">
                                        <a class="nav-link" href="apartments.php">Short Let</a>
                                    </li>
                                    <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'gallery.php') ? 'active' : ''; ?>">
                                        <a class="nav-link" href="gallery.php">Gallery</a>
                                    </li>
                                    <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>">
                                        <a class="nav-link" href="contact.php">Contact</a>
                                    </li>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                    <li class="nav-item">
                                        <a class="nav-link" href="my-bookings.php">My Bookings</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="logout.php">Logout</a>
                                    </li>
                                    <?php else: ?>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#" data-toggle="modal" data-target="#loginModal">Login</a>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login / Register</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="authTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="login-tab" data-toggle="tab" href="#login" role="tab">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="register-tab" data-toggle="tab" href="#register" role="tab">Register</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="authTabContent">
                        <div class="tab-pane fade show active" id="login" role="tabpanel">
                            <form id="loginForm" class="mt-3">
                                <div class="form-group">
                                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                                </div>
                                <button type="submit" class="btn btn-book btn-block">Login</button>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="register" role="tabpanel">
                            <form id="registerForm" class="mt-3">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="name" placeholder="Full Name" required>
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                                </div>
                                <div class="form-group">
                                    <input type="tel" class="form-control" name="phone" placeholder="Phone Number" required>
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                                </div>
                                <button type="submit" class="btn btn-book btn-block">Register</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>