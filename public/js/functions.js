function combinePIN(className, hiddenInputId) {
    let pin = '';
    $(className).each(function () {
        pin += $(this).val();
    });
    $(hiddenInputId).val(pin);
}

$('.pin-input-main').on('input', function () {
    if (this.value.length === 1) {
        $(this).next('.pin-input-main').focus();
    }

    combinePIN('.pin-input-main', '#pinValue');

    if ($('#pinValue').val().length === 4) {
        $('#confirmPinSection').fadeIn();
        $('.pin-input-confirm').first().focus();
    }
});

$('.pin-input-main').on('keydown', function (e) {
    if (e.key === 'Backspace' && $(this).val() === '') {
        $(this).prev('.pin-input-main').focus();
    }
});

$('.pin-input-confirm').on('input', function () {
    if (this.value.length === 1) {
        $(this).next('.pin-input-confirm').focus();
    }
    combinePIN('.pin-input-confirm', '#confirmSetPinValue');
});

$('.pin-input-confirm').on('keydown', function (e) {
    if (e.key === 'Backspace' && $(this).val() === '') {
        $(this).prev('.pin-input-confirm').focus();
    }
});

$('#pinForm').on('submit', function (e) {
    const pin = $('#pinValue').val();
    const confirmPin = $('#confirmSetPinValue').val();

    if (pin.length < 4 || confirmPin.length < 4) {
        alert('Please enter all 4 digits for both PIN fields.');
        e.preventDefault();
    } else if (pin !== confirmPin) {
        $('#ConfirmpinStatus').text("Pins don't match").addClass('text-danger');
        e.preventDefault();
    }
});

$(document).ready(function () {

    $('#sendCodeBtn').on('click', function () {
        const email = $('#email').val().trim();

        if (!email) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Email',
                text: 'Please enter your email address first.'
            });
            return;
        }

        $.ajax({
            url: '/verify-email',
            type: 'POST',
            data: {
                email: email
            },
            dataType: 'json',
            beforeSend: function () {
                Swal.fire({
                    title: 'Sending Code...',
                    text: 'Please wait while we send a verification code to your email.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            },
            success: function (data) {
                Swal.close();
                Swal.fire({
                    icon: data.valid ? 'success' : 'error',
                    title: data.valid ? 'Code Sent!' : 'Failed to Send',
                    text: data.message
                });

                if (data.valid) {
                    $('#codeSection').removeClass('d-none');
                    $('#sendCodeBtn').prop('disabled', true).text('Code Sent').removeClass('btn-outline-success').addClass('btn-success');
                }
            },
            error: function () {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong while sending the verification code.'
                });
            }
        });
    });

    $('.pin-code').on('input', function () {
        if (this.value.length === 1) {
            $(this).next('.pin-code').focus();
        }

        combinePIN('.pin-code', '#code');

        const email = $('#email').val().trim();
        const code = $('#code').val().trim();

        if (code.length === 6) {
            if (!email || !code) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please enter both your email and verification code.'
                });
                return;
            }

            $.ajax({
                url: '/confirm-code',
                type: 'POST',
                data: { email, code },
                dataType: 'json',
                beforeSend: function () {
                    Swal.fire({
                        title: 'Verifying...',
                        text: 'Checking your verification code.',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: function (data) {
                    Swal.close();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Verified!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        $('form').attr('data-email-verified', 'true');
                        $('#email, #code').prop('readonly', true);
                        $('#sendCodeBtn').prop('disabled', true).text('Verified');
                        $('#sendCodeBtn').addClass('btn btn-sm btn-outline-danger');
                        $('#codeSection').addClass('d-none');
                        $('#email-status').text('✅ Email verified successfully!')
                            .removeClass('text-danger')
                            .addClass('text-success');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Verification Failed',
                            text: data.message || 'Invalid or expired code.'
                        });
                        $('form').attr('data-email-verified', 'false');
                    }
                },
                error: function (xhr, status, error) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong while verifying your code.'
                    });
                }
            });
        }
    });

    $('.pin-code').on('keydown', function (e) {
        if (e.key === 'Backspace' && $(this).val() === '') {
            $(this).prev('.pin-code').focus();
        }
    });

    $('form[action="/add-membership"]').on('submit', function (e) {
        if ($(this).attr('data-email-verified') !== 'true') {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Email Not Verified',
                text: 'Please verify your email before submitting the form.'
            });
        }
    });

    $('#contactNum').on('input', function () {
        let mobile = $(this).val().trim();
        let feedback = $('#feedback');

        let phPattern = /^(09\d{9}|\+639\d{9})$/;

        if (mobile.length >= 2 && mobile.length < 4) {
            if (mobile.startsWith("09") || mobile.startsWith("+63")) {
                feedback.addClass('d-none').removeClass('d-block');
            } else {
                feedback.text("Invalid number.").addClass('d-block').removeClass('d-none');
            }
            return;
        }

        if (mobile.length < 11) {
            feedback.text("");
            return;
        }

        if (!phPattern.test(mobile)) {
            feedback.text("Invalid number.").addClass('d-block').removeClass('d-none');
        } else {
            feedback.addClass('d-none').removeClass('d-block');
        }
    });

    $('form[action="/membership-pin"]').on('submit', function (e) {
        e.preventDefault();

        let contactNum = $('#contactNum').val().trim();
        let phPattern = /^(09\d{9}|\+639\d{9})$/;

        if (!phPattern.test(contactNum)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Number',
                text: 'Contact number must start with 09 or +63 and be 11 digits.'
            });
            return;
        }

        this.submit();
    });
});



