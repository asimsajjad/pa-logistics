 <!-- DataTables Example -->
<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              <div class="add_page" style="float: right;">
                  <?php if(!empty($this->uri->segment(4))){
                      
                      $var='admin/alluser';
                  
                  }else{
                      
                     $var='admin/allappointment';   
                  }
                  
                  ?>
                           <a class="nav-link" title="Create Section" href="<?php echo  base_url().$var;?>"><input type="button" name="Back" value="Back" class="btn btn-danger"/>
                   </a>  </div> </div>
            <div class="card-body table_style">
                <?php

                  
                     $key= $donation[0];
                  
                    ?>
           

              <!--td><img  id="myImg<?php //echo $key->id;  ?>" src="<?php //echo $key->upload_path.$key->image;?>"   
			  style="width:80px; height:20px; max-width:40px"/><div id="myModal<?php //echo $key->id;  ?>" class="modal"> 
			  <span class="close">&times;</span> <img class="modal-content" id="img<?php //echo $key->id;  ?>">
			  <div id="caption"></div></td-->
			  
			  <p>
			 <strong> Name:</strong> <?php echo $key['name'];?></br> </p>
         
                    <p><strong>Email:</strong> <?php echo $key['email']; ?></br> </p>
                     
                    <?php if(!empty($this->uri->segment(4))){?>
                         
                        <p><strong>Address: </strong> <?php echo $key['address'].$key['address1']; ?></br> </p>
                        <p><strong>City: </strong> <?php echo $key['city']; ?></br> </p>
                     <p><strong>State: </strong> <?php echo $key['state']; ?></br> </p>
                     <p><strong>phone: </strong> <?php echo $key['phone']; ?></br> </p>
                     <p><strong>Zip: </strong> <?php echo $key['zip']; ?></br> </p>
                     <?php if($key['birth']!=''){?>
                     <p><strong>D-O-B: </strong> <?php echo $key['birth']; ?></br> </p>
                     <?php } ?>
                       <?php  }else{ ?>
                            
                            <p><strong>Date:</strong> <?php echo $key['Date']; ?></br> </p>
                            
                     <p><strong>Date:</strong> <?php echo $key['time']; ?></br> </p>
                        
                       <?php  }?>
           
           
            </div>
         
          </div>

        </div>
