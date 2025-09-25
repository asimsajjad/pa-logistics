 <!-- DataTables Example -->
<div class="card mb-3">
            <div class="pt-card-header pt-border-bottom d-flex align-items-center justify-content-between bg-transparent">
            <h3>Calendar Week View</h3>
              
              	<div class="add_page" style="float: right;"> 
				   <a href="<?php echo  base_url().'admin/calendar';?>"><input type="button" name="add" value="Monthly" class="btn btn-success pt-cta"/></a>  &nbsp; 
				   <a href="<?php echo  base_url().'admin/calendar_day_view';?>"><input type="button" name="add" value="Daily" class="btn btn-success pt-cta"/></a>  &nbsp; 
				   <a href="<?php echo  base_url().'admin/event/add';?>"><input type="button" name="add" value="Add Event" class="btn btn-success pt-cta"/></a>
            	</div>
            </div>
            
            
            <div class="card-bodys table_style">
                
            

			<form method="post" action="" class="form form-inline" id="searchform">
				<select name="unit" class="form-control">
						<option value="">Select Unit</option>
						<?php 
							if(!empty($vehicles)){
								foreach($vehicles as $val){
									echo '<option value="'.$val['id'].'"';
									if($this->input->post('unit')==$val['id']) { echo ' selected '; }
									echo '>'.$val['vname'].' ('.$val['vnumber'].')</option>';
								}
							}
						?>
					</select> &nbsp;
					<select name="type" class="form-control">
						<option value="">Both</option>
						<option value="Dispatch" <?php if($this->input->post('type')=='Dispatch') { echo ' selected '; }?>>Dispatch</option>
						<option value="Events" <?php if($this->input->post('type')=='Events') { echo ' selected '; }?>>Events</option>
					</select> &nbsp;
					<input type="hidden" name="search" value="true">
					<input type="submit" name="searchf" value="Search" class="btn btn-primary  pt-cta">
			</form>
		
		<br>
		
    <div class="clearfix"></div>
    <div class="content calendar_page">
	
        <?php 
        
       
            $page_heading = '';
            $base_url = base_url('admin/calendar_weekly');
            $event_url = base_url('admin/dispatch/update/');
            $current_year = $this->uri->segment(3);
			$current_month = $this->uri->segment(4);
			$current_day = $this->uri->segment(5);
        
        ?> 
	
    <div class="calendar-div">
		<div class="table-responsive pt-tbl-responsive pt-calendar-table">
		<table class="table">
		 <?php
  //echo'<pre>'; print_r($events); echo'</pre>';	
    

   //echo strtotime('Y/m/d',date('Y-m-d'));  
		 $week_days = array('1'=>'Mon','2'=>'Tue','3'=>'Wed','4'=>'Thu','5'=>'Fri','6'=>'Sat','7'=>'Sun');
		 
			$current_date = $sdate;
			
			echo '<tr>
				<th colspan="2" class="text-left"><a class="btn btn-success btn-sm calendar-btn pt-cta" href="'.$base_url.'/'.date('Y/m/d',strtotime("-1 week",strtotime($current_date))).'"><< Prev</a>
				</th>
				<th colspan="3" class="text-center">'.date('F Y',strtotime($current_date)).'</th>
				<th colspan="2" class="text-right"><a class="btn btn-success btn-sm calendar-btn pt-cta" href="'.$base_url.'/'.date('Y/m/d',strtotime("+1 week",strtotime($current_date))).'">Next >></a></th>
				</tr>';
				?>
            <tr><th width="14.29%">Mon</th><th width="14.29%">Tue</th><th width="14.29%">Wed</th><th width="14.29%">Thu</th><th width="14.29%">Fri</th><th width="14.29%">Sat</th><th width="14.29%">Sun</th></tr>
            
            <?php  
			
			echo '<tr>';
			
				$date = date('m-d-Y',strtotime($current_date));
				$day = date('d',strtotime($current_date));
				
			for($i = 1; $i <= 7; $i++) {
			
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
				  
					echo '<td>'.$date.'<br><br>'.$event_title.'</td>';
					
					
				 $date = date('m-d-Y',strtotime("+$i day",strtotime($current_date)));
				 $day = date('d',strtotime("+$i day",strtotime($current_date)));
				
				}   
			
			echo '</tr>'; 
            ?>
        </table>
		</div>
    </div>
    
            </div>
             
            </div>
         
          </div>

<script>
jQuery(document).ready(function(){
	jQuery('.calendar-btn').click(function(e){
		e.preventDefault();
		var href = jQuery(this).attr('href');
		jQuery('#searchform').attr('action',href);
		jQuery('#searchform').submit();
	});
});
</script>
