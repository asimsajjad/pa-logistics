 <!-- DataTables Example -->
<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              User
              <div class="add_page" style="float: right;">
                   <a class="nav-link" title="Create Section" href="<?php echo  base_url().'admin/adduser';?>"><input type="button" name="add" value="Add New" class="btn btn-success"/>
                   </a>  </div>
              </div>
            <div class="card-body table_style">
               <div class="table-responsive">
               <?php if($this->session->userdata('role')==1){?>
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <?php  }
                    else {?>
                      <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">

                  <?php  }
                    ?><thead >
                    <tr>
						  <th>Sr.No</th>
						  
						  <th>Name</th>
						  <!--th>Image (1200 X 600)</th-->
						 
          
						  <th>Email</th>
						 <th>View</th>
						  
						   <?php if($this->session->userdata('role')==1){?>
              <th colssapn="2">Action</th><?php }?>
                    </tr>
                  </thead>
                 
                
                   
                    <tr >
                       <?php

                  if(!empty($user)){
                      $i=1;
                  foreach($user as $key)
                  {
                     
                    ?>
            <td ><?php echo $i;?></td>
            <td><?php echo $key['name'];?></td>

              <!--td><img  id="myImg<?php //echo $key->id;  ?>" src="<?php //echo $key->upload_path.$key->image;?>"   
			  style="width:80px; height:20px; max-width:40px"/><div id="myModal<?php //echo $key->id;  ?>" class="modal"> 
			  <span class="close">&times;</span> <img class="modal-content" id="img<?php //echo $key->id;  ?>">
			  <div id="caption"></div></td-->
			  
            
			
         
            <td><?php echo $key['email']; ?></td>
            
            <td><a class="btn btn-sm btn-success" href="<?php echo base_url();?>admin/user/view/<?php  echo $key['id']; ?>">Edit <i class="fas fa-edit" title="Delete" alt="Delete"></i></a>
            <!--a href="<?php //echo base_url();?>AdminAppointment/update_user/<?php // echo $key['id']; ?>">Edit</a></td>
             <td>   <!--a href="<?php //echo base_url().'admin/appointment/update/'.$key['id'];?> " class="btn btn-sm btn-success">Edit</i></a--> 
  
          <!--a style="color:#ff0000;" href="<?php //echo base_url().'admin/user/delete/'.$key['id'];?>" onclick="return confirm('Are you sure to Delete  it  this is cannot be restore ?')" ><i class="fas fa-trash-alt" title="Delete" alt="Delete"></i></a-->
          </td></tr> 
		  
		  
		  

                    <?php
                    $i++;
                    }
                    }else{
                        
                     echo'<td></td><td></td><td>No record found</td><td></td><td></td>';   
                    }


                  ?>

                   
                    </tr>
                   
                   
                  </tbody>
                </table>
              </div>
            </div>
         
          </div>

        </div>
 </div>

