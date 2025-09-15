<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="horizontal">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="shortcut icon" type="image/png" href="<?php echo BASE_URL; ?>/public/assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/css/styles.css" />

  <title>Modernize Bootstrap Admin</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/assets/libs/owl.carousel/dist/assets/owl.carousel.min.css" />
</head>

<body>
  <div class="toast toast-onload align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-body hstack align-items-start gap-6">
      <i class="ti ti-alert-circle fs-6"></i>
      <div>
        <h5 class="text-white fs-3 mb-1">Welcome to Modernize</h5>
        <h6 class="text-white fs-2 mb-0">Easy to costomize the Template!!!</h6>
      </div>
      <button type="button" class="btn-close btn-close-white fs-2 m-0 ms-auto shadow-none" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
  <div class="preloader">
    <img src="<?php echo BASE_URL; ?>/public/assets/images/logos/favicon.png" alt="loader" class="lds-ripple img-fluid" />
  </div>
  <div id="main-wrapper">
    <aside class="left-sidebar with-vertical">
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <a href="<?php echo BASE_URL; ?>/admin/dashboard" class="text-nowrap logo-img">
            <img src="<?php echo BASE_URL; ?>/public/assets/images/logos/dark-logo.svg" class="dark-logo" alt="Logo-Dark" />
            <img src="<?php echo BASE_URL; ?>/public/assets/images/logos/light-logo.svg" class="light-logo" alt="Logo-light" />
          </a>
          <a href="javascript:void(0)" class="sidebartoggler ms-auto text-decoration-none fs-5 d-block d-xl-none">
            <i class="ti ti-x"></i>
          </a>
        </div>

        <nav class="sidebar-nav scroll-sidebar" data-simplebar>
          <ul id="sidebarnav">
            <li class="nav-small-cap">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu">Home</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="<?php echo BASE_URL; ?>/admin/dashboard" id="get-url" aria-expanded="false">
                <span>
                  <i class="ti ti-aperture"></i>
                </span>
                <span class="hide-menu">Dashboard</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="<?php echo BASE_URL; ?>/admin/users" aria-expanded="false">
                <span>
                  <i class="ti ti-users"></i>
                </span>
                <span class="hide-menu">Users</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="<?php echo BASE_URL; ?>/admin/reports" aria-expanded="false">
                <span>
                  <i class="ti ti-chart-line"></i>
                </span>
                <span class="hide-menu">Reports</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="<?php echo BASE_URL; ?>/admin/settings" aria-expanded="false">
                <span>
                  <i class="ti ti-settings"></i>
                </span>
                <span class="hide-menu">Settings</span>
              </a>
            </li>
          </ul>
        </nav>

        <div class="fixed-profile p-3 mx-4 mb-2 bg-secondary-subtle rounded mt-3">
          <div class="hstack gap-3">
            <div class="john-img">
              <img src="<?php echo BASE_URL; ?>/public/assets/images/profile/user-1.jpg" class="rounded-circle" width="40" height="40" alt="modernize-img" />
            </div>
            <div class="john-title">
              <h6 class="mb-0 fs-4 fw-semibold">Mathew</h6>
              <span class="fs-2">Designer</span>
            </div>
            <button class="border-0 bg-transparent text-primary ms-auto" tabindex="0" type="button" aria-label="logout" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="logout">
              <i class="ti ti-power fs-6"></i>
            </button>
          </div>
        </div>
      </div>
    </aside>
    <div class="page-wrapper">
      <header class="topbar">
        <div class="with-vertical">
          <nav class="navbar navbar-expand-lg p-0">
            <ul class="navbar-nav">
              <li class="nav-item nav-icon-hover-bg rounded-circle ms-n2">
                <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)">
                  <i class="ti ti-menu-2"></i>
                </a>
              </li>
              <li class="nav-item nav-icon-hover-bg rounded-circle d-none d-lg-flex">
                <a class="nav-link" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exampleModal">
                  <i class="ti ti-search"></i>
                </a>
              </li>
            </ul>

            <ul class="navbar-nav quick-links d-none d-lg-flex align-items-center">
              <li class="nav-item nav-icon-hover-bg rounded w-auto dropdown d-none d-lg-block mx-0">
                <div class="hover-dd">
                  <a class="nav-link" href="javascript:void(0)">
                    Apps<span class="mt-1">
                      <i class="ti ti-chevron-down fs-3"></i>
                    </span>
                  </a>
                  <div class="dropdown-menu dropdown-menu-nav dropdown-menu-animate-up py-0">
                    <div class="row">
                      <div class="col-8">
                        <div class="ps-7 pt-7">
                          <div class="border-bottom">
                            <div class="row">
                              <div class="col-6">
                                <div class="position-relative">
                                  <a href="<?php echo BASE_URL; ?>/admin/chat" class="d-flex align-items-center pb-9 position-relative">
                                    <div class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                      <img src="<?php echo BASE_URL; ?>/public/assets/images/svgs/icon-dd-chat.svg" alt="modernize-img" class="img-fluid" width="24" height="24" />
                                    </div>
                                    <div>
                                      <h6 class="mb-1 fw-semibold fs-3">
                                        Chat Application
                                      </h6>
                                      <span class="fs-2 d-block text-body-secondary">New messages arrived</span>
                                    </div>
                                  </a>
                                  <a href="<?php echo BASE_URL; ?>/admin/invoice" class="d-flex align-items-center pb-9 position-relative">
                                    <div class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                      <img src="<?php echo BASE_URL; ?>/public/assets/images/svgs/icon-dd-invoice.svg" alt="modernize-img" class="img-fluid" width="24" height="24" />
                                    </div>
                                    <div>
                                      <h6 class="mb-1 fw-semibold fs-3">Invoice App</h6>
                                      <span class="fs-2 d-block text-body-secondary">Get latest invoice</span>
                                    </div>
                                  </a>
                                  <a href="<?php echo BASE_URL; ?>/admin/contact2" class="d-flex align-items-center pb-9 position-relative">
                                    <div class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                      <img src="<?php echo BASE_URL; ?>/public/assets/images/svgs/icon-dd-mobile.svg" alt="modernize-img" class="img-fluid" width="24" height="24" />
                                    </div>
                                    <div>
                                      <h6 class="mb-1 fw-semibold fs-3">
                                        Contact Application
                                      </h6>
                                      <span class="fs-2 d-block text-body-secondary">2 Unsaved Contacts</span>
                                    </div>
                                  </a>
                                  <a href="<?php echo BASE_URL; ?>/admin/email" class="d-flex align-items-center pb-9 position-relative">
                                    <div class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                      <img src="<?php echo BASE_URL; ?>/public/assets/images/svgs/icon-dd-message-box.svg" alt="modernize-img" class="img-fluid" width="24" height="24" />
                                    </div>
                                    <div>
                                      <h6 class="mb-1 fw-semibold fs-3">Email App</h6>
                                      <span class="fs-2 d-block text-body-secondary">Get new emails</span>
                                    </div>
                                  </a>
                                </div>
                              </div>
                              <div class="col-6">
                                <div class="position-relative">
                                  <a href="<?php echo BASE_URL; ?>/admin/user-profile" class="d-flex align-items-center pb-9 position-relative">
                                    <div class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                      <img src="<?php echo BASE_URL; ?>/public/assets/images/svgs/icon-dd-cart.svg" alt="modernize-img" class="img-fluid" width="24" height="24" />
                                    </div>
                                    <div>
                                      <h6 class="mb-1 fw-semibold fs-3">
                                        User Profile
                                      </h6>
                                      <span class="fs-2 d-block text-body-secondary">learn more information</span>
                                    </div>
                                  </a>
                                  <a href="<?php echo BASE_URL; ?>/admin/calendar" class="d-flex align-items-center pb-9 position-relative">
                                    <div class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                      <img src="<?php echo BASE_URL; ?>/public/assets/images/svgs/icon-dd-date.svg" alt="modernize-img" class="img-fluid" width="24" height="24" />
                                    </div>
                                    <div>
                                      <h6 class="mb-1 fw-semibold fs-3">
                                        Calendar App
                                      </h6>
                                      <span class="fs-2 d-block text-body-secondary">Get dates</span>
                                    </div>
                                  </a>
                                  <a href="<?php echo BASE_URL; ?>/admin/contact" class="d-flex align-items-center pb-9 position-relative">
                                    <div class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                      <img src="<?php echo BASE_URL; ?>/public/assets/images/svgs/icon-dd-lifebuoy.svg" alt="modernize-img" class="img-fluid" width="24" height="24" />
                                    </div>
                                    <div>
                                      <h6 class="mb-1 fw-semibold fs-3">
                                        Contact List Table
                                      </h6>
                                      <span class="fs-2 d-block text-body-secondary">Add new contact</span>
                                    </div>
                                  </a>
                                  <a href="<?php echo BASE_URL; ?>/admin/notes" class="d-flex align-items-center pb-9 position-relative">
                                    <div class="text-bg-light rounded-1 me-3 p-6 d-flex align-items-center justify-content-center">
                                      <img src="<?php echo BASE_URL; ?>/public/assets/images/svgs/icon-dd-application.svg" alt="modernize-img" class="img-fluid" width="24" height="24" />
                                    </div>
                                    <div>
                                      <h6 class="mb-1 fw-semibold fs-3">
                                        Notes Application
                                      </h6>
                                      <span class="fs-2 d-block text-body-secondary">To-do and Daily tasks</span>
                                    </div>
                                  </a>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="row align-items-center py-3">
                            <div class="col-8">
                              <a class="fw-semibold d-flex align-items-center lh-1" href="javascript:void(0)">
                                <i class="ti ti-help fs-6 me-2"></i>Frequently Asked Questions
                              </a>
                            </div>
                            <div class="col-4">
                              <div class="d-flex justify-content-end pe-4">
                                <button class="btn btn-primary">Check</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-4 ms-n4">
                        <div class="position-relative p-7 border-start h-100">
                          <h5 class="fs-5 mb-9 fw-semibold">Quick Links</h5>
                          <ul class="">
                            <li class="mb-3">
                              <a class="fw-semibold bg-hover-primary" href="<?php echo BASE_URL; ?>/admin/pricing">Pricing Page</a>
                            </li>
                            <li class="mb-3">
                              <a class="fw-semibold bg-hover-primary" href="<?php echo BASE_URL; ?>/admin/authentication-login">Authentication
                                Design</a>
                            </li>
                            <li class="mb-3">
                              <a class="fw-semibold bg-hover-primary" href="<?php echo BASE_URL; ?>/admin/authentication-register">Register Now</a>
                            </li>
                            <li class="mb-3">
                              <a class="fw-semibold bg-hover-primary" href="<?php echo BASE_URL; ?>/admin/authentication-error">404 Error Page</a>
                            </li>
                            <li class="mb-3">
                              <a class="fw-semibold bg-hover-primary" href="<?php echo BASE_URL; ?>/admin/notes">Notes App</a>
                            </li>
                            <li class="mb-3">
                              <a class="fw-semibold bg-hover-primary" href="<?php echo BASE_URL; ?>/admin/user-profile">User Application</a>
                            </li>
                            <li class="mb-3">
                              <a class="fw-semibold bg-hover-primary" href="<?php echo BASE_URL; ?>/admin/account-settings">Account Settings</a>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              <li class="nav-item dropdown-hover d-none d-lg-block">
                <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/chat">Chat</a>
              </li>
              <li class="nav-item dropdown-hover d-none d-lg-block">
                <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/calendar">Calendar</a>
              </li>
              <li class="nav-item dropdown-hover d-none d-lg-block">
                <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/email">Email</a>
              </li>
            </ul>

            <div class="d-block d-lg-none py-4">
              <a href="<?php echo BASE_URL; ?>/admin/dashboard" class="text-nowrap logo-img">
                <img src="<?php echo BASE_URL; ?>/public/assets/images/logos/dark-logo.svg" class="dark-logo" alt="Logo-Dark" />
                <img src="<?php echo BASE_URL; ?>/public/assets/images/logos/light-logo.svg" class="light-logo" alt="Logo-light" />
              </a>
            </div>
            <a class="navbar-toggler nav-icon-hover-bg rounded-circle p-0 mx-0 border-0" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
              <i class="ti ti-dots fs-7"></i>
            </a>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
              <div class="d-flex align-items-center justify-content-between">
                <a href="javascript:void(0)" class="nav-link nav-icon-hover-bg rounded-circle mx-0 ms-n1 d-flex d-lg-none align-items-center justify-content-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobilenavbar" aria-controls="offcanvasWithBothOptions">
                  <i class="ti ti-align-justified fs-7"></i>
                </a>
                <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">
                  <li class="nav-item nav-icon-hover-bg rounded-circle">
                    <a class="nav-link moon dark-layout" href="javascript:void(0)">
                      <i class="ti ti-moon moon"></i>
                    </a>
                    <a class="nav-link sun light-layout" href="javascript:void(0)">
                      <i class="ti ti-sun sun"></i>
                    </a>
                  </li>
                  <li class="nav-item nav-icon-hover-bg rounded-circle dropdown">
                    <a class="nav-link" href="javascript:void(0)" id="drop2" aria-expanded="false">
                      <img src="<?php echo BASE_URL; ?>/public/assets/images/svgs/icon-flag-en.svg" alt="modernize-img" width="20px" height="20px" class="rounded-circle object-fit-cover round-20" />
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                      <div class="message-body">
                        <a href="javascript:void(0)" class="d-flex align-items-center gap-2 py-3 px-4 dropdown-item">
                          <div class="position-relative">
                            <img src="<?php echo BASE_URL; ?>/public/assets/images/svgs/icon-flag-en.svg" alt="modernize-img" width="20px" height="20px" class="rounded-circle object-fit-cover round-20" />
                          </div>
                          <p class="mb-0 fs-3">English (UK)</p>
                        </a>
                        <a href="javascript:void(0)" class="d-flex align-items-center gap-2 py-3 px-4 dropdown-item">
                          <div class="position-relative">
                            <img src="<?php echo BASE_URL; ?>/public/assets/images/svgs/icon-flag-cn.svg" alt="modernize-img" width="20px" height="20px" class="rounded-circle object-fit-cover round-20" />
                          </div>
                          <p class="mb-0 fs-3">中国人 (Chinese)</p>
                        </a>
                        <a href="javascript:void(0)" class="d-flex align-items-center gap-2 py-3 px-4 dropdown-item">
                          <div class="position-relative">
                            <img src="<?php echo BASE_URL; ?>/public/assets/images/svgs/icon-flag-fr.svg" alt="modernize-img" width="20px" height="20px" class="rounded-circle object-fit-cover round-20" />
                          </div>
                          <p class="mb-0 fs-3">français (French)</p>
                        </a>
                        <a href="javascript:void(0)" class="d-flex align-items-center gap-2 py-3 px-4 dropdown-item">
                          <div class="position-relative">
                            <img src="<?php echo BASE_URL; ?>/public/assets/images/svgs/icon-flag-sa.svg" alt="modernize-img" width="20px" height="20px" class="rounded-circle object-fit-cover round-20" />
                          </div>
                          <p class="mb-0 fs-3">عربي (Arabic)</p>
                        </a>
                      </div>
                    </div>
                  </li>
                  <li class="nav-item nav-icon-hover-bg rounded-circle">
                    <a class="nav-link position-relative" href="javascript:void(0)" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                      <i class="ti ti-basket"></i>
                      <span class="popup-badge rounded-pill bg-danger text-white fs-2">2</span>
                    </a>
                  </li>
                  <li class="nav-item nav-icon-hover-bg rounded-circle dropdown">
                    <a class="nav-link position-relative" href="javascript:void(0)" id="drop2" aria-expanded="false">
                      <i class="ti ti-bell-ringing"></i>
                      <div class="notification bg-primary rounded-circle"></div>
                    </a>
                    <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                      <div class="d-flex align-items-center justify-content-between py-3 px-7">
                        <h5 class="mb-0 fs-5 fw-semibold">Notifications</h5>
                        <span class="badge text-bg-primary rounded-4 px-3 py-1 lh-sm">5 new</span>
                      </div>
                      <div class="message-body" data-simplebar>
                        <a href="javascript:void(0)" class="py-6 px-7 d-flex align-items-center dropdown-item">
                          <span class="me-3">
                            <img src="<?php echo BASE_URL; ?>/public/assets/images/profile/user-2.jpg" alt="user" class="rounded-circle" width="48" height="48" />
                          </span>
                          <div class="w-100">
                            <h6 class="mb-1 fw-semibold lh-base">Roman Joined the Team!</h6>
                            <span class="fs-2 d-block text-body-secondary">Congratulate him</span>
                          </div>
                        </a>
                        <a href="javascript:void(0)" class="py-6 px-7 d-flex align-items-center dropdown-item">
                          <span class="me-3">
                            <img src="<?php echo BASE_URL; ?>/public/assets/images/profile/user-3.jpg" alt="user" class="rounded-circle" width="48" height="48" />
                          </span>
                          <div class="w-100">
                            <h6 class="mb-1 fw-semibold lh-base">New message</h6>
                            <span class="fs-2 d-block text-body-secondary">Salma sent you new message</span>
                          </div>
                        </a>
                        <a href="javascript:void(0)" class="py-6 px-7 d-flex align-items-center dropdown-item">
                          <span class="me-3">
                            <img src="<?php echo BASE_URL; ?>/public/assets/images/profile/user-4.jpg" alt="user" class="rounded-circle" width="48" height="48" />
                          </span>
                          <div class="w-100">
                            <h6 class="mb-1 fw-semibold lh-base">Bianca sent payment</h6>
                            <span class="fs-2 d-block text-body-secondary">Check your earnings</span>
                          </div>
                        </a>
                        <a href="javascript:void(0)" class="py-6 px-7 d-flex align-items-center dropdown-item">
                          <span class="me-3">
                            <img src="<?php echo BASE_URL; ?>/public/assets/images/profile/user-5.jpg" alt="user" class="rounded-circle" width="48" height="48" />
                          </span>
                          <div class="w-100">
                            <h6 class="mb-1 fw-semibold lh-base">Jolly completed tasks</h6>
                            <span class="fs-2 d-block text-body-secondary">Assign her new tasks</span>
                          </div>
                        </a>
                        <a href="javascript:void(0)" class="py-6 px-7 d-flex align-items-center dropdown-item">
                          <span class="me-3">
                            <img src="<?php echo BASE_URL; ?>/public/assets/images/profile/user-6.jpg" alt="user" class="rounded-circle" width="48" height="48" />
                          </span>
                          <div class="w-100">
                            <h6 class="mb-1 fw-semibold lh-base">John received payment</h6>
                            <span class="fs-2 d-block text-body-secondary">$230 deducted from account</span>
                          </div>
                        </a>
                        <a href="javascript:void(0)" class="py-6 px-7 d-flex align-items-center dropdown-item">
                          <span class="me-3">
                            <img src="<?php echo BASE_URL; ?>/public/assets/images/profile/user-7.jpg" alt="user" class="rounded-circle" width="48" height="48" />
                          </span>
                          <div class="w-100">
                            <h6 class="mb-1 fw-semibold lh-base">Roman Joined the Team!</h6>
                            <span class="fs-2 d-block text-body-secondary">Congratulate him</span>
                          </div>
                        </a>
                      </div>
                      <div class="py-6 px-7 mb-1">
                        <button class="btn btn-outline-primary w-100">See All Notifications</button>
                      </div>
                    </div>
                  </li>
                  <li class="nav-item dropdown">
                    <a class="nav-link pe-0" href="javascript:void(0)" id="drop1" aria-expanded="false">
                      <div class="d-flex align-items-center">
                        <div class="user-profile-img">
                          <img src="<?php echo BASE_URL; ?>/public/assets/images/profile/user-1.jpg" class="rounded-circle" width="35" height="35" alt="modernize-img" />
                        </div>
                      </div>
                    </a>
                    <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop1">
                      <div class="profile-dropdown position-relative" data-simplebar>
                        <div class="py-3 px-7 pb-0">
                          <h5 class="mb-0 fs-5 fw-semibold">User Profile</h5>
                        </div>
                        <div class="d-flex align-items-center py-9 mx-7 border-bottom">
                          <img src="<?php echo BASE_URL; ?>/public/assets/images/profile/user-1.jpg" class="rounded-circle" width="80" height="80" alt="modernize-img" />
                          <div class="ms-3">
                            <h5 class="mb-1 fs-3">Mathew Anderson</h5>
                            <span class="mb-1 d-block">Designer</span>
                            <p class="mb-0 d-flex align-items-center gap-2">
                              <i class="ti ti-mail fs-4"></i> info@modernize.com
                            </p>
                          </div>
                        </div>
                        <div class="message-body">
                          <a href="<?php echo BASE_URL; ?>/admin/user-profile" class="py-8 px-7 mt-8 d-flex align-items-center">
                            <span class="d-flex align-items-center justify-content-center text-bg-light rounded-1 p-6">
                              <img src="<?php echo BASE_URL; ?>/public/assets/images/svgs/icon-account.svg" alt="modernize-img" width="24" height="24" />
                            </span>
                            <div class="w-100 ps-3">
                              <h6 class="mb-1 fs-3 fw-semibold lh-base">My Profile</h6>
                              <span class="fs-2 d-block text-body-secondary">Account Settings</span>
                            </div>
                          </a>
                          <a href="<?php echo BASE_URL; ?>/admin/email" class="py-8 px-7 d-flex align-items-center">
                            <span class="d-flex align-items-center justify-content-center text-bg-light rounded-1 p-6">
                              <img src="<?php echo BASE_URL; ?>/public/assets/images/svgs/icon-inbox.svg" alt="modernize-img" width="24" height="24" />
                            </span>
                            <div class="w-100 ps-3">
                              <h6 class="mb-1 fs-3 fw-semibold lh-base">My Inbox</h6>
                              <span class="fs-2 d-block text-body-secondary">Messages & Emails</span>
                            </div>
                          </a>
                          <a href="<?php echo BASE_URL; ?>/admin/notes" class="py-8 px-7 d-flex align-items-center">
                            <span class="d-flex align-items-center justify-content-center text-bg-light rounded-1 p-6">
                              <img src="<?php echo BASE_URL; ?>/public/assets/images/svgs/icon-tasks.svg" alt="modernize-img" width="24" height="24" />
                            </span>
                            <div class="w-100 ps-3">
                              <h6 class="mb-1 fs-3 fw-semibold lh-base">My Task</h6>
                              <span class="fs-2 d-block text-body-secondary">To-do and Daily Tasks</span>
                            </div>
                          </a>
                        </div>
                        <div class="d-grid py-4 px-7 pt-8">
                          <div class="upgrade-plan bg-primary-subtle position-relative overflow-hidden rounded-4 p-4 mb-9">
                            <div class="row">
                              <div class="col-6">
                                <h5 class="fs-4 mb-3 fw-semibold">Unlimited Access</h5>
                                <button class="btn btn-primary">Upgrade</button>
                              </div>
                              <div class="col-6">
                                <div class="m-n4 unlimited-img">
                                  <img src="<?php echo BASE_URL; ?>/public/assets/images/backgrounds/unlimited-bg.png" alt="modernize-img" class="w-100" />
                                </div>
                              </div>
                            </div>
                          </div>
                          <a href="<?php echo BASE_URL; ?>/admin/authentication-login" class="btn btn-outline-primary">Log Out</a>
                        </div>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </nav>
          <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="mobilenavbar" aria-labelledby="offcanvasWithBothOptionsLabel">
            <nav class="sidebar-nav scroll-sidebar">
              <div class="offcanvas-header justify-content-between">
                <img src="<?php echo BASE_URL; ?>/public/assets/images/logos/favicon.ico" alt="modernize-img" class="img-fluid" />
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
              </div>
              <div class="offcanvas-body h-n80" data-simplebar="" data-simplebar>
                <ul id="sidebarnav">
                  <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Home</span>
                  </li>
                  <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo BASE_URL; ?>/admin/dashboard" id="get-url" aria-expanded="false">
                      <span>
                        <i class="ti ti-aperture"></i>
                      </span>
                      <span class="hide-menu">Dashboard</span>
                    </a>
                  </li>
                  <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo BASE_URL; ?>/admin/users" aria-expanded="false">
                      <span>
                        <i class="ti ti-users"></i>
                      </span>
                      <span class="hide-menu">Users</span>
                    </a>
                  </li>
                  <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo BASE_URL; ?>/admin/reports" aria-expanded="false">
                      <span>
                        <i class="ti ti-chart-line"></i>
                      </span>
                      <span class="hide-menu">Reports</span>
                    </a>
                  </li>
                  <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo BASE_URL; ?>/admin/settings" aria-expanded="false">
                      <span>
                        <i class="ti ti-settings"></i>
                      </span>
                      <span class="hide-menu">Settings</span>
                    </a>
                  </li>
                </ul>
              </div>
            </nav>
          </div>
        </div>
      </header>
      <div class="body-wrapper">
        <div class="container-fluid">
          <?php include $contentView; ?>
        </div>
      </div>
    </div>
  </div>
  <div class="dark-transparent sidebartoggler"></div>
  <div class="dark-transparent sidebartoggler"></div>
  <script src="<?php echo BASE_URL; ?>/public/assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="<?php echo BASE_URL; ?>/public/assets/libs/simplebar/dist/simplebar.min.js"></script>
  <script src="<?php echo BASE_URL; ?>/public/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo BASE_URL; ?>/public/assets/js/app.min.js"></script>
  <script src="<?php echo BASE_URL; ?>/public/assets/js/app.horizontal.init.js"></script>
  <script src="<?php echo BASE_URL; ?>/public/assets/js/app-style-switcher.js"></script>
  <script src="<?php echo BASE_URL; ?>/public/assets/js/sidebarmenu.js"></script>
  <script src="<?php echo BASE_URL; ?>/public/assets/js/custom.js"></script>
  <script src="<?php echo BASE_URL; ?>/public/assets/libs/owl.carousel/dist/owl.carousel.min.js"></script>
  <script src="<?php echo BASE_URL; ?>/public/assets/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="<?php echo BASE_URL; ?>/public/assets/js/dashboard.js"></script>
</body>

</html>