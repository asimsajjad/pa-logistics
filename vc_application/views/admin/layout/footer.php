        </div>
      </div>
    </div>
    <!-- /.container-fluid -->
</section>

<footer class="pt-dashboard-footer">
          <div class="container-fluid">
            <div class="copyright">
              <a href="#">PA transport</a> © <?php echo date('Y');?>. All Rights Reserved.
            </div>
          </div>
        </footer>

    
    

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
      <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            <a class="btn btn-primary" href="<?php  echo base_url().'AdminLogin/logout/';  ?>">Logout</a>
          </div>
        </div>
      </div>
    </div>

	<!-- The Modal -->
<div id="myModal" class="modal">
  <span class="close">&times;</span>
  <img class="modal-content" id="img01">
  <div id="caption"></div>
</div>
	
</main>
	
	
	
    <!-- Bootstrap core JavaScript-->
    
    <script src="<?php echo base_url().'admin/vendor/bootstrap/js/bootstrap.bundle.min.js'; ?>"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?php echo base_url().'admin/vendor/jquery-easing/jquery.easing.min.js'; ?>"></script>

    <!-- Page level plugin JavaScript-->
    <!--script src="<?php echo base_url().'admin/vendor/chart.js/Chart.min.js'; ?>"></script-->
    <script src="<?php echo base_url().'admin/vendor/datatables/jquery.dataTables.js';  ?>"></script>
   

    <!-- Custom scripts for all pages-->
    <script src="<?php echo base_url().'admin/js/sb-admin.min.js'; ?>"></script>

    <!-- Demo scripts for this page-->
    <script src="<?php echo base_url().'admin/js/demo/datatables-demo.js'; ?>"></script>
    <!--script src="<?php echo base_url().'admin/js/demo/chart-area-demo.js'; ?>"></script-->
    <script src="<?php echo base_url().'admin/js/demo/jquery.tagsinput-revisited.js'?>"></script>
<script>

	$(".menu-item").click(function(){
   $('.menu-item.active').removeClass('active');
    $(this).addClass('active');
});



	$('#slug-source').keyup(function (){
    $('#slug-target').val($(this).val().toLowerCase().replace(/\s+|[,\/]/g, "-" ));
    
});

$('#form-tags-6').tagsInput({
					'delimiter': [',', ';'] 
				});

/* Custom js */
jQuery(document).ready(function(){

  jQuery(".pt-humburger").click(function(){
      jQuery(".pt-dashboard-wrapper").toggleClass("pt-fullwidth-toggle");
      jQuery('.nav-dropdown').not($(this).siblings()).hide("fast");
  });

  jQuery('.pt-dashboard-sidebar .nav-item .nav-link:not(:only-child)').click(function(e) {
        jQuery(this).siblings('.nav-dropdown').slideToggle("slow");

        jQuery('.nav-dropdown').not($(this).siblings()).hide("slow");
        e.stopPropagation();
  });
   
}); 
/* Custom js End */
</script>
  </body>

</html>

  
