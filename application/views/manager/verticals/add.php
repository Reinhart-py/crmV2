<?php require_once(APPPATH."views/manager/elements/header.php"); ?>


<div class="content-wrapper"> 
  
  
  
  <section class="content-header">
    <h1> Verticals </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>
      <li class="active">Verticals</li>
    </ol>
  </section>
  
  
  
  <section class="content"> 
    
    
    
   
     <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> <?php echo get_message($this->session->flashdata('message'),'message'); ?>
     
      <form role="form" action="<?php echo base_url(); ?>manager/verticals/add" method="post" name="add_form">
      
       <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Initial Fields</h3>
      </div>
      
      
      
      <div class="box-body" >
      
        <div class="row">
         
          
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Company Name<span>*</span></label>
                <input type="text" name="company_name" class="form-control" style="text-transform: capitalize;" required="" >
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>POC Name<span>*</span></label>
                <input type="text" required class="form-control" name="poc"  >
              </div>
            </div>

            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Email-ID</label>
                <input type="email" class="form-control" name="email"  >
              </div>
            </div>

              <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>POC Contact Number</label>
                <input type="text" class="form-control" name="contact1" >
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>POC Alt Contact Number</label>
                <input type="text" class="form-control" name="contact2" >
              </div>
            </div>

            <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Whatsapp Web</label>
                <input type="text" class="form-control" name="whatsapp_web"  >
              </div>
            </div>

             <div class="col-lg-3 col-sm-6">
              <div class="form-group">
                <label>Designation</label>
                <input type="text" class="form-control" name="designation"  >
              </div>
            </div>


            <div class="col-lg-6 col-sm-6">
              <div class="form-group">
                <label>Category<span>*</span></label>
                <select class="form-control" required name="category" id="catselect" onchange="getSubCat()">
                <option value="">Select Category</option>
               
                  <?php if(isset($cat_list) && !empty($cat_list)){
                    foreach($cat_list as $cli){
                  ?>
                  <option value="<?php echo $cli['id']; ?>" ><?php echo $cli['title']; ?></option>
              <?php } }  ?>
            </select>
            </div>
            </div>

              <div class="col-lg-6 col-sm-6">
              <div class="form-group">
                <label>SubCategory</label>
                <select class="form-control" required name="sub_category" id="subcatselect">
                <option value="">Choose Sub Category</option>
               
            </select>
            </div>
            </div>

             
             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Last Follow Up Date</label>
                <input type="text" class="form-control datepicker" name="lastfollowup_date"  >
              </div>
            </div>


             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Next Follow Up Date</label>
                <input type="text" class="form-control datepicker2" name="followup_date"  >
              </div>
            </div>



            
            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Status<span>*</span></label>
                <?php
                  $db_id = 81; $act_id = 50; $inact_id = 51; $ni_id = 56;
                  if(isset($st_list) && !empty($st_list)){
                    foreach($st_list as $cli){
                      if(strcasecmp(trim($cli['title']), 'Database') === 0) $db_id = $cli['id'];
                      if(strcasecmp(trim($cli['title']), 'Active') === 0) $act_id = $cli['id'];
                      if(strcasecmp(trim($cli['title']), 'Inactive') === 0) $inact_id = $cli['id'];
                      if(strcasecmp(trim($cli['title']), 'Not Interested') === 0) $ni_id = $cli['id'];
                    }
                  }
                ?>
                <select class="form-control" required name="status">
                  <option value="<?php echo $db_id; ?>" selected="selected">Database</option>
                  <option value="<?php echo $act_id; ?>">Active</option>
                  <option value="<?php echo $inact_id; ?>">Inactive</option>
                  <option value="<?php echo $ni_id; ?>">Not Interested</option>
                </select>
              </div>
            </div>


            


           
          

             <div class="col-lg-6 col-sm-6">
              <div class="form-group">
                <label>Nationality</label>
                <select id="country2" class="form-control" name="nationality" onchange="get_state2()">                          
                <option value="">Select</option>
                            <?php if(isset($c_list) && !empty($c_list)){
                              foreach($c_list as $cli){
                            ?>
                            <option value="<?php echo $cli['id']; ?>" ><?php echo $cli['name']; ?></option>
                        <?php } }  ?>
                      </select>
              </div>
            </div>


            <div class="col-lg-6 col-sm-6">
              <div class="form-group">
                <label>Nationality Area(State / Province / Emirate)</label>
                 <select id="state2" class="form-control" name="nationality_area">
                 <option value="">Select</option> 
                 </select>
                
              </div>
            </div>

          

 <div class="col-lg-4 col-sm-6">
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


        


            <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>State / Province / Emirate</label>
                 <select id="state" class="form-control" name="state">
                 <option value="">Select</option> 
                 </select>
                
              </div>
            </div>
           

              <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>City</label>
                <input type="text" class="form-control" name="city"  >
              </div>
            </div>

           

         

              <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Source Details</label>
                <input type="text" class="form-control" name="lead_source"  >
              </div>
            </div> 


             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Address 1</label>
                <input type="text" name="address" class="form-control" />
              </div>
            </div>

             <div class="col-lg-4 col-sm-6">
              <div class="form-group">
                <label>Address 2</label>
                <input type="text" name="address2" class="form-control" />
              </div>
            </div>

        


         

            <div class="col-md-12">
              <div class="form-group">
                <label>Remark</label>
                <textarea class="form-control" name="remark" ></textarea>
              </div>
            </div> 


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

 
<script>

 $(document).ready(function () { 

  $(document).on("click", "#add_btn", function () {

    

   $('form[name=add_form]').validate({         

  submitHandler: function(form) {

      form.submit();

    }

  });

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
            if(result){
              $('#state').html(result);
            }           
        }
        });
}
function get_state2()
{  
        var stateID = $("#country2").val();     
        $.ajax({
          type: "POST",
          data: 'c_id='+stateID,
          url: "<?php echo base_url(); ?>" + "ci_admin_user/get_state",
          success:function(result){
            if(result){
              $('#state2').html(result);
            }           
        }
        });
}

function getSubCat()
{  
        var stateID = $("#catselect").val();     
        $.ajax({
          type: "POST",
          data: 'c_id='+stateID,
          url: "<?php echo base_url(); ?>" + "ci_admin_terms/getSubCatAjax",
          success:function(result){
            if(result){
              $('#subcatselect').html(result);
            }           
        }
        });
}

</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
