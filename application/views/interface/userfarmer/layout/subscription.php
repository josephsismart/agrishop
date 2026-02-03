<!-- <div class="modal fade show" id="modalFreeTrial" tabindex="-1" aria-labelledby="gcashModalLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
<div class="modal fade" id="modalFreeTrial" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-4 overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header bg-success text-white justify-content-center">
                <h4 class="modal-title fw-bold text-center mb-0">
                    🎉 Congratulations, Farmer!
                </h4>
            </div>

            <!-- BODY -->
            <div class="modal-body text-center px-4 py-4">

                <h3 class="text-success fw-bold mb-3">
                    🌱 FREE 2-MONTH PRO SUBSCRIPTION
                </h3>

                <p class="fs-5 mb-4">
                    Welcome to <strong>AgriShop</strong>! <br>
                    Enjoy <strong>FULL ACCESS</strong> for <strong>2 months</strong>.
                </p>

                <!-- FEATURES -->
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="p-3 border rounded bg-light text-start">
                            <ul class="list-unstyled mb-0">
                                <li>✅ Unlimited Farms</li>
                                <li>✅ Unlimited Produce</li>
                                <li>⭐ Priority in Search</li>
                                <li>🚜 More Customer Visibility</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <p class="text-muted fs-6 mt-4">
                    ⏰ After 2 months, your farms remain safe, but management features will be limited.
                </p>

                <p class="fs-5 text-success fw-bold mt-3 mb-0">
                    🎊 Welcome to PRO Access!
                </p>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer justify-content-center">
                <button
                    type="button"
                    class="btn btn-success btn-lg px-5 fw-bold start-selling"
                    data-dismiss="modal">
                    Start Selling 🌾
                </button>
            </div>

        </div>
    </div>
</div>






<!-- <div class="modal fade show" id="modalRenewSub" tabindex="-1" aria-labelledby="gcashModalLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->

<div class="modal fade" id="modalRenewSub" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-4">

            <!-- HEADER -->
            <div class="modal-header bg-warning text-dark text-center">
                <h4 class="modal-title w-100 fw-bold">
                    🔔 Subscription Expired
                </h4>
            </div>

            <!-- BODY -->
            <div class="modal-body text-center px-4 py-4">

                <h3 class="fw-bold text-success mb-3">
                    🌱 Continue Selling Without Limits
                </h3>

                <p class="fs-5">
                    Your farms and produce are <b>SAFE</b>.
                    To manage and add more, renew your subscription.
                </p>

                <div class="bg-light border rounded p-4 my-4">
                    <h2 class="text-success fw-bold">
                        ₱99 <small class="fs-5">/ month</small>
                    </h2>
                    <p class="fs-6 text-muted mb-0">
                        Less than ₱4 per day
                    </p>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-2 fs-5">✅ Unlimited Farms</div>
                    <div class="col-md-6 mb-2 fs-5">✅ Unlimited Produce</div>
                    <div class="col-md-6 mb-2 fs-5">⭐ Priority Search</div>
                    <div class="col-md-6 mb-2 fs-5">📈 More Sales</div>
                </div>

                <p class="text-muted mt-3 fs-6">
                    No contracts. Cancel anytime.
                </p>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer justify-content-center gap-2">
                <button class="btn btn-success btn-lg px-5 fw-bold" style="font-size:1.1rem !important;">
                    Subscribe Now 🌾
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    function fireConfetti() {
        confetti({
            particleCount: 160,
            spread: 100,
            origin: {
                y: 0.6
            },
            zIndex: 99999
        });
    }

    $('#modalFreeTrial').on('shown.bs.modal', function() {
        setTimeout(fireConfetti, 300);
    });

    let agrishop_login_sub_free_confirmed = "<?= $this->session->agrishop_login_sub_free_confirmed ?>";
    if (agrishop_login_sub_free_confirmed == 'f') {
        $('#modalFreeTrial').modal('show');
    }

    let agrishop_login_sub_free_expired = "<?= $this->session->agrishop_login_sub_free_expired ?>";
    if (agrishop_login_sub_free_expired == 't') {
        $('#modalRenewSub').modal('show');
    }

    // $('#modalFreeTrial').modal('show');

    $(".start-selling").click(function() {
        $.post("<?= base_url('userfarmer/FarmProduce/confirmFreeTrial') ?>", {
            confirm: true
        }, function(res) {
            let j = JSON.parse(res);
            if (j.success == true) {
                successAlert(j.message);
            } else {
                errorAlert(j.message);
            }
        });
    });
</script>