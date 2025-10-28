<?php
$this->layout('Layout', ['mainContent' => $this->fetch('Layout')]);
$this->start('mainContent');
?>

<!-- Add your content here to be displayed in the browser -->
<main>
    <section class="hero" style="padding: 0 !important;">
        <div class="container d-flex justify-content-center align-items-center flex-column">
            <div class="search-form mt-2 mx-2">
                <div class="row">
                    <div class="col-lg-6 hero-text d-none d-md-flex justify-content-center flex-column px-5 pt-5 text-md-center text-lg-start">
                        <h1>Be a member and avail exclusive discounts!</h1>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et
                            dolore magna aliqua. Browse thousands of verified listings from trusted agents.</p>
                    </div>
                    <div class="col-lg-6">
                        <hr class="d-block d-lg-none mx-2 mb-5">
                        <h2 class="text-center mb-5 fw-bold">Registration Form</h2>
                        <form action="/membership-pin" method="POST">
                            <div class="row g-3 mx-2 mb-2">
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
                                <div class="col-8 col-md-9">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" id="email" name="email" required="">
                                        <label for="email">Email</label>
                                        <div id="email-status" class="text-danger mt-1"></div>
                                    </div>
                                </div>
                                <div class="col-4 col-md-3 d-flex justify-content-center align-items-center" id="sendCodeSection">
                                    <button class="btn btn-sm btn-outline-success" type="button" id="sendCodeBtn">Send Code</button>
                                </div>
                                <div id="codeSection" class="text-center">
                                    <label class="fw-bold mb-2">Enter 6-Digit Code</label>
                                    <div class="d-flex justify-content-center gap-2 mb-3 px-2 px-md-5">
                                        <input type="password" maxlength="1" class="form-control pin-input text-center pin-code ms-md-5" required>
                                        <input type="password" maxlength="1" class="form-control pin-input text-center pin-code" required>
                                        <input type="password" maxlength="1" class="form-control pin-input text-center pin-code" required>
                                        <input type="password" maxlength="1" class="form-control pin-input text-center pin-code" required>
                                        <input type="password" maxlength="1" class="form-control pin-input text-center pin-code" required>
                                        <input type="password" maxlength="1" class="form-control pin-input text-center pin-code me-md-5" required>
                                    </div>
                                    <input type="hidden" id="code" placeholder="Enter verification code">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-search w-100">
                                        Register Account
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </section><!-- /Hero Section -->
</main>



<?php
$this->stop();
?>