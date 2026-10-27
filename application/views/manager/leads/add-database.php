<?php require_once(APPPATH."views/manager/elements/header.php"); ?>


<div class="container">
<div class="content-wrapper"> 
  
  
  
  <section class="content-header">
    <h1> <?php echo isset($info['page_heading']) ? $info['page_heading'] : 'Add Database'; ?> </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo base_url(); ?>manager/dashboard"><i class="fa fa-dashboard"></i>Home</a></li>
      <li><a href="<?php echo base_url(); ?>manager/leads">Database</a></li>
      <li class="active"><?php echo isset($info['page_heading']) ? $info['page_heading'] : 'Add Database'; ?></li>
    </ol>
  </section>
  
  
  
  <section class="content"> 
    
    
    
   
     <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> <?php echo get_message($this->session->flashdata('message'),'message'); ?>
     
      <form role="form" action="<?php echo base_url(); ?>manager/leads/add" method="post" name="add_form">
      
       <div class="box box-default">
      <div class="box-header with-border" style="position: relative; text-align: center; padding: 14px 15px; min-height: 48px;">
        <h3 class="box-title" style="float: none; display: inline-block; font-weight: 600; font-size: 18px; margin: 0; line-height: 24px;">Initial Category Fields</h3>
        <div style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); display: inline-flex; align-items: center; gap: 8px;">
          <span id="topZoneIndicator" class="label" style="background: #000000; color: #ffffff; font-weight: 700; padding: 4px 12px; border-radius: 4px; font-size: 12px; letter-spacing: 0.5px; display: none;"></span>
          <span id="topDncrIndicator" class="label label-danger" style="background: #dc2626; color: #ffffff; font-weight: 700; padding: 4px 12px; border-radius: 4px; font-size: 12px; letter-spacing: 0.5px; display: none;">DNCR</span>
          <span id="topClosedIndicator" class="label label-primary" style="background: #2563eb; color: #ffffff; font-weight: 700; padding: 4px 12px; border-radius: 4px; font-size: 12px; letter-spacing: 0.5px; display: none;">Closed</span>
        </div>
      </div>
      
      
      
      <div class="box-body" >
      
        <div class="row">
         
          
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Company Name <span class="text-danger">*</span></label>
                <select name="customer" id="company_customer_select" class="form-control select2-company-ajax" style="width: 100%;" required>
                  <option value="">Select or type company name...</option>
                  <?php if (!empty($company_list)) { foreach ($company_list as $cmp) { if (!empty($cmp['customer'])) { ?>
                  <option value="<?php echo htmlspecialchars($cmp['customer']); ?>"><?php echo htmlspecialchars($cmp['customer']); ?></option>
                  <?php } } } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>POC Name</label>
                <input type="text" class="form-control" name="poc">
              </div>
            </div>
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>POC Contact Number</label>
                <input type="text" class="form-control" name="contact1">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Alternative Contact Number</label>
                <input type="text" class="form-control" name="alt_contact" placeholder="Alternative Contact Number">
              </div>
            </div>

             <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" name="email">
              </div>
            </div>
           
           
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Account Number</label>
                <input type="text" class="form-control" name="a_number">
              </div>
            </div>
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Account Under Vertical</label>
                <input type="text" class="form-control" name="a_department">
              </div>
            </div>
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Current Monthly Revenue Amount</label>
                <input type="text" class="form-control only_number" name="r_amount">
              </div>
            </div>
              <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Current Monthly Revenue Date</label>
                <input type="text" class="form-control datepicker" name="r_date">
              </div>
            </div>

             <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Category</label>
                <select class="form-control" style="width: 100%;" name="category">
                <option value="">Select</option>
                  <option value="New" selected="selected">New</option>
                  <option value="Existing">Existing</option>                  
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Company Status</label>
                <select class="form-control" style="width: 100%;" name="company_status" id="company_status_select">
                  <option value="Active" selected="selected">Active</option>
                  <option value="Inactive">Inactive</option>
                  <option value="Closed">Closed</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Industry Category</label>
                <select class="form-control" style="width: 100%;" name="industry_category">
                  <option value="">Select Industry Category</option>
                  <?php if(isset($business_categories) && !empty($business_categories)){
                    foreach($business_categories as $bcat){ ?>
                      <option value="<?php echo htmlspecialchars($bcat['title']); ?>"><?php echo htmlspecialchars($bcat['title']); ?></option>
                  <?php } } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Corporate Tax Status</label>
                <select class="form-control" style="width: 100%;" name="corporate_tax_status">
                  <option value="Not Registered" selected="selected">Not Registered</option>
                  <option value="Registered">Registered</option>
                  <option value="Not Applicable">Not Applicable</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>VAT Status</label>
                <select class="form-control" style="width: 100%;" name="vat_status">
                  <option value="Not Registered" selected="selected">Not Registered</option>
                  <option value="Registered">Registered</option>
                  <option value="Not Applicable">Not Applicable</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>DNCR/DND</label>
                <select class="form-control" name="dnd_status">
                  <option value="No" selected="selected">No</option>
                  <option value="Yes">Yes</option>
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>DNCR Date</label>
                <input type="text" class="form-control datepicker" name="dnd_check_date" placeholder="DD-MM-YYYY">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Service Category</label>
                <select class="form-control" style="width: 100%;" name="s_cat" id="pcatselect" onchange="getpSubCat()">
                <option value="">Select Category</option>
               
                  <?php if(isset($p_cat) && !empty($p_cat)){
                    foreach($p_cat as $cli){
                  ?>
                  <option value="<?php echo $cli['id']; ?>" ><?php echo $cli['title']; ?></option>
              <?php } }  ?>                 
                </select>
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Product Category</label>
                <select class="form-control" style="width: 100%;" name="p_cat" id="psubcatselect">
                <option value="">Select</option>
                                              
                </select>
              </div>
            </div>
              <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Product Details</label>
                <input type="text" class="form-control" name="p_details"  >
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Trade License Number</label>
                <input type="text" class="form-control" name="tl_number" placeholder="Enter Trade License Number">
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
                <label>Trade License Issue Date</label>
                <input type="text" class="form-control datepicker" name="tl_issue_date" placeholder="DD-MM-YYYY">
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Trade License Expiry</label>
                <input type="text" class="form-control datepicker" name="tl_expire_date" placeholder="DD-MM-YYYY">
              </div>
            </div>

             <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Qty</label>
                <input type="text" class="form-control only_number" name="qty" >
              </div>
            </div>
             <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>MRC</label>
                <input type="text" name="mrc" class="form-control only_number" >
              </div>
            </div>
             <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Revenue</label>
                <input type="text" name="revenue" class="form-control only_number" >
              </div>
            </div>
            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>MRR</label>
                <input type="text" name="mrr" class="form-control only_number" readonly >
              </div>
            </div>
            
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Location</label>
                <input type="text" class="form-control" name="location">
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Quote Status</label>
                <select class="form-control" style="width: 100%;" name="quote_status">
                  <option value="Not Given" selected="selected">Not Given</option>
                  <option value="Quote Given">Quote Given</option>
                  <option value="Proposal Sent">Proposal Sent</option>
                  <option value="Under Review">Under Review</option>
                  <option value="Revised Quote">Revised Quote</option>
                  <option value="Accepted">Accepted</option>
                  <option value="Rejected">Rejected</option>
                </select>
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Quote Mode</label>
                <select class="form-control" style="width: 100%;" name="quote_mode">
                  <option value="">Select Quote Mode</option>
                  <option value="Email">Email</option>
                  <option value="WhatsApp">WhatsApp</option>
                  <option value="SMS/Text">SMS/Text</option>
                  <option value="Phone Call/Verbal">Phone Call/Verbal</option>
                  <option value="Other">Other</option>
                </select>
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Quote Amount</label>
                <input type="number" step="0.01" class="form-control" name="quote_amount" placeholder="0.00">
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Status<span>*</span></label>
                <select class="form-control" required name="status" id="lead_status_select">
                  <option value="">Select Status</option>
                  <?php if(isset($st_list) && !empty($st_list)){
                    foreach($st_list as $cli){ ?>
                      <option value="<?php echo $cli['id']; ?>"><?php echo $cli['status']; ?></option>
                  <?php } } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-4 col-sm-6" id="status_category_col">
              <div class="form-group">
                <label>Status Category</label>
                <select class="form-control" id="status_category_dropdown" style="margin-bottom: 5px;">
                  <option value="">Select Status Category</option>
                </select>
                <input type="text" class="form-control" name="status_category" id="status_category_input" placeholder="Enter custom status category..." style="display:none;">
                <input type="hidden" name="lost_reason" id="hidden_lost_reason">
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Status Remarks</label>
                <input type="text" class="form-control" name="status_remarks" placeholder="Enter status remarks / explanation">
              </div>
            </div>
           
             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Exp. Close Date</label>
                <input type="text" class="form-control datepicker2" name="close_date" id="close_date"  >
              </div>
            </div>

            <div class="col-md-12">
              <div class="form-group">
                <label>Remarks</label>
                <textarea class="form-control" name="remark"></textarea>
              </div>
            </div>

          </div>
        </div>
      </div>

       <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">More Category Field</h3>
      </div>
      
      
      
      <div class="box-body" >
      
        <div class="row">


           <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" class="form-control" />
              </div>
            </div>

           
           

           <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Country</label>
                <select id="country" class="form-control" name="country" onchange="get_state()">                          
                <option value="">Select</option>
                            <?php if(isset($c_list) && !empty($c_list)){
                              foreach($c_list as $cli){
                            ?>
                            <option value="<?php echo $cli['id']; ?>" ><?php echo $cli['name']; ?></option>
                        <?php } }  ?>
                      </select>
              </div>
            </div>


            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>State / Province / Emirate</label>
                 <select id="state" class="form-control" name="state">
                 <option value="">Select</option> 
                 </select>
                
              </div>
            </div>

             <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>City</label>
                <input type="text" class="form-control" name="city"  >
              </div>
            </div>

             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Alternate POC Name</label>
                <input type="text" class="form-control" name="poc2">
              </div>
            </div>
             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Alternate POC Contact Number</label>
                <input type="text" class="form-control" name="contact2">
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Channel partner</label>
               
                <select class="form-control" style="width: 100%;" name="c_partner">
                  <option value="SRJ Electronics Trading LLC" selected="selected">SRJ Electronics Trading LLC</option>
                                    
                </select>
              </div>
            </div>

             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>One time charges</label>
                <input type="text" class="form-control" name="otc"  >
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Lead Category</label>
               
                 <select class="form-control" style="width: 100%;" name="lead_cat" >
                <option value="">Select Category</option>
               
                  <?php if(isset($lc_list) && !empty($lc_list)){
                    foreach($lc_list as $cli){
                  ?>
                  <option value="<?php echo $cli['id']; ?>" ><?php echo $cli['title']; ?></option>
              <?php } }  ?>                 
                </select>
              </div>
            </div>
            <?php 
              $cur_user_role = (int)$this->session->userdata('manager_role');
              if ($cur_user_role == 1 || $cur_user_role == 4) { 
                $my_id = (int)$this->session->userdata('manager_id');
            ?>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Primary Owner</label>
                <select class="form-control select2" name="primary_owner" style="width: 100%;">
                  <?php if(isset($u_list) && !empty($u_list)) { 
                    foreach($u_list as $li){
                      $is_owner = ((int)$li['id'] === $my_id);
                  ?>
                    <option <?php if($is_owner){ echo "selected"; } ?> value="<?php echo $li['id']; ?>"><?php echo htmlspecialchars($li['name']); ?></option>          
                  <?php } } ?>
                </select>
              </div>
            </div>
            <?php } ?>
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Database Assigned to Collaborators</label>
                <select class="form-control select2" name="lead_assign[]" multiple="multiple" data-placeholder="Select Collaborator(s)" style="width: 100%;">
                  <?php if(isset($u_list) && !empty($u_list)) { 
                    $my_id = (int)$this->session->userdata('manager_id');
                    foreach($u_list as $li){
                      if((int)$li['id'] === $my_id){ continue; }
                  ?>
                    <option value="<?php echo $li['id']; ?>"><?php echo htmlspecialchars($li['name']); ?></option>          
                  <?php } } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Website / Source Link</label>
                <input type="text" class="form-control" name="lead_source">
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Data Source</label>
                <select class="form-control" style="width: 100%;" name="data_source">
                  <option value="">Select Data Source</option>
                  <?php 
                    $avail_sources = array();
                    if(isset($data_sources) && !empty($data_sources)){
                      foreach($data_sources as $ds_item){
                        $avail_sources[] = $ds_item['title'];
                      }
                    }
                    if(empty($avail_sources)){
                      $avail_sources = array('Google Maps', 'GPS', 'Referral', 'Website', 'Social Media', 'Other');
                    }
                    foreach($avail_sources as $src_opt){ ?>
                      <option value="<?php echo htmlspecialchars($src_opt); ?>"><?php echo htmlspecialchars($src_opt); ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Data Batch Referral Number</label>
                <input type="text" class="form-control" name="data_batch_referral_no" placeholder="Enter Data Batch / Referral No">
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Database Category</label>
                <select class="form-control" style="width: 100%;" name="database_category">
                  <option value="">Select Database Category</option>
                  <?php 
                    $avail_db_cats = array();
                    if(isset($database_categories) && !empty($database_categories)){
                      foreach($database_categories as $dc_item){
                        $avail_db_cats[] = $dc_item['title'];
                      }
                    }
                    if(empty($avail_db_cats)){
                      $avail_db_cats = array('New Database', 'Online', 'Reference', 'Ported Out', 'Competitor DB', 'Contract Agent', 'Raw / Unverified', 'Cleansed', 'High Value', 'Re-engaged', 'Other');
                    }
                    foreach($avail_db_cats as $db_opt){ ?>
                      <option value="<?php echo htmlspecialchars($db_opt); ?>"><?php echo htmlspecialchars($db_opt); ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
           
        
        </div>
      </div>
        
         
        
      </div>


      <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Followup Field</h3>
      </div>
      
      
      
      <div class="box-body" >
      
        <div class="row">

            <div class="col-lg-2 col-sm-6">
              <div class="form-group">
                <label>Next Follow Up Date</label>
                <input type="text" class="form-control datepicker2" name="followup_date" id="followup_date"  >
              </div>
            </div>

            <div class="col-lg-2 col-sm-6">
              <div class="form-group">
                <label>Next Follow Up Time</label>
               
                <div class="input-group bootstrap-timepicker timepicker">
                  <input  type="text" class="form-control input-small timepicker" name="followup_time">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                </div>
              </div>
            </div>

            <div class="col-lg-8 col-sm-6">
              <div class="form-group">
                <label>Remark</label>
                <input type="text" class="form-control" name="followup_remark"  >
              </div>
            </div>

        </div>
        </div>
        </div>


         <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Meeting Field</h3>
      </div>
      
      
      
      <div class="box-body" >

      <div class="row remark_row">
          <div class="col-md-7">

        <div class="row">

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Next Meeting Date</label>
                <input type="text" class="form-control datepicker2" name="m_date"  >
              </div>
            </div>
            
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Next Meeting Time</label>
               
                <div class="input-group bootstrap-timepicker timepicker">
                  <input  type="text" class="form-control input-small timepicker" name="m_time">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                </div>
              </div>
            </div>

               <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>POC</label>
                <input type="text" class="form-control" name="m_poc"  >
              </div>
            </div>

		</div>
        <div class="row">
        


            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Attendee 1</label>
                <select class="form-control select2" name="a1" style="width: 100%;">
                <option value="">Select</option>
                <?php if(isset($u_list) && !empty($u_list))
                  { 
                    foreach($u_list as $li){
                  ?>

                  <option value="<?php echo $li['id']; ?>"><?php echo $li['name']; ?></option>          

               <?php } } ?>
                </select>


              </div>
            </div>


            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Attendee 2</label>

                <select class="form-control select2" name="a2" style="width: 100%;">
                <option value="">Select</option>
                <?php if(isset($u_list) && !empty($u_list))
                  { 
                    foreach($u_list as $li){
                  ?>

                  <option value="<?php echo $li['id']; ?>"><?php echo $li['name']; ?></option>          

               <?php } } ?>
                </select>

              </div>
            </div>


            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Mobile</label>
                <input type="text" class="form-control" name="m_mobile"  >
              </div>
            </div>


        </div>



          </div>

          <div class="col-md-5">

          <div class="form-group remark-group">
                <label>Remark</label>
                <textarea type="text" class="form-control" name="m_remark"></textarea>
              </div>
          </div>

      </div>
      
        </div>
        </div>


         <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Vertical Selection</h3>
      </div>
      
      
      
      <div class="box-body" >
      
        <div class="row">
       
          <div class="col-lg-2 col-sm-6">
              <div class="form-group">
                <label>Select Vertical</label>
                <select class="form-control select2" name="vertical_id" style="width: 100%;">
                <option value="">Select</option>
                <?php if(isset($v_list) && !empty($v_list))
                  { 
                    foreach($v_list as $li){
                  ?>

                  <option value="<?php echo $li['id']; ?>"><?php echo $li['poc']; ?>- <?php echo $li['company_name']; ?></option>          

               <?php } } ?>
            </select>
              </div>
            </div>

          <div class="col-lg-2 col-sm-6">
              <div class="form-group">
                <label>Amount to Pay</label>
                <input type="text" class="form-control only_number" name="v_amount"  >
              </div>
            </div>

            <div class="col-lg-8 col-sm-6">
              <div class="form-group">
                <label>Remark</label>
                <input type="text" class="form-control" name="v_remark">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="box box-default">
      <div class="box-header">
		<div class="col-sm-12">
       		<h3 class="box-title">Back Office Updates</h3>
        </div>
        </div>
<div class="box-body" >
      
        <div class="row">
  
    <div class="table-responsive">

          <table class="table table-border">
            <tr>
              <th>Category</th>
              <th>Date</th>
              <th>Status</th>
              <th>Reference</th>
              <th>Remarks</th>
            </tr>
            <tr>
              <td><input type="text" class="form-control" name="bo_cat[]" value="SPR" readonly=""></td>
              <td><input type="text" class="form-control datepicker" name="bo_date[]"  ></td>
              <td>
                <select class="form-control" style="width: 100%;" name="bo_status[]">
                  <option value="Not Applicable" selected="selected">Not Applicable</option>
                  <option value="Pending Approval">Pending Approval</option>
                  <option value="Query">Query</option>
                  <option value="Approved">Approved</option> 
                  <option value="Expired">Expired</option> 
                  <option value="Other">Other</option>                    
                </select>
              </td>
              <td><input type="text" class="form-control" name="bo_ref[]" ></td>
              <td><input type="text" class="form-control" name="bo_remark[]" ></td>
            </tr>
            <tr>
              <td><input type="text" class="form-control" name="bo_cat[]" value="Company Code" readonly=""></td>
              <td><input type="text" class="form-control datepicker" name="bo_date[]"  ></td>
              <td>
                <select class="form-control" style="width: 100%;" name="bo_status[]">
                  <option value="Not Applicable" selected="selected">Not Applicable</option>
                  <option value="Pending Approval">Pending Approval</option>
                  <option value="Query">Query</option>
                  <option value="Approved">Approved</option> 
                  <option value="Expired">Expired</option> 
                  <option value="Other">Other</option>                    
                </select>
              </td>
              <td colspan="2"><input type="text" class="form-control" name="bo_remark[]" ></td>
            </tr>
            <tr>
              <td><input type="text" class="form-control" name="bo_cat[]" value="Lead Activity" readonly=""></td>
              <td><input type="text" class="form-control datepicker" name="bo_date[]"  ></td>
              <td>
                <select class="form-control" style="width: 100%;" name="bo_status[]">
                  <option value="Not Applicable" selected="selected">Not Applicable</option>
                  <option value="Pending Approval">Pending Approval</option>
                  <option value="Query">Query</option>
                  <option value="Approved">Approved</option> 
                  <option value="Expired">Expired</option> 
                  <option value="Other">Other</option>                    
                </select>
              </td>
              <td><input type="text" class="form-control" name="bo_ref[]" ></td>
              <td><input type="text" class="form-control" name="bo_remark[]" ></td>
            </tr>
            <tr>
              <td><input type="text" class="form-control" name="bo_cat[]" value="Document Col/Sub" readonly=""></td>
              <td><input type="text" class="form-control datepicker" name="bo_date[]"  ></td>
              <td>
                <select class="form-control" style="width: 100%;" name="bo_status[]">
                  <option value="Not Applicable" selected="selected">Not Applicable</option>
                  <option value="Pending Approval">Pending Approval</option>
                  <option value="Query">Query</option>
                  <option value="Approved">Approved</option> 
                  <option value="Expired">Expired</option> 
                  <option value="Other">Other</option>                    
                </select>
              </td>
              <td><input type="text" class="form-control" name="bo_ref[]" ></td>
              <td><input type="text" class="form-control" name="bo_remark[]" ></td>
            </tr>
            <tr>
              <td><input type="text" class="form-control" name="bo_cat[]" value="Pending Documents" readonly=""></td>
              <td><input type="text" class="form-control datepicker" name="bo_date[]"  ></td>
              <td>
                <select class="form-control" style="width: 100%;" name="bo_status[]">
                  <option value="Not Applicable" selected="selected">Not Applicable</option>
                  <option value="Pending Approval">Pending Approval</option>
                  <option value="Query">Query</option>
                  <option value="Approved">Approved</option> 
                  <option value="Expired">Expired</option> 
                  <option value="Other">Other</option>                    
                </select>
              </td>
              <td colspan="2"><input type="text" class="form-control" name="bo_remark[]" ></td>
            </tr>
            <tr>
              <td><input type="text" class="form-control" name="bo_cat[]" value="Video Verification" readonly=""></td>
              <td><input type="text" class="form-control datepicker" name="bo_date[]"  ></td>
              <td>
                <select class="form-control" style="width: 100%;" name="bo_status[]">
                  <option value="Not Applicable" selected="selected">Not Applicable</option>
                  <option value="Pending Approval">Pending Approval</option>
                  <option value="Query">Query</option>
                  <option value="Approved">Approved</option> 
                  <option value="Expired">Expired</option> 
                  <option value="Other">Other</option>                    
                </select>
              </td>
              <td><input type="text" class="form-control" name="bo_ref[]" ></td>
              <td><input type="text" class="form-control" name="bo_remark[]" ></td>
            </tr>

             <tr>
              <td><input type="text" class="form-control" name="bo_cat[]" value="Post Verification" readonly=""></td>
              <td><input type="text" class="form-control datepicker" name="bo_date[]"  ></td>
              <td>
                <select class="form-control" style="width: 100%;" name="bo_status[]">
                  <option value="Not Applicable" selected="selected">Not Applicable</option>
                  <option value="Pending Approval">Pending Approval</option>
                  <option value="Query">Query</option>
                  <option value="Approved">Approved</option> 
                  <option value="Expired">Expired</option> 
                  <option value="Other">Other</option>                    
                </select>
              </td>
              <td><input type="text" class="form-control" name="bo_ref[]" ></td>
              <td><input type="text" class="form-control" name="bo_remark[]" ></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
      
      <div class="box-footer text-center">
        <button id="add_btn" type="submit" class="btn btn-info">Submit</button>
        <button type="reset" class="btn btn-default">Cancel</button>
      </div>
      </form> 
    
  </section>
  
   
  
</div>

 
</div>
<script>







 $(document).ready(function () { 

  $(document).on("click", "#add_btn", function () {

    

   function checkCloseDateValidity() {
     var fu = $('#followup_date').val();
     var cd = $('#close_date').val();
     if (fu && cd) {
       var fuP = fu.split('-');
       var cdP = cd.split('-');
       if (fuP.length === 3 && cdP.length === 3) {
         var fuDate = new Date(fuP[2], fuP[1] - 1, fuP[0]);
         var cdDate = new Date(cdP[2], cdP[1] - 1, cdP[0]);
         if (cdDate < fuDate) {
           alert('Exp. Close Date cannot be previous or past of the Follow Up Date (' + fu + ')!');
           $('#close_date').val(fu);
           return false;
         }
       }
     }
     return true;
   }

   $('#close_date, #followup_date').on('change changeDate', function() {
     checkCloseDateValidity();
   });

   $('form[name=add_form]').validate({         
     submitHandler: function(form) {
       if (!checkCloseDateValidity()) {
         return false;
       }
       form.submit();
     }
   });

 });


var input = $('[name="mrc"],[name="revenue"]'),
    input1 = $('[name="mrc"]'),
    input2 = $('[name="revenue"]'),
    input3 = $('[name="mrr"]');
input.change(function () {
    input3.val((parseInt(input1.val()) || 0) + (parseInt(input2.val()) || 0));
});



});




</script>
<script>

   function get_state()
{
  
      var stateID = $("#country").val();     
        $.ajax({
              type: "POST",
        data: 'c_id='+stateID,
          url: "<?php echo base_url(); ?>" + "ci_admin_user/get_state",
        success:function(result){
        if(result)
        {
        $('#state').html(result);
        }
        else
        {
        alert('error');
        }
        }
          });


}

function check_cname()
{
  
      var c_name =$(".c_username").val();     
        $.ajax({
              type: "POST",
        data: 'c_name='+c_name,
          url: "<?php echo base_url(); ?>" + "ci_admin_user/check_name",
        success:function(data){
        if(data=='done')
        {
        $('.alert_msg').text('');
        }
        else
        {
        $('.c_username').val('');
        $('.alert_msg').text('Company name already exist.');
        }
        }
          });


}

function getpSubCat()
{  
        var stateID = $("#pcatselect").val();     
        $.ajax({
          type: "POST",
          data: 'c_id='+stateID,
          url: "<?php echo base_url(); ?>" + "ci_admin_terms/getSubCatAjax",
          success:function(result){
            if(result){
              $('#psubcatselect').html(result);
            }           
        }
        });
}

$(document).ready(function() {
  var statusCategoryMap = {
    '43': [
      'Pricing / Budget Constraints',
      'Competitor Chosen',
      'Product / Requirements Mismatch',
      'Client Postponed / Not Interested',
      'Unreachable / Dropped Contact',
      'Other'
    ],
    '44': [
      'Wrong Number / Invalid Contact',
      'Fake / Spam Enquiry',
      'Duplicate Lead Entry',
      'Not Interested / Out of Scope',
      'Out of Service Territory',
      'Other'
    ],
    '36': [
      'Raw Database',
      'Cleansed Lead',
      'Cold Prospect',
      'Direct Reference',
      'Other'
    ],
    '37': [
      'Proposal Under Discussion',
      'Decision Maker Pending',
      'Budget Approval Awaited',
      'Expected This Month',
      'Other'
    ],
    '38': [
      'Follow-up in Progress',
      'Negotiation Ongoing',
      'KYC / Document Collection',
      'Awaiting Client Feedback',
      'Other'
    ],
    '39': [
      'Successfully Converted',
      'Contract Signed',
      'Payment Received',
      'Other'
    ],
    '40': [
      'Documents Submitted',
      'Form Submitted to Operator',
      'Back Office Review',
      'Other'
    ],
    '41': [
      'Sim / Service Dispatched',
      'Activation Under Process',
      'Verification Pending',
      'Other'
    ],
    '42': [
      'Service Live',
      'Handover Completed',
      'First Bill Generated',
      'Other'
    ],
    '45': [
      'No Answer / Ringing',
      'Line Busy / Engaged',
      'Switched Off / Unreachable',
      'Call Disconnected',
      'Other'
    ],
    '46': [
      'DND Registered',
      'Do Not Call Request',
      'Customer Refusal',
      'Other'
    ],
    '47': [
      'Callback Requested',
      'Awaiting Decision Maker',
      'Call After 2 Days',
      'Call Next Week',
      'Call Next Month',
      'Other'
    ],
    '48': [
      'Future Requirement (1-3 Months)',
      'Future Requirement (3-6 Months)',
      'Budget Available Next Year',
      'Contract Renewal Later',
      'Other'
    ],
    '49': [
      'Not Interested in Service',
      'Currently Satisfied with Competitor',
      'No Requirement',
      'Price Too High',
      'Other'
    ],
    '50': [
      'Wrong Number / Invalid Contact',
      'Disconnected Number',
      'Company Closed / Ceased Operations',
      'Duplicate Entry',
      'Other'
    ],
    'default': [
      'General Enquiry',
      'Follow-up Required',
      'High Priority',
      'Decision Maker Unavailable',
      'Other'
    ]
  };

  function updateStatusCategoryOptions(selectedStatus, preserveVal) {
    var opts = statusCategoryMap[selectedStatus] || statusCategoryMap['default'];
    var $dd = $('#status_category_dropdown');
    var currentInputVal = preserveVal || $('#status_category_input').val();
    $dd.empty().append('<option value="">Select Status Category</option>');

    var matched = false;
    for (var i = 0; i < opts.length; i++) {
      var opt = opts[i];
      var isSel = (currentInputVal && currentInputVal === opt);
      if (isSel) matched = true;
      $dd.append($('<option></option>').attr('value', opt).text(opt).prop('selected', isSel));
    }

    if (currentInputVal && !matched) {
      $dd.val('Other');
      $('#status_category_input').show().val(currentInputVal);
    } else if (matched && currentInputVal !== 'Other') {
      $('#status_category_input').hide().val(currentInputVal);
    } else if (currentInputVal === 'Other') {
      $('#status_category_input').show().val('');
    } else {
      $('#status_category_input').hide();
    }
    $('#hidden_lost_reason').val($('#status_category_input').val());
  }

  $('#lead_status_select').on('change', function() {
    updateStatusCategoryOptions($(this).val(), null);
  });

  $('#status_category_dropdown').on('change', function() {
    var chosen = $(this).val();
    if (chosen === 'Other') {
      $('#status_category_input').show().val('').focus();
      $('#hidden_lost_reason').val('');
    } else if (chosen !== '') {
      $('#status_category_input').hide().val(chosen);
      $('#hidden_lost_reason').val(chosen);
    } else {
      $('#status_category_input').hide().val('');
      $('#hidden_lost_reason').val('');
    }
  });

  $('#status_category_input').on('input change', function() {
    $('#hidden_lost_reason').val($(this).val());
  });

  function updateTopHeaderBadges() {
    var dndVal = $('select[name="dnd_status"]').val();
    if (dndVal === 'Yes') {
      $('#topDncrIndicator').show();
    } else {
      $('#topDncrIndicator').hide();
    }
    var csVal = $('#company_status_select').val();
    if (csVal === 'Closed') {
      $('#topClosedIndicator').show();
    } else {
      $('#topClosedIndicator').hide();
    }

    var zoneVal = $('#tl_zone_select').val();
    var selectedOpt = $('#tl_authority_select option:selected');
    var detectedZone = selectedOpt.attr('data-zone') || '';

    if (!zoneVal && detectedZone) {
      $('#tl_zone_select').val(detectedZone);
      zoneVal = detectedZone;
    }

    var finalZone = zoneVal || detectedZone;
    if (finalZone && (finalZone.toLowerCase() === 'mainland' || finalZone.toLowerCase() === 'freezone')) {
      var badgeText = finalZone.toLowerCase() === 'freezone' ? 'Freezone' : 'Mainland';
      $('#topZoneIndicator').text(badgeText).show();
    } else {
      $('#topZoneIndicator').hide();
    }
  }

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
    updateTopHeaderBadges();
  });

  $('#tl_zone_select').on('change', function() {
    applyTradeLicenseDependencies(false);
    updateTopHeaderBadges();
  });

  $('#tl_authority_select').on('change select2:select', function() {
    updateTopHeaderBadges();
  });

  $('form').on('submit', function() {
    $('#tl_zone_select, #tl_authority_select').prop('disabled', false);
  });

  $('select[name="dnd_status"], #company_status_select').on('change', function() {
    updateTopHeaderBadges();
  });

  applyTradeLicenseDependencies(true);
  updateTopHeaderBadges();

  updateStatusCategoryOptions($('#lead_status_select').val(), '');

  if ($.fn.select2) {
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

});
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
