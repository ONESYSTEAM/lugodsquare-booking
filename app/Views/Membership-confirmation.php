<?php
$this->layout('Layout', ['mainContent' => $this->fetch('Layout')]);
$this->start('mainContent');
$this->insert('Errors/Toasts');

if (!isset($_SESSION['membership_id'])) {
	header("Location:/membership-registration");
	exit;
}
?>

<section class="hero bg-dark" style="padding: 0 !important;">
	<div class="container d-flex justify-content-center align-items-center flex-column">
		<div class="row justify-content-center p-3">
			<div class="col-md-9 p-4 text-center">
				<h1 class="fw-bold text-light">Congratulations!</h1>
				<p class="text-light">You're now an official member, <br> <strong><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></strong>!</p>
				<div class="alert alert-danger fw-bold">
					Your Membership ID: <br> <span class="text-dark"><?php echo htmlspecialchars($_SESSION['membership_id']); ?></span>
				</div>
				<p class="text-light mb-4">Keep this ID for future court bookings to avail exclusive discounts! To get your physical ID, drop by our office and bring a small fee for processing.</p>
				<div class="d-flex gap-2">
					<a href="/end-session " class="btn btn-danger flex-fill">Go to Homepage</a>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
$this->stop();
?>