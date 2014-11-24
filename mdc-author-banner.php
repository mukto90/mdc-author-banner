<?php
/*
Plugin Name:	MDC Author Banner
Description:	A simple plugin to allow authors to upload a banner image. Banner image can be shown anywhere using shortcode or calling a function. Use shortcode <strong>[mdc_author_banner_url]</strong> or function <strong>mdc_author_banner_url();</strong> to return uploaded banner url.
Author:			Nazmul Ahsan
Author URI:		http://mukto.medhabi.com
Plugin URI:		https://wordpress.org/plugins/mdc-author-banner/
Version:		1.0.1
License:		GPLv3
Tags: author image, profile, avatar, author banner, banner image, mdc
*/

function mdc_author_banner_submenu(){
	add_submenu_page('profile.php', 'MDC Author Banner', 'MDC Author Banner', 'read', 'mdc-author-banner', 'mdc_author_banner_uploader', '');
}
add_action( 'admin_menu', 'mdc_author_banner_submenu' );

function mdc_author_banner_css(){
	?>
	<style>
		.banner_preview{
			width: 300px
		}
	</style>
	<?php
}
add_action('admin_head', 'mdc_author_banner_css');

function mdc_author_banner_uploader(){
	$id = get_current_user_id();
	?>
	<div class="wrapper">
		<h2>Banner Image</h2>
		<form action="" method="post" enctype="multipart/form-data">
			<label for="file">Select an image:</label>
			<input type="file" name="file" id="file">
			<input type="submit" name="submit" value="Upload" class="button button-primary">
		</form>
		<?php
			$upload_to = wp_upload_dir();
			$upload_directory = $upload_to[basedir].'/banners/';
			$upload_url = $upload_to[baseurl].'/banners/';

			if (!file_exists($upload_directory)) {
				mkdir($upload_directory, 0777, true);
			}
			$allowedExts = array("gif", "jpeg", "jpg", "png");
			$temp = explode(".", $_FILES["file"]["name"]);
			$extension = end($temp);
			if($_POST){
			if ((($_FILES["file"]["type"] == "image/gif")
			|| ($_FILES["file"]["type"] == "image/jpeg")
			|| ($_FILES["file"]["type"] == "image/jpg")
			|| ($_FILES["file"]["type"] == "image/pjpeg")
			|| ($_FILES["file"]["type"] == "image/x-png")
			|| ($_FILES["file"]["type"] == "image/png"))
			// && ($_FILES["file"]["size"] < 20000)
			&& in_array($extension, $allowedExts)) {
				if ($_FILES["file"]["error"] > 0) {
					echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
					} else {
					// echo "Upload: " . $_FILES["file"]["name"] . "<br>";
					// echo "Type: " . $_FILES["file"]["type"] . "<br>";
					// echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
					// echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";
					if (file_exists($upload_directory . $_FILES["file"]["name"])) {
						echo $_FILES["file"]["name"] . " already exists. Please rename your file and upload again.<br />";
					} else {
						move_uploaded_file($_FILES["file"]["tmp_name"], $upload_directory . $_FILES["file"]["name"]);
						// echo "Stored in: " . $upload_directory . $_FILES["file"]["name"];
						echo "New banner has been uploaded successfully!<br />";
					}
					update_user_meta($id, 'mdc_author_banner', $_FILES["file"]["name"]);
				}
				} else {
					echo "Invalid file";
			}
		}
		if(strpos(mdc_author_banner_url($id), ".") != false){
		?>
		<img class="banner_preview" src="<?php echo mdc_author_banner_url($id);?>" />
		<?php }?>
	</div>
	<?php
}

function mdc_author_banner_url($user_id){
	$upload_to = wp_upload_dir();
	$upload_url = $upload_to[baseurl].'/banners/';
	$image_name = get_user_meta($user_id, 'mdc_author_banner', true);
	$banner_image_url = $upload_url.$image_name;
	return $banner_image_url;
}
add_shortcode('mdc_author_banner_url', 'mdc_author_banner_url');