<?php if (!defined('FLUX_ROOT')) exit;

if ($session->isLoggedIn()) {
    $user_info = array();
    if ($auth->allowedToSeeAccountID) {
        $user_info[] = array(
            'title' => "Account ID",
            'value' => $session->account->account_id );
    }
    $user_info = array_merge($user_info, array(
        array(
            'title' => "Username",
            'value' => $session->account->userid ),
        array(
            'title' => "Email",
            'value' => $session->account->email ),
        array(
            'title' => "Group ID",
            'value' => $session->account->group_id ),
        array(
            'title' => "State",
            'value' => 
            //(!$account->confirmed && $account->confirm_code) ? (Flux::message('AccountStatePending')) : 
            (
                (($state = $this->accountStateText($session->account->state)) && !$session->account->unban_time) ? $state : 
                (
                    $session->account->unban_time ? sprintf(htmlspecialchars(Flux::message('AccountStateTempBanned')), date(Flux::config('DateTimeFormat'), $session->account->unban_time)) :
                    "Unknown" 
                )
            ),
        ),
    ));
    $user_info = array_merge($user_info, array(
        array(
            'title' => "Login Count",
            'value' => $session->account->logincount ),
        array(
            'title' => "Last Login",
            'value' => (!$session->account->lastlogin || $session->account->lastlogin == '0000-00-00 00:00:00') ? Flux::message('NeverLabel') : $this->formatDateTime($session->account->lastlogin)  ),
        array(
            'title' => "Last IP",
            'value' => $session->account->last_ip ),
        array(
            'title' => "Birthdate",
            'value' => $session->account->birthdate),
        array(
            'title' => "Last Donation Date",
            'value' => $session->account->last_donation_date ),
        array(
            'title' => "Last Donation Amount",
            'value' => $session->account->last_donation_amount ),
    ));
    $currencies = array();
    if($credits_bal=$session->account->balance) {
        $currencies[] = array(
            'title' => "Balance",
            'icon' => 'fluxicon-Credits',
            'value' => $credits_bal,
        );
    }

    /*
      Available Icons:
        fluxicon-Vote
        fluxicon-Events
        fluxicon-Credits
        fluxicon-Zeny
    $currencies[] = array(
        'title' => "Vote",
        'icon' => 'fluxicon-Vote',
        'value' => 99999999,
    );
    $currencies[] = array(
        'title' => "Events",
        'icon' => 'fluxicon-Events',
        'value' => 99999999,
    );
    $currencies[] = array(
        'title' => "Credits",
        'icon' => 'fluxicon-Credits',
        'value' => 99999999,
    );
    $currencies[] = array(
        'title' => "Zeny",
        'icon' => 'fluxicon-Zeny',
        'value' => 99999999,
    );
    */
}

?>
<?php if ($session->isLoggedIn()): ?>
<section class="user">
    <div class="user_details">
        <span class="user_name">
            <?php echo $session->account->userid ?>
        </span>
    </div>
    <div class="user_picture_area">
        <div class="user_picture" style="background: url('https://www.gravatar.com/avatar/<?php echo md5($session->account->email) ?>?s=47') ">
        </div>
    </div>
</section>
<div class="fcp">
        <div class="fcp_modal">
            <div class="row h-100 m-0 user_info_market d-flex justify-content-center align-items-center">
                    <div class="d-flex flex-wrap flex-row justify-content-center align-items-center user_info_market_links">
                        <span data-toggle="tooltip" data-placement="top" title="Go to Marketplace">
                            <a href="<?php echo $this->url('purchase') ?>"><i class="fas fa-store"></i></a>
                        </span>
                        <span data-toggle="tooltip" data-placement="top" title="Make a donation">
                            <a href="<?php echo $this->url('donate') ?>"><i class="fas fa-donate"></i></a>
                        </span>
                    </div>
                    <?php if(count($currencies)>0): ?>
                    <div class="d-flex user_info_market_values justify-content-center align-items-center">
                            <div class="d-flex flex-row justify-content-center align-items-center flex-wrap">
                                <?php foreach($currencies as $currency): ?>
                                <div class="user_info_market_currency"  data-toggle="tooltip"  title="<?php echo $currency['title'] ?>">
                                    <span class="<?php echo $currency['icon'] ?>"></span>
                                    <span class="value"><?php echo $currency['value'] ?></span>
                                </div>
                                <?php endforeach ?>
                            </div>
                    </div>
                    <?php endif ?>
            </div>
        </div>
    <div class="fcp_modal">
        <div class="row h-100 m-0 user_info">
                <div class="d-flex flex-column justify-content-center align-items-center user_info_menu">
                    <span data-toggle="tooltip" data-placement="left" title="Account Settings">
                    <a href="<?php echo $this->url('account','view') ?>"><i class="fas fa-cog"></i></a>
                    </span>
                    <span data-toggle="tooltip" data-placement="left" title="The account gender is <?php echo $session->account->sex=="M" ? strtolower(Flux::message('GenderTypeMale')) : (Flux::message('GenderTypeFemale')) ?>.">
                    <a href="<?php echo $this->url('account','view') ?>"><i class="fas fa-<?php echo $session->account->sex=="M" ? 'mars' : 'venus' ?>"></i></a>
                    </span>
                    <span data-toggle="tooltip" data-placement="left" title="Account Characters">
                    <a href="<?php echo $this->url('account','view') ?>"><i class="fas fa-user"></i></a>
                    </span>
                    <span data-toggle="tooltip" data-placement="left" title="Account Items">
                    <a href="<?php echo $this->url('account','view') ?>"><i class="fas fa-briefcase"></i></a>
                    </span>
                </div>
                <div class="v_divider"></div>
                <div class="d-flex flex-column justify-content-center align-items-center">
                        <table class="m_user_info">
                                <?php foreach($user_info as $uinfo): if (!$uinfo['value']) continue; ?>
                                <tr>
                                    <th><?php echo $uinfo['title'] ?></th>
                                    <td><?php echo $uinfo['value'] ?></td>
                                </tr>
                                <?php endforeach ?>
                        </table>
                </div>
        </div>
    </div>
</div>
<?php else: ?>
<section class="user">
    <div class="user_details">
        <div class='d-inline-block'><a href="<?php echo $this->url('account','login') ?>"><button class="btn btn-secondary">Login</button></a></div>
        <div class='d-inline-block'><a href="<?php echo $this->url('account','create') ?>"><button class="btn btn-warning">Register</button></a></div>
    </div>
</section>
<?php endif ?>