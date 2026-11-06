<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/crm-ureg.css?v=20261106_1">
<?php
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$sel = function ($a, $b) { return ((string)$a === (string)$b && (string)$a !== '') ? ' selected' : ''; };
$enc = base64_encode($list['id']);
$back = urldecode((string)$this->session->userdata('manager_return_string'));
$back = $back !== '' && strpos($back, 'verticals') !== false ? base_url() . 'manager/' . $back : base_url() . 'manager/verticals/view/' . $enc;
?>
<style>
  .ureg .vf-head { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
  .ureg .vf-head h1 small { display: block; margin-top: 2px; font-size: 13px; font-weight: 500; color: var(--crm-muted, #64748b); letter-spacing: 0; }
  .ureg .vf-head .vf-view { margin-left: auto; font-weight: 600; font-size: 13px; }
  .ureg .box-body > .row > .col-md-12 .form-group { grid-template-columns: 112px minmax(0, 1fr); }
  @media (min-width: 1200px) { .ureg .vf-2 { width: 50% !important; } }
</style>
<div class="content-wrapper ureg">
  <section class="content-header vf-head">
    <h1>Edit Vertical<small>V-<?php echo $h($list['v_id']); ?> · <?php echo $h($list['company_name']); ?></small></h1>
    <a class="vf-view" href="<?php echo base_url(); ?>manager/verticals/view/<?php echo $enc; ?>"><i class="fa fa-eye"></i> View</a>
  </section>

  <section class="content">
    <div class="container">
      <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> <?php echo get_message($this->session->flashdata('message'),'message'); ?>

      <form role="form" action="<?php echo base_url(); ?>manager/verticals/edit/<?php echo $enc; ?>" method="post" name="add_form" autocomplete="off">

        <div class="box box-default">
          <div class="box-header with-border"><h3 class="box-title">Vertical</h3></div>
          <div class="box-body">
            <div class="row">
              <div class="col-lg-4 col-sm-6 vf-2">
                <div class="form-group"><label for="vf_company">Company<span>*</span></label>
                  <input id="vf_company" type="text" name="company_name" class="form-control" style="text-transform: capitalize;" value="<?php echo $h($list['company_name']); ?>" required></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="catselect">Category<span>*</span></label>
                  <select class="form-control" required name="category" id="catselect" onchange="getSubCat()">
                    <option value="">Select Category</option>
                    <?php if (!empty($cat_list)) foreach ($cat_list as $cli) { ?><option value="<?php echo $cli['id']; ?>"<?php echo $sel($list['category'], $cli['id']); ?>><?php echo $h($cli['title']); ?></option><?php } ?>
                  </select></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="subcatselect">Sub category<span>*</span></label>
                  <select class="form-control" required name="sub_category" id="subcatselect">
                    <option value="">Choose Sub Category</option>
                    <?php if (!empty($scat_list)) foreach ($scat_list as $cli) { ?><option value="<?php echo $cli['id']; ?>"<?php echo $sel($list['sub_category'], $cli['id']); ?>><?php echo $h($cli['title']); ?></option><?php } ?>
                  </select></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_status">Status<span>*</span></label>
                  <select id="vf_status" class="form-control" required name="status">
                    <option value="">Select Status</option>
                    <?php if (!empty($st_list)) foreach ($st_list as $cli) { ?><option value="<?php echo $cli['id']; ?>"<?php echo $sel($list['status'], $cli['id']); ?>><?php echo $h($cli['title']); ?></option><?php } ?>
                  </select></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_source">Source</label>
                  <input id="vf_source" type="text" class="form-control" name="lead_source" value="<?php echo $h($list['lead_source']); ?>" placeholder="Where they came from"></div>
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
                  <input id="vf_poc" type="text" required class="form-control" name="poc" value="<?php echo $h($list['poc']); ?>"></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_desig">Designation</label>
                  <input id="vf_desig" type="text" class="form-control" name="designation" value="<?php echo $h($list['designation']); ?>"></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_email">Email</label>
                  <input id="vf_email" type="email" class="form-control" name="email" value="<?php echo $h($list['email']); ?>"></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_c1">Phone</label>
                  <input id="vf_c1" type="tel" class="form-control" name="contact1" value="<?php echo $h($list['contact1']); ?>"></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_c2">Alt. phone</label>
                  <input id="vf_c2" type="tel" class="form-control" name="contact2" value="<?php echo $h($list['contact2']); ?>"></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_wa">WhatsApp</label>
                  <input id="vf_wa" type="text" class="form-control" name="whatsapp_web" value="<?php echo $h($list['whatsapp_web']); ?>"></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="country2">Nationality</label>
                  <select id="country2" class="form-control" name="nationality" onchange="get_state2()">
                    <option value="">Select</option>
                    <?php if (!empty($c_list)) foreach ($c_list as $cli) { ?><option value="<?php echo $cli['id']; ?>"<?php echo $sel($list['nationality'], $cli['id']); ?>><?php echo $h($cli['name']); ?></option><?php } ?>
                  </select></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="state2">Nationality area</label>
                  <select id="state2" class="form-control" name="nationality_area">
                    <option value="">Select</option>
                    <?php if (!empty($area_list)) foreach ($area_list as $cli) { ?><option value="<?php echo $cli['id']; ?>"<?php echo $sel($list['nationality_area'], $cli['id']); ?>><?php echo $h($cli['name']); ?></option><?php } ?>
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
                    <?php if (!empty($c_list)) foreach ($c_list as $cli) { ?><option value="<?php echo $cli['id']; ?>"<?php echo $sel($list['country'], $cli['id']); ?>><?php echo $h($cli['name']); ?></option><?php } ?>
                  </select></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="state">State / Emirate</label>
                  <select id="state" class="form-control" name="state">
                    <option value="">Select</option>
                    <?php if (!empty($state_list)) foreach ($state_list as $cli) { ?><option value="<?php echo $cli['id']; ?>"<?php echo $sel($list['state'], $cli['id']); ?>><?php echo $h($cli['name']); ?></option><?php } ?>
                  </select></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_city">City</label>
                  <input id="vf_city" type="text" class="form-control" name="city" value="<?php echo $h($list['city']); ?>"></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_a1">Address</label>
                  <input id="vf_a1" type="text" name="address" class="form-control" value="<?php echo $h($list['address']); ?>"></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_a2">Address 2</label>
                  <input id="vf_a2" type="text" name="address2" class="form-control" value="<?php echo $h($list['address2']); ?>"></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_lfu">Last follow-up</label>
                  <input id="vf_lfu" type="text" class="form-control datepicker" name="lastfollowup_date" value="<?php echo $h(simple_date($list['lastfollowup_date'])); ?>"></div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group"><label for="vf_nfu">Next follow-up</label>
                  <input id="vf_nfu" type="text" class="form-control datepicker2" name="followup_date" value="<?php echo $h(simple_date($list['followup_date'])); ?>"></div>
              </div>
              <div class="col-md-12">
                <div class="form-group"><label for="vf_remark">Remark</label>
                  <textarea id="vf_remark" class="form-control" name="remark" rows="3"><?php echo $h($list['remark']); ?></textarea></div>
              </div>
            </div>
          </div>
        </div>

        <div class="box-footer text-center ureg-footer">
          <a href="<?php echo $h($back); ?>" class="btn btn-default">Cancel</a>
          <button id="add_btn" type="submit" class="btn btn-info">Save changes</button>
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
