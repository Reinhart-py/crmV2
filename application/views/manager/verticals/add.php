<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-ureg.css?v=20261106_3">
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$back = urldecode((string)$this->session->userdata('manager_return_string'));
$back = $back !== '' && strpos($back, 'verticals') !== false ? base_url() . 'manager/' . $back : base_url() . 'manager/verticals';
// Status choices: Database (default), Active, Inactive, Not Interested, matched by title from the status list.
$db_id = 81; $act_id = 50; $inact_id = 51; $ni_id = 56;
if (!empty($st_list)) foreach ($st_list as $cli) {
  $t = trim($cli['title']);
  if (strcasecmp($t, 'Database') === 0) $db_id = $cli['id'];
  if (strcasecmp($t, 'Active') === 0) $act_id = $cli['id'];
  if (strcasecmp($t, 'Inactive') === 0) $inact_id = $cli['id'];
  if (strcasecmp($t, 'Not Interested') === 0) $ni_id = $cli['id'];
}
?>
<style>
  .ureg .vf-head h1 small { display: block; margin-top: 2px; font-size: 13px; font-weight: 500; color: var(--crm-muted, #64748b); letter-spacing: 0; }
  .ureg .box-body > .row > .col-md-12 .form-group { grid-template-columns: 112px minmax(0, 1fr); }
  @media (min-width: 1200px) { .ureg .vf-2 { width: 50% !important; } }
</style>
<div class="content-wrapper ureg">
  <section class="content-header vf-head">
    <h1>Add Vertical<small>A referral partner who brings clients</small></h1>
  </section>

  <section class="content">
    <div class="container">
      <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> <?php echo get_message($this->session->flashdata('message'),'message'); ?>

      <form role="form" action="<?php echo base_url(); ?>manager/verticals/add" method="post" name="add_form" autocomplete="off">

        <div class="box box-default">
          <div class="box-header with-border"><h3 class="box-title">Vertical</h3></div>
          <div class="box-body">
            <div class="row">
              <div class="col-lg-4 col-sm-6 vf-2">
                <div class="form-group"><label for="vf_company">Company<span>*</span></label>
                  <input id="vf_company" type="text" name="company_name" class="form-control" style="text-transform: capitalize;" required></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="catselect">Category<span>*</span></label>
                  <select class="form-control" required name="category" id="catselect" onchange="getSubCat()">
                    <option value="">Select Category</option>
                    <?php if (!empty($cat_list)) foreach ($cat_list as $cli) { ?><option value="<?php echo $cli['id']; ?>"><?php echo $h($cli['title']); ?></option><?php } ?>
                  </select></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="subcatselect">Sub category<span>*</span></label>
                  <select class="form-control" required name="sub_category" id="subcatselect">
                    <option value="">Choose Sub Category</option>
                  </select></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_status">Status<span>*</span></label>
                  <select id="vf_status" class="form-control" required name="status">
                    <option value="<?php echo $h($db_id); ?>" selected>Database</option>
                    <option value="<?php echo $h($act_id); ?>">Active</option>
                    <option value="<?php echo $h($inact_id); ?>">Inactive</option>
                    <option value="<?php echo $h($ni_id); ?>">Not Interested</option>
                  </select></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_source">Source</label>
                  <input id="vf_source" type="text" class="form-control" name="lead_source" placeholder="Where they came from"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="box box-default">
          <div class="box-header with-border"><h3 class="box-title">Contact person</h3></div>
          <div class="box-body">
            <div class="row">
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_poc">Name<span>*</span></label>
                  <input id="vf_poc" type="text" required class="form-control" name="poc"></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_desig">Designation</label>
                  <input id="vf_desig" type="text" class="form-control" name="designation"></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_email">Email</label>
                  <input id="vf_email" type="email" class="form-control" name="email"></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_c1">Phone</label>
                  <input id="vf_c1" type="tel" class="form-control" name="contact1"></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_c2">Alt. phone</label>
                  <input id="vf_c2" type="tel" class="form-control" name="contact2"></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_wa">WhatsApp</label>
                  <input id="vf_wa" type="text" class="form-control" name="whatsapp_web"></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="country2">Nationality</label>
                  <select id="country2" class="form-control" name="nationality" onchange="get_state2()">
                    <option value="">Select</option>
                    <?php if (!empty($c_list)) foreach ($c_list as $cli) { ?><option value="<?php echo $cli['id']; ?>"><?php echo $h($cli['name']); ?></option><?php } ?>
                  </select></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="state2">Nationality area</label>
                  <select id="state2" class="form-control" name="nationality_area">
                    <option value="">Select</option>
                  </select></div>
              </div>
            </div>
          </div>
        </div>

        <div class="box box-default">
          <div class="box-header with-border"><h3 class="box-title">Location &amp; follow-up</h3></div>
          <div class="box-body">
            <div class="row">
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="country">Country</label>
                  <select id="country" class="form-control" name="country" onchange="get_state()">
                    <option value="">Select</option>
                    <?php if (!empty($c_list)) foreach ($c_list as $cli) { ?><option value="<?php echo $cli['id']; ?>"><?php echo $h($cli['name']); ?></option><?php } ?>
                  </select></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="state">State / Emirate</label>
                  <select id="state" class="form-control" name="state">
                    <option value="">Select</option>
                  </select></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_city">City</label>
                  <input id="vf_city" type="text" class="form-control" name="city"></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_a1">Address</label>
                  <input id="vf_a1" type="text" name="address" class="form-control"></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_a2">Address 2</label>
                  <input id="vf_a2" type="text" name="address2" class="form-control"></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_lfu">Last follow-up</label>
                  <input id="vf_lfu" type="text" class="form-control datepicker" name="lastfollowup_date"></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_nfu">Next follow-up</label>
                  <input id="vf_nfu" type="text" class="form-control datepicker2" name="followup_date"></div>
              </div>
              <div class="col-md-12">
                <div class="form-group"><label for="vf_remark">Remark</label>
                  <textarea id="vf_remark" class="form-control" name="remark" rows="3"></textarea></div>
              </div>
            </div>
          </div>
        </div>

        <div class="box-footer text-center ureg-footer">
          <a href="<?php echo $h($back); ?>" class="btn btn-default">Cancel</a>
          <button id="add_btn" type="submit" class="btn btn-info">Add vertical</button>
        </div>
      </form>
    </div>
  </section>
</div>

<script>
$(document).ready(function () {
  $(document).on("click", "#add_btn", function () {
    $('form[name=add_form]').validate({ submitHandler: function (form) { form.submit(); } });
  });
});
function get_state() {
  $.ajax({ type: "POST", data: 'c_id=' + $("#country").val(), url: "<?php echo base_url(); ?>ci_admin_user/get_state",
    success: function (result) { if (result) { $('#state').html(result); } } });
}
function get_state2() {
  $.ajax({ type: "POST", data: 'c_id=' + $("#country2").val(), url: "<?php echo base_url(); ?>ci_admin_user/get_state",
    success: function (result) { if (result) { $('#state2').html(result); } } });
}
function getSubCat() {
  $.ajax({ type: "POST", data: 'c_id=' + $("#catselect").val(), url: "<?php echo base_url(); ?>ci_admin_terms/getSubCatAjax",
    success: function (result) { if (result) { $('#subcatselect').html(result); } } });
}
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
