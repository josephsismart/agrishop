<!-- Modal -->
<!-- <div class="modal fade show" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:0;">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="profileModalLabel">My Profile</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url() ?>requestprofile" method="post">
                    <div class="card-body">
                        <h6><i class="fas fa-camera-retro"></i> Photo</h6>
                        <div class="col-xl-2 col-md-12">
                            <center class="mt-3">
                                <div class="form-group">
                                    <img name="previewPic" src="<?= base_url() ?>dist/img/media/icons/1x1.png" onclick="$('[name=pic]').trigger('click')" width="120" height="120" class="border border-white border-2 rounded elevation-2" type="button" alt="User Image">
                                </div>
                                <div class="form-group">
                                    <input name="pic" type="file" accept="image/*" onchange="imageView('pic','previewPic','imgtargetLink')" nr="1" hidden="">
                                    <!-- <input name="personId" type="text" nr="1" > -->
                                    <input name="img_path" type="text" nr="1" hidden="">
                                </div>
                            </center>
                        </div>
                        <div class="row">
                            <h6><i class="fas fa-user"></i> Name</h6>
                            <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                <input type="text" class="form-control form-control-sm text-uppercase" name="firstName" placeholder="FIRST NAME" autocomplete="off">
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-6 mb-2">
                                <input type="text" class="form-control form-control-sm text-uppercase" name="middleName" placeholder="MIDDLE NAME" autocomplete="off" nr="1">
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6 col-6 mb-2">
                                <input type="text" class="form-control form-control-sm text-uppercase" name="lastName" placeholder="LAST NAME" autocomplete="off">
                            </div>
                            <div class="col-lg-1 col-md-6 col-sm-6 col-6 mb-2">
                                <input type="text" class="form-control form-control-sm text-uppercase" name="extName" placeholder="EXTN" autocomplete="off" nr="1">
                            </div>
                            <div class="col-6">
                                <h6 class="mt-3"><i class="fas fa-birthday-cake"></i> Birthdate</h6>
                                <div class="col-12">
                                    <input type="date" class="form-control form-control-sm" name="birthdate" nr="1">
                                </div>
                            </div>
                            <div class="col-6">
                                <h6 class="mt-3"><i class="fas fa-venus-mars"></i> Sex</h6>
                                <div class="col-12">
                                    <select class="form-control form-control-sm" name="sex">
                                        <option value="t">MALE</option>
                                        <option value="f">FEMALE</option>
                                    </select>
                                </div>
                            </div>

                            <h6 class="mt-3"> <i class="fas fa-map-marker-alt"></i> Address</h6>
                            <div class="col-lg-4 col-md-6 col-sm-6 col-6" data-select2-id="151">
                                <div class="input-group mb-2" data-select2-id="150">
                                    <!-- <div class="input-group-prepend">
														<small class="input-group-text text-xs text-bold p-1">CITY</small>
													</div> -->
                                    <select class="form-control selectCityMunList select2-hidden-accessible" style="width:100%" onchange="getLocation(['CityMunList','BarangayList'],
																														['BarangayList','PurokList'],'PersonnelInfo')" type="select" name="cty" data-select2-id="52" tabindex="-1" aria-hidden="true">
                                        <option value="160201" data-select2-id="54">BUTUAN CITY (Capital)</option>
                                        <option value="160202" data-select2-id="163">BUENAVISTA</option>
                                        <option value="160203" data-select2-id="164">CITY OF CABADBARAN</option>
                                        <option value="160204" data-select2-id="165">CARMEN</option>
                                        <option value="160205" data-select2-id="166">JABONGA</option>
                                        <option value="160206" data-select2-id="167">KITCHARAO</option>
                                        <option value="160207" data-select2-id="168">LAS NIEVES</option>
                                        <option value="160208" data-select2-id="169">MAGALLANES</option>
                                        <option value="160209" data-select2-id="170">NASIPIT</option>
                                        <option value="160210" data-select2-id="171">SANTIAGO</option>
                                        <option value="160211" data-select2-id="172">TUBAY</option>
                                        <option value="160212" data-select2-id="173">REMEDIOS T. ROMUALDEZ</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                                <div class="input-group mb-2">
                                    <!-- <div class="input-group-prepend">
														<small class="input-group-text text-xs text-bold p-1">BRGY</small>
													</div> -->
                                    <select class="form-control selectBarangayList select2-hidden-accessible" style="width:100%" onchange="getLocation(['BarangayList'],['PurokList'],'PersonnelInfo')" type="select" name="brgy" tabindex="-1" aria-hidden="true" data-select2-id="145">
                                        <option value="160202002" data-select2-id="147">AGAO</option>
                                        <option value="160202003">AGUSAN PEQUEÑO</option>
                                        <option value="160202004" data-select2-id="148">AMBAGO</option>
                                        <option value="160202006">AMPARO</option>
                                        <option value="160202007">AMPAYON</option>
                                        <option value="160202008">ANTICALA</option>
                                        <option value="160202009">ANTONGALON</option>
                                        <option value="160202010">AUPAGAN</option>
                                        <option value="160202012">BAAN KM 3</option>
                                        <option value="160202033">BAAN RIVERSIDE</option>
                                        <option value="160202013">BABAG</option>
                                        <option value="160202014">BADING</option>
                                        <option value="160202016">BANCASI</option>
                                        <option value="160202089">URDUJA</option>
                                        <option value="160202090">VILLAKANANGA</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn bg-warning text-black"> <i class="fas fa-save"></i> Update Profile</button>
            </div>
        </div>
    </div>
</div>

<!-- <div class="modal fade show" id="registerFarmerModal" tabindex="-1" aria-labelledby="registerFarmerModalLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
<div class="modal fade" id="registerFarmerModal" tabindex="-1" aria-labelledby="registerFarmerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:0;">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="registerFarmerModalLabel">Register as Farmer</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- <form action="<?= base_url() ?>requestprofile" method="post"> -->
            <?= form_open(base_url('/registerfarmer'), 'id=form_save_dataRegisterFarmer'); ?>
            <div class="modal-body">
                <div class="card-body">
                    <div class="row">
                        <h6><i class="fas fa-address-card"></i> Identification</h6>
                        <div class="col-12">
                            <select class="form-control form-control-sm" name="sex">
                                <option value="t">PLEASE SELECT ID TO BE PRESENTED</option>
                                <option value="f">SSS</option>
                                <option value="f">TIN</option>
                                <option value="f">PAG-IBIG</option>
                                <option value="f">PHILHEALTH</option>
                            </select>
                            <div class="col-12">
                                <center class="mt-3">
                                    <div class="form-group">
                                        <img name="previewPic" src="<?= base_url() ?>dist/img/media/icons/1x1.png" onclick="$('[name=pic]').trigger('click')" width="120" height="120" class="border border-white border-2 rounded elevation-2" type="button" alt="User Image">
                                    </div>
                                    <div class="form-group">
                                        <input name="pic" type="file" accept="image/*" onchange="imageView('pic','previewPic','imgtargetLink')" nr="1" hidden="">
                                        <input name="img_path" type="text" nr="1" hidden="">
                                    </div>
                                    <label class="form-label text-xs"><i>ID PICTURE</i></label>
                                </center>
                            </div>
                        </div>
                        <div class="col-12">
                            <h6 class="mt-3"><i class="fas fa-sitemap"></i> Organization Membership</h6>
                            <div class="col-12">
                                <select class="form-control form-control-sm" name="sex">
                                    <option>PLEASE SELECT ORGANIZATION MEMBERSHIP</option>
                                    <option value="t">Federation of Free Farmers (FFF)</option>
                                    <option value="f">Rural Missionaries of the Philippines</option>
                                    <option value="f">AgriCOOPh (Philippine Family Farmers’ Agriculture Fishery Forestry Cooperatives Federation)</option>
                                    <option value="f">Alyansa Agrikultura (AA)</option>
                                    <option value="f">Aniban ng Manggagawa sa Agrikultura (AMA)</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Submit Request</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- <div class="modal fade show" id="farmModal" tabindex="-1" aria-labelledby="farmModalLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
    <!-- <div class="modal fade" id="farmModal" tabindex="-1" aria-labelledby="farmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:0;">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="farmModalLabel">My Farms</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url() ?>requestprofile" method="post">
            <div class="modal-body">
                <div class="card-body">
                    <div class="col-4">
                        <button type="button" class="btn btn-primary"><i class="fas fa-plus"></i> Add Farm</button>

                    </div>
                    <h6><i class="fas fa-table mt-3"></i> Farm List</h6>
                    <div class="col-12" style="overflow-x: auto;">
                        <table id="tblFarmList" style="width:100%;" class="table table-sm table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>ID</th>
                                    <th>Address</th>
                                    <th>Contact</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Name</td>
                                    <td>ID</td>
                                    <td>Address</td>
                                    <td>Contact</td>
                                    <td>Email</td>
                                    <td>Status</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>


            </div>

        </div>
    </div>
</div> -->