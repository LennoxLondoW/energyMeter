<?php
session_start();
require_once '../controlers/contactControler.php';
require_once '../blades/header.php';
?>
<div class="container">
  <div class="contact-agile">
    <!--<br><br>-->
    <!--<h4 class="latest-text w3_latest_text non_ck" <?php $element->is_editable(current_page_table, 'page_inner_title', 'text'); ?>><?php echo $page_inner_title; ?></h4>-->
    <!--<br><br>-->
    <!--<div class="row">-->
    <!--  <div class="col-md-3 location-agileinfo">-->
    <!--    <div class="icon-w3">-->
    <!--      <span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span>-->
    <!--    </div>-->
    <!--    <h3>Address</h3>-->
    <!--    <h4>Moi University,</h4>-->
    <!--    <h4>Main Campus,</h4>-->
    <!--    <h4>Eldoret.</h4>-->
    <!--  </div>-->
    <!--  <div class="col-md-3 call-agileits">-->
    <!--    <div class="icon-w3">-->
    <!--      <span class="glyphicon glyphicon-earphone" aria-hidden="true"></span>-->
    <!--    </div>-->
    <!--    <h3>Call</h3>-->
    <!--    <h4>+254708889764</h4>-->
    <!--    <h4>+254739288203</h4>-->
    <!--    <h4>+254735970091</h4>-->
    <!--  </div>-->
    <!--  <div class="col-md-3 mail-wthree">-->
    <!--    <div class="icon-w3">-->
    <!--      <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>-->
    <!--    </div>-->
    <!--    <h3>Email</h3>-->
    <!--    <h4><a href="mailto:info@anchortrends.com">info@anchortrends.com</a></h4>-->
    <!--    <h4><a href="mailto:lennoxlondow3@gmail.com">lennoxlondow3@gmail.com</a></h4>-->
    <!--    <h4><a href="mailto:info@memehub.co.ke">info@memehub.co.ke</a></h4>-->
    <!--  </div>-->
    <!--  <div class="col-md-3 social-w3l">-->
    <!--    <div class="icon-w3">-->
    <!--      <span class="glyphicon glyphicon-globe" aria-hidden="true"></span>-->
    <!--    </div>-->
    <!--    <h3>Social media</h3>-->
    <!--    <ul>-->
    <!--      <li><a target="_blank" href="https://web.facebook.com/AnchorTrends-104142282051971"><i class="fa fa-facebook" aria-hidden="true"></i><span class="text">Facebook</span></a></li>-->
    <!--      <li class="twt"><a target="_blank" href="https://twitter.com/Anchor_Trends"><i class="fa fa-twitter" aria-hidden="true"></i><span class="text">Twitter</span></a></li>-->
    <!--      <li class="ggp" style="background: green !important; color: white!important;"><a target="_blank" href="https://wa.me/254708889764"><i style="background: green !important; color: white!important;" class="fa fa-whatsapp" aria-hidden="true"></i><span class="text">Whatsapp</span></a></li>-->
    <!--    </ul>-->
    <!--  </div>-->
    <!--</div>-->
    <!--<div class="clearfix"></div>-->
    <?php
    if ($element->page_editable) {
    ?>
      <h3 class="mb-sm-4 mb-3">Update Email</h3>
      <?php
      //pick email settings from table
      $settings = new App();
      $settings->activeTable = "lentec_email_settings";
      $settings->comparisons = [];
      $settings->joiners = [''];
      $settings->order = " BY id ASC ";
      $settings->cols = "section_id, section_title";
      $settings->limit = 2000;
      $settings->offset = 0;


      ?>
      <div class="edit_div">
        <table>
          <caption>
            <h3>Set Your Emails</h3>
          </caption>
          <thead>
            <tr>
              <th>Object</th>
              <th>Data</th>
            </tr>
          </thead>
          <tbody>
            <?php

              foreach ($settings->getData() as $key => $value) {
                echo '<tr>
                              <td >' . ucwords(str_replace("email_", " ", $value['section_id'])) . '</td>
                              <td> 
                              ' . (
                                    $value['section_id'] === 'email_company_logo' ?
                                        '<img style="height:30px;" src="' . base_path . $value['section_title'] . '" alt="logo" ' . $element->is_editable('email_settings', $value['section_id'], 'image', true) . '>' :
                                         '<span class="non_ck" ' . $element->is_editable('email_settings', $value['section_id'], 'text', true) . '>' . $value['section_title'] . '</span>'
                                  ) . '
                            </td>
                        </tr>';
              }
            ?>
            

          </tbody>
        </table>
        <button class="preview" onclick="Swal.fire({html: `<br><br><iframe style='width:90%; height:80vh; border:none; outline:none;' src='<?php echo base_path; ?>contact/preview/true'></iframe>`,customClass:'swal-wide'})" class='sc_button sc_button_default sc_button_size_normal sc_button_icon_left preview'>Preview
                        Template</button>
      </div>

      <?php
      //pick email settings from table
    } else {
      ?>

      <!--<form style="margin-bottom: 100px;" action="<?php echo base_path; ?>contact" class="ajax" method="post" id="contact" enctype="multipart/form-data">-->
      <!--  <?php echo $message; ?>-->
      <!--  <input type="text" style="background: transparent; color: grey; border: solid .5px rgba(155,155,155,.5);" name="ask_name" placeholder="YOUR NAME" required="">-->
      <!--  <input type="text" style="background: transparent; color: grey; border: solid .5px rgba(155,155,155,.5);" name="ask_email" placeholder="YOUR EMAIL" required="">-->
      <!--  <input type="text" style="background: transparent; color: grey; border: solid .5px rgba(155,155,155,.5);" name="ask_tel" placeholder="YOUR NUMBER" required="">-->
      <!--  <input type="text" style="background: transparent; color: grey; border: solid .5px rgba(155,155,155,.5);" name="ask_subject" placeholder="SUBJECT" required="">-->
      <!--  <input type="hidden" name="ask_date" readonly="" value="<?php echo date("Ymd"); ?>">-->
      <!--  <input type="hidden" name="ask_admin_email" readonly="" value="lennoxlondow3@gmail.com">-->
      <!--  <textarea name="ask_message" style="background: transparent; color: grey; border: solid .5px rgba(155,155,155,.2);" class="clr" minlength="15" maxlength="2000" placeholder="YOUR MESSAGE" required=""></textarea>-->
      <!--  <input type="submit" value="SEND MESSAGE">-->
      <!--</form>-->
    <?php
    }
    ?>
  </div>
</div>



<?php
require_once '../blades/footer.php';
?>
