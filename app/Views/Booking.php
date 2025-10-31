<?php
$this->layout('Layout', ['mainContent' => $this->fetch('Layout')]);
$this->start('mainContent');
$this->insert('Errors/Toasts');
?>

<?php if (!empty($_SESSION['booking_success'])): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Booking Confirmed!',
            text: 'Your court has been successfully booked. 🏸',
            timer: 2500,
            showConfirmButton: false
        });
    </script>
    <?php unset($_SESSION['booking_success']);
    ?>
<?php endif; ?>

<div class="textured-bg">
    <header id="header" class="header d-flex align-items-center fixed-top">
        <div
            class="header-container container-fluid container-xl position-relative d-flex align-items-center justify-content-between">
            <a href="/" class="logo d-flex align-items-center me-auto me-xl-0">
                <!-- <img src="img/logo.webp" alt=""> -->
                <h1 class="sitename"><?= $_ENV['APP_NAME'] ?? '' ?></h1>
            </a>
            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Merchandise</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>
            <a class="btn-getstarted bg-danger" href="/membership-registration">Join Now</a>
        </div>
    </header>
    <section id="hero" class="hero section">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-text" data-aos="fade-right" data-aos-delay="200">
                    <h1 class="warband-stencil-textured" style="font-size: clamp(48px, 6.5vw, 96px); line-height: 1.05;">Smash the Hassle <br> Booking Made Easy!</h1>
                    <p>Take the stress out of scheduling. Our platform connects you with verified courts and trusted facilities, ensuring a seamless booking experience every time. <br> Whether you&rsquo;re training, competing, or hosting, you&rsquo;ll enjoy secure reservations, real-time availability, and reliable service — all in one place.</p>
                    <a href="#booking-form">
                        <div class="hero-badge bg-danger">
                            <i class="bi bi-star-fill"></i>
                            <span>Book Now</span>
                        </div>
                    </a>
                    <img src="<?= assets('img/assets/honeycomb-pattern.webp') ?>" class="d-lg-block d-none" style="position:absolute" alt="honeycomb-pattern.webp">
                </div>
                <div class="col-lg-6 hero-images mt-2" data-aos="fade-left" data-aos-delay="400">
                    <div class="image-stack">
                        <div class="main-image">
                            <img src="<?= assets('img/assets/pickleball.webp') ?>" alt="Luxury Property" class="img-fluid">
                            <div class="property-tag">
                                <span class="price">Pickleball Court</span>
                                <span class="type">Featured</span>
                            </div>
                        </div>
                        <div class="floating-card">
                            <div class="agent-info">
                                <img src="img/real-estate/agent-4.webp" alt="Agent" class="agent-avatar">
                                <div class="agent-details">
                                    <h5>Lugod Square</h5>
                                    <p>Barangay 16, Gingoog City</p>
                                    <div class="rating">
                                        <a href="https://maps.app.goo.gl/PVKim6ow4Lt7Emp79" class="text-danger" target="_blank"> <i class="bi bi-geo-alt text-danger"></i> Visit Us</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="main bg-dark py-5">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row align-items-center">
            <h2 class="text-center m-3 fw-bold text-light" id="booking-form">Booking Details</h2>
            <div class="col-lg-12 hero-text" data-aos="fade-up" data-aos-delay="200">
                <div class="search-form mt-2" data-aos="fade-up" data-aos-delay="300">
                    <div id="memberBanner" class="alert alert-info alert-dismissible fade show py-2 px-3 mb-3" role="alert">
                        🎉 <strong>Become a member</strong> today and enjoy <strong>10% off</strong> all court bookings!
                        <a href="/membership-registration" class="text-success text-decoration-underline">Join now</a>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="margin-top: -8px;"></button>
                    </div>
                    <form action="/booking" method="POST">
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <div class="row g-3">
                                    <h6 class="text-light text-center">Customer Details</h6>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="membershipId" name="membershipId">
                                            <label for="membershipId">Membership ID (Optional)</label>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div id="memberStatus" class="form-text text-muted mt-1"></div>
                                                <a class="btn btn-sm btn-link text-warning mt-1 d-none" id="checkWallet" data-bs-toggle="modal" data-bs-target="#exampleModal">Check Wallet</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="pinSection" class="mt-3 text-center d-none">
                                        <label class="form-label fw-bold mb-2 text-light">Enter 4-Digit PIN</label>
                                        <div class="row justify-content-center">
                                            <div class="col-9 col-md-4 d-flex justify-content-center gap-2 px-4 px-md-0">
                                                <input type="text" maxlength="1" class="pin-input form-control text-center" id="pin1">
                                                <input type="text" maxlength="1" class="pin-input form-control text-center" id="pin2">
                                                <input type="text" maxlength="1" class="pin-input form-control text-center" id="pin3">
                                                <input type="text" maxlength="1" class="pin-input form-control text-center" id="pin4">
                                            </div>
                                        </div>
                                        <div id="pinStatus" class="mt-2 text-center small text-muted"></div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="firstName" name="firstName" required="">
                                            <label for="firstName">First Name</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="lastName" name="lastName" required="">
                                            <label for="lastName">Last Name</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="contactNum" name="contactNum" required="">
                                            <label for="contactNum">Contact Number</label>
                                            <div id="feedback" class="text-danger"></div>
                                        </div>
                                    </div>
                                    <div class="col-8 col-md-10">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" id="email" name="email" required="">
                                            <label for="email">Email</label>
                                        </div>
                                    </div>
                                    <div class="col-4 col-md-2 d-flex justify-content-center align-items-center" id="sendCodeSection">
                                        <button class="btn btn-sm btn-outline-danger" type="button" id="sendCodeBtn">Send Code</button>
                                    </div>
                                    <div id="codeSection" class="text-center d-none ">
                                        <label class="fw-bold mb-2">Enter 6-Digit Code</label>
                                        <div class="row justify-content-center">
                                            <div class=" col-md-6 d-flex justify-content-center gap-2 mb-3 px-3 px-md-0">
                                                <input type="password" maxlength="1" class="form-control pin-input text-center pin-code">
                                                <input type="password" maxlength="1" class="form-control pin-input text-center pin-code">
                                                <input type="password" maxlength="1" class="form-control pin-input text-center pin-code">
                                                <input type="password" maxlength="1" class="form-control pin-input text-center pin-code">
                                                <input type="password" maxlength="1" class="form-control pin-input text-center pin-code">
                                                <input type="password" maxlength="1" class="form-control pin-input text-center pin-code">
                                            </div>
                                        </div>
                                        <input type="hidden" id="code" placeholder="Enter verification code">
                                    </div>

                                    <h6 class="text-light text-center">Court Details</h6>
                                    <div class="col-md-12">
                                        <div class="form-floating">
                                            <select class="form-select" id="court" name="courtType" required="">
                                                <option value="" hidden></option>
                                                <?php foreach ($courts as $court): ?>
                                                    <option value="<?= $court['id'] ?>"><?= $court['court_type'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label for="court">Court</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="capacity" id="capacity">
                                            <label for="capacity">Capacity</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="amount" id="amount">
                                            <label for="amount">Amount</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="row g-3">

                                    <h6 class="text-light text-center">Booking Schedule</h6>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="date" class="form-control" id="date" name="date" required="">
                                            <label for="date">Date</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <select class="form-select" id="startTime" name="startTime" required="">
                                                <option value="" hidden></option>
                                                <option value="07:00">7:00 AM</option>
                                                <option value="08:00">8:00 AM</option>
                                                <option value="09:00">9:00 AM</option>
                                                <option value="10:00">10:00 AM</option>
                                                <option value="11:00">11:00 AM</option>
                                                <option value="12:00">12:00 PM</option>
                                                <option value="13:00">1:00 PM</option>
                                                <option value="14:00">2:00 PM</option>
                                                <option value="15:00">3:00 PM</option>
                                                <option value="16:00">4:00 PM</option>
                                                <option value="17:00">5:00 PM</option>
                                            </select>
                                            <label for="startTime">Start Time</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <select class="form-select" id="endTime" name="endTime" required="">
                                                <option value="" hidden></option>
                                                <option value="08:00">8:00 AM</option>
                                                <option value="09:00">9:00 AM</option>
                                                <option value="10:00">10:00 AM</option>
                                                <option value="11:00">11:00 AM</option>
                                                <option value="12:00">12:00 PM</option>
                                                <option value="13:00">1:00 PM</option>
                                                <option value="14:00">2:00 PM</option>
                                                <option value="15:00">3:00 PM</option>
                                                <option value="16:00">4:00 PM</option>
                                                <option value="17:00">5:00 PM</option>
                                            </select>
                                            <label for="endTime">End Time</label>
                                        </div>
                                    </div>
                                    <div id="subtotalSection" class="d-none mt-2 text-light">
                                        <div>Subtotal: <span id="subtotalText">₱0.00</span></div>
                                        <div>Membership Discount: <span id="discountText">₱0.00</span></div>
                                        <hr>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="useWallet">
                                            <label class="form-check-label" for="useWallet">
                                                Use Wallet Balance
                                            </label>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="walletText" name="walletBalance">
                                                <label for="total">Wallet Balance</label>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="walletBalance">
                                    <input type="hidden" name="subTotal" id="subTotal">
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="total" name="total" required="">
                                            <label for="total">Total Amount</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-danger w-100">
                                            Book Court
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<footer id="footer" class="footer position-relative bg-danger">
    <div class="footer-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="copyright">
                        <p>&copy; <?= date("Y") ?> <strong>Lugod Square</strong>. <span>All rights reserved.</span></p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="credits">
                        Powered by: <a href="https://onesysteam.com/" target="_blank">OneSysteam</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Wallet Balance</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="p-3 bg-light rounded">
                    <h5 class="mb-3">Hello, <span id="walletMemberName"></span>!</h5>
                    <p>Your current wallet balance is:</p>
                    <h3 class="text-danger">₱<span id="walletBalanceAmount">0.00</span></h3>
                    <hr>
                    <p>You can use your wallet balance to pay for your bookings and enjoy exclusive discounts as a valued member.</p>
                    <div class="alert alert-info" role="alert">
                        Note: If your wallet balance covers the total booking amount, no additional payment will be required.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center bg-dark"><i class="bi bi-arrow-up-short"></i></a>

<?php $this->stop() ?>