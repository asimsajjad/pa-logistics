 <!-- DataTables Example -->
<div class="card mb-3">
            <div class="card-header">
              <i class="fas fa-table"></i>
              Calendar Monthly View 
              <div class="add_page" style="float: right;"> 
				   <a href="<?php echo  base_url().'admin/calendar_weekly';?>"><input type="button" name="add" value="Weekly" class="btn btn-success"/></a>  &nbsp; 
				   <a href="<?php echo  base_url().'admin/calendar_day_view';?>"><input type="button" name="add" value="Daily" class="btn btn-success"/></a>  &nbsp; 
				   <a href="<?php echo  base_url().'admin/event/add';?>"><input type="button" name="add" value="Add Event" class="btn btn-success"/></a>
                   </div>
            </div>
            <?php 
            $page_heading = '';
            $base_url = base_url('admin/calendar');
            $event_url = base_url('admin/dispatch/update/');
            $current_year = $this->uri->segment(3);
			$current_month = $this->uri->segment(4);
        ?> 
            
            <div class="card-body table_style">
                
            <div class="col-sm-12 text-center">
                 <div class="container">
		
		<div class="col-sm-12 text-center">
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
					<input type="submit" name="searchf" value="Search" class="btn btn-primary">
			</form>
		</div>
		<br>
    <div class="clearfix"></div>
    <div class="content calendar_page">
	 
    <div class="calendar-div">
	
        <table class="table table-bordered">
		 <?php
  //echo'<pre>'; print_r($events); echo'</pre>';	
    

   //echo strtotime('Y/m/d',date('Y-m-d'));  
		 $week_days = array('1'=>'Mon','2'=>'Tue','3'=>'Wed','4'=>'Thu','5'=>'Fri','6'=>'Sat','7'=>'Sun');
		 
			
			if($current_year!='' && $current_month!='') { 
				$current_date = $current_year.'-'.$current_month.'-01';
				$first_day = date('D',strtotime($current_date));
			} else {
				$current_date = date('Y-m-d');
				$first_day = date('D',strtotime(date('Y-m-01')));
			}
            $month_last_day = date('t',strtotime($current_date));  
			$endtr = 1;
			$current_week_day = 7;
			$start_month = 'no';
			echo '<tr>
				<th colspan="2" class="text-left"><a class="btn btn-success btn-sm calendar-btn" href="'.$base_url.'/'.date('Y/m',strtotime("-1 month",strtotime($current_date))).'"><< Prev</a></th>
				<th colspan="3" class="text-center">'.date('Y F',strtotime($current_date)).'</th>
				<th colspan="2" class="text-right"><a class="btn btn-success btn-sm calendar-btn" href="'.$base_url.'/'.date('Y/m',strtotime("+1 month",strtotime($current_date))).'">Next >></a></th>
				</tr>';
				?>
            <tr><th width="14.29%">Mon</th><th width="14.29%">Tue</th><th width="14.29%">Wed</th><th width="14.29%">Thu</th><th width="14.29%">Fri</th><th width="14.29%">Sat</th><th width="14.29%">Sun</th></tr>
            
            <?php  
			
			echo '<tr>';
			
			for($i = 1; $i <= $month_last_day; $i++) {
				if($week_days[$endtr]==$first_day || $start_month == 'yes') {
					$start_month = 'yes';
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
					  
					  if($i==$event_enddate_month){ 
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
				 
					if($i==$event_date_month){ 
						$event_title .= '<a style="border-bottom: 1px solid #fff;display: block;font-size: 14px;padding: 2px 2px;background:';
						if($info['vehicle']==5) { $event_title .= '#bcf4bc;'; }
						elseif($info['vehicle']==6) { $event_title .= '#e8f4bc;'; }
						elseif($info['vehicle']==7) { $event_title .= '#bce2f4;'; }
						elseif($info['vehicle']==10) { $event_title .= '#f4c1bc;'; }
						elseif($info['vehicle']==11) { $event_title .= '#e1ddee;'; }
						else { $event_title .= '#f4bceb;'; }
						$event_title .= '" href="'.$event_url.''.$info['id'].'">'.$info['location'].' ['.$info['dcode'].']</a>';
					}
				 }
				}
				
				
					if(!empty($events)) {	
					 foreach($events as $info){
					  $event_enddate_month = date('d',strtotime($info['cdate']));
					  if($event_enddate_month < 10){ $event_enddate_month = str_replace('0','',$event_enddate_month); }
					  else{ $event_enddate_month = date('d',strtotime($info['cdate'])); }
					  
					  if($i==$event_enddate_month){ 
						$event_title .= '<a href="'.base_url('admin/event/update/').''.$info['id'].'" style="border-bottom: 1px solid #fff;display: block;font-size: 14px;padding: 2px 2px;background:#19b377;color:#fff;">'.$info['title'].'</a>';
					  }
					 }
					}
				 
				 
					
					echo '<td>'.$i.'<br>'.$event_title.'</td>';
					
				} else {
					echo '<td></td>';
					$i--;
				} 
				if(($endtr%7)==0) { echo '</tr><tr>'; $current_week_day = 7; }
				else { $current_week_day--; }
				$endtr++;
			}
			
			for($w=0;$w<$current_week_day;$w++){
				echo '<td></td>';
			}
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