<center>
<html lang="en">
  <head>
    <title>How to reduce or compress image size while uploading using PHP </title>
  </head>
    <style>
  span { clear:both; display:block; margin-bottom:30px; }
  span a { font-weight:bold; color:#0099FF; }
  img { max-width:150px; padding:3px; border:2px solid #eee; border-radius:3px;}
  table td { padding-bottom:10px;}
  label { display:block; font-weight:bold; padding-bottom:3px }
  p.red { color:#FF0000; }
  
  
  	.button {
    background-color: #4CAF50; /* Green */
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    -webkit-transition-duration: 0.4s; /* Safari */
    transition-duration: 0.4s;
}



.button2:hover {
    box-shadow: 0 12px 16px 0 rgba(0,0,0,0.24),0 17px 50px 0 rgba(0,0,0,0.19);
}   
		   
	   
  
  
  
  </style>
  <script type='text/javascript'>
function preview_image(event) 
{
 var reader = new FileReader();
 reader.onload = function()
 {
  var output = document.getElementById('output_image');
  output.src = reader.result;
 }
 reader.readAsDataURL(event.target.files[0]);
}



</script>
  <body>
  <div class="container-fluid">
    <center>
    <div class="row">
      
      
<img src="typeimage.jpg" width="500px"/>
      <div class="col-md-6 col-xs-12">
        <form method="post" enctype="multipart/form-data">
          <table width="500" border="0">
            <tr>
            <td><label>Upload image <font color="#FF0000;">*</font></label><input type="file" name="uploadImg" value=""  accept="image/*" onchange="preview_image(event)" /></td>
            </tr>
            <img id="output_image"/>
            <tr>
            <td><label>New width</label><input type="text" name="width" value=""></td>
           
            <td><label>New height</label><input type="text" name="height" value=""></td>
            </tr>
            <tr>
            <td><label>Quality %</label><input type="text" name="quality" value=""></td>
            </tr>
            <tr>
            <td><input  class="button button2" type="submit" name="submit" value="Upload & Compress" /></td>
            </tr>
          </table>
        </form>
      </div>
    </div></center>
    
    <div class="row">
      <div class="col-md-12">
        <?php
        $success = false;
        if(isset($_POST['submit']) && !empty($_POST['submit'])) {
          if(isset($_FILES['uploadImg']['name']) && @$_FILES['uploadImg']['name'] != "") {
            if($_FILES['uploadImg']['error'] > 0) {
              echo '<h4>Increase post_max_size and upload_max_filesize limit in php.ini file.</h4>';
            } else {
              if($_FILES['uploadImg']['size'] / 1024 <= 5120) { // 5MB
                if($_FILES['uploadImg']['type'] == 'image/jpeg' || 
                   $_FILES['uploadImg']['type'] == 'image/pjpeg' || 
                   $_FILES['uploadImg']['type'] == 'image/png' ||
                   $_FILES['uploadImg']['type'] == 'image/gif'){
                  
               
                $source_file = $_FILES['uploadImg']['tmp_name'];
                $target_file = "uploads/compressed_" . $_FILES['uploadImg']['name']; 
                  $width      = $_POST['width'];
                  $height     = $_POST['height'];
                  $quality    = $_POST['quality'];
                  //$image_name = $_FILES['uploadImg']['name'];
               $success = compress_image($source_file, $target_file, $width, $height, $quality);
                  if($success) {
                    // Optional. The original file is uploaded to the server only for the comparison purpose.
                    copy($source_file, "uploads/original_" . $_FILES['uploadImg']['name']);
                  }
                }
              } else {
                echo '<h4>Image should be maximun 5MB in size!</h4>';
              }
            }
          } else {
            echo "<h4>Please select an image first!</h4>";
          }
        }
        ?>
        
        <!-- Displaying original and compressed images -->
        <?php if($success) { ?>
        <?php $destination = "uploads/"; 

     
?>
          <table border="3" bgcolor="brown" cellpadding="30">
            <tr>
            <td>
              <a href="<?php echo $destination . "original_" . $_FILES['uploadImg']['name']?>" target="_blank" title="View actual size">
                <img src='<?php echo $destination . "original_" . $_FILES['uploadImg']['name']?>'>
              </a><br>
              Original : <?php echo round(filesize($destination . "original_" . $_FILES['uploadImg']['name'])/1024,2); ?>KB
              <br>
              file name: <?php $filename=$_FILES['uploadImg']['name'];
echo $filename; ?>


 <br>
 Date-<?php   echo date("m-d-y"); ?>
 <br>
              <a href="download.php?file=<?php echo $destination . "original_" . $_FILES['uploadImg']['name']?>">Download original File</a>
              <br>
  <?php  list($width, $height, $type, $attr) = getimagesize("uploads/original_" . $_FILES['uploadImg']['name']);

echo "Image width " .$width;
echo "<BR>";
echo "Image height " .$height;
echo "<BR>";
echo "Image type " .$type;
echo "<BR>";
echo "Attribute " .$attr;
?>
            </td>

            <td>
              <a href="<?php echo $destination . "compressed_" . $_FILES['uploadImg']['name']?>" target="_blank" title="View actual size">
                <img src='<?php echo $destination . "compressed_" . $_FILES['uploadImg']['name']?>'>
              </a><br>
              Compressed : <?php echo round(filesize($destination . "compressed_" . $_FILES['uploadImg']['name'])/1024, 2); ?>KB
              <br>
file name: <?php $filename=$_FILES['uploadImg']['name'];
echo $filename; ?>
<br>
 Date-<?php   echo date("m-d-y"); ?>
              <br>
              <?php  list($width, $height, $type, $attr) = getimagesize("uploads/compressed_" . $_FILES['uploadImg']['name']);

echo "Image width " .$width;
echo "<BR>";
echo "Image height " .$height; 
echo "<BR>";

echo "Image type " .$type;
echo "<BR>";
echo "Attribute " .$attr;
?>
           
            </td>

            </tr>
          </table>
        <?php } ?>
      </div>
    </div>
    
      
      
      
      
      
      
<?php
function compress_image($source_file, $target_file, $nwidth, $nheight, $quality) {
  //Return an array consisting of image type, height, widh and mime type.
  $image_info = getimagesize($source_file);
  if(!($nwidth > 0)) $nwidth = $image_info[0];
  if(!($nheight > 0)) $nheight = $image_info[1];
  
  if(!empty($image_info)) {
    switch($image_info['mime']) {
      case 'image/jpeg' :
        if($quality == '' || $quality < 0 || $quality > 100) $quality = 75; //Default quality
        // Create a new image from the file or the url.
      $image = imagecreatefromjpeg($source_file);
   $thumb = imagecreatetruecolor($nwidth, $nheight);
        //Resize the $thumb image
        imagecopyresized($thumb, $image, 0, 0, 0, 0, $nwidth, $nheight, $image_info[0], $image_info[1]);
        // Output image to the browser or file.
        return imagejpeg($thumb, $target_file, $quality); 
        
        break;
      
      case 'image/png' :
        if($quality == '' || $quality < 0 || $quality > 9) $quality = 6; //Default quality
        // Create a new image from the file or the url.
        $image = imagecreatefrompng($source_file);
        $thumb = imagecreatetruecolor($nwidth, $nheight);
        //Resize the $thumb image
        imagecopyresized($thumb, $image, 0, 0, 0, 0, $nwidth, $nheight, $image_info[0], $image_info[1]);
        // Output image to the browser or file.
        return imagepng($thumb, $target_file, $quality);
        break;
        
      case 'image/gif' :
        if($quality == '' || $quality < 0 || $quality > 100) $quality = 75; //Default quality
        // Create a new image from the file or the url.
        $image = imagecreatefromgif($source_file);
        $thumb = imagecreatetruecolor($nwidth, $nheight);
        //Resize the $thumb image
        imagecopyresized($thumb, $image, 0, 0, 0, 0, $nwidth, $nheight, $image_info[0], $image_info[1]);
        // Output image to the browser or file.
        return imagegif($thumb, $target_file, $quality); //$success = true;
        break;
        
      default:
        echo "<h4>Not supported file type!</h4>";
        break;
    }
  }
}
?>





</body>

</center>
















<br>


<br>
<br>

