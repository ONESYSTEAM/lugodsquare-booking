<?php
$this->layout('Layout', ['mainContent' => $this->fetch('Layout')]);
$this->start('mainContent');
$this->insert('Errors/Toasts');

if(!isset($_SESSION['membership_id'])) {
    header("Location:/membership-registration");
    exit;
}
?>

<section class="hero bg-dark" style="padding: 0 !important;">
    <div class="container d-flex justify-content-center align-items-center flex-column">
        <div class="row justify-content-center p-3">
            <div class="col-md-9 p-4 text-center">
                <h1 class="fw-bold text-light">Welcome!</h1>
                <p class="text-light">Your Membership ID:</p>
                <h4 class="fw-bold text-danger"><?= htmlspecialchars($_SESSION['membership_id']); ?></h4>
                <hr>
                <p class="text-light">Create a 4-digit PIN to secure your membership.</p>

                <form method="POST" id="pinForm" action="/membership-pin/set">
                    <label class="fw-bold mb-2 text-light">Enter PIN</label>
                    <div class="row justify-content-center">
                        <div class="col-md-6 d-flex justify-content-center gap-2 mb-3 px-5 mx-md-5">
                            <input type="text" maxlength="1" class="form-control pin-input text-center pin-input-main ms-md-5" required>
                            <input type="text" maxlength="1" class="form-control pin-input text-center pin-input-main" required>
                            <input type="text" maxlength="1" class="form-control pin-input text-center pin-input-main" required>
                            <input type="text" maxlength="1" class="form-control pin-input text-center pin-input-main me-md-5" required>
                        </div>
                    </div>
                    <div id="confirmPinSection">
                        <label class="fw-bold mb-2">Confirm PIN</label>
                        <div class="row justify-content-center">
                            <div class="col-md-6 d-flex justify-content-center gap-2 mb-3 px-5 mx-md-5">
                                <input type="text" maxlength="1" class="form-control pin-input text-center pin-input-confirm ms-md-5" required>
                                <input type="text" maxlength="1" class="form-control pin-input text-center pin-input-confirm" required>
                                <input type="text" maxlength="1" class="form-control pin-input text-center pin-input-confirm" required>
                                <input type="text" maxlength="1" class="form-control pin-input text-center pin-input-confirm me-md-5" required>
                            </div>
                        </div>
                        <div id="ConfirmpinStatus" class="mb-2 text-center small"></div>
                    </div>
                    <input type="hidden" name="pin" id="pinValue">
                    <input type="hidden" name="confirmPin" id="confirmSetPinValue">
                    <button type="submit" class="btn btn-danger w-100" name="submitPinBtn">Save PIN</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php
$this->stop();
?>