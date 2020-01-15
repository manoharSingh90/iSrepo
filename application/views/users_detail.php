<div class="pageArea">
  <div class="pageHeader clearfix">
    <h2 class="float-left pageTitle"><a href="<?php echo base_url('users-list');?>" class="text-white mr-2">Back</a> | <span class="ml-2"><?php echo $title;?></span></h2>
    <div class="float-right pageAction">
      <h3 class="userName">Hello <?php echo ucwords($this->session->userdata('name'));?></h3>
      | <a href="<?php echo base_url('logout');?>" class="logoutBtn"><img src="<?php echo base_url();?>assets/img/icons/logout_icon.png" alt="#"/></a></div>
  </div>
  <div class="profileDetail clearfix">
    <?php 
      /*if($usersDtl[0]->gender=="Male") 
        $defaultProfile="male.jpg";
      else
        $defaultProfile="female.jpg";*/
     ?>
    <div class="profile-link"><span><?php if($usersDtl[0]->image !='') {?><img src="<?php //if($usersDtl[0]->image && file_exists($usersDtl[0]->image)) { 
      echo $usersDtl[0]->image; 
      //}  ?>" alt="<?php echo $usersDtl[0]->name;?>"/> <?php } ?></span>
      <h1><?php echo $usersDtl[0]->name;?></h1>
      <small class="d-block">Joined on <?php echo date('d M, Y',strtotime($usersDtl[0]->created_dttm));?></small></div>
    <ul>
      <li><span class="text-uppercase d-block">Gender</span> <?php echo $usersDtl[0]->gender;?></li>
      <li><span class="text-uppercase d-block">Age Group</span> <?php echo $usersDtl[0]->age_bracket_desc;?></li>
      <li><span class="text-uppercase d-block">Registered E- mail</span> <?php echo $usersDtl[0]->email;?></li>
      <li><span class="text-uppercase d-block">Phone no.</span> <?php echo $usersDtl[0]->phone;?></li>
    </ul>
  </div>
  <div class="profileMore">
    <h2>interests</h2>
    <div class="moreInfo">
      <?php if($usersInterestOptionsList) { 
        foreach ($usersInterestOptionsList as $key => $value) { ?>
          <div class="infoList">
          <h3><?php echo $value['interest_title'];?></h3>
            <ul>
              <?php if($value['option']){
              foreach ($value['option']as $option) { ?>
              <li><?php echo $option->option_text ;?></li>
            <?php } } ?>
            </ul>
          </div>
      <?php } } ?>
    </div>
  </div>
</div>

