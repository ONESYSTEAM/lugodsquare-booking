<?php
$this->layout('Layout', ['mainContent' => $this->fetch('Layout')]);
$this->start('mainContent');
$this->insert('Errors/Toasts');
?>

<section class="hero bg-dark" style="padding: 0 !important;">
    <div class="container d-flex justify-content-center align-items-center flex-column">
        <div class="mt-2 mx-2">
            <div class="row">
                <div class="col-lg-6 hero-text justify-content-center align-items-center d-flex flex-column px-5 pt-5 text-md-center text-lg-start">
                    <h1 class="warband-stencil-textured text-light" style="font-size: clamp(48px, 6.5vw, 96px); line-height: 1.05;">Join the Club, Enjoy the Perks!</h1>
                    <p class="text-light">Skip the waiting line and start playing sooner! Become a member and score exclusive discounts on every booking. Whether it&rsquo;s badminton, tennis, or pickleball — we&rsquo;ve got your spot ready.</p>
                </div>
                <div class="col-lg-6 mb-5">
                    <hr class="d-block d-lg-none mx-2 mb-5">
                    <h2 class="text-center mb-5 fw-bold text-light">Registration Form</h2>
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
                                    <input type="date" class="form-control" id="birthDate" name="birthDate" required="">
                                    <label for="birthDate">Birth Date</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="address" name="address" required="">
                                    <label for="address">Address</label>
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
                                <button class="btn btn-sm btn-outline-danger" type="button" id="sendCodeBtn">Send Code</button>
                            </div>
                            <div id="codeSection" class="text-center d-none">
                                <label class="fw-bold mb-2 text-light">Enter 6-Digit Code</label>
                                <div class="d-flex justify-content-center gap-2 mb-3 px-2 px-md-5">
                                    <input type="text" maxlength="1" class="form-control pin-input text-center pin-code ms-md-5" required>
                                    <input type="text" maxlength="1" class="form-control pin-input text-center pin-code" required>
                                    <input type="text" maxlength="1" class="form-control pin-input text-center pin-code" required>
                                    <input type="text" maxlength="1" class="form-control pin-input text-center pin-code" required>
                                    <input type="text" maxlength="1" class="form-control pin-input text-center pin-code" required>
                                    <input type="text" maxlength="1" class="form-control pin-input text-center pin-code me-md-5" required>
                                </div>
                                <input type="hidden" id="code" placeholder="Enter verification code">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-danger w-100">Register Account</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$this->stop();
?>