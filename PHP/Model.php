  <!--Add a modal box-->
  <?php 
  $modal_userID=1;
  $modal_user=array();
  $modal_user=getUserData($modal_userID);

function loaduserdata($userID){
    global $modal_userID, $modal_user;
    $modal_userID = $userID;
    $modal_user = getUserData($modal_userID);
}
?>
  <div class="modal-box" id="modal-box"style="display: none;">
    <!--Top-->
    <div class="modal-box-top">
    </div>
    <!--main body-->
    <div class="modal-box-content">
        <p>
            <?php print_r($modal_user);
                echo "<br>";
                echo $modal_userID;
            ?>
        </p>
    </div>
    <!--Bottom-->
    <div class="modal-box-bottom">
    </div>
  </div>

  <script>

</script>