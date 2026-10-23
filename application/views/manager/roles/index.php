<?php require_once(APPPATH."views/manager/elements/header.php"); ?>
<div class="content-wrapper px-4"> 
  <section class="content-header">
   <h1><?php echo $info['page_heading']; ?></h1>
  </section>
  <section class="content"> 
    <div class="row"> 
      <div class="col-xs-12">
        <div class="box">
          <?php echo get_message($this->session->flashdata('error_message'),'error_message'); ?> <?php echo get_message($this->session->flashdata('message'),'message'); ?>
          <?php if((isset($user_list)) && (!empty($user_list))) { ?>
          <div class="box-body table-responsive" style="padding:10px 15px">
            <table id="example1" class="table table-hover table-bordered">
              <thead>
                <tr>
                  <th style="width:10px">S.No.</th>                  
                  <th>Name</th> 
                  <th>Level</th>                 
                  <th style="width:10px">Status</th>                 
                  <th>Created</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($user_list as $k=>$li) { ?>
                <tr>
                  <td><?php echo $k+1; ?></td>
                  <td><?php echo $li['title'];?></td>      
                  <td><?php echo $li['level'];?></td> 
                  <td><?php 
				  if($li['status']==0) { ?>
                    <span class="label label-warning">Disabled</span>
                    <?php } elseif($li['status']==1) { ?>
                    <span class="label label-info">Active</span>
                    <?php } ?></td>
                  <td><?php echo date('d-M-Y',strtotime($li['created'])); ?></td>
                  <td class="center hidden-phone">
                    <a href="<?php echo base_url();?>manager/roles/edit/<?php echo $li['id']; ?>" class="btn btn-primary btn-xs" data-toggle="tooltip" title="Edit"><span class="glyphicon glyphicon-edit"></span></a>
                    <?php if($li['deletable']==1){ ?> 
                    <a href="<?php echo base_url();?>manager/roles/delete/<?php echo $li['id']; ?>" onclick="return do_confirm();" class="btn btn-danger btn-xs" data-toggle="tooltip" title="Remove"><i class="fa fa-remove"></i></a>
                    <?php } ?>
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
          <?php } else { ?>
          <div class="alert alert-info">No data Found</div>
          <?php } ?> 
        </div>
      </div>
    </div>
  </section>
</div>
<script>
function do_confirm(){    
  var job = confirm("Are you sure to delete this record?");    
  if(!job) {        
    return false;    
  }
}
</script>
<?php require_once(APPPATH."views/manager/elements/footer.php"); ?>
