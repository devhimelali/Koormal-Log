<div id="moveWorkOrderModal" data-bs-backdrop="static" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog">
        <form id="moveWorkOrderForm">
            @csrf
            <input type="hidden" name="wo_number" id="wo_number">
            <input type="hidden" name="from_date" id="from_date">
            <input type="hidden" name="from_shift" id="from_shift">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Move Work Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="form-step step-1">
                        <label class="form-label">Why do you want to move Work Order <span id="workorder_number_display"></span>?</label>
                        <textarea class="form-control" name="reason" id="reason" rows="5" required></textarea>
                    </div>

                    <div class="form-step step-2 d-none">
                        <label class="form-label">Select new date:</label>
                        <input type="text" class="form-control" name="to_date" id="to_date" required>
                    </div>

                    <div class="form-step step-3 d-none">
                        <label class="form-label d-block mb-2 font-weight-bold">Select Shift:</label>
                        <div class="btn-group" data-toggle="buttons" id="shiftSelector">
                            <label class="btn btn-outline-primary">
                                <input type="radio" name="to_shift" value="Day" autocomplete="off" required>
                                <span class="mr-1">🌞</span> Day
                            </label>
                            <label class="btn btn-outline-secondary ml-2">
                                <input type="radio" name="to_shift" value="Night" autocomplete="off">
                                <span class="mr-1">🌙</span> Night
                            </label>
                        </div>
                    </div>




                    <div class="form-step step-4 d-none">
                        <p id="confirmation_text" class="text-danger fw-bold fs-6"></p>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary d-none" id="prevStep">Previous</button>
                    <button type="button" class="btn btn-primary" id="nextStep">Next</button>
                    <button type="submit" class="btn btn-success d-none" id="submitMove">Yes, Move</button>
                </div>
            </div>
        </form>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->