<?php
$this->layout('Layout', ['mainContent' => $this->fetch('Layout')]);
$this->start('mainContent');
?>

<!-- Add your content here to be displayed in the browser -->

<main>
    <section class="hero">
        <div class="container d-flex justify-content-center align-items-center flex-column">
            <div class="row justify-content-center p-3">
                <div class="col-md-9 p-4 shadow-sm text-center search-form ">
                    <h1 class="fw-bold">Welcome!</h1    >
                    <p>Your Membership ID:</p>
                    <h4 class="fw-bold"><?= htmlspecialchars($_SESSION['membership_id']); ?></h4>
                    <hr>
                    <p>Create a 4-digit PIN to secure your membership.</p>

                    <form method="POST" id="pinForm" action="/member">
                        <!-- PIN -->
                        <label class="fw-bold mb-2">Enter PIN</label>
                        <div class="d-flex justify-content-center gap-2 mb-3 px-5 mx-md-5">
                            <input type="password" maxlength="1" class="form-control pin-input text-center pin-input-main ms-md-5" required>
                            <input type="password" maxlength="1" class="form-control pin-input text-center pin-input-main" required>
                            <input type="password" maxlength="1" class="form-control pin-input text-center pin-input-main" required>
                            <input type="password" maxlength="1" class="form-control pin-input text-center pin-input-main me-md-5" required>
                        </div>

                        <!-- Confirm PIN -->
                        <div id="confirmPinSection">
                            <label class="fw-bold mb-2">Confirm PIN</label>
                            <div class="d-flex justify-content-center gap-2 mb-3 px-5 mx-md-5">
                                <input type="password" maxlength="1" class="form-control pin-input text-center pin-input-confirm ms-md-5" required>
                                <input type="password" maxlength="1" class="form-control pin-input text-center pin-input-confirm" required>
                                <input type="password" maxlength="1" class="form-control pin-input text-center pin-input-confirm" required>
                                <input type="password" maxlength="1" class="form-control pin-input text-center pin-input-confirm me-md-5" required>
                            </div>
                            <div id="ConfirmpinStatus" class="mb-2 text-center small"></div>
                        </div>

                        <input type="hidden" name="pin" id="pinValue">
                        <input type="hidden" name="confirmPin" id="confirmSetPinValue">

                        <button type="submit" class="btn btn-search w-100">Save PIN</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
$this->stop();
?>