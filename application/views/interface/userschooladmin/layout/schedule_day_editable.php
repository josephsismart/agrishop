<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>
<?php
if (!$this->session->schoolmis_login_level) {
    redirect(base_url('login'));
}
$uri = $this->session->schoolmis_login_uri;
?>
<table class="table table-bordered" id="scheduleTable">
    <thead>
        <tr>
            <th>Start Time</th>
            <th>Duration</th>
            <th>End Time</th>
            <th>Subject</th>
            <th>Teacher</th>
            <th>Advisory</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($schedules as $i => $row): ?>
            <tr>
            <!-- $this->load->view("interface/userschooladmin/layout/schedule_day_editable", $data); -->
                <td>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-danger btn-xs btnRemoveRow">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                        <div class="input-group-append flex-grow-1">
                            <select class="form-control form-control-sm startTime" name="start_time_id[]" onchange="triggerRecalculateWithDelay();">
                                <?php
                                $thisQuery = $this->db->query("SELECT * FROM building_sectioning.tbl_timetable WHERE is_active=true ORDER BY order_by");
                                foreach ($thisQuery->result() as $value) {
                                    $selected = ($value->id == $row->from_timetable_id) ? 'selected' : '';
                                    echo '<option value="' . $value->id . '" class="' . $value->time_ . '" ' . $selected . '>' . htmlspecialchars($value->time_char) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </td>
                <td>
                    <select class="form-control form-control-sm duration" name="duration_id[]" onchange="triggerRecalculateWithDelay();">
                        <?php
                        $thisQuery = $this->db->query("SELECT * FROM building_sectioning.tble_duration_minutes WHERE is_active=true ORDER BY order_by");
                        foreach ($thisQuery->result() as $value) {
                            $selected = ($value->id == $row->duration_id) ? 'selected' : '';
                            echo '<option value="' . $value->id . '" class="' . $value->minutes_ . '" ' . $selected . '>' . htmlspecialchars($value->minutes_char) . '</option>';
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm endTime" readonly value="<?= $row->to_time ?>" name="end_time_id[]">
                </td>
                <td>
                    <select class="form-control form-control-sm select2-subject" name="subject_id[]" style="width: 150px;">
                        <option value="">Select subject</option> <!-- ✅ This is the placeholder -->
                        <?php
                        $party_type_id = $row->teaching ? 17 : 23;
                        $thisQuery = $this->db->query("SELECT * FROM global.tbl_party t1 WHERE t1.party_type_id=17 or t1.party_type_id=23 ORDER BY t1.description");
                        foreach ($thisQuery->result() as $value) {
                            $selected = ($value->id == $row->subject_id) ? 'selected' : '';
                            $abbr = htmlspecialchars($value->abbr);
                            $desc = htmlspecialchars($value->description);
                            $text = $abbr . ' - ' . $desc;

                            echo '<option value="' . $value->id . '" ' . $selected . ' data-html="<strong>' . $abbr . '</strong> - <em>' . $desc . '</em>" data-text="' . $text . '">' . $text . '</option>';
                        }
                        ?>
                    </select>
                </td>
                <td>
                <input type="text" class="form-control form-control-sm endTime" readonly value="<?= $row->to_time ?>" name="end_time_id[]">
                </td>
                <td align="center">
                    <input type="radio" name="advisory" value="<?= $i ?>" <?= $row->advisory ? 'checked' : '' ?>>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>