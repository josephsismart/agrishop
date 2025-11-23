<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script src="<?= base_url() ?>assets/plugins/jquery/jquery.form.min.js"></script>
<?php 
    if (!$this->session->schoolmis_login_level) {
        redirect(base_url('login'));
    }
    $uri = 'userteacher'; //$this->session->schoolmis_login_uri;
 ?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mt-2">
      <div class="col-sm-6">
        <h1><i class="nav-icon fas fa-cog fa-spin data_excel"></i> Controller</h1>
      </div>
      <!-- <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Advanced Form</li>
        </ol>
      </div> -->
    </div>
  </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 form_save_dataVariantCategory">
        <div class="row">
          <div class="col-12">
            <?= form_open(base_url($uri.'/Datacontroller/saveVariantCategory'), 'id=form_save_dataVariantCategory'); ?>
              <input type="" name="typeId" hidden>
              <div class="card card-navy">
                <div class="card-header">
                  <h3 class="card-title"><i class="fa fa-cog"></i> Rice Variety & Category</h3>

                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    <!-- <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button> -->
                  </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-2" style="overflow: auto;">
                  <div class="row">
                    <div class="col-lg-5 col-md-12 col-sm-12">
                        <div class="input-group mb-2">
                          <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-angle-double-down"></i></span>
                          </div>
                          <select class="form-control" name="itemType">
                            <option value="1">VARIETY</option>
                            <option value="2">CATEGORY</option>
                            <option value="3">SUBCATEGORY</option>
                          </select>
                      </div>
                    </div>
                    <div class="col-lg-7 col-md-12 col-sm-12">
                        <div class="input-group mb-2">
                          <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-font"></i></span>
                          </div>
                          <input type="text" class="form-control text-uppercase" name="description" placeholder="DESCRIPTION" autocomplete="off">
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-info submitBtnPrimary">Save Data</button>
                  <button type="button" class="btn btn-default float-right" onclick="clear_form('form_save_dataVariantCategory')"><i class="fa fa-times"></i> Cancel</button>
                </div>
              </div>
              <!-- /.card -->
            </form>
          </div>
          
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><i class="fa fa-list"></i> Rice Variety & Category List</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                  <!-- <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button> -->
                </div>
              </div>
              <div class="card-body p-2" style="overflow: auto;">
                <div class="table-responsive mailbox-messages">
                  <form>
                    <table id="tblVariantCategory" style="width:100%;" class="table table-bordered table-hover table-data-checkbox">
                      <thead>
                      <tr>
                        <th width="1">#</th>
                        <th><i class='fa fa-angle-double-down'></i> Type & Description</th>
                      </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-8 col-md-6 col-sm-12 col-xs-12 form_save_dataMemberUser">
        <div class="row">
          <div class="col-12">
            <?= form_open(base_url($uri.'/Datacontroller/saveMemberUser'), 'id=form_save_dataMemberUser'); ?>
              <input type="" name="personId" hidden>
              <div class="card card-navy">
                <div class="card-header">
                  <h3 class="card-title"><i class="fa fa-user"></i> Person Member & User</h3>

                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    <!-- <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button> -->
                  </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-2" style="overflow: auto;">
                  <div class="row">
                    <div class="col-lg-2 col-md-12 col-sm-12">
                        <div class="input-group mb-2">
                          <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-angle-double-down"></i></span>
                          </div>
                          <select class="form-control" name="partyType">
                            <option value="2">MEMBER</option>
                            <option value="1">USER</option>
                          </select>
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-12 col-sm-12">
                        <div class="input-group mb-2">
                          <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                          </div>
                        <input type="text" class="form-control text-uppercase" name="firstName" placeholder="FIRST NAME" autocomplete="off">
                      </div>
                    </div>
                    <div class="col-lg-1 col-md-12 col-sm-12 mb-2">
                        <input type="text" class="form-control text-uppercase" name="middleName" placeholder="M.I." autocomplete="off" nr="1">
                    </div>
                    <div class="col-lg-4 col-md-12 col-sm-12 mb-2">
                        <input type="text" class="form-control text-uppercase" name="lastName" placeholder="LAST NAME" autocomplete="off">
                    </div>
                    <div class="col-lg-1 col-md-12 col-sm-12 mb-2">
                        <input type="text" class="form-control text-uppercase" name="extName" placeholder="EXTN" autocomplete="off" nr="1">
                    </div>
                    <div class="col-lg-2 col-md-12 col-sm-12">
                      <div class="input-group mb-2">
                        <div class="input-group-prepend">
                          <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                        </div>
                        <select class="form-control" name="sex">
                          <option value="1">MALE</option>
                          <option value="0">FEMALE</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-lg-10 col-md-12 col-sm-12">
                        <div class="input-group mb-2">
                          <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-home"></i></span>
                          </div>
                          <input type="text" class="form-control text-uppercase" name="homeAddress" placeholder="ADDRESS" autocomplete="off">
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-info submitBtnPrimary">Save Data</button>
                  <button type="button" class="btn btn-default float-right" onclick="clear_form('form_save_dataMemberUser')"><i class="fa fa-times"></i> Cancel</button>
                </div>
              </div>
              <!-- /.card -->
            </form>
          </div>
          
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title"><i class="fa fa-list"></i> Member & User List</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                  <!-- <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button> -->
                </div>
              </div>
              <div class="card-body p-2" style="overflow: auto;">
                <div class="table-responsive mailbox-messages">
                  <form>
                    <table id="tblMemberUser" style="width:100%;" class="table table-bordered table-hover table-data-checkbox">
                      <thead>
                      <tr>
                        <th width="1">#</th>
                        <th width="80"><i class='fa fa-angle-double-down'></i> Type</th>
                        <th><i class='fa fa-user'></i> Person Details</th>
                      </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6">
          <div class="table-responsive">
          </div>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<script type="text/javascript">
  $(function(){
    getTable("VariantCategory");
    getTable("MemberUser");

    saveForm("VariantCategory",["VariantCategory"],null);
    saveForm("MemberUser",["MemberUser"],null);
  });
</script>