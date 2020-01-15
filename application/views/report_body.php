<div class="col-12">
	<?php if($range =='-'){
		$set_date = $start . '-' . $end;
	}else{
		$set_date = $current_date;
	} ?>
	<p class=" font-weight-bold mt-3 mb-3 tagWrap">View as <span class="cust_viewDtl"><?= $range?></span><span id="setDate"> (<?= $set_date;?>)</span></p><a href="javascript:void(0);" class="btn btn-primary ml-2 data_export">EXPORT DATA (.xls)</a>
</div>
<div class="col-5">
	<div class="roundBox">
		<h2 class="text-uppercase text-dark h6 font-weight-bold mb-2">Active Users</h2>
		<?php if($active_users !=0) {?>
			<div class="row align-items-center">
				<div class="col-7">
					<div id="piechart" style="width: 100%; height: 195px;"></div>
					<div class="d-block pt-1 text-center"><small class="d-block pb-1">Total Users</small>
						<p class="m-0 h5 text-dark font-weight-bold"><?= $active_users;?></p>
					</div>
				</div>
				<div class="col-5">
					<ul class="chartLegends">
						<li>
							<div class="legendBox"><b style="background:#bf1f81;"></b>Male Users</div>
							<p class="text-dark h6 pt-1"><?= $male_users;?> (<?= sprintf('%0.2f', $male_percent);?>%)</p>
						</li>
						<li>
							<div class="legendBox"><b style="background:#80024f;"></b>Female Users</div>
							<p class="text-dark h6 pt-1"><?= $female_users;?> (<?= sprintf('%0.2f', $female_percent);?>%)</p>
						</li>
					</ul>
				</div>
			</div>
		<?php }else{echo '<div class="text-center pt-5 pb-5"> No record found </div> ';} ?>
	</div>
</div>
<div class="col-7">
	<div class="roundBox">
		<h2 class="text-uppercase text-dark h6 font-weight-bold mb-2">User Profiles (Age)</h2>
		<div class="tableWrap">

			<table class="scrollTbl">
				<thead>
					<tr>
						<th rowspan="2">Age Group</th>
						<th rowspan="2" class="text-center">Total Users</th>
						<th colspan="2" class="text-center">Male</th>
						<th colspan="2" class="text-center">Female</th>
					</tr>
					<tr>
						<th class="text-center">in no.</th>
						<th class="text-center">in %</th>
						<th class="text-center">in no.</th>
						<th class="text-center">in %</th>
					</tr>
				</thead>
				<tbody>
					<?php if(!empty($profile_age)){
						foreach ($profile_age as $key => $key_age) { ?>
							<tr>
								<td class="text-dark font-weight-bold"><?= $key_age->bracket;?> years</td>
								<td class="text-center"><?= $key_age->Total;?></td>
								<td class="text-center"><?= $key_age->MALE;?></td>
								<td class="text-center"><?= sprintf('%0.2f', $key_age->Male_percent); ?> %</td>
								<td class="text-center"><?= $key_age->Female;?></td>
								<td class="text-center"><?= sprintf('%0.2f', $key_age->Female_percent); ?> %</td>
							</tr>
						<?php } }else{echo"<tr><td colspan='6' class='text-center'> No record found";}?>            
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="col-6">
		<div class="roundBox mt-4">
			<h2 class="text-uppercase text-dark h6 font-weight-bold mb-2">User Profiles (Interest)</h2>
			<div class="tableWrap">

				<table class="scrollTbl sortable">
					<thead> 
						<tr>
							<th rowspan="2">Interest</th>
							<th rowspan="2" class="text-center">Total Users</th>
							<th colspan="2" class="text-center">Male</th>
							<th colspan="2" class="text-center">Female</th>
						</tr>
						
						<tr>
							<th class="text-center">in no.</th>
							<th class="text-center">in %</th>
							<th class="text-center">in no.</th>
							<th class="text-center">in %</th>
						</tr>
					</thead>
					<tbody>
						<?php if(!empty($profile_interest)){
							foreach ($profile_interest as $key => $intrest_key) { ?>              
								<tr>
									<td class="text-dark font-weight-bold"><?= $intrest_key->intrest;?></td>
									<td class="text-center"><?= $intrest_key->Total;?></td>
									<td class="text-center"><?= $intrest_key->MALE;?></td>
									<td class="text-center"><?= sprintf('%0.2f', $intrest_key->Male_percent);?>%</td>
									<td class="text-center"><?= $intrest_key->Female;?></td>
									<td class="text-center"><?= sprintf('%0.2f', $intrest_key->Female_percent);?>%</td>
								</tr>
							<?php } }else{echo"<tr><td colspan='6' class='text-center'> No record found";}?>            
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="col-6">
			<div class="roundBox mt-4">
				<h2 class="text-uppercase text-dark h6 font-weight-bold mb-2">User Profiles (Country)</h2>
				<div class="tableWrap">

					<table class="scrollTbl">
						<thead>
							<tr>
								<th rowspan="2">Country</th>
								<th rowspan="2" class="text-center">Total Users</th>
								<th colspan="2" class="text-center">Male</th>
								<th colspan="2" class="text-center">Female</th>
								<th rowspan="2"></th>
							</tr>
							<tr>
								<th class="text-center">in no.</th>
								<th class="text-center">in %</th>
								<th class="text-center">in no.</th>
								<th class="text-center">in %</th>
							</tr>
						</thead>
						<tbody>
							<?php if(!empty($profile_country)){
								foreach ($profile_country as $key => $profile_value) { ?>
									<tr>
										<td class="text-dark font-weight-bold viewAs"><?= $user_country = $profile_value->country ==''?'Unknown':$profile_value->country?></td>
										<td class="text-center"><?= $profile_value->Total;?></td>
										<td class="text-center"><?= $profile_value->MALE;?></td>
										<td class="text-center"><?= sprintf('%0.2f', $profile_value->Male_percent);?>%</td>
										<td class="text-center"><?= $profile_value->Female;?></td>
										<td class="text-center"><?= sprintf('%0.2f', $profile_value->Female_percent);?>%</td>
										<td class="text-center"><a href="#" class="text-link city_view" data-toggle="modal" rel="<?= $profile_value->countryId;?>" >View</a></td>
									</tr>
								<?php } }else{echo"<tr><td colspan='6' class='text-center'> No record found";}?>
								
								
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="col-5">
				<div class="roundBox mt-4">
					<h2 class="text-uppercase text-dark h6 font-weight-bold mb-2">User Profiles (Profile Completion)</h2>
					<div id="profilechart" style="width: 100%; height: 300px;"></div>
				</div>
			</div>
			<div class="col-7">
				<div class="roundBox mt-4">
					<h2 class="text-uppercase text-dark h6 font-weight-bold mb-2">Usage Trends</h2>
					<div class="row">
						<div class="col-8">
							<div id="linechart" style="width: 100%; height: 300px;"></div>
						</div>
						<div class="col-4">
							<ul class="chartLegends checkbox-legend pr-0 pl-0">
								<li>
									<div class="custom-control custom-checkbox d-inline-block">
										<input type="checkbox" class="custom-control-input checkbox" name="series" id="lg-01" value="1" checked />
										<label class="custom-control-label" for="lg-01"></label>
									</div>
									<div class="legendBox"><b style="background:#69003b;"></b>Sample Redeemed</div>
								</li>
								<li>
									<div class="custom-control custom-checkbox d-inline-block">
										<input type="checkbox" class="custom-control-input checkbox" name="series" value="2" id="lg-02" checked>
										<label class="custom-control-label" for="lg-02"></label>
									</div>
									<div class="legendBox"><b style="background:#ff701a;"></b> Promos Redeemed</div>
								</li>
								<li>
									<div class="custom-control custom-checkbox d-inline-block">
										<input type="checkbox" class="custom-control-input checkbox" name="series" value="3" id="lg-03" checked>
										<label class="custom-control-label" for="lg-03"></label>
									</div>
									<div class="legendBox"><b style="background:#eb4e00;"></b>Reviews Given</div>
								</li>
								<li>
									<div class="custom-control custom-checkbox d-inline-block">
										<input type="checkbox" class="custom-control-input checkbox" name="series" value="4" id="lg-04" checked>
										<label class="custom-control-label" for="lg-04"></label>
									</div>
									<div class="legendBox"><b style="background:#c38625;"></b>Comments</div>
								</li>
								<li>
									<div class="custom-control custom-checkbox d-inline-block">
										<input type="checkbox" class="custom-control-input checkbox" name="series" value="5" id="lg-05" checked>
										<label class="custom-control-label" for="lg-05"></label>
									</div>
									<div class="legendBox"><b style="background:#ee219b;"></b>Likes</div>
								</li>
								<li>
									<div class="custom-control custom-checkbox d-inline-block">
										<input type="checkbox" class="custom-control-input checkbox" name="series" value="6" id="lg-06" checked>
										<label class="custom-control-label" for="lg-06"></label>
									</div>
									<div class="legendBox"><b style="background:#ff9d00;"></b>Brand Page Viewed</div>
								</li>
								<li>
									<div class="custom-control custom-checkbox d-inline-block">
										<input type="checkbox" class="custom-control-input checkbox" name="series" value="7" id="lg-07" checked>
										<label class="custom-control-label" for="lg-07"></label>
									</div>
									<div class="legendBox"><b style="background:#ac0bbb;"></b>Campaigns Viewed</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>