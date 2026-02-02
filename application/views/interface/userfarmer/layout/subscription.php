<!-- <div class="modal fade show" id="modalFreeTrial" tabindex="-1" aria-labelledby="gcashModalLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
<div class="modal fade" id="modalFreeTrial" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-4">

            <!-- HEADER -->
            <div class="modal-header bg-success text-white text-center">
                <h4 class="modal-title w-100 fw-bold">
                    🎉 Congratulations, Farmer!
                </h4>
            </div>

            <!-- BODY -->
            <div class="modal-body text-center px-4 py-4">

                <h3 class="text-success fw-bold mb-3">
                    🌱 FREE 2-MONTH PRO SUBSCRIPTION
                </h3>
                <p class="fs-5">
                    Welcome to <strong>AgriShop</strong>!
                    You can now enjoy <strong>FULL ACCESS</strong> for <b>2 months</b>.
                </p>

                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light">
                            ✅ Unlimited Farms<br>
                            ✅ Unlimited Produce
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light">
                            ⭐ Priority in Search<br>
                            🚜 More Customer Visibility
                        </div>
                    </div>
                </div>

                <p class="text-muted fs-6 mt-3">
                    ⏰ After 2 months, your farms are safe but management will be limited.
                </p>


                <p class="fs-5 text-success fw-bold">
                    🎊 Welcome to PRO Access!
                </p>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer justify-content-center">
                <button class="btn btn-success btn-lg px-4 py-0 fw-bold" data-dismiss="modal" style="font-size:1.1rem !important;">
                    Start Selling 🌾
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


    // $('#modalFreeTrial').modal('show');
</script>





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
                <button class="btn btn-outline-secondary btn-lg" data-dismiss="modal">
                    Later
                </button>
                <button class="btn btn-success btn-lg px-5 fw-bold">
                    Subscribe Now 🌾
                </button>
            </div>

        </div>
    </div>
</div>