<?php
  $host_url = "https://$_SERVER[HTTP_HOST]/admin/";
  $page_url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  $page_url_arr = explode('/', $page_url);
  $page_url_name = $page_url_arr[4];

?>
<div class="menu-bar-view h-100vh pd-10 br-all bg-white hide-native-scrollbar bx-shdw-2 transition-05">
        
    <div class="w-100 row-view j-end">
        <div class="menu-open-btn row-view v-center pd-5-10 ft-sz-20 br-r-5 bx-shdw cl-white bg-red"><i class='bx bx-chevron-left'></i></div>
    </div>
        
    <a href="<?php echo $host_url; ?>dashboard" class="dspl-in-block txt-deco-n w-100 pd-10 mg-t-20 cl-black <?php if($page_url_name=='dashboard'){ echo 'menu-active-btn'; } ?>">
        <i class='bx bx-bar-chart-alt-2'></i>
        &nbsp;Dashboard
    </a>
    
    
    <a href="<?php echo $host_url; ?>game" class="dspl-in-block txt-deco-n w-100 pd-10 mg-t-20 cl-black <?php if($page_url_name=='game'){ echo 'menu-active-btn'; } ?>">
        <i class='bx bx-bar-chart-alt-2'></i>
        &nbsp;Control Matches
    </a>
    
    
      <a href="<?php echo $host_url; ?>chart" class="dspl-in-block txt-deco-n w-100 pd-10 mg-t-20 cl-black <?php if($page_url_name=='chart'){ echo 'menu-active-btn'; } ?>">
        <i class='bx bx-bar-chart-alt-2'></i>
        &nbsp;Chart data
    </a>
        
         <a href="<?php echo $host_url; ?>recently-played-top" class="dspl-in-block txt-deco-n w-100 pd-10 mg-t-10 cl-black <?php if($page_url_name=='recently-played-top'){ echo 'menu-active-btn'; } ?>">
        <i class='bx bx-notepad' ></i>
        &nbsp;Top Bet Records
    </a>
    
    <a href="<?php echo $host_url; ?>recent-played" class="dspl-in-block txt-deco-n w-100 pd-10 mg-t-10 cl-black <?php if($page_url_name=='recent-played'){ echo 'menu-active-btn'; } ?>">
        <i class='bx bx-notepad' ></i>
        &nbsp;Recently Played
    </a>
        
    <a href="<?php echo $host_url; ?>casino-bet-history" class="dspl-in-block txt-deco-n w-100 pd-10 mg-t-10 cl-black <?php if($page_url_name=='casino-bet-history'){ echo 'menu-active-btn'; } ?>">
        <i class='bx bx-notepad' ></i>
        &nbsp;Casino bet History
    </a>

    <a href="<?php echo $host_url; ?>sports-bet-history" class="dspl-in-block txt-deco-n w-100 pd-10 mg-t-10 cl-black <?php if($page_url_name=='sports-bet-history'){ echo 'menu-active-btn'; } ?>">
        <i class='bx bx-notepad' ></i>
        &nbsp;Sports bet history
    </a>


    <div class="h-line-view mg-t-10"></div>
        
    <a href="<?php echo $host_url; ?>users-data" class="dspl-in-block txt-deco-n w-100 pd-10 mg-t-10 cl-black <?php if($page_url_name=='users-data'){ echo 'menu-active-btn'; } ?>">
        <i class='bx bx-group' ></i>
        &nbsp;Users Data
    </a>
    
     <a href="<?php echo $host_url; ?>changebank" class="dspl-in-block txt-deco-n w-100 pd-10 mg-t-10 cl-black <?php if($page_url_name=='changebank'){ echo 'menu-active-btn'; } ?>">
        <i class='bx bx-group' ></i>
        &nbsp;Change bank account
    </a>
    
        
         <a href="<?php echo $host_url; ?>users-data/index1.php" class="dspl-in-block txt-deco-n w-100 pd-10 mg-t-10 cl-black <?php if($page_url_name=='users-data/index1.php'){ echo 'menu-active-btn'; } ?>">
        <i class='bx bx-group' ></i>
        &nbsp;Top User Balances
    </a>
        
        
    <a href="<?php echo $host_url; ?>recharge-records" class="dspl-in-block txt-deco-n w-100 pd-10 mg-t-10 cl-black <?php if($page_url_name=='recharge-records'){ echo 'menu-active-btn'; } ?>">
        <i class='bx bx-receipt'></i>
        &nbsp;Recharge Records
    </a>
     <a href="<?php echo $host_url; ?>manual-withdraw-records" class="dspl-in-block txt-deco-n w-100 pd-10 mg-t-10 cl-black <?php if($page_url_name=='manual-withdraw-records'){ echo 'menu-active-btn'; } ?>">
        <i class='bx bx-receipt'></i>
        &nbsp;Manual Withdraw Records
    </a>
    
       <a href="<?php echo $host_url; ?>withdraw-statistics" class="dspl-in-block txt-deco-n w-100 pd-10 mg-t-10 cl-black <?php if($page_url_name=='withdraw-statistics'){ echo 'menu-active-btn'; } ?>">
        <i class='bx bx-receipt'></i>
        &nbsp;Withdraw statistics
    </a>
    <a href="<?php echo $host_url; ?>game-statistics" class="dspl-in-block txt-deco-n w-100 pd-10 mg-t-10 cl-black <?php if($page_url_name=='game-statistics'){ echo 'menu-active-btn'; } ?>">
        <i class='bx bx-receipt'></i>
        &nbsp;Game statistics
    </a>
    
    <a href="<?php echo $host_url; ?>withdraw-records" class="dspl-in-block txt-deco-n w-100 pd-10 mg-t-10 cl-black <?php if($page_url_name=='withdraw-records'){ echo 'menu-active-btn'; } ?>">
        <i class='bx bx-receipt'></i>
        &nbsp;Withdraw Records 
    </a>
      
    <a href="<?php echo $host_url; ?>manage-withdraw" class="dspl-in-block txt-deco-n w-100 pd-10 mg-t-10 cl-black <?php if($page_url_name=='manage-withdraw'){ echo 'menu-active-btn'; } ?>">
        <i class='bx bx-receipt'></i>
        &nbsp;Bet add Withdraw 
    </a>
    
    <a href="<?php echo $host_url; ?>manage-salary" class="dspl-in-block txt-deco-n w-100 pd-10 mg-t-10 cl-black <?php if($page_url_name=='manage-salary'){ echo 'menu-active-btn'; } ?>">
        <i class='bx bx-gift' ></i>
        &nbsp;Manage Salary
    </a>
    
    <div class="h-line-view mg-t-10"></div>
        
    <a href="<?php echo $host_url; ?>manage-rewards" class="dspl-in-block txt-deco-n w-100 pd-10 mg-t-10 cl-black <?php if($page_url_name=='manage-rewards'){ echo 'menu-active-btn'; } ?>">
        <i class='bx bx-gift' ></i>
        &nbsp;Manage Rewards
    </a>
    
    <a href="<?php echo $host_url; ?>manage-sliders" class="dspl-in-block txt-deco-n w-100 pd-10 mg-t-10 cl-black <?php if($page_url_name=='manage-sliders'){ echo 'menu-active-btn'; } ?>">
        <i class='bx bx-images'></i>
        &nbsp;Manage Sliders
    </a>
            
   <a href="<?php echo $host_url; ?>send-message" class="dspl-in-block txt-deco-n w-100 pd-10 mg-t-10 cl-black <?php if($page_url_name=='send-message'){ echo 'menu-active-btn'; } ?>">
        <i class='bx bx-message-square-dots'></i>
        &nbsp;Send Message
    </a>
    
    <a href="<?php echo $host_url; ?>manage-admins" class="dspl-in-block txt-deco-n w-100 pd-10 mg-t-10 cl-black <?php if($page_url_name=='manage-admins'){ echo 'menu-active-btn'; } ?>">
        <i class='bx bx-user-plus' ></i>
        &nbsp;Manage Admins
    </a>
        
    <a href="http://api.winco.site/payments/bharatpe/manager/?mode=prod-9874-mode" class="dspl-in-block txt-deco-n w-100 pd-10 mg-t-10 cl-black">
        <i class='bx bx-cog' ></i>
        &nbsp;Manage Payments
    </a>
    
    <a href="<?php echo $host_url; ?>manage-settings" class="dspl-in-block txt-deco-n w-100 pd-10 mg-t-10 cl-black <?php if($page_url_name=='manage-settings'){ echo 'menu-active-btn'; } ?>">
        <i class='bx bx-cog' ></i>
        &nbsp;Manage Settings
    </a>
    
    <div class="h-line-view mg-t-10"></div>
    
    <a href="<?php echo $host_url; ?>logout-account" class="dspl-in-block txt-deco-n w-100 pd-10 mg-t-10 cl-red">
        <i class='bx bx-log-out-circle'></i>
        &nbsp;Logout Account
    </a>
</div>