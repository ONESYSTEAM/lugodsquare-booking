$(document).ready(function () {

    let isMember = false;
    const discountRate = 0.10;

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

    $('.pin-input').on('input', function () {
        let $this = $(this);
        let value = $this.val();

        if (value.length === 1) {
            $this.next('.pin-input').focus();
        }

        let pin = '';
        $('.pin-input').each(function () {
            pin += $(this).val();
        });

        if (pin.length === 4) {
            verifyPin(pin);
        }
    });

    $('.pin-input').on('keydown', function (e) {
        if (e.key === 'Backspace' && $(this).val() === '') {
            $(this).prev('.pin-input').focus();
        }
    });

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
                    $('#pinSection').addClass('d-none');
                    $('#memberStatus').text('Membership ID Verified. Enjoy your 10% Discount!')
                        .removeClass('text-danger text-muted')
                        .addClass('text-success');
                    $('#checkWallet').removeClass('d-none');
                    $('#firstName').val(response.firstName);
                    $('#lastName').val(response.lastName);
                    $('#email').val(response.email);
                    $('#contactNum').val(response.contactNum);
                    $('#walletText').val(response.wallet).attr('readonly', true);
                    $('#walletBalance').val(response.wallet);
                    $('#walletMemberName').text(response.firstName + ' ' + response.lastName);
                    $('#walletBalanceAmount').text(parseFloat(response.wallet).toFixed(2));
                    $('#sendCodeBtn').text('Verified').removeClass('btn-outline-danger').addClass('btn-danger').prop('disabled', true);
                    $('#firstName, #lastName, #email, #contactNum, #membershipId')
                        .attr('readonly', true);

                    const banner = $('#memberBanner');
                    if (banner.is(':visible')) {
                        banner.fadeOut(600, function () {
                            $(this).remove();
                            localStorage.setItem('bannerClosed', 'true');
                        });
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Membership Verified!',
                        text: 'Your discount will be applied automatically.',
                        timer: 5000,
                        showConfirmButton: false
                    });

                    $('#firstName, #lastName, #email, #contactNum').trigger('input');

                } else {
                    $('#pinStatus').text('Incorrect PIN ❌')
                        .removeClass('text-success')
                        .addClass('text-danger');
                    $('.pin-input').val('');
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

    const today = new Date().toISOString().split("T")[0];
    $("#date").attr("min", today);
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
                            timer: 1000,
                            willClose: () => {
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

    $('#court').on('change', function () {
        const courtType = $(this).val();

        if (courtType) {
            $('#capacity, #amount').val('Loading...');
            $.ajax({
                url: '/get-court-details',
                method: 'POST',
                data: { court_type: courtType },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        $('#capacity').val(response.data.capacity);
                        $('#amount').val(response.data.amount);
                        $('#capacity, #amount')
                            .attr('readonly', true).trigger('input');
                    } else {
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
            $('#capacity, #amount').val('');
        }
    });

    $('#court, #date').on('change', function () {
        const court = $('#court').val();
        const date = $('#date').val();

        if (!court || !date) return;

        $.ajax({
            url: '/get-booked-slots',
            method: 'POST',
            dataType: 'json',
            data: { court, date },
            success: function (response) {
                $('#startTime option, #endTime option').prop('disabled', false).removeClass('text-danger');
                console.log(response.bookedSlots);
                const allDayStart = "07:00";
                const allDayEnd = "17:00";

                function timeNum(t) {
                    return parseInt(t.replace(":", ""), 10);
                }

                const slots = (response.bookedSlots ?? []).map(s => ({
                    start: timeNum(s.start_time),
                    end: timeNum(s.end_time)
                })).sort((a, b) => a.start - b.start);

                let currentStart = timeNum(allDayStart);
                let fullyBooked = false;

                for (let i = 0; i < slots.length; i++) {
                    if (slots[i].start > currentStart) {
                        fullyBooked = false;
                        break;
                    }
                    if (slots[i].end > currentStart) {
                        currentStart = slots[i].end;
                    }
                    if (currentStart >= timeNum(allDayEnd)) {
                        fullyBooked = true;
                        break;
                    }
                }
                if (fullyBooked) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Date Fully Booked',
                        text: 'All time slots for this date are fully booked. Please select another date.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        $('#date').val('');
                        $('#startTime').val('');
                        $('#endTime').val('');
                    });
                } else {
                    if (response.bookedSlots && response.bookedSlots.length > 0) {
                        response.bookedSlots.forEach(slot => {
                            const start = slot.start_time;
                            const end = slot.end_time;

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
                $('#startTime option, #endTime option').each(function () {
                    $(this).prop('disabled', false).removeClass('text-danger');
                });
            }
        });
    });

    function checkRequiredFields() {
        const requiredFields = [
            '#firstName',
            '#lastName',
            '#contactNum',
            '#email',
            '#court',
            '#capacity',
            '#amount'
        ];

        let allFilled = true;
        requiredFields.forEach(function (selector) {
            if ($(selector).val().trim() === '') {
                allFilled = false;
            }
        });

        $('#date, #startTime, #endTime, #total').prop('disabled', !allFilled);
    }

    checkRequiredFields();

    $('#firstName, #lastName, #contactNum, #email, #court, #capacity, #amount').on('input change', function () {
        checkRequiredFields();
    });

    $('form[action="/booking"]').on('submit', function (e) {
        e.preventDefault();

        const contactNum = $('#contactNum').val().trim();
        const phPattern = /^(09\d{9}|\+639\d{9})$/;

        if (!phPattern.test(contactNum)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Contact Number',
                text: 'Please enter a valid Philippine number starting with 09 or +63 and exactly 11 digits.',
            });
            return;
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
                        $('#total').val(response.subtotal);
                        $('#walletText').val(response.wallet_balance);
                    }
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while calculating the deduction. Please try again.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    });
});