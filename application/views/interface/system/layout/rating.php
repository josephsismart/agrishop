<!-- <div class="modal fade show" id="modalRate" tabindex="-1" aria-labelledby="modalRateLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->

<div class="modal fade" id="modalRate">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Rate Transaction</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">

                <input type="hidden" id="rate_transaction_id">
                <input type="hidden" id="rating_value">

                <div id="starContainer" style="font-size:30px;">
                    <i class="fa fa-star star" data-value="1"></i>
                    <i class="fa fa-star star" data-value="2"></i>
                    <i class="fa fa-star star" data-value="3"></i>
                    <i class="fa fa-star star" data-value="4"></i>
                    <i class="fa fa-star star" data-value="5"></i>
                </div>

                <textarea id="rating_comment" class="form-control mt-3" placeholder="Write feedback (optional)"></textarea>

            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" id="submitRating">
                    Submit Rating
                </button>
            </div>

        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).on('click', '.star', function() {
        let value = $(this).data('value');
        $('#rating_value').val(value);

        $('.star').removeClass('text-warning');

        $('.star').each(function() {
            if ($(this).data('value') <= value) {
                $(this).addClass('text-warning');
            }
        });
    });


    function rateTransaction(transaction_id) {
        $('#rate_transaction_id').val(transaction_id);
        $('#rating_value').val(0);
        $('#rating_comment').val('');

        $('.star').removeClass('text-warning');

        $('#modalRate').modal('show');
    }

    $('#submitRating').click(function() {

        let transaction_id = $('#rate_transaction_id').val();
        let rating_value = $('#rating_value').val();
        let comment = $('#rating_comment').val();

        if (rating_value == 0) {
            alert('Please select rating');
            return;
        }

        $.ajax({
            url: "<?= base_url('save-rating') ?>",
            type: "POST",
            data: {
                transaction_id: transaction_id,
                rating_value: rating_value,
                review_comment: comment
            },
            success: function(res) {

                $('#rateModal').modal('hide');

                // reload datatable / listing
                if (typeof table !== "undefined") {
                    table.ajax.reload(null, false);
                }
                successAlert("Thank you!");

                $('#modalRate').modal('hide');
                getTable('RateOrderListing', 0, 5);

            }
        });

    });
</script>