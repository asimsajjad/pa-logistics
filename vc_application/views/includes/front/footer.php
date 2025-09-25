<!-- footer  -->
<footer>
      <div class="footer-main col-sm-12 col-md-12 col-lg-12 col-xs-12">
        
	      <div class="payment-method">
		     <div class="container">
		         
				  <p>@2018,PA Transport.All Rights Reserved.</p>
				</div>
		  </div>
		   
     </div>
</footer>

</div><!--End of Wrapper-->
<script type="text/javascript">
   
        jQuery(document).ready(function(){
         jQuery('.add_cart_donation').click(function(event){
             event.preventDefault();
            
         var data_id=jQuery(this).attr('data-id');
           var data_name=jQuery(this).attr('data-name');
           var data_price=jQuery(this).attr('data-price');
           var data_q=jQuery('.get-quantity-price').val();
           
              jQuery.ajax({
            type: 'post',
            url: "<?php echo base_url();?>Front/carts",
            //data: jQuery(this).serialize(),
            data: {id: data_id, name: data_name, price: data_price,data_q:data_q},
           success: function (data) {
              //alert(data);
              /*jQuery('#error-home').show();
              jQuery('#Home-form')[0].reset();*/
              jQuery('.counted_cart').html(data);
             jQuery('.paddes').show();
              setTimeout(function() {
        jQuery('.paddes').hide();
    }, 1500);
            }
          });

         });
         
         
          jQuery(document).on('click', '.remove_inventory', function(){
  var row_id = jQuery(this).attr("id");
  if(confirm("Are you sure you want to remove this?"))
  {
   jQuery.ajax({
    url:"<?php echo base_url();?>Front/remove_cart",
    method:"POST",
    data:{row_id:row_id},
    success:function(data)
    {
     //alert("Product removed from Cart");
     //jQuery('#cart_details').html(data);
     location.reload();
    }
   });
  }
  else
  {
   return false;
  }
 });
 
 
    });
</script>
<!--script src="/assets/js/jquery-3.2.1.slim.min.js"></script-->
<script src="/assets/js/bootstrap.min.js"></script>
</body>
</html>
