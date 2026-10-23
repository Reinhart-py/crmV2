<?php require_once(APPPATH."views/manager/elements/header.php"); ?>

<div class="container">
<div class="content-wrapper"> 
  
  <section class="content-header">
    <h1> Onboard Client </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?php echo base_url(); ?>manager/kam/my-client">KAM</a></li>
      <li class="active">Onboard Client</li>
    </ol>
  </section>
  
  <section class="content"> 
    
    <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> 
    <?php echo get_message($this->session->flashdata('message'),'message'); ?>
     
    <form role="form" action="<?php echo base_url(); ?>manager/kam/onboard" method="post" name="onboard_client_form">
      
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Client and Group Details</h3>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Client Name <span>*</span></label>
                <select name="customer" id="kam_company_select" class="form-control select2-company-ajax" style="width: 100%;" required>
                  <option value=""></option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>POC Name <span>*</span></label>
                <input type="text" class="form-control" name="poc" id="kam_poc_name" placeholder="Contact Person Name" required>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Group Name</label>
                <input type="text" class="form-control" name="group_name" id="kam_group_name">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Status</label>
                <select class="form-control select2" style="width: 100%;" name="status_category" id="kam_status_category">
                  <option value="">Select Status</option>
                  <option value="Active" selected="selected">Active</option>
                  <option value="In Progress">In Progress</option>
                  <option value="Pending Review">Pending Review</option>
                  <option value="Follow up">Follow up</option>
                  <option value="Inactive">Inactive</option>
                  <option value="Closed">Closed</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Category</label>
                <select class="form-control select2" style="width: 100%;" name="category" id="kam_category">
                  <option value="">Select Category</option>
                  <option value="Existing" selected="selected">Existing</option>
                  <option value="New">New</option>
                  <option value="Prospect">Prospect</option>
                  <?php if(isset($business_categories) && !empty($business_categories)){
                    foreach($business_categories as $bc){ ?>
                      <option value="<?php echo htmlspecialchars($bc['title']); ?>"><?php echo htmlspecialchars($bc['title']); ?></option>
                  <?php } } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Client Status</label>
                <select class="form-control select2" style="width: 100%;" name="client_status" id="kam_client_status">
                  <option value="Active" selected="selected">Active</option>
                  <option value="Inactive">Inactive</option>
                  <option value="Closed">Closed</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Onboarding Date</label>
                <input type="text" class="form-control datepicker" name="onboarding_date" value="<?php echo date('d-m-Y'); ?>" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>EOS Date</label>
                <input type="text" class="form-control datepicker" name="eos_date" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>EL Expiry Date</label>
                <input type="text" class="form-control datepicker" name="el_expiry" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>EL Action</label>
                <input type="text" class="form-control" name="el_due" placeholder="EL Action / Due Info">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Inactive Reason</label>
                <input type="text" class="form-control" name="inactive_reason">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Amount</label>
                <input type="text" class="form-control only_number" name="amount">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Group (Y/N)</label>
                <select class="form-control select2" style="width: 100%;" name="group_yn">
                  <option value="N" selected="selected">No (N)</option>
                  <option value="Y">Yes (Y)</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group" style="position: relative;">
                <label>Primary Assignee / Executive <span>*</span></label>
                <div class="input-group">
                  <input type="text" id="onboard_assign_input" class="form-control" placeholder="Type 3+ letters to search..." autocomplete="off">
                  <span class="input-group-btn">
                    <button class="btn btn-default" type="button" onclick="clearOnboardAssign()"><i class="fa fa-times"></i></button>
                  </span>
                </div>
                <input type="hidden" name="lead_assign" id="onboard_lead_assign_val" value="<?php echo (int)$this->session->userdata('manager_id'); ?>" required>
                <div id="onboard_assign_dropdown" style="display:none; position:absolute; top:100%; left:0; right:0; z-index:9999; background:#fff; border:1px solid #cbd5e1; border-radius:4px; box-shadow:0 8px 16px rgba(0,0,0,0.12); max-height:220px; overflow-y:auto; margin-top:2px;"></div>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Collaborators / Additional Assignees</label>
                <select class="form-control select2" name="agents[]" multiple="multiple" data-placeholder="Select Additional Assignees" style="width: 100%;">
                  <?php if(isset($u_list) && !empty($u_list)) {
                    foreach($u_list as $u){ ?>
                      <option value="<?php echo $u['id']; ?>">
                        <?php echo htmlspecialchars($u['name']); ?>
                      </option>
                  <?php } } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Executive Designation</label>
                <input type="text" class="form-control" name="executive" placeholder="Designation / Notes">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Trade License, Compliance & FTA Details</h3>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Trade License Number</label>
                <input type="text" class="form-control" name="tl_number" id="tl_number" placeholder="Enter Trade License Number">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Trade License Emirates</label>
                <select class="form-control" name="tl_emirate" id="tl_emirate_select">
                  <option value="">Select Emirate</option>
                  <option value="Abu Dhabi">Abu Dhabi</option>
                  <option value="Dubai">Dubai</option>
                  <option value="Sharjah">Sharjah</option>
                  <option value="Ajman">Ajman</option>
                  <option value="Umm Al Quwain">Umm Al Quwain</option>
                  <option value="Ras Al Khaimah">Ras Al Khaimah</option>
                  <option value="Fujairah">Fujairah</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Trade License Zone</label>
                <select class="form-control" name="tl_zone" id="tl_zone_select" disabled>
                  <option value="">Select Zone</option>
                  <option value="Mainland">Mainland</option>
                  <option value="Freezone">Freezone</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Licensing Authorities</label>
                <select class="form-control select2" name="tl_authority" id="tl_authority_select" style="width: 100%;" disabled>
                  <option value="">Select Authority</option>
                  <optgroup label="Mainland">
                    <option value="Abu Dhabi DED" data-emirate="Abu Dhabi" data-zone="Mainland">Abu Dhabi DED</option>
                    <option value="Abu Dhabi Department of Economic Development — ADDED" data-emirate="Abu Dhabi" data-zone="Mainland">Abu Dhabi Department of Economic Development — ADDED</option>
                    <option value="Dubai Economy (DED / DET)" data-emirate="Dubai" data-zone="Mainland">Dubai Economy (DED / DET)</option>
                    <option value="Dubai Economy (DED)" data-emirate="Dubai" data-zone="Mainland">Dubai Economy (DED)</option>
                    <option value="Dubai Department of Economy and Tourism — DET" data-emirate="Dubai" data-zone="Mainland">Dubai Department of Economy and Tourism — DET</option>
                    <option value="Sharjah SEDD" data-emirate="Sharjah" data-zone="Mainland">Sharjah SEDD</option>
                    <option value="Sharjah Economic Development Department — SEDD" data-emirate="Sharjah" data-zone="Mainland">Sharjah Economic Development Department — SEDD</option>
                    <option value="Ajman DED" data-emirate="Ajman" data-zone="Mainland">Ajman DED</option>
                    <option value="Ajman Department of Economic Development — Ajman DED" data-emirate="Ajman" data-zone="Mainland">Ajman Department of Economic Development — Ajman DED</option>
                    <option value="RAK DED" data-emirate="Ras Al Khaimah" data-zone="Mainland">RAK DED</option>
                    <option value="Ras Al Khaimah Department of Economic Development — RAK DED" data-emirate="Ras Al Khaimah" data-zone="Mainland">Ras Al Khaimah Department of Economic Development — RAK DED</option>
                    <option value="Fujairah Municipality" data-emirate="Fujairah" data-zone="Mainland">Fujairah Municipality</option>
                    <option value="Fujairah Municipality — FM" data-emirate="Fujairah" data-zone="Mainland">Fujairah Municipality — FM</option>
                    <option value="Dibba Municipality — DM" data-emirate="Fujairah" data-zone="Mainland">Dibba Municipality — DM</option>
                    <option value="UAQ DED" data-emirate="Umm Al Quwain" data-zone="Mainland">UAQ DED</option>
                    <option value="Umm Al Quwain Department of Economic Development — UAQ DED" data-emirate="Umm Al Quwain" data-zone="Mainland">Umm Al Quwain Department of Economic Development — UAQ DED</option>
                  </optgroup>
                  <optgroup label="Free zones">
                    <option value="ADGM (Abu Dhabi Global Market)" data-emirate="Abu Dhabi" data-zone="Freezone">ADGM (Abu Dhabi Global Market)</option>
                    <option value="Abu Dhabi Global Market — ADGM" data-emirate="Abu Dhabi" data-zone="Freezone">Abu Dhabi Global Market — ADGM</option>
                    <option value="Masdar City Free Zone" data-emirate="Abu Dhabi" data-zone="Freezone">Masdar City Free Zone</option>
                    <option value="Masdar City Free Zone — Masdar" data-emirate="Abu Dhabi" data-zone="Freezone">Masdar City Free Zone — Masdar</option>
                    <option value="KIZAD / KEZAD" data-emirate="Abu Dhabi" data-zone="Freezone">KIZAD / KEZAD</option>
                    <option value="Khalifa Economic Zones Abu Dhabi — KEZAD" data-emirate="Abu Dhabi" data-zone="Freezone">Khalifa Economic Zones Abu Dhabi — KEZAD</option>
                    <option value="twofour54" data-emirate="Abu Dhabi" data-zone="Freezone">twofour54</option>
                    <option value="Media Zone Authority – twofour54 — TCA" data-emirate="Abu Dhabi" data-zone="Freezone">Media Zone Authority – twofour54 — TCA</option>
                    <option value="Abu Dhabi Airports Free Zone — ADAFZ" data-emirate="Abu Dhabi" data-zone="Freezone">Abu Dhabi Airports Free Zone — ADAFZ</option>
                    <option value="Tawazun Industrial Park — TIP" data-emirate="Abu Dhabi" data-zone="Freezone">Tawazun Industrial Park — TIP</option>
                    <option value="DMCC (Dubai Multi Commodities Centre)" data-emirate="Dubai" data-zone="Freezone">DMCC (Dubai Multi Commodities Centre)</option>
                    <option value="Dubai Multi Commodities Centre — DMCC" data-emirate="Dubai" data-zone="Freezone">Dubai Multi Commodities Centre — DMCC</option>
                    <option value="IFZA (International Free Zone Authority)" data-emirate="Dubai" data-zone="Freezone">IFZA (International Free Zone Authority)</option>
                    <option value="IFZA Dubai" data-emirate="Dubai" data-zone="Freezone">IFZA Dubai</option>
                    <option value="International Free Zone Authority — IFZA" data-emirate="Dubai" data-zone="Freezone">International Free Zone Authority — IFZA</option>
                    <option value="DIFC (Dubai International Financial Centre)" data-emirate="Dubai" data-zone="Freezone">DIFC (Dubai International Financial Centre)</option>
                    <option value="Dubai International Financial Centre — DIFC" data-emirate="Dubai" data-zone="Freezone">Dubai International Financial Centre — DIFC</option>
                    <option value="DAFZA (Dubai Airport Freezone)" data-emirate="Dubai" data-zone="Freezone">DAFZA (Dubai Airport Freezone)</option>
                    <option value="Dubai Airport Free Zone Authority — DAFZA" data-emirate="Dubai" data-zone="Freezone">Dubai Airport Free Zone Authority — DAFZA</option>
                    <option value="JAFZA (Jebel Ali Free Zone)" data-emirate="Dubai" data-zone="Freezone">JAFZA (Jebel Ali Free Zone)</option>
                    <option value="Jebel Ali Free Zone Authority — JAFZA" data-emirate="Dubai" data-zone="Freezone">Jebel Ali Free Zone Authority — JAFZA</option>
                    <option value="DSOA / Dubai Silicon Oasis" data-emirate="Dubai" data-zone="Freezone">DSOA / Dubai Silicon Oasis</option>
                    <option value="Dubai Silicon Oasis — DSO" data-emirate="Dubai" data-zone="Freezone">Dubai Silicon Oasis — DSO</option>
                    <option value="DWTC (Dubai World Trade Centre)" data-emirate="Dubai" data-zone="Freezone">DWTC (Dubai World Trade Centre)</option>
                    <option value="Dubai World Trade Centre Authority — DWTC" data-emirate="Dubai" data-zone="Freezone">Dubai World Trade Centre Authority — DWTC</option>
                    <option value="Meydan Free Zone" data-emirate="Dubai" data-zone="Freezone">Meydan Free Zone</option>
                    <option value="Meydan Free Zone — MFZ" data-emirate="Dubai" data-zone="Freezone">Meydan Free Zone — MFZ</option>
                    <option value="Dubai South (DWC)" data-emirate="Dubai" data-zone="Freezone">Dubai South (DWC)</option>
                    <option value="Dubai South — DS" data-emirate="Dubai" data-zone="Freezone">Dubai South — DS</option>
                    <option value="Dubai Internet City (TECOM)" data-emirate="Dubai" data-zone="Freezone">Dubai Internet City (TECOM)</option>
                    <option value="Dubai Media City (TECOM)" data-emirate="Dubai" data-zone="Freezone">Dubai Media City (TECOM)</option>
                    <option value="Dubai Knowledge Park (TECOM)" data-emirate="Dubai" data-zone="Freezone">Dubai Knowledge Park (TECOM)</option>
                    <option value="Dubai Production City (TECOM)" data-emirate="Dubai" data-zone="Freezone">Dubai Production City (TECOM)</option>
                    <option value="Dubai Studio City (TECOM)" data-emirate="Dubai" data-zone="Freezone">Dubai Studio City (TECOM)</option>
                    <option value="Dubai Outsource City (TECOM)" data-emirate="Dubai" data-zone="Freezone">Dubai Outsource City (TECOM)</option>
                    <option value="Dubai Science Park (TECOM)" data-emirate="Dubai" data-zone="Freezone">Dubai Science Park (TECOM)</option>
                    <option value="Dubai Design District (d3)" data-emirate="Dubai" data-zone="Freezone">Dubai Design District (d3)</option>
                    <option value="Dubai Maritime City" data-emirate="Dubai" data-zone="Freezone">Dubai Maritime City</option>
                    <option value="Dubai Maritime City Authority — DMCA" data-emirate="Dubai" data-zone="Freezone">Dubai Maritime City Authority — DMCA</option>
                    <option value="Dubai Healthcare City (DHCC)" data-emirate="Dubai" data-zone="Freezone">Dubai Healthcare City (DHCC)</option>
                    <option value="Dubai Healthcare City Authority — DHCC" data-emirate="Dubai" data-zone="Freezone">Dubai Healthcare City Authority — DHCC</option>
                    <option value="Dubai CommerCity" data-emirate="Dubai" data-zone="Freezone">Dubai CommerCity</option>
                    <option value="Dubai Development Authority — DDA" data-emirate="Dubai" data-zone="Freezone">Dubai Development Authority — DDA</option>
                    <option value="Dubai Humanitarian — DH" data-emirate="Dubai" data-zone="Freezone">Dubai Humanitarian — DH</option>
                    <option value="Trakhees — Trakhees" data-emirate="Dubai" data-zone="Freezone">Trakhees — Trakhees</option>
                    <option value="National Industries Park — NIP" data-emirate="Dubai" data-zone="Freezone">National Industries Park — NIP</option>
                    <option value="Expo City Dubai — Expo City" data-emirate="Dubai" data-zone="Freezone">Expo City Dubai — Expo City</option>
                    <option value="SHAMS (Sharjah Media City)" data-emirate="Sharjah" data-zone="Freezone">SHAMS (Sharjah Media City)</option>
                    <option value="Sharjah Media City (SHAMS)" data-emirate="Sharjah" data-zone="Freezone">Sharjah Media City (SHAMS)</option>
                    <option value="Sharjah Media City — SHAMS" data-emirate="Sharjah" data-zone="Freezone">Sharjah Media City — SHAMS</option>
                    <option value="SAIF Zone (Sharjah Airport International Free Zone)" data-emirate="Sharjah" data-zone="Freezone">SAIF Zone (Sharjah Airport International Free Zone)</option>
                    <option value="Sharjah Airport International Free Zone — SAIF Zone" data-emirate="Sharjah" data-zone="Freezone">Sharjah Airport International Free Zone — SAIF Zone</option>
                    <option value="HFZA (Hamriyah Free Zone)" data-emirate="Sharjah" data-zone="Freezone">HFZA (Hamriyah Free Zone)</option>
                    <option value="Hamriyah Free Zone Authority — HFZA" data-emirate="Sharjah" data-zone="Freezone">Hamriyah Free Zone Authority — HFZA</option>
                    <option value="SPCFZ (Sharjah Publishing City Free Zone)" data-emirate="Sharjah" data-zone="Freezone">SPCFZ (Sharjah Publishing City Free Zone)</option>
                    <option value="Sharjah Publishing City Free Zone — SPC" data-emirate="Sharjah" data-zone="Freezone">Sharjah Publishing City Free Zone — SPC</option>
                    <option value="SRTI Park (Sharjah Research Tech & Innovation)" data-emirate="Sharjah" data-zone="Freezone">SRTI Park (Sharjah Research Tech & Innovation)</option>
                    <option value="Sharjah Research, Technology and Innovation Park — SRTIP" data-emirate="Sharjah" data-zone="Freezone">Sharjah Research, Technology and Innovation Park — SRTIP</option>
                    <option value="Sharjah Healthcare City — SHCC" data-emirate="Sharjah" data-zone="Freezone">Sharjah Healthcare City — SHCC</option>
                    <option value="Sharjah Oasis for Technology and Innovation — SOTI" data-emirate="Sharjah" data-zone="Freezone">Sharjah Oasis for Technology and Innovation — SOTI</option>
                    <option value="AFZA (Ajman Free Zone)" data-emirate="Ajman" data-zone="Freezone">AFZA (Ajman Free Zone)</option>
                    <option value="Ajman Free Zone — AFZ" data-emirate="Ajman" data-zone="Freezone">Ajman Free Zone — AFZ</option>
                    <option value="Ajman Media City Free Zone (AMCFZ)" data-emirate="Ajman" data-zone="Freezone">Ajman Media City Free Zone (AMCFZ)</option>
                    <option value="Ajman Media City Free Zone — AMCFZ" data-emirate="Ajman" data-zone="Freezone">Ajman Media City Free Zone — AMCFZ</option>
                    <option value="RAKEZ (Ras Al Khaimah Economic Zone)" data-emirate="Ras Al Khaimah" data-zone="Freezone">RAKEZ (Ras Al Khaimah Economic Zone)</option>
                    <option value="Ras Al Khaimah Economic Zone — RAKEZ" data-emirate="Ras Al Khaimah" data-zone="Freezone">Ras Al Khaimah Economic Zone — RAKEZ</option>
                    <option value="RAK Maritime City" data-emirate="Ras Al Khaimah" data-zone="Freezone">RAK Maritime City</option>
                    <option value="Ras Al Khaimah Maritime City — RAKMC" data-emirate="Ras Al Khaimah" data-zone="Freezone">Ras Al Khaimah Maritime City — RAKMC</option>
                    <option value="Innovation City — IC" data-emirate="Ras Al Khaimah" data-zone="Freezone">Innovation City — IC</option>
                    <option value="RAK Digital Assets Oasis — RAK DAO" data-emirate="Ras Al Khaimah" data-zone="Freezone">RAK Digital Assets Oasis — RAK DAO</option>
                    <option value="Fujairah Free Zone (FFZ)" data-emirate="Fujairah" data-zone="Freezone">Fujairah Free Zone (FFZ)</option>
                    <option value="Fujairah Free Zone — FFZ" data-emirate="Fujairah" data-zone="Freezone">Fujairah Free Zone — FFZ</option>
                    <option value="Creative City Fujairah" data-emirate="Fujairah" data-zone="Freezone">Creative City Fujairah</option>
                    <option value="Creative City Fujairah — CCF" data-emirate="Fujairah" data-zone="Freezone">Creative City Fujairah — CCF</option>
                    <option value="UAQ FTZ (Umm Al Quwain Free Trade Zone)" data-emirate="Umm Al Quwain" data-zone="Freezone">UAQ FTZ (Umm Al Quwain Free Trade Zone)</option>
                    <option value="Umm Al Quwain Free Trade Zone — UAQ FTZ" data-emirate="Umm Al Quwain" data-zone="Freezone">Umm Al Quwain Free Trade Zone — UAQ FTZ</option>
                  </optgroup>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Trade License Expiry</label>
                <input type="text" class="form-control datepicker" name="tl_expire_date" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>TL Due</label>
                <input type="text" class="form-control" name="tl_due">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Last MOL</label>
                <input type="text" class="form-control datepicker" name="last_mol" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>No of MOL Employee</label>
                <input type="text" class="form-control only_number" name="no_of_mol_employee">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Year Ending</label>
                <input type="text" class="form-control" name="year_ending">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>CT Reg</label>
                <input type="text" class="form-control" name="ct_reg">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>TRC Reg</label>
                <input type="text" class="form-control" name="trc_reg">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>VAT Qtr</label>
                <input type="text" class="form-control" name="vat_qtr">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>FTA Remark</label>
                <input type="text" class="form-control" name="fta_remark">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Due Date</label>
                <input type="text" class="form-control datepicker" name="due_date" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>WA</label>
                <input type="text" class="form-control" name="wa">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>DB</label>
                <input type="text" class="form-control" name="db_field">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>DB Storage</label>
                <input type="text" class="form-control" name="db_storage">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>SFT</label>
                <input type="text" class="form-control" name="sft">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>AML</label>
                <input type="text" class="form-control" name="aml">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>ICV</label>
                <input type="text" class="form-control" name="icv">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>FTA Update</label>
                <input type="text" class="form-control" name="fta_update">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>FTA Username</label>
                <input type="text" class="form-control" name="fta_username">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>FTA Password</label>
                <input type="password" class="form-control" name="fta_password">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Audit 2025</label>
                <input type="text" class="form-control" name="audit_2025">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>SBR</label>
                <input type="text" class="form-control" name="sbr">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Revenue</label>
                <input type="text" class="form-control only_number" name="revenue">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Expected Date</label>
                <input type="text" class="form-control datepicker" name="expected_date" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Remarks</label>
                <input type="text" class="form-control" name="remarks">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Software Details</h3>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Software Name</label>
                <input type="text" class="form-control" name="software_name">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Software Access</label>
                <input type="text" class="form-control" name="software_access">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Soft ID</label>
                <input type="text" class="form-control" name="soft_id">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Soft Pwd</label>
                <input type="password" class="form-control" name="soft_pwd">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Tasks and Reviews</h3>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Pending Task</label>
                <input type="text" class="form-control" name="pending_task">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Pending Task - Meeting Review</label>
                <input type="text" class="form-control" name="pending_task_meeting">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Fee Discussion</label>
                <input type="text" class="form-control" name="fee_discussion">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Last MA Update</label>
                <input type="text" class="form-control datepicker" name="last_ma_update" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Last Review Date</label>
                <input type="text" class="form-control datepicker" name="last_review_date" placeholder="DD-MM-YYYY" autocomplete="off">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>CFO</label>
                <input type="text" class="form-control" name="cfo">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Audit / Review</label>
                <input type="text" class="form-control" name="audit_review">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Accounting</label>
                <input type="text" class="form-control" name="accounting">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Bookkeeping</label>
                <input type="text" class="form-control" name="bookkeeping">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>YE</label>
                <input type="text" class="form-control" name="ye">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Vat</label>
                <input type="text" class="form-control" name="vat">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>MA Frequency</label>
                <input type="text" class="form-control" name="ma_frequency">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>MIS</label>
                <input type="text" class="form-control" name="mis">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Queries/Review</label>
                <input type="text" class="form-control" name="queries_review">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Visits</label>
                <input type="text" class="form-control" name="visits">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Stock</label>
                <input type="text" class="form-control" name="stock">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Operations</label>
                <input type="text" class="form-control" name="operations">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>AML</label>
                <input type="text" class="form-control" name="aml_task">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>CFO/Consulting</label>
                <input type="text" class="form-control" name="cfo_consulting">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Internal Review</label>
                <input type="text" class="form-control" name="internal_review">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Contact and Visit Details</h3>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Location</label>
                <input type="text" class="form-control" name="location" id="kam_location">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Number</label>
                <input type="text" class="form-control" name="number" id="kam_contact">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" name="email" id="kam_email">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>V-Man</label>
                <input type="text" class="form-control" name="v_man">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>V-Exe</label>
                <input type="text" class="form-control" name="v_exe">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Frequency</label>
                <input type="text" class="form-control" name="frequency">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Monthly Visits</label>
                <input type="text" class="form-control only_number" name="monthly_visits">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Day</label>
                <input type="text" class="form-control" name="day">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="box-footer text-center" style="padding: 20px 15px; background: transparent;">
        <button type="submit" class="btn btn-danger btn-lg" style="min-width: 180px;"><i class="fa fa-user-plus"></i> Onboard Client</button>
        <a href="<?php echo base_url(); ?>manager/kam/my-client" class="btn btn-default btn-lg" style="margin-left: 10px;">Cancel</a>
      </div>

    </form>
    
  </section>
  
</div>
</div>

<script>
$(document).ready(function() {
  if ($.fn.select2) {
    $('.select2').select2();

    $('.select2-company-ajax').select2({
      placeholder: 'Type company name (min 3 characters)...',
      minimumInputLength: 3,
      allowClear: true,
      tags: true,
      createTag: function (params) {
        var term = $.trim(params.term);
        if (term === '' || term.length < 3) {
          return null;
        }
        return {
          id: term,
          text: term,
          newTag: true
        };
      },
      ajax: {
        url: '<?php echo base_url(); ?>manager/leads/ajax_search_companies',
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            term: params.term
          };
        },
        processResults: function (data) {
          return {
            results: (data || []).map(function(item) {
              return { id: item, text: item };
            })
          };
        },
        cache: true
      }
    });
  }

  if ($.fn.datepicker) {
    $('.datepicker').datepicker({
      format: 'dd-mm-yyyy',
      autoclose: true,
      todayHighlight: true
    });
  }

  $('.only_number').on('keyup keydown blur', function(e) {
    $(this).val($(this).val().replace(/[^0-9.]/g, ''));
  });

  $('#kam_company_select').on('change select2:select', function() {
    var val = $(this).val();
    if (!val || val.length < 2) return;
    $.ajax({
      url: '<?php echo base_url(); ?>manager/kam/ajax_get_company_pocs',
      type: 'GET',
      data: { company: val },
      dataType: 'json',
      success: function(resp) {
        if (resp && resp.status === 'success') {
          if (resp.primary_poc && !$('#kam_poc_name').val()) {
            $('#kam_poc_name').val(resp.primary_poc);
          }
          if (resp.contact && !$('#kam_contact').val()) {
            $('#kam_contact').val(resp.contact);
          }
          if (resp.email && !$('#kam_email').val()) {
            $('#kam_email').val(resp.email);
          }
          if (resp.location && !$('#kam_location').val()) {
            $('#kam_location').val(resp.location);
          }
          if (resp.group_name && !$('#kam_group_name').val()) {
            $('#kam_group_name').val(resp.group_name);
          }
          if (resp.tl_number && !$('#tl_number').val()) {
            $('#tl_number').val(resp.tl_number);
          }
          if (resp.tl_expire_date && !$('input[name="tl_expire_date"]').val()) {
            $('input[name="tl_expire_date"]').val(resp.tl_expire_date);
          }
          if (resp.tl_emirate) {
            $('#tl_emirate_select').val(resp.tl_emirate).trigger('change');
            if (resp.tl_zone) {
              $('#tl_zone_select').val(resp.tl_zone).trigger('change');
              if (resp.tl_authority) {
                $('#tl_authority_select').val(resp.tl_authority).trigger('change');
              }
            }
          }
        }
      }
    });
  });

  var originalAuthorityHtml = $('#tl_authority_select').html();

  function applyTradeLicenseDependencies(initialLoad) {
    var selEmirate = $('#tl_emirate_select').val() || '';
    var selZone = $('#tl_zone_select').val() || '';
    var currentAuth = $('#tl_authority_select').val() || '';

    if (!selEmirate) {
      $('#tl_zone_select').prop('disabled', true);
      if (!initialLoad) {
        $('#tl_zone_select').val('');
      }
      selZone = '';
      $('#tl_authority_select').prop('disabled', true);
      if (!initialLoad) {
        $('#tl_authority_select').val('');
      }
      currentAuth = '';
    } else {
      $('#tl_zone_select').prop('disabled', false);
      if (!selZone) {
        $('#tl_authority_select').prop('disabled', true);
        if (!initialLoad) {
          $('#tl_authority_select').val('');
        }
        currentAuth = '';
      } else {
        $('#tl_authority_select').prop('disabled', false);
      }
    }

    var tempDiv = $('<div>').html(originalAuthorityHtml);
    tempDiv.find('option').each(function() {
      var opt = $(this);
      var optVal = opt.val();
      if (!optVal) return;
      var optEm = opt.attr('data-emirate') || '';
      var optZn = opt.attr('data-zone') || '';

      var matchEm = !selEmirate || (optEm.toLowerCase() === selEmirate.toLowerCase());
      var matchZn = !selZone || (optZn.toLowerCase() === selZone.toLowerCase());

      if (!matchEm || !matchZn) {
        opt.remove();
      }
    });

    tempDiv.find('optgroup').each(function() {
      if ($(this).find('option').length === 0) {
        $(this).remove();
      }
    });

    $('#tl_authority_select').html(tempDiv.html());

    if (currentAuth && $('#tl_authority_select').find('option[value="' + currentAuth.replace(/"/g, '\\"') + '"]').length > 0) {
      $('#tl_authority_select').val(currentAuth);
    } else {
      $('#tl_authority_select').val('');
    }

    if ($.fn.select2 && $('#tl_authority_select').data('select2')) {
      $('#tl_authority_select').select2();
    }
  }

  $('#tl_emirate_select').on('change', function() {
    applyTradeLicenseDependencies(false);
  });

  $('#tl_zone_select').on('change', function() {
    applyTradeLicenseDependencies(false);
  });

  applyTradeLicenseDependencies(true);
});

var onboardUsers = <?php 
  $clean_u = array();
  if(!empty($u_list)){
    foreach($u_list as $u){
      $clean_u[] = array(
        'id' => (int)$u['id'],
        'name' => htmlspecialchars($u['name']),
        'email' => htmlspecialchars($u['email'])
      );
    }
  }
  echo json_encode($clean_u);
?>;

var curOnboardUserId = <?php echo (int)$this->session->userdata('manager_id'); ?>;
var curOnboardUserName = "<?php echo addslashes($this->session->userdata('manager_name')); ?>";

document.addEventListener("DOMContentLoaded", function() {
  var assignInput = document.getElementById("onboard_assign_input");
  var assignHidden = document.getElementById("onboard_lead_assign_val");
  var assignDropdown = document.getElementById("onboard_assign_dropdown");

  if (curOnboardUserId > 0 && curOnboardUserName !== "") {
    assignInput.value = curOnboardUserName;
  }

  assignInput.addEventListener("input", function() {
    var query = this.value.trim().toLowerCase();
    assignDropdown.innerHTML = "";
    
    if (query.length < 3) {
      assignDropdown.style.display = "none";
      return;
    }

    var matches = onboardUsers.filter(function(u) {
      return u.name.toLowerCase().indexOf(query) !== -1 || u.email.toLowerCase().indexOf(query) !== -1;
    });

    if (matches.length === 0) {
      assignDropdown.innerHTML = '<div style="padding:10px 14px; color:#64748b; font-size:13px;">No assignee found</div>';
      assignDropdown.style.display = "block";
      return;
    }

    matches.forEach(function(u) {
      var item = document.createElement("div");
      item.style.padding = "9px 14px";
      item.style.cursor = "pointer";
      item.style.borderBottom = "1px solid #f1f5f9";
      item.style.fontSize = "13px";
      item.innerHTML = '<strong>' + u.name + '</strong> <span style="color:#64748b; font-size:12px;">(' + u.email + ')</span>';
      
      item.addEventListener("mouseenter", function() {
        this.style.background = "#eff6ff";
      });
      item.addEventListener("mouseleave", function() {
        this.style.background = "#fff";
      });

      item.addEventListener("click", function() {
        assignInput.value = u.name;
        assignHidden.value = u.id;
        assignDropdown.style.display = "none";
      });

      assignDropdown.appendChild(item);
    });

    assignDropdown.style.display = "block";
  });

  document.addEventListener("click", function(e) {
    if (!assignInput.contains(e.target) && !assignDropdown.contains(e.target)) {
      assignDropdown.style.display = "none";
    }
  });
});

function clearOnboardAssign() {
  document.getElementById("onboard_assign_input").value = "";
  document.getElementById("onboard_lead_assign_val").value = "";
  document.getElementById("onboard_assign_dropdown").style.display = "none";
  document.getElementById("onboard_assign_input").focus();
}
</script>

<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
