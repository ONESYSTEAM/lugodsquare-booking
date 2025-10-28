<?php
$this->layout('Layout', ['mainContent' => $this->fetch('Layout')]);
$this->start('mainContent');
?>

<!-- Add your content here to be displayed in the browser -->
<main>
  <section class="hero">
    <div class="container d-flex justify-content-center align-items-center">
      <div class="row justify-content-center">
        <div class="col-md-9">
          <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body text-center p-5">
              <h1 class="mb-3 fw-bold">Congratulations!</h1>
              <h6 class="mb-4">You're now an official member, <strong><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></strong>!</h6>

              <div class="alert alert-primary fw-bold">
                Your Membership ID: <span class="text-dark"><?php echo htmlspecialchars($_SESSION['membership_id']); ?></span>
              </div>

              <p class="text-secondary mb-4">Keep this ID for future court bookings to avail exclusive discounts!</p>

              <div class="d-flex gap-2">
                <a href="/remove-session " class="btn btn-confirm flex-fill">Go to Homepage</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

</main>


<?php
$this->stop();
?>