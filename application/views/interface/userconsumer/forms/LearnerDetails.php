<input name="details" nr="1" hidden />
<input id="ersid" hidden name="rsId" />
<div class="card-body p-2 pb-0">
    <div class="row pb-0">
        <!-- <div class="col-xl-2 col-md-12">
            <center>
                <div class="form-group">
                    <img name="previewPic" src="<?= $system_svg_1x1 ?>" width="120" height="120" class="border border-primary border-2 rounded elevation-1" alt="User Image">
                </div>
                <div class="form-group">
                    <input name="pic" type="file" accept="image/*" onchange="imageView('pic','previewPic','imgtargetLink')" nr="1" hidden />
                    <input name="img_path" type="text" nr="1" hidden />
                    <div class="form-group mt-n2">
                        <button type="button" class="btn btn-xs bg-black"><i class="fa fa-camera"></i> camera</button>
                        <button type="button" class="btn btn-xs bg-black" onclick="$('[name=pic]').trigger('click')" width="120" height="120" class="border border-primary border-2 rounded elevation-1">
                            <i class="fa fa-upload"></i> upload</button>
                    </div>
                </div>
            </center>
        </div> -->

        <div class="col-xl-2 col-md-12">
            <center>
                <div class="form-group">
                    <img name="previewPic" id="previewPic" 
                        src="<?= $system_svg_1x1 ?>" 
                        width="120" height="120" 
                        class="border border-primary border-2 rounded elevation-1" 
                        alt="User Image">

                    <!-- Live camera preview -->
                    <video id="cameraStream" width="150" height="150" autoplay playsinline hidden></video>
                    <canvas id="cameraCanvas" hidden></canvas>
                </div>

                <div class="form-group">
                    <!-- <input name="pic" type="file" accept="image/*" 
                        onchange="imageView2('pic','previewPic','imgtargetLink')"  nr="1" hidden /> -->
                    <input name="pic" type="file" accept="image/*" onchange="imageView2('pic','previewPic','imgtargetLink')" nr="1"  />
                    <input name="img_path" type="text" nr="1"  />
                    <input type="" name="pic_captured" nr="1" id="picCaptured">

                    <div class="form-group mt-n2">
                        <!-- Single toggle button -->
                        <button type="button" id="cameraBtn" class="btn btn-xs bg-black" onclick="toggleCamera()">
                            <i class="fa fa-camera"></i> Camera
                        </button>

                        <button type="button" id="switchCamBtn" class="btn btn-xs bg-black" onclick="switchCamera()">
                            <i class="fa fa-refresh"></i> Switch
                        </button>

                        <!-- Upload -->
                        <button type="button" class="btn btn-xs bg-black" onclick="$('[name=pic]').trigger('click')">
                            <i class="fa fa-upload"></i> Upload
                        </button>
                    </div>
                </div>
            </center>
        </div>

        <script>
            let stream = null;
            let cameraOn = false;
            let useFront = true; // default to front cam
            window.addEventListener("load", function () {
                resetPreview();
            });

            function resetPreview() {
                let preview = document.getElementById("previewPic");
                preview.src = "<?= $system_svg_1x1 ?>" + "?t=" + new Date().getTime();
            }
            function imageView2(a, b, c) {
                var fileInput = $("[name=" + a + "]")[0]; // Get the file input element
                var file = fileInput.files[0]; // Get the selected file

                if (!file) {
                    // If no file chosen, clear preview
                    $("[name=" + b + "]").attr("src", "");
                    return;
                }

                if (file.size > 2 * 1024 * 1024) { // 2MB limit
                    alert("Picture must be less than 2MB");
                    fileInput.value = ""; // reset input
                    $("[name=" + b + "]").attr("src", ""); // clear preview
                    return;
                }

                var reader = new FileReader();

                reader.onload = function(e) {
                    // Add timestamp query string to force browser to refresh image
                    const timestamp = new Date().getTime();
                    $("[name=" + b + "]").attr("src", e.target.result + "?t=" + timestamp);
                };

                // Reset first, then read again (forces refresh even if same file is selected)
                $("[name=" + b + "]").attr("src", "");
                reader.readAsDataURL(file);
            }
            
            
            async function startCamera() {
                const video = document.getElementById('cameraStream');
                video.hidden = false;

                // stop any existing stream before switching
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }

                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: useFront ? "user" : { exact: "environment" } }
                    });
                    video.srcObject = stream;

                    // Mirror only front camera
                    if (useFront) {
                        video.classList.add("mirrored");
                    } else {
                        video.classList.remove("mirrored");
                    }

                } catch (err) {
                    alert("Camera error: " + err.message);
                    video.hidden = true;
                    cameraOn = false;
                }
            }

            async function toggleCamera() {
                const video = document.getElementById('cameraStream');
                const canvas = document.getElementById('cameraCanvas');
                const img = document.getElementById('previewPic');
                const btn = document.getElementById('cameraBtn');

                if (!cameraOn) {
                    await startCamera();
                    img.hidden = true;
                    btn.innerHTML = '<i class="fa fa-check"></i> Capture';
                    cameraOn = true;

                } else {
                    // Capture snapshot
                    const videoWidth = video.videoWidth;
                    const videoHeight = video.videoHeight;

                    // Pick the smaller side for square dimension
                    const size = Math.min(videoWidth, videoHeight);

                    // Center crop coordinates
                    const startX = (videoWidth - size) / 2;
                    const startY = (videoHeight - size) / 2;

                    canvas.width = size;
                    canvas.height = size;
                    const ctx = canvas.getContext('2d');

                    ctx.setTransform(1, 0, 0, 1, 0, 0);
                    if (video.classList.contains("mirrored")) {
                        ctx.translate(canvas.width, 0);
                        ctx.scale(-1, 1);
                    }

                    // Draw square-cropped image
                    ctx.drawImage(video, startX, startY, size, size, 0, 0, size, size);
                    ctx.setTransform(1, 0, 0, 1, 0, 0);

                    // Convert to file
                    canvas.toBlob(function(blob) {
                        const file = new File([blob], "capture.jpg", { type: "image/jpeg" });
                        const dt = new DataTransfer();
                        dt.items.add(file);
                        document.getElementsByName('pic')[0].files = dt.files;
                    }, 'image/jpeg', 0.9);

                    // Stop camera
                    if (stream) {
                        stream.getTracks().forEach(track => track.stop());
                        stream = null;
                    }

                    video.hidden = true;
                    img.hidden = false;
                    img.src = canvas.toDataURL('image/jpeg');

                    btn.innerHTML = '<i class="fa fa-camera"></i> Camera';
                    cameraOn = false;
                    
                    // Capture snapshot
                    // canvas.width = 700;
                    // canvas.height = 580;
                    // const ctx = canvas.getContext('2d');

                    // ctx.setTransform(1, 0, 0, 1, 0, 0);
                    // if (video.classList.contains("mirrored")) {
                    //     ctx.translate(canvas.width, 0);
                    //     ctx.scale(-1, 1);
                    // }
                    // ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    // ctx.setTransform(1, 0, 0, 1, 0, 0);

                    // // Convert to file
                    // canvas.toBlob(function(blob) {
                    //     const file = new File([blob], "capture.jpg", { type: "image/jpeg" });
                    //     const dt = new DataTransfer();
                    //     dt.items.add(file);
                    //     document.getElementsByName('pic')[0].files = dt.files;
                    // }, 'image/jpeg', 0.9);

                    // // Stop camera
                    // if (stream) {
                    //     stream.getTracks().forEach(track => track.stop());
                    //     stream = null;
                    // }

                    // video.hidden = true;
                    // img.hidden = false;
                    // img.src = canvas.toDataURL('image/jpeg');

                    // btn.innerHTML = '<i class="fa fa-camera"></i> Camera';
                    // cameraOn = false;
                }
            }

            function switchCamera() {
                useFront = !useFront; // toggle front/back
                if (cameraOn) {
                    startCamera(); // restart with new mode
                }
            }
        </script>

        <div class="col-xl-10 col-md-12 mt-2">
            <div class="row">
                <div class="col-6 col-lg-2">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text text-bold text-success text-xs">LRN</span>
                        </div>
                        <input type="number" class="form-control form-control-sm text-uppercase" name="lrn" placeholder="LEARNER'S REFERENCE NUMBER (LRN)" autocomplete="off">
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text firstName text-primary"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control form-control-sm text-uppercase" name="firstName" placeholder="FIRST NAME" autocomplete="off">
                    </div>
                </div>
                <div class="col-6 col-lg-2 mb-2">
                    <input type="text" class="form-control form-control-sm text-uppercase" name="middleName" placeholder="MIDDLE NAME" autocomplete="off" nr="1">
                </div>
                <div class="col-6 col-lg-3 mb-2">
                    <input type="text" class="form-control form-control-sm text-uppercase" name="lastName" placeholder="LAST NAME" autocomplete="off">
                </div>
                <div class="col-3 col-lg-1 mb-2">
                    <input type="text" class="form-control form-control-sm text-uppercase" name="extName" placeholder="EXTN" autocomplete="off" nr="1">
                </div>
                <div class="col-3 col-lg-1">
                    <!-- <div class="input-group mb-2">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                                            </div> -->
                    <select class="form-control form-control-sm" name="sex">
                        <option value="t">MALE</option>
                        <option value="f">FEMALE</option>
                    </select>
                    <!-- </div> -->
                </div>
                <div class="col-6 col-lg-2">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text birthdate"><i class="fas fa-birthday-cake"></i></span>
                        </div>
                        <input type="date" class="form-control form-control-sm" name="birthdate" value="<?= date('Y-m-d'); ?>">
                    </div>
                </div>

                <div class="col-6 col-lg-2">
                    <div class="input-group mb-2">
                        <select class="form-control  form-control-sm select2 selectCityMunList" style="width:100%" onchange="getLocation(['CityMunList','BarangayList'],
																														['BarangayList','PurokList'],'EnrollmentInfo')" type="select" name="cty">
                        </select>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <div class="input-group mb-2">
                        <select class="form-control form-control-sm select2 selectBarangayList" data-placeholder="SELECT BARANGAY" onchange="getLocation('BarangayList','PurokList','EnrollmentInfo')" type="select" name="brgy" style="width:100%;">
                        </select>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text homeAddress"><i class="fas fa-home"></i></span>
                        </div>
                        <input type="text" class="form-control form-control-sm text-uppercase" name="homeAddress" placeholder="ADDRESS DETAILS" autocomplete="off" nr="1">
                    </div>
                </div>

                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text text-xs text-bold">STATUS</span>
                        </div>
                        <div class="input-group-prepend">
                            <select class="form-control form-control-sm selectLearnerStatus" name="status">
                            </select>
                        </div>
                        <input type="date" class="form-control form-control-sm" name="enrollDate" autocomplete="off">
                        <!-- <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-edit text-primary"></i></span>
                            <button type="submit" class="btn btn-info btn-sm submitBtnPrimary"><i class="fa fa-check"></i> Update Data</button>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-custom-content w-100 mb-2">
            <!-- <p class="lead mb-0">Other Information goes here</p> -->
        </div>



        <div class="card w-100 collapse-header collapsed-card">
            <!-- <div class="card w-100"> -->
            <div class="card-header p-1 pr-2 pl-2 bg-gradient-purple rounded" role="button" data-card-widget="collapse">
                <h3 class="card-title lead text-white">Other Information goes here</h3>
                <!-- <div class="card-tools mt-n1 pb-0">
                    <button type="button" class="btn btn-tool btn-xs"><i class="fas fa-plus"></i></button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button>
                </div> -->
            </div>
            <!-- <div class="card-body collapse-body p-2 mb-n3"> -->

            <div class="card-body collapse-body p-2 mb-n3" style="overflow: auto;display: none;">
                <div class="card card-navy card-outline w-100">
                    <div class="row p-2">
                        <div class="col-lg-6 col-12">
                            <div class="tab-custom-content w-100">
                                <p class="lead mb-0">Father's name</p>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 mb-2">
                                    <input type="text" class="form-control form-control-sm text-uppercase" name="ffname" placeholder="FIRST NAME" autocomplete="off" nr="1">
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 mb-2">
                                    <input type="text" class="form-control form-control-sm text-uppercase" name="fmname" placeholder="MIDDLE NAME" autocomplete="off" nr="1">
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <input type="text" class="form-control form-control-sm text-uppercase" name="flname" placeholder="LAST NAME" autocomplete="off" nr="1">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-12">
                            <div class="tab-custom-content w-100">
                                <p class="lead mb-0">Mother's Maiden name</p>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 mb-2">
                                    <input type="text" class="form-control form-control-sm text-uppercase" name="mfname" placeholder="FIRST NAME" autocomplete="off" nr="1">
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 mb-2">
                                    <input type="text" class="form-control form-control-sm text-uppercase" name="mmname" placeholder="MIDDLE NAME" autocomplete="off" nr="1">
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <input type="text" class="form-control form-control-sm text-uppercase" name="mlname" placeholder="LAST NAME" autocomplete="off" nr="1">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-12">
                            <div class="tab-custom-content w-100">
                                <p class="lead mb-0">Guardian's name</p>
                            </div>
                            <div class="row">
                                <div class="col-8 mb-2">
                                    <input type="text" class="form-control form-control-sm text-uppercase" name="guardianName" placeholder="GUARDIAN'S NAME" autocomplete="off" nr="1">
                                </div>
                                <div class="col-4 mb-2">
                                    <input type="text" class="form-control form-control-sm text-uppercase" name="relationship" placeholder="RELATIOSHIP" autocomplete="off" nr="1">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-12">
                            <div class="tab-custom-content w-100">
                                <p class="lead mb-0 text-orange text-bold">Incase of Emergency please contact</p>
                            </div>
                            <div class="row">
                                <div class="col-3 mb-2">
                                    <select class="form-control form-control-sm" name="ioe">
                                        <option value="M">MOTHER</option>
                                        <option value="F">FATHER</option>
                                        <option value="G">GUARDIAN</option>
                                    </select>
                                </div>
                                <div class="col-4 mb-2">
                                    <input type="text" class="form-control form-control-sm text-uppercase" name="contactNumber" placeholder="CONTACT NUMBER" autocomplete="off" nr="1">
                                </div>
                                <div class="col-5 mb-2">
                                    <input type="text" class="form-control form-control-sm text-uppercase lead mb-0 text-sm text-bold text-gray" name="mfg" readonly nr="1" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="card w-100 collapse-header collapsed-card">
            <!-- <div class="card w-100"> -->
            <div class="card-header p-1 pr-2 pl-2 bg-gradient-purple rounded" role="button" data-card-widget="collapse">
                <h3 class="card-title lead text-white">Additional Information goes here</h3>
                <!-- <div class="card-tools mt-n1 pb-0">
                    <button type="button" class="btn btn-tool btn-xs"><i class="fas fa-plus"></i></button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button>
                </div> -->
            </div>
            <!-- <div class="card-body collapse-body p-2 mb-n3"> -->

            <div class="card-body collapse-body p-2 mb-n3" style="overflow: auto;display: none;">
                <div class="card card-pink card-outline w-100">
                    <div class="row p-2">
                        <div class="col-lg-12 col-12">
                            <div class="tab-custom-content w-100">
                                <!-- <p class="lead mb-0">Mother's Maiden name</p> -->
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 mb-2">
                                    <input type="text" class="form-control form-control-sm text-uppercase" name="mother_tongue" placeholder="MOTHER TONGUE" autocomplete="off" nr="1">
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 mb-2">
                                    <input type="text" class="form-control form-control-sm text-uppercase" name="ip_ethnic_group" placeholder="IP ETHNIC GROUP" autocomplete="off" nr="1">
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <input type="text" class="form-control form-control-sm text-uppercase" name="religion" placeholder="RELIGION" autocomplete="off" nr="1">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 col-12">
                            <div class="tab-custom-content w-100">
                                <!-- <p class="lead mb-0">Guardian's name</p> -->
                            </div>
                            <div class="row">
                                <div class="col-2 mb-2">
                                    <select class="form-control form-control-sm" name="four_ps" nr="1">
                                        <option value="true">YES</option>
                                        <option value="false">NO</option>
                                    </select>
                                </div>
                                <div class="col-3 mb-2">
                                    <input type="text" class="form-control form-control-sm text-uppercase" name="learning_modality" placeholder="LEARNING MODALITY" autocomplete="off" nr="1">
                                </div>
                                <div class="col-7 mb-2">
                                    <input type="text" class="form-control form-control-sm text-uppercase" name="remarks" placeholder="REMARKS" autocomplete="off" nr="1">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer w-100 p-1">
            <button type="submit" class="btn btn-info submitBtnPrimary">Save Data</button>
            <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close" onclick="clear_form('form_save_dataVariantCategory')"><i class="fa fa-times"></i> Cancel</button>
        </div>


    </div>
</div>