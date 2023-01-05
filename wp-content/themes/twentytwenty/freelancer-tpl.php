<!-- 
Template Name: home php
Version: 1.0.0
Auteur: habib ouldmoussa 
-->
<?php
session_start();
get_header();

// Récupere la requete dans une variable session pour la garder dans le rafraichisement de la page 
if (isset($_POST['job_search'])) {
  $job_search = $_POST['job_search'];
  $_SESSION['job_search'] = $_POST['job_search'];
} else {
  if (!isset($_SESSION['job_search'])) {
    $job_search = "";
  }
  $job_search = $_SESSION['job_search'];
}
if (isset($_POST['page_nb'])) {
  $page_nb = $_POST['page_nb'] * 120;
  $_SESSION['page_nb'] = $page_nb;
} else {
  if (!isset($_SESSION['page_nb'])) {
    $page_nb = 0;
  } else {
    $page_nb = $_SESSION['page_nb'];
  }
}
?>

<main id="site-content">
  <!-- moteur de recherche  -->
  <form method="POST" method="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <input type="search" name="job_search" value="<?= $job_search ?>" style="width:40%;display:inline;">
    <input type="text" name="page_nb" value="<?= $_POST['page_nb'] / 120 ?>" style="width:40%;display:inline;">
    <input type="submit" value="cherchez" style="width:10%">
  </form>
  <?php
//class freelancer curl pour récuperer les données de l'api
  class freelancer_curl
  {

    public $url = "";
    public function set_url($url)
    {
      $this->url = $url;
    }
    public function get_search_freelancer()
    {

      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => $this->url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
      ));

      $response = curl_exec($curl);

      curl_close($curl);

      return json_decode($response)->result;
    }
  }
  //instancier la class freelancer curl 
  $freelancer_search = new freelancer_curl;
  $freelancer_search->set_url("https://www.freelancer.com/api/projects/0.1/projects/all/?limit=120&query=" . $job_search . "&offset=" . $page_nb . "&full_description=true&job_details=true&user_details=true&user_reputation=true&user_employer_reputation_extra=true&user_country_details=true&user_profile_description=true&user_avatar=true&user_recommendations=true&user_responsiveness=true&user_status=true&compact=true&sanction_details=true&limited_account=true&marketing_mobile_number=true&user_badge_details=true");
  $reponse = $freelancer_search->get_search_freelancer();

  ?>
  <script>
    //this script is not use 
    jQuery(document).ready(function($) {
      function ajaxCall() {
        $.ajax({
          method: "GET",
          url: "https://www.freelancer.com/api/projects/0.1/projects/all/?limit=10&query=<?= $_POST['job_search'] ?>&offset=0&full_description=true&job_details=true&user_details=true&user_reputation=true&user_employer_reputation_extra=true&user_country_details=true&user_profile_description=true&user_avatar=true&user_recommendations=true&user_responsiveness=true&user_status=true&compact=true&sanction_details=true&limited_account=true&marketing_mobile_number=true&user_badge_details=true",
        }).done(function(data) {

          //console.log( "Data Saved: " + msg);
        });
      }

      // setInterval (ajaxCall, 50000); 

    })
  </script>

  <table class="tftable" style="border:1px solid #000; position:relative; width:2000px!important;font-size:12px;">
    <tr>
      <th>Titre</th>
      <th>Id</th>
      <th>Pays</th>
      <th>timezone</th>
      <th>bid_count</th>
      <th>username</th>
      <th>public_name</th>
      <th>Reputation</th>
      <th>status</th>
      <th>role</th>
      <th>chosen_role</th>
      <th>registration_date</th>
      <th>budget_maximum</th>
      <th>bid_avg</th>
      <th>language</th>
      <th>company</th>
      <th>city</th>
      <th>time_submitted</th>
      <th>currency_code</th>
      <th>type</th>
    </tr>
    <?php foreach ($reponse->projects as $key => $value) : ?>

      <?php
      $freelancer_user = new freelancer_curl;
      $freelancer_user->set_url('https://www.freelancer.com/api/users/0.1/users/' . $value->owner_id . '?status=true&user_details=true&reputation=true&employer_reputation_extra=true&country_details=true&profile_description=true&avatar=true&user_recommendations=true&responsiveness=true&marketing_mobile_number=true');
      $reponse_user = $freelancer_user->get_search_freelancer();
      if (($value->time_submitted - $reponse_user->registration_date) < 86400) {
        $color_tab_singup = "LightCoral";
        $tab_singup = true;
      } elseif (($value->time_submitted - $reponse_user->registration_date) > 31536000) {
        $color_tab_singup = "LawnGreen";
        $tab_singup = false;
      } else {
        $tab_singup = false;
      }
      if ($value->status == "frozen") {
        $color_frozen_made = "LightCoral";
      } else {
        $color_frozen_made = "";
      }

      if (($value->bid_stats->bid_count) < 10) {
        $color_tab_bid_count = "DarkGreen";
        $color_text_tab_bid_count = "white";
        $color_frozen_made = "Aqua";
        $tab_bid_count = true;
      } else if (($value->bid_stats->bid_count) < 20) {
        $color_tab_bid_count = "Aquamarine";
        $tab_bid_count = true;
        $color_frozen_made = "Aqua";
        $color_text_tab_bid_count = "black";
      } else if (($value->bid_stats->bid_count) <= 50) {
        $color_tab_bid_count = "Lime";
        $tab_bid_count = true;
        $color_text_tab_bid_count = "black";
      } else {
        $color_tab_bid_count = "";
        $tab_bid_count = false;
        $color_text_tab_bid_count = "black";
      }
      if (($value->budget->maximum) > 99) {
        $color_tab_budget_maximum = "Aquamarine";
        $tab_budget_maximum = true;
      } else {
        $color_tab_budget_maximum = "";
        $tab_budget_maximum = false;
      }
      if ($reponse_user->status->deposit_made == true) {
        $color_deposit_made = "Aquamarine";
        $tab_deposit_made = true;
      } else {
        $color_deposit_made = "";
        $tab_deposit_made = false;
      }
      if ($reponse_user->role == "employer") {
        $color_tab_role = "Aquamarine";
        $tab_role = true;
      } else {
        $color_tab_role = "";
        $tab_role = false;
      }
      if ($reponse_user->chosen_role == "employer") {
        $color_tab_chosen_role = "Aquamarine";
        $tab_chosen_role = true;
      } else if ($reponse_user->chosen_role == "freelancer") {
        $color_tab_chosen_role = "LightCoral";
        $tab_chosen_role = false;
      }      
      if ($reponse_user->status->phone_verified == 1) {
        $color_tab_phone_verified = "Aquamarine";
        $tab_phone_verified = true;
      } else {
        $color_tab_phone_verified = "";
        $tab_phone_verified = false;
      }
      if ($value->language == "fr") {
        $color_tab_language = "Aquamarine";
        $tab_language = true;
      } else {
        $color_tab_language = "";
        $tab_language = false;
      }
      if ($reponse_user->role !== "freelancer" && $reponse_user->chosen_role !== "freelancer" && $reponse_user->employer_reputation->entire_history->overall >= 2.5 && $reponse_user->employer_reputation->project_stats->complete >= 10 &&  $reponse_user->employer_reputation->entire_history->completion_rate >= 0.5) {
        $color_tab_reputation = "Aquamarine";
        $tab_reputation = true;
      } else {
        $color_tab_reputation = "";
        $tab_reputation = false;
      }      
      if ($value->status !== "frozen") {
        if ($value->status !== "closed") {       
          if ($value->budget->maximum <= 3000) {
            if ($reponse_user->location->country->name !== "India") {
              if ($reponse_user->location->country->name !== "Bangladesh") {
                if ($reponse_user->location->country->name !== "Sri Lanka") {
                  if (strpos($reponse_user->timezone->timezone, 'Africa') == false) {
                    if (strpos($reponse_user->timezone->timezone, 'Dubai') == false) {
                      if ($reponse_user->location->country->name  !== "Saudi Arabia") {
                        if ($reponse_user->location->country->name  !== "United Arab Emirates") {
                          if ($reponse_user->location->country->name  !== "Pakistan") {                        
      ?>
                            <tr id="row_<?= $key ?>" onClick="handleClick(this.id)" style="background-color:<?= $color_frozen_made ?>">
                              <td id=<?= $key ?> width="200px"> <a href="https://www.freelancer.com/projects/<?= $value->seo_url ?>"> <?= $value->title ?></a><br>
                                <?php if ($tab_phone_verified) : ?><img src="https://img2.freepng.fr/20180208/vpw/kisspng-check-mark-x-mark-clip-art-check-marks-5a7c0970695394.0588040215180783204314.jpg" width="20px" title="téléphone verifié">
                                <?php endif; ?>
                                <?php if ($tab_bid_count) : ?><img src="https://w7.pngwing.com/pngs/43/12/png-transparent-auction-gavel-computer-icons-bidding-auction-angle-desktop-wallpaper-internet-thumbnail.png" width="20px" title="nombre de bids bon">
                                <?php endif; ?>

                                <?php if ($tab_budget_maximum) : ?><img src="https://cdn-icons-png.flaticon.com/512/741/741744.png" width="20px" title="le budget est élevé"> <?php endif; ?>
                                <?php if ($tab_deposit_made) : ?><img src="https://cdn-icons-png.flaticon.com/512/2721/2721121.png" width="20px" title="Debot fait"> <?php endif; ?>
                                <?php if ($tab_chosen_role) : ?><img src="https://toppng.com/uploads/preview/circled-user-icon-user-pro-icon-11553397069rpnu1bqqup.png" width="20px" title="employer"> <?php endif; ?>
                                <?php if ($tab_language) : ?><img src="https://image.pngaaa.com/438/2771438-middle.png" width="20px" title="fr"> <?php endif; ?>
                                <?php if ($tab_reputation) : ?><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ4kvlBmLaJJjPgDvv3dBHI_kR17sDzMqBkgtRfiZe_lZlFalqhDzSx7V2-F6OyISdQRSg&usqp=CAU" width="20px" title="bonne réputation"> <?php endif; ?>
                                <?php if ($tab_singup) : ?><img src="https://www.rawshorts.com/freeicons/wp-content/uploads/2017/01/red_prodpictxmark_2_1484336301-1.png" width="20px" title="attention inscritpiton très recente"> <?php endif; ?>
                              </td>
                              <td> <?= $value->id ?></td>
                              <td style="width:80px"><?php echo $reponse_user->location->country->name; ?> <img src="https://www.freelancer.com/<?php echo $reponse_user->location->country->flag_url; ?>"></td>
                              <td style="width:100px"> <?= $reponse_user->timezone->timezone; ?> </td>
                              <td style="background-color:<?= $color_tab_bid_count ?>;color:<?= $color_text_tab_bid_count ?>"><?= $value->bid_stats->bid_count ?></td>
                              <td onMouseOver="document.getElementById('detail_username_<?= $value->owner_id ?>').style.display='block'" onMouseOut="document.getElementById('detail_username_<?= $value->owner_id ?>').style.display='none'"> <a href="https://www.freelancer.com/u/<?= $reponse_user->username ?>"><?= $reponse_user->username ?></a>
                                <div style="display:none;position:relative;border:3px solid red;width:300px;left:20px;" id="detail_username_<?= $value->owner_id ?>">
                                </div>
                              </td>
                              <td><?= $reponse_user->public_name ?><img src="https://www.freelancer.com<?= $reponse_user->avatar ?>"></td>                              
                              <td onMouseOver="document.getElementById('detail_reputation_<?= $value->owner_id ?>').style.display='block'" onMouseOut="document.getElementById('detail_reputation_<?= $value->owner_id ?>').style.display='none'" style="height:100px;background-color:<?= $color_tab_reputation ?>"> <?= $reponse_user->employer_reputation->entire_history->overall ?>
                                <div style="display:none;position:relative;border:3px solid red;width:300px;left:20px;" id="detail_reputation_<?= $value->owner_id ?>">
                                  Payment : <?= $reponse_user->employer_reputation->entire_history->category_ratings->payment_prom ?><br>
                                  Clarté des explications :<?= $reponse_user->employer_reputation->entire_history->category_ratings->clarity_spec ?><br>
                                  Nombre de projets :<?= $reponse_user->employer_reputation->entire_history->all ?><br>
                                  Ratio de projet completé :<?= $reponse_user->employer_reputation->entire_history->completion_rate ?><br>
                                  Communication : <?= $reponse_user->employer_reputation->entire_history->category_ratings->communication ?><br>
                                  Professionalisme : <?= $reponse_user->employer_reputation->entire_history->category_ratings->professionalism ?><br>
                                  Projets incompletés :<?= $reponse_user->employer_reputation->entire_history->incomplete ?><br>
                                  Projets ouverts :<?= $reponse_user->employer_reputation->project_stats->open ?><br>
                                  Projets completés : <?= $reponse_user->employer_reputation->project_stats->complete ?><br>
                                  Travaux en cours :<?= $reponse_user->employer_reputation->project_stats->work_in_progress ?><br>
                                </div>
                              </td>
                              <td onMouseOver="document.getElementById('detail_status_<?= $value->owner_id ?>').style.display='block'" onMouseOut="document.getElementById('detail_status_<?= $value->owner_id ?>').style.display='none'" style="height:100px;background-color: <?= $color_deposit_made ?>">
                                Depot vérifié : <?= $reponse_user->status->deposit_made ?> <br>
                                <div style="display:none;position:relative;border:3px solid red;width:300px;left:20px;" id="detail_status_<?= $value->owner_id ?>">Paiement vérifié : <?= $reponse_user->status->payment_verified ?> <br>
                                  E-mail vérifié : <?= $reponse_user->status->email_verified ?><br>
                                  Profile completé : <?= $reponse_user->status->profile_complete ?><br>
                                  Téléphone vérifié : <?= $reponse_user->status->phone_verified ?><br>
                                  Identité vérifiée : <?= $reponse_user->status->identity_verified ?><br>
                                  Connecté à Facebook : <?= $reponse_user->status->facebook_connected ?><br>
                                  Freelancer verifié :<?= $reponse_user->status->freelancer_verified_user ?><br>
                                  Connecté à linkedin : <?= $reponse_user->status->linkedin_connected ?><br>
                                </div>
                              </td>
                              <td style="background-color:<?= $color_tab_role ?>"><?= $reponse_user->role ?></td>
                              <td style="background-color:<?= $color_tab_chosen_role ?>"> <?= $reponse_user->chosen_role ?></td>
                              <td style="background-color:<?= $color_tab_singup ?>;width:200px;"><?= date(DATE_ATOM, $value->time_submitted) ?></td>
                              <td style="background-color:<?= $color_tab_budget_maximum ?>"> <?= $value->budget->maximum ?></td>

                              <td><?php if ($value->bid_stats->bid_avg != null) echo $value->bid_stats->bid_avg  ?></td>
                              <td style="background-color: <?= $color_tab_language ?>"><?= $value->language ?></td>
                              <td> <?= $reponse_user->company ?></td>
                              <td> <?= $reponse_user->location->city ?></td>
                              <td> <?= $reponse_user->registration_date ?></td>                              
                              <td> <?= $value->currency->code ?></td>
                              <td> <?= $value->type ?></td>
                            </tr>
    <?php
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
    endforeach; ?>
  </table>
</main><!-- #site-content -->
<?php get_template_part('template-parts/footer-menus-widgets'); ?>

<?php
get_footer();
