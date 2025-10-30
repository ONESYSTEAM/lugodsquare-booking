$(document).ready(function () {

    let isMember = false;
    const discountRate = 0.10;
    // When membership ID is complete (14 digits)
    $('#membershipId').on('input', function () {
        const idValue = $(this).val().trim();

        if (idValue.length === 14) {
            $('#memberStatus').text('Checking membership...')
                .removeClass('text-danger text-success')
                .addClass('text-muted');

            $.ajax({
                url: '/check-membership',
                method: 'POST',
                data: {
                    membership_id: idValue
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        $('#memberStatus')
                            .text('Membership ID found. Please enter your PIN.')
                            .removeClass('text-danger text-muted')
                            .addClass('text-success');
                        $('#pinSection').removeClass('d-none');
                        $('#pin1').focus();
                    } else {
                        $('#memberStatus')
                            .text('Invalid or unregistered ID ❌')
                            .removeClass('text-success text-muted')
                            .addClass('text-danger');
                        $('#pinSection').addClass('d-none');
                    }
                },
                error: function () {
                    $('#memberStatus').text('Server error. Please try again later.')
                        .removeClass('text-success')
                        .addClass('text-danger');
                }
            });
        } else {
            $('#memberStatus').text('');
            $('#pinSection').addClass('d-none');
        }
    });

    // Handle PIN input movement
    $('.pin-input').on('input', function () {
        let $this = $(this);
        let value = $this.val();

        // Move to next box if one digit is entered
        if (value.length === 1) {
            $this.next('.pin-input').focus();
        }

        // When all boxes are filled, verify automatically
        let pin = '';
        $('.pin-input').each(function () {
            pin += $(this).val();
        });

        if (pin.length === 4) {
            verifyPin(pin);
        }
    });

    // Handle backspace navigation between boxes
    $('.pin-input').on('keydown', function (e) {
        if (e.key === 'Backspace' && $(this).val() === '') {
            $(this).prev('.pin-input').focus();
        }
    });

    // Function: verify PIN via AJAX
    function verifyPin(pin) {
        $.ajax({
            url: '/verify-pin',
            method: 'POST',
            data: {
                membership_id: $('#membershipId').val(),
                pin: pin
            },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    isMember = true;

                    $('#pinStatus').text('PIN verified ✅')
                        .removeClass('text-danger')
                        .addClass('text-success');

                    // Hide PIN section
                    $('#pinSection').addClass('d-none');
                    // Update member status
                    $('#memberStatus').text('Membership ID Verified. Enjoy your 10% Discount!')
                        .removeClass('text-danger text-muted')
                        .addClass('text-success');
                    // Show Wallet Check button
                    $('#checkWallet').removeClass('d-none');
                    // Auto-fill fields
                    $('#firstName').val(response.firstName);
                    $('#lastName').val(response.lastName);
                    $('#email').val(response.email);
                    $('#contactNum').val(response.contactNum);
                    $('#walletText').val(parseFloat(response.wallet).toFixed(2)).attr('readonly', true);
                    $('#walletBalance').val(response.wallet);
                    $('#walletMemberName').text(response.firstName + ' ' + response.lastName);
                    $('#walletBalanceAmount').text(parseFloat(response.wallet).toFixed(2));
                    $('#sendCodeBtn').text('Verified').removeClass('btn-outline-danger').addClass('btn-danger').prop('disabled', true);

                    // Disable editing (lock fields)
                    $('#firstName, #lastName, #email, #contactNum, #membershipId')
                        .attr('readonly', true);

                    // 🌟 Hide the discount banner when membership is verified
                    const banner = $('#memberBanner');
                    if (banner.is(':visible')) {
                        banner.fadeOut(600, function () {
                            $(this).remove();
                            localStorage.setItem('bannerClosed', 'true');
                        });
                    }

                    // Optional feedback (requires SweetAlert2)
                    Swal.fire({
                        icon: 'success',
                        title: 'Membership Verified!',
                        text: 'Your discount will be applied automatically.',
                        timer: 5000,
                        showConfirmButton: false
                    });

                } else {
                    $('#pinStatus').text('Incorrect PIN ❌')
                        .removeClass('text-success')
                        .addClass('text-danger');
                    $('.pin-input').val(''); // clear inputs
                    $('#pin1').focus();
                }
            },
            error: function () {
                $('#pinStatus').text('Error verifying PIN. Please try again.')
                    .removeClass('text-success')
                    .addClass('text-danger');
            }
        });
    }

    // Set min date to today
    const today = new Date().toISOString().split("T")[0];
    $("#date").attr("min", today);

    // Trigger when start or end time changes
    $('#startTime, #endTime').on('change', function () {
        const startTime = $('#startTime').val();
        const endTime = $('#endTime').val();
        const rate = parseFloat($('#amount').val());

        if (startTime && endTime && rate) {
            $.ajax({
                url: '/calculate-total',
                method: 'POST',
                dataType: 'json',
                data: {
                    start_time: startTime,
                    end_time: endTime,
                    rate: rate,
                    isMember: isMember,
                    discountRate: discountRate
                },
                success: function (response) {
                    if (response.status === 'success') {
                        $('#total').val(parseFloat(response.total).toFixed(2)).attr('readonly', true);

                        let loadingMsg = isMember ? "Applying membership discount..." : "Calculating total...";
                        Swal.fire({
                            title: loadingMsg,
                            didOpen: () => Swal.showLoading(),
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            timer: 1000, // 1 second delay
                            willClose: () => {
                                // Apply discount after "loading"
                                if (isMember) {
                                    $('#subtotalSection').removeClass('d-none');
                                    $('#subtotalText').text(`₱${response.subtotal}`);
                                    $('#discountText').text(`-₱${response.discount}`);
                                    $('#subTotal').val(response.total);
                                    Swal.fire({
                                        icon: "success",
                                        title: "Discount Applied!",
                                        text: `You saved ₱${(parseFloat(response.subtotal.toString().replace(/,/g, '')) * discountRate)} as a member.`,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });

                                } else {
                                    $('#subtotalSection').addClass('d-none');
                                    Swal.fire({
                                        icon: "success",
                                        title: "Total Calculated",
                                        text: `Total: ₱${response.total}`,
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                }
                            }
                        });
                    } else {
                        $('#total').val('');
                        Swal.fire({
                            icon: 'warning',
                            text: response.message || 'Invalid time selection.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        text: 'Error calculating total amount.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }
    });


    // When a court is selected
    $('#court').on('change', function () {
        const courtType = $(this).val();

        if (courtType) {
            // Show loading indicator (optional)
            $('#capacity, #amount').val('Loading...');

            $.ajax({
                url: '/get-court-details', // 🔹 Your backend route
                method: 'POST',
                data: { court_type: courtType },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        // Fill input fields with data from backend
                        $('#capacity').val(response.data.capacity);
                        $('#amount').val(response.data.amount);

                        // Disable editing (lock fields)
                        $('#capacity, #amount')
                            .attr('readonly', true);
                    } else {
                        // Handle not found
                        $('#capacity, #amount').val('');
                        alert('Court details not found.');
                    }
                },
                error: function () {
                    $('#capacity, #amount').val('');
                    alert('Error fetching court details.');
                }
            });
        } else {
            // Clear fields when no court selected
            $('#capacity, #amount').val('');
        }
    });

    function checkAvailability() {
        const court = $('#court').val();
        const date = $('#date').val();

        if (!court || !date) return;

        $.ajax({
            url: '/get-booked-slots',
            method: 'POST',
            dataType: 'json',
            data: { court, date },
            success: function (response) {
                // Re-enable all options first
                $('#startTime option, #endTime option').prop('disabled', false).removeClass('text-danger');
                // --- FULLY BOOKED DETECTION ---
                const allDayStart = '07:00:00';
                const allDayEnd = '17:00:00';

                // Check if all booked slots cover the full 7 AM – 5 PM range
                const isFullyBooked = response.bookedSlots.some(slot =>
                    slot.start_time >= allDayStart && slot.end_time <= allDayEnd
                );

                if (isFullyBooked) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Date Fully Booked',
                        text: 'All time slots for this date are fully booked. Please select another date.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Clear and reset date input
                        $('#date').val('');
                        // Also clear times just in case
                        $('#startTime').val('');
                        $('#endTime').val('');
                    });
                } else {
                    if (response.bookedSlots && response.bookedSlots.length > 0) {
                        response.bookedSlots.forEach(slot => {
                            const start = slot.start_time;
                            const end = slot.end_time;

                            // Disable start times that fall within booked ranges
                            $('#startTime option, #endTime option').each(function () {
                                const val = $(this).val();
                                if (val >= start && val < end) {
                                    $(this).prop('disabled', true).addClass('text-danger');
                                }
                            });
                        });

                        Swal.fire({
                            icon: 'info',
                            title: 'Some time slots are unavailable',
                            text: 'Unavailable times are shown in red and cannot be selected.',
                            timer: 5000,
                            showConfirmButton: false
                        });
                    }
                }

            },
            error: function () {
                Swal.fire({
                    icon: 'success',
                    title: 'All time slots are available!',
                    text: 'You can freely choose any time for this court and date.',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    }

    // Trigger check when court or date is changed
    $('#court, #date').on('change', checkAvailability);

    // Function to check if all required fields in Customer + Court sections are filled
    function checkRequiredFields() {
        const requiredFields = [
            '#firstName',
            '#lastName',
            '#contactNum',
            '#email'
        ];

        let allFilled = true;
        requiredFields.forEach(function (selector) {
            if ($(selector).val().trim() === '') {
                allFilled = false;
            }
        });

        // Enable or disable booking schedule fields
        $('#date, #startTime, #endTime, #total,#court, #capacity, #amount').prop('disabled', !allFilled);
    }

    // Check fields on page load
    checkRequiredFields();

    // Check again whenever an input changes
    $('#firstName, #lastName, #contactNum, #email, #court, #capacity, #amount').on('input change', function () {
        checkRequiredFields();
    });


    $('form[action="/booking"]').on('submit', function (e) {
        e.preventDefault();

        const contactNum = $('#contactNum').val().trim();

        // Philippine number validation: 11 digits starting with 09 or +63
        const phPattern = /^(09\d{9}|\+639\d{9})$/;

        if (!phPattern.test(contactNum)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Contact Number',
                text: 'Please enter a valid Philippine number starting with 09 or +63 and exactly 11 digits.',
            });
            return; // stop submission
        }

        const formData = $(this).serialize();

        Swal.fire({
            title: 'Processing your booking...',
            text: 'Please wait while we confirm your slot.',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: '/booking',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                Swal.close();

                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Booking Confirmed!',
                        text: 'Your court has been successfully booked. Please check your email for confirmation details.',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location.href = '/';
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Booking Failed',
                        text: response.message || 'There was a problem with your booking. Please try again.'
                    });
                }
            },
            error: function (xhr) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'Something went wrong while processing your booking.'
                });
                console.error(xhr.responseText);
            }
        });
    });

    // Format number to ₱x,xxx.xx
    function formatCurrency(n) {
        return '₱' + Number(n).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Parse text (e.g. "₱3,000.00") to number
    function parseCurrency(text) {
        if (text === undefined || text === null) return 0;
        const cleaned = String(text).replace(/[^0-9.-]+/g, '');
        const n = parseFloat(cleaned);
        return isNaN(n) ? 0 : n;
    }


    $('#useWallet').on('change', function () {
        const isChecked = $(this).is(':checked');

        $.ajax({
            url: '/calculateDeduction',
            method: 'POST',
            dataType: 'json',
            data: {
                wallet: $('#walletBalance').val(),
                total: $('#subTotal').val()
            },
            success: function (response) {
                if (response.status === 'success') {
                    if (isChecked) {
                        Swal.fire({
                            icon: response.icon,
                            title: response.title,
                            text: response.noBalanceMessage ? response.noBalanceMessage : 'Your wallet balance has been applied to the total amount.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        if (response.zero_balance) {
                            $('#useWallet').prop('checked', false);
                            return;
                        }
                        $('#total').val(response.deducted_amount);
                        $('#walletText').val(parseFloat(response.new_wallet_balance).toFixed(2));
                    } else if (!isChecked) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Wallet Deduction Removed',
                            text: 'Your wallet balance deduction has been removed.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        // Unchecked → revert to original values
                        $('#total').val(response.subtotal);
                        $('#walletText').val(response.wallet_balance);
                    }
                }
            }
        });
    });
});