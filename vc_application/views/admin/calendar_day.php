 <!-- DataTables Example -->
<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Calendar Day View
              <div class="add_page" style="float: right;"> 
				   <a href="<?php echo  base_url().'admin/calendar_weekly';?>"><input type="button" name="add" value="Weekly" class="btn btn-success"/></a>  &nbsp; 
				   <a href="<?php echo  base_url().'admin/calendar';?>"><input type="button" name="add" value="Monthly" class="btn btn-success"/></a>  &nbsp; 
				   <a href="<?php echo  base_url().'admin/event/add';?>"><input type="button" name="add" value="Add Event" class="btn btn-success"/></a>
                   </div>
            </div>
            
            
            <div class="card-body table_style">
                
            <div class="col-sm-12 text-center">
                 <div class="container">

    <div class="clearfix"></div>
    <div class="content calendar_page">
	
        <?php 
        
       
            $page_heading = '';
            $base_url = base_url('admin/calendar_day_view');
            $event_url = base_url('admin/dispatch/update/');
            $current_year = $this->uri->segment(3);
			$current_month = $this->uri->segment(4);
			$current_day = $this->uri->segment(5);
        
        ?> 
	
    <div class="calendar-div">
	
        <table class="table">
		 <?php
  //echo'<pre>'; print_r($events); echo'</pre>';	
    

   //echo strtotime('Y/m/d',date('Y-m-d'));  
		 $week_days = array('1'=>'Mon','2'=>'Tue','3'=>'Wed','4'=>'Thu','5'=>'Fri','6'=>'Sat','7'=>'Sun');
		 
			$current_date = $sdate;
			
			echo '<tr>
				<th class="text-left"><a class="btn btn-success btn-sm" href="'.$base_url.'/'.date('Y/m/d',strtotime("-1 day",strtotime($current_date))).'"><< Prev</a>
				</th>
				<th class="text-center">'.date('l d F Y',strtotime($current_date)).'</th>
				<th class="text-right"><a class="btn btn-success btn-sm" href="'.$base_url.'/'.date('Y/m/d',strtotime("+1 day",strtotime($current_date))).'">Next >></a></th>
				</tr>';
				?>
            <!--tr><th colspan="3"></th></tr-->
            
            <?php  
			
			echo '<tr>';
			
				$date = date('m-d-Y',strtotime($current_date));
				$day = date('d',strtotime($current_date));
				$event_title='';
				if(!empty($dispatch)) {	
				 foreach($dispatch as $info){
					  $event_date_month = date('d',strtotime($info['pudate']));
					  if($event_date_month < 10){ $event_date_month = str_replace('0','',$event_date_month); }
					  else{ $event_date_month = date('d',strtotime($info['pudate'])); }
					
					/*if(!strstr($info['dodate'],'0000')) {					
					  $event_enddate_month = date('d',strtotime($info['dodate']));
					  if($event_enddate_month < 10){ $event_enddate_month = str_replace('0','',$event_enddate_month); }
					  else{ $event_enddate_month = date('d',strtotime($info['dodate'])); }
					  
					  if($day==$event_enddate_month){ 
						$event_title .= '<a style="border-bottom: 1px solid #fff;display: block;font-size: 14px;padding: 2px 2px;background:';
						if($info['vehicle']==5) { $event_title .= '#bcf4bc;'; }
						elseif($info['vehicle']==6) { $event_title .= '#e8f4bc;'; }
						elseif($info['vehicle']==7) { $event_title .= '#bce2f4;'; }
						elseif($info['vehicle']==10) { $event_title .= '#f4c1bc;'; }
						elseif($info['vehicle']==11) { $event_title .= '#e1ddee;'; }
						else { $event_title .= '#f4bceb;'; }
						$event_title .= '" href="'.$event_url.''.$info['id'].'">End '.$info['location'].' ('.$info['city'].')</a>';
					}
					}*/
				 
					if($day==$event_date_month){ 
						$event_title .= '<a style="border-bottom: 1px solid #fff;display: block;font-size: 14px;padding: 2px 2px;background:';
						if($info['vehicle']==5) { $event_title .= '#bcf4bc;'; }
						elseif($info['vehicle']==6) { $event_title .= '#e8f4bc;'; }
						elseif($info['vehicle']==7) { $event_title .= '#bce2f4;'; }
						elseif($info['vehicle']==10) { $event_title .= '#f4c1bc;'; }
						elseif($info['vehicle']==11) { $event_title .= '#e1ddee;'; }
						else { $event_title .= '#f4bceb;'; }
						$event_title .= '" href="'.$event_url.''.$info['id'].'">'.$info['location'].' ('.$info['city'].') - '.$info['driver'].'</a>';
					}
				 }
				}
				
				
					if(!empty($events)) {	
					 foreach($events as $info){
					  $event_enddate_month = date('d',strtotime($info['cdate']));
					  if($event_enddate_month < 10){ $event_enddate_month = str_replace('0','',$event_enddate_month); }
					  else{ $event_enddate_month = date('d',strtotime($info['cdate'])); }
					  
					  if($day==$event_enddate_month){ 
						$event_title .= '<a href="'.base_url('admin/event/update/').''.$info['id'].'" style="border-bottom: 1px solid #fff;display: block;font-size: 14px;padding: 2px 2px;background:#19b377;color:#fff;">'.$info['title'].'</a>';
					  }
					 }
					}
				  
					echo '<td colspan="3">'.$date.'<br><br>'.$event_title.'</td>';
					 
			echo '</tr>'; 
            ?>
        </table>
    </div>
    </div>
</div>
            </div>
             
            </div>
         
          </div>

        </div>
