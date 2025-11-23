
<div class="modal fade" id="graphPresentation">
  <div class="modal-dialog modal-xl">
    <div class="modal-content bg-dark">
      <div class="modal-header">
        <h4 class="modal-title"><i class="fa fa-chart-bar"></i> Graphical Presentation</h4>
        <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body pb-0" style="overflow: auto;">
        <div id="myContact" style="min-width:100%; height: auto;"></div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="statusHistory">
  <div class="modal-dialog modal-sm">
    <?= form_open(base_url('usermanagement/Dataentry/saveDataStatusHistory'), 'id=form_save_dataStatusHistory'); ?>
      <div class="modal-content bg-default">
        <div class="modal-header">
          <h6 class="modal-title"><i class="fa fa-viruses"></i> Status History</h6>
          <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body pb-0" style="overflow: auto;">
          
            <input class="personStatusId" name="personStatusId" hidden value="1">
                
            <div class="row">
              <div class="col-md-12">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <span class="input-group-text text-xs"><i class="fas fa-user"></i></span>
                    </div>
                    <input class="form-control form-control-sm personStatusName" type="text" readonly id="personStatusName">
                </div>
              </div>
              <div class="col-md-12">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-virus"></i></span>
                    </div>
                    <select class="form-control form-control-sm" name="statusCOVID">
                      <?php foreach ($getStatus['data1'] as $key => $value): ?>
                          <option value='<?= $value['id'] ?>'><?= $value['text'] ?></option>
                      <?php endforeach ?>
                    </select>
                </div>
              </div>
              <div class="col-md-12">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                    </div>
                    <input type="date" value="<?= Date('Y-m-d') ?>" class="form-control form-control-sm" name="dateEntried">
                </div>
              </div>
            </div>

            <table id="tbl_person_covid19_status" style="width:100%;" class="table table-bordered table-hover">
              <thead>
              <tr>
                <th>#</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
              </thead>
              <tbody>
              </tbody>
            </table>

        </div>
        <div class="modal-footer justify-content-between">
          <button type="submit" class="btn btn-info btn-sm submitBtnStatusHistory"><i class="fa fa-check"></i> Save Data</button>
          <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
        </div>
      </div>
    </form>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="testHistory">
  <div class="modal-dialog modal-md">
    <?= form_open(base_url('usermanagement/Dataentry/saveDataTestHistory'), 'id=form_save_dataTestHistory'); ?>
      <div class="modal-content bg-default">
        <div class="modal-header">
          <h6 class="modal-title"><i class="fa fa-vials"></i> Test History</h6>
          <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body pb-0" style="overflow: auto;">
          
            <input class="personTestId" name="personTestId" hidden value="1">
                
            <div class="row">
              <div class="col-md-12">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <span class="input-group-text text-xs"><i class="fas fa-user"></i></span>
                    </div>
                    <input class="form-control form-control-sm personTestName" type="text" readonly id="personTestName">
                </div>
              </div>
              <div class="col-md-12">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-vial"></i></span>
                    </div>
                    <select class="form-control form-control-sm" name="testCOVID">
                      <?php foreach ($getStatus['data4'] as $key => $value): ?>
                          <option value='<?= $value['id'] ?>'><?= $value['text'] ?></option>
                      <?php endforeach ?>
                    </select>
                </div>
              </div>
              <div class="col-md-12">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-file-alt"></i></span>
                    </div>
                    <select class="form-control form-control-sm" name="resultCOVID">
                      <?php foreach ($getStatus['data6'] as $key => $value): ?>
                          <option value='<?= $value['id'] ?>'><?= $value['text'] ?></option>
                      <?php endforeach ?>
                    </select>
                </div>
              </div>
              <div class="col-md-12">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                    </div>
                    <input type="date" value="<?= Date('Y-m-d') ?>" class="form-control form-control-sm" name="dateEntried">
                </div>
              </div>
            </div>

            <table id="tbl_person_covid19_test" style="width:100%;" class="table table-bordered table-hover">
              <thead>
              <tr>
                <th>#</th>
                <th>Test</th>
                <th>Result</th>
                <th>Date</th>
              </tr>
              </thead>
              <tbody>
              </tbody>
            </table>

        </div>
        <div class="modal-footer justify-content-between">
          <button type="submit" class="btn btn-info btn-sm submitBtnTestHistory"><i class="fa fa-check"></i> Save Data</button>
          <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
        </div>
      </div>
    </form>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="graphAnalytics">
  <div class="modal-dialog modal-xl">
    <div class="modal-content bg-dark">
      <div class="modal-header">
        <h4 class="modal-title"><i class="fa fa-chart-bar"></i> Graphical Presentation</h4>
        <button type="button" class="close text-danger" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body pb-0">
        <div id="chart5" style="min-width: 100%; height: auto;margin:-15px;"></div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>