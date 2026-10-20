<footer class="crm-footer">
<span>&copy; 2026 Rolexto CRM Platform. All rights reserved.</span>
<span>Rolexto Real Estate &amp; Business Setup</span>
</footer>
</div>
</div>
<script src="<?php echo base_url(); ?>assets/plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/bootstrap/js/jquery.validate.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/colorpicker/bootstrap-colorpicker.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/fastclick/fastclick.js"></script>
<script src="<?php echo base_url(); ?>assets/dist/js/demo.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/select2/select2.full.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/input-mask/jquery.inputmask.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/input-mask/jquery.inputmask.extensions.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/daterangepicker/daterangepicker.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/timepicker/bootstrap-timepicker.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/input-mask/jquery.inputmask.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/input-mask/jquery.inputmask.extensions.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/chartjs/Chart.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/multiselect/multiselect-js.js"></script>

<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.2.7/js/dataTables.rowReorder.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.5/js/dataTables.responsive.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/countdown/countdown-timer.js"></script>
<script src="<?php echo base_url(); ?>assets/dist/js/app.min.js"></script>
<script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/jquery.geocomplete.js"></script> 
<script src="<?php echo base_url(); ?>assets/plugins/uploadify/js/jquery.dm-uploader.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/uploadify/demo-ui.js"></script>
 <script src="https://lipis.github.io/bootstrap-sweetalert/dist/sweetalert.js"></script>
    <link rel="stylesheet" href="https://lipis.github.io/bootstrap-sweetalert/dist/sweetalert.css" />
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAY0iXbSmvQSxjus3PEJoKnJZreLXQuP1Y&amp;libraries=places"></script> 

<script>
  $(function () {

    $('form').attr('autocomplete', 'off');
    $('.only_number').keyup(function () { 
    this.value = this.value.replace(/[^0-9.]/g,'');
    });

    $('.only_alpha').keyup(function () {
         this.value = this.value.replace(/[^A-Za-z ]/g, "");   

    });
    $(".select2").select2();
    $("#datemask").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
    $("#datemask2").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
    $("[data-mask]").inputmask();
    $('#reservation').daterangepicker();
    $('#reservationtime').daterangepicker({timePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A'});
    $('#daterange-btn').daterangepicker(
        {
          ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          },
          startDate: moment().subtract(29, 'days'),
          endDate: moment()
        },
        function (start, end) {
          $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }
    );

   
    $('.multiselect-ui').multiselect({
        includeSelectAllOption: true
    });	
	
 $('.datepicker').datepicker({
     format: "dd-mm-yyyy",
     autoclose: true,
}).on('changeDate', function (ev) {
     $(this).datepicker('hide');
});
 $('.datepicker1').datepicker({
     format: "yyyy-mm-dd",
     autoclose: true,
}).on('changeDate', function (ev) {
     $(this).datepicker('hide');
});

	
$('.datepicker2').datepicker({
     format: "dd-mm-yyyy",
     startDate: new Date(),
     autoclose: true,
}).on('changeDate', function (ev) {
     $(this).datepicker('hide');
});

    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass: 'iradio_minimal-blue'
    });
    $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
      checkboxClass: 'icheckbox_minimal-red',
      radioClass: 'iradio_minimal-red'
    });
    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass: 'iradio_flat-green'
    });
    $(".my-colorpicker1").colorpicker();
    $(".my-colorpicker2").colorpicker();
    $(".timepicker").timepicker({
      showInputs: false
    });
  });
 $(function(){
	 var addreess_fg= $("#geocomplete").val();
        $("#geocomplete").geocomplete({
          	map: ".map_canvas",
			location: addreess_fg,		
          	details: "form",
		  	types: ['establishment'],         
		   	autoselect: true,
          	blur: true,
		 	markerOptions: {
      			draggable: true
    		},
          geocodeAfterResult: true
        });
		
		 $("#geocomplete").bind("geocode:dragged", function(event, latLng){
			
			
          $("input[name=lat]").val(latLng.lat());
          $("input[name=lng]").val(latLng.lng());
		  $.ajax({ 
		  url:'https://maps.googleapis.com/maps/api/geocode/json?latlng='+latLng.lat()+','+latLng.lng()+'&key=AIzaSyAY0iXbSmvQSxjus3PEJoKnJZreLXQuP1Y',
         success: function(data){
            $("input[name=address]").val(data.results[0].formatted_address);
         }
}); 
		  
          $("#reset").show();
        });
        
        
        $("#reset").click(function(){
          $("#geocomplete").geocomplete("resetMarker");
          $("#reset").hide();
          return false;
        });
		
 });
  
  $(function () {
    $("#example1").DataTable({
      "pageLength": 50,
      rowReorder: {
            selector: 'td:nth-child(2)'
        },
      responsive: true
    });
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false
    });
    if ($("#example_lead").length && !$.fn.DataTable.isDataTable('#example_lead')) {
      $('#example_lead').DataTable({
        "pageLength": 50,
        responsive: true
      });
    }
  });
  
 
  $(function () {
    $(".textarea").wysihtml5();
  });
</script>
<script>
  $(function () {
    $('.mailbox-messages input[type="checkbox"]').iCheck({
      checkboxClass: 'icheckbox_flat-blue',
      radioClass: 'iradio_flat-blue'
    });
    $(".checkbox-toggle").click(function () {
      var clicks = $(this).data('clicks');
      if (clicks) {
        $(".mailbox-messages input[type='checkbox']").iCheck("uncheck");
        $(".fa", this).removeClass("fa-check-square-o").addClass('fa-square-o');
      } else {
        $(".mailbox-messages input[type='checkbox']").iCheck("check");
        $(".fa", this).removeClass("fa-square-o").addClass('fa-check-square-o');
      }
      $(this).data("clicks", !clicks);
    });
   
  });

$(document).on("click", ".logout, a[href*='manager/logout']", function(e){       
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    swal({
        title: "Are you sure?",
        text: "You want to logout!",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: false,
        closeOnCancel: true
    },
    function(isConfirm) {
        if (isConfirm) {
            $.ajax({
                url: '<?php echo base_url(); ?>manager/logout',            
                error: function() {
                    window.location.href = '<?php echo base_url(); ?>manager/login';
                },
                success: function(data) {
                    swal("Logged Out!", "", "success");
                    setTimeout(function() {
                        window.location.href = '<?php echo base_url(); ?>manager/login';
                    }, 500);
                }
            });
        } 
    });
    return false;
});
</script>

<script src="<?php echo base_url(); ?>assets/dist/js/jquery.slimmenu.js"></script> 
<script>
        $('.munk_menu').slimmenu(
            {
                resizeWidth: '767',
                collapserTitle: '',
                animSpeed: 'medium',
                indentChildren: true,
                childrenIndenter: '&raquo;'
            });
    </script>
    
<script>
$(document).ready(function() {
    $(document).on('click', '#crmMobileHamburger', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('body').toggleClass('sidebar-open');
    });

    $(document).on('click', '#crmSidebarClose', function(e) {
        e.preventDefault();
        $('body').removeClass('sidebar-open');
    });

    $(document).on('click', function(e) {
        if ($('body').hasClass('sidebar-open')) {
            if (!$(e.target).closest('.main-sidebar').length && !$(e.target).closest('#crmMobileHamburger').length) {
                $('body').removeClass('sidebar-open');
            }
        }
    });

    $(document).on('click', '.main-sidebar .sidebar-menu > li.treeview > a', function(e) {
        e.preventDefault();
        var $li = $(this).parent('li.treeview');
        var isOpen = $li.hasClass('menu-open') || $li.hasClass('is-expanded');
        $('.main-sidebar .sidebar-menu > li.treeview').removeClass('menu-open is-expanded').find('> .treeview-menu').slideUp(200);
        if (!isOpen) {
            $li.addClass('menu-open is-expanded').find('> .treeview-menu').slideDown(200);
        }
    });

    // Universal Back Button handler
    $(document).on('click', 'a.btn:contains("Back"), a:has(i.fa-arrow-left)', function(e) {
        if ($(this).attr('href') === 'javascript:window.history.back();' || $(this).data('action') === 'back') {
            e.preventDefault();
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = '<?php echo base_url(); ?>manager/dashboard';
            }
        }
    });
});
</script>

</body>
</html>
