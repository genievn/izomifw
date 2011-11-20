<?php
import('projects.base.module.files.includes.*');
define('DIRECTORY', $abs.config('root.upload_folder'));
define('WEB_DIRECTORY', '/extra/uploaded');
class FilesController extends Object{

    public function fileManager()
    {
        $render = $this->getTemplate('file_manager');
        
        
        
        # set callback for CkEditor
        $render->setCkEditorCallback($_REQUEST["CKEditorFuncNum"]);
        return $render;
    }
    
    
    public function jsFileManager()
    {
        $render = $this->getTemplate('js_file_manager');
        $render->setPreviewCmpId(@$_REQUEST["previewCmpId"]);
        $render->setValueCmpId(@$_REQUEST["valueCmpId"]);
        return $render;
    }
    
    public function jsFileManagerTinyMCE()
    {
        $render = $this->getTemplate('js_file_manager_tinymce');
        $render->setPreviewCmpId(@$_REQUEST["previewCmpId"]);
        $render->setValueCmpId(@$_REQUEST["valueCmpId"]);
        return $render;
    }
    
	public function treeData(){
		$uploadedFolder = config('root.upload_folder');
		$data = get_directory_contents($uploadedFolder, true);
		$render  = $this->getTemplate('json_tree_data');
		$render->setTreeData(json_encode($data));
		return $render;
	}
    public function doAction()
    {    
    	$render = $this->getTemplate('dummy');
    	
        // Setup some variables
        if (@$_REQUEST['action'] == 'download') {
	        $_POST['action'] = $_REQUEST['action'];
	        $_POST['directory'] = $_REQUEST['directory'];
        }

        if (@$_REQUEST['directory']) {
	        $directory = DIRECTORY . $_REQUEST['directory'];
        } else {
	        $directory = DIRECTORY;
        }
        
        # turns to platform directory separator
        $directory = str_replace("/", DIRECTORY_SEPARATOR, $directory);

        switch (@$_REQUEST['action']) {
	        default:
		        $dir = opendir($directory);
		        $i = 0;
		
		        // Get a list of all the files in the directory
		        while ($temp = readdir($dir)) {
			        if (stristr($temp, '_fm_')) continue; // If this is a temp file, skip it.
			        if (@$_POST['images_only'] && !preg_match('/\.(jpeg|jpg|gif|png)$/', $temp)) continue; // If it isnt an image, skip it
			        if (is_dir($directory . DIRECTORY_SEPARATOR . $temp)) continue; // If its a directory skip it
			
			        $results[$i]['name'] = $temp;
			        $results[$i]['size'] = filesize($directory . DIRECTORY_SEPARATOR . $temp);
			        $results[$i]['type'] = filetype($directory . DIRECTORY_SEPARATOR . $temp);
			        $results[$i]['permissions'] = format_permissions(fileperms($directory . DIRECTORY_SEPARATOR . $temp));
			        $results[$i]['ctime'] = filectime($directory . DIRECTORY_SEPARATOR . $temp);
			        $results[$i]['mtime'] = filemtime($directory . DIRECTORY_SEPARATOR . $temp);
			        $results[$i]['owner'] = fileowner($directory . DIRECTORY_SEPARATOR . $temp);
			        $results[$i]['group'] = filegroup($directory . DIRECTORY_SEPARATOR . $temp);
			        $results[$i]['relative_path'] = str_replace(DIRECTORY, '', $directory) . DIRECTORY_SEPARATOR . $temp;
			        $results[$i]['full_path'] = $directory . DIRECTORY_SEPARATOR . $temp;
			        $results[$i]['web_path'] = WEB_DIRECTORY . str_replace(DIRECTORY, '', $directory) . DIRECTORY_SEPARATOR . $temp;
			        $i++;
		        }
		
		        if (is_array($results)) {
			        $data['count'] = count($results);
			        $data['data'] = $results;
		        } else {
			        $data['count'] = 0;
			        $data['data'] = '';
		        }
		
		        //print json_encode($data);
		        $render->setData($data);
		        return $render;
		        //exit();
		        break;
	        case 'upload' :
		        // Upload a file to this directory
		        foreach ($_FILES as $file) {
			        if (is_uploaded_file($file['tmp_name'])) {
				        // Set the filename for the uploaded file
				        $filename = $directory . "/" . $file['name'];
			
				        if (file_exists($filename) == true) {
					        // File already exists \\
					        print '{"success": false, "message": "' . $_POST['directory'] . $file['name'] . ' already exists"}';
					        break;
				        } else if (copy($file['tmp_name'], $filename) == false) {
					        // File can not be copied \\
					        print '{"success": false, "message": "Could not upload' . $file['name'] . '"}';
					        break;
				        } else {
					        print '{"success": true, "message": "Upload complete"}';
				        }
			        }
		        }
		        exit();
		        break;
	        case "download" :
		        if ($directory && $_REQUEST['file'] && is_file($directory . DIRECTORY_SEPARATOR . $_REQUEST['file'])) {
			        header("Content-type: application/x-download");
			        header("Content-Disposition: attachment; filename=\"" . $_REQUEST['file'] . "\";");
			        header("Content-Length: " . filesize($directory . DIRECTORY_SEPARATOR . $_REQUEST['file']));
			
			        print file_get_contents($directory . DIRECTORY_SEPARATOR . $_REQUEST['file']);
		        }
		        exit();
		        break;
	        case "rename" :
		        if ($_POST['file'] && is_file($directory . DIRECTORY_SEPARATOR . $_POST['file'])) {
			        if (rename($directory . DIRECTORY_SEPARATOR . $_POST['file'], $directory . DIRECTORY_SEPARATOR . $_POST['new_name'])) {
				        print '{"success": true, "message": "File renamed successfully"}';
			        } else {
				        print '{"success": false, "message": "Could not rename ' . $_POST['file'] . '"}';
			        }
		        } else {
			        print '{"success": false, "message": "Could not rename ' . $_POST['file'] . '"}';
		        }
		        exit();
		        break;
	        case "chmod" :
		        if ($_POST['file'] && is_file($directory . DIRECTORY_SEPARATOR . $_POST['file'])) {
			        // First calculate our permissions
			        if ($_POST['owner_read']) {
				        $owner_perms += 4;
			        }
			        if ($_POST['owner_write']) {
				        $owner_perms += 2;
			        }
			        if ($_POST['owner_execute']) {
				        $owner_perms += 1;
			        }
			
			        if ($_POST['group_read']) {
				        $group_perms += 4;
			        }
			        if ($_POST['group_write']) {
				        $group_perms += 2;
			        }
			        if ($_POST['group_execute']) {
				        $group_perms += 1;
			        }
			
			        if ($_POST['everyone_read']) {
				        $everyone_perms += 4;
			        }
			        if ($_POST['everyone_write']) {
				        $everyone_perms += 2;
			        }
			        if ($_POST['everyone_execute']) {
				        $everyone_perms += 1;
			        }
			
			        $permissions = 0 . $owner_perms . $group_perms . $everyone_perms;
			
			        if (chmod($directory . DIRECTORY_SEPARATOR . $_POST['file'], octdec($permissions))) {
				        print json_encode(array('success' => true, 'message' => 'File chmod\'d successfully ' . $permissions));
			        } else {
				        print json_encode(array('success' => false, 'message' => 'Could not chmod ' . $_POST['file']));
			        }
		        } else {
			        print json_encode(array('success' => false, 'message' => 'Could not chmod ' . $_POST['file']));
		        }
		        exit();
		        break;
	        case "delete" :
		        if ($_POST['file'] && is_file($directory . DIRECTORY_SEPARATOR . $_POST['file'])) {
			        if (unlink($directory . DIRECTORY_SEPARATOR . $_POST['file'])) {
				        print json_encode(array('success' => true, 'message' => 'File deleted successfully'));
			        } else {
				        print json_encode(array('success' => false, 'message' => 'Could not delete ' . $_POST['file']));
			        }
		        } else {
			        print json_encode(array('success' => false, 'message' => 'Could not delete ' . $_POST['file']));
		        }
		        exit();
		        break;
	        case "move" :
		        if ($_POST['file'] && $_POST['new_directory'] && is_file($directory . DIRECTORY_SEPARATOR . $_POST['file']) && !file_exists(DIRECTORY . $_POST['new_directory'] . DIRECTORY_SEPARATOR . $_POST['file'])) {
			        if (rename($directory . DIRECTORY_SEPARATOR . $_POST['file'], DIRECTORY . $_POST['new_directory'] . DIRECTORY_SEPARATOR . $_POST['file'])) {
				        print json_encode(array('success' => true, 'message' => 'File moved successfully'));
			        } else {
				        print json_encode(array('success' => false, 'message' => 'Could not move ' . $_POST['file']));
			        }
		        } else {
			        print json_encode(array('success' => false, 'message' => 'Could not move ' . $_POST['file']));
		        }
		        exit();
		        break;
	        case "new_directory" :
		        if ($directory && $_POST['new_directory']) {
			        if (mkdir($directory . "/" . $_POST['new_directory'])) {
				        print json_encode(array('success' => true, 'message' => 'Directory created successfully'));
			        } else {
				        print json_encode(array('success' => false, 'message' => 'Could not create ' . $_POST['new_directory']));
			        }
		        } else {
			        print json_encode(array('success' => false, 'message' => 'Could not create ' . $_POST['new_directory']));
		        }
		        exit();
		        break;
	        case "rename_directory" :
		        if ($directory && $_POST['new_name']) {
			        if (rename($directory, substr($directory, 0, strrpos($directory, DIRECTORY_SEPARATOR)) . DIRECTORY_SEPARATOR . $_POST['new_name'])) {
				        print json_encode(array('success' => true, 'message' => 'Directory renamed successfully'));
			        } else {
				        print json_encode(array('success' => false, 'message' => 'Could not rename ' . $directory));
			        }
		        } else {
			        print json_encode(array('success' => false, 'message' => 'Could not rename ' . $directory));
		        }
		        exit();
		        break;
	        case "chmod_directory" :
		        if ($directory && $_POST['permissions']) {
			        if (chmod($directory, octdec(0 . $_POST['permissions']))) {
				        print json_encode(array('success' => true, 'message' => 'Directory chmod\'d successfully'));
			        } else {
				        print json_encode(array('success' => false, 'message' => 'Could not chmod ' . $directory));
			        }
		        } else {
			        print json_encode(array('success' => false, 'message' => 'Could not chmod ' . $directory));
		        }
		        exit();
		        break;
	        case "delete_directory" :
		        if ($_POST['directory'] && $directory != DIRECTORY && stristr($directory, DIRECTORY)) {
			        if (rmdir_r($directory)) {
				        print json_encode(array('success' => true, 'message' => 'Directory deleted successfully'));
			        } else {
				        print json_encode(array('success' => false, 'message' => 'Could not delete ' . $directory));
			        }
		        } else {
			        print json_encode(array('success' => false, 'message' => 'Could not delete ' . $directory));
		        }
		        exit();
		        break;
	        case "create_temp_image" :
		        if ($_POST['image']) {
			        // Create a temp image copy of the image we are trying to edit
			        $temp_image = str_replace(basename($_POST['image']), '_fm_' . basename($_POST['image']), $_POST['image']);
			        if (copy(DIRECTORY . $_POST['image'], DIRECTORY . $temp_image)) {
				        list($width, $height) = getimagesize(DIRECTORY . $temp_image);
				        //print json_encode(array('success' => true, 'message' => 'Temporary image created successfully', 'width' => $width, 'height' => $height));
				        $render->setSuccess(true);
				        $render->setMessage("Temporary image created successfully");
				        $render->setWidth($width);
				        $render->setHeight($height);
			        } else {
				        //print json_encode(array('success' => false, 'message' => 'Error: Could not create temporary image'));
				        $render->setSuccess(false);
				        $render->setMessage('Error: Could not create temporary image');
			        }
		        } else {
			        //print json_encode(array('success' => false, 'message' => 'Error: No image specified'));
			        $render->setSuccess(false);
			        $render->setMessage('Error: No image specified');
		        }
		        //exit();
		        return $render;
		        break;
	        case "delete_temp_image" :
		        if ($_POST['image']) {
			        // Delete our temp image
			        $temp_image = str_replace(basename($_POST['image']), '_fm_' . basename($_POST['image']), $_POST['image']);
			        if (unlink(DIRECTORY . $temp_image)) {
				        print json_encode(array('success' => true, 'message' => 'Image successfully deleted'));
			        } else {
				        print json_encode(array('success' => false, 'message' => 'Error: Temporary image could not be deleted'));
			        }
		        } else {
			        print json_encode(array('success' => false, 'message' => 'Error: No image specified'));
		        }
		        exit();
		        break;
	        case "save_image" :
		        if ($_POST['image']) {
			        // Overwrite our original image with our temp image
			        $temp_image = str_replace(basename($_POST['image']), '_fm_' . basename($_POST['image']), $_POST['image']);
			        if (copy(DIRECTORY . $temp_image, DIRECTORY . $_POST['image'])) {
				        print json_encode(array('success' => true, 'message' => 'Image successfully saved'));
			        } else {
				        print json_encode(array('success' => false, 'message' => 'Error: Could not save image'));
			        }
		        } else {
			        print json_encode(array('success' => false, 'message' => 'Error: No image specified'));
		        }
		        exit();
		        break;
	        case "resize_image" :
		        if ($_POST['image']) {
			        // Make sure we are editing our temp image, and not the original
			        $temp_image = str_replace(basename($_POST['image']), '_fm_' . basename($_POST['image']), $_POST['image']);
			
			        $image = new imageTransform();
			        $image->jpegOutputQuality = 80;
			        $image->sourceFile = DIRECTORY . $temp_image;
			        $image->targetFile = DIRECTORY . $temp_image;
			        $image->resizeToWidth = $_POST['resize_width'];
			        $image->resizeToHeight = $_POST['resize_height'];
			        if ($image->resize()) {
				        print json_encode(array('success' => true, 'message' => 'Image successfully resized'));
			        } else {
				        print json_encode(array('success' => false, 'message' => 'Error: Could not resize image'));
			        }
		        } else {
			        print json_encode(array('success' => false, 'message' => 'Error: No image specified'));
		        }
		        exit();
		        break;
	        case "rotate_image" :
		        if ($_POST['image']) {
			        // Make sure we are editing our temp image, and not the original
			        $temp_image = str_replace(basename($_POST['image']), '_fm_' . basename($_POST['image']), $_POST['image']);
			
			        $image = new imageTransform();
			        $image->jpegOutputQuality = 80;
			        $image->sourceFile = DIRECTORY . $temp_image;
			        $image->targetFile = DIRECTORY . $temp_image;
			        if ($image->rotate(-$_POST['rotate_degrees'])) { // Rotate in negative degrees so it goes clockwise
				        //print json_encode(array('success' => true, 'message' => 'Image successfully rotated'));
				        $render->setSuccess(true);
				        $render->setMessage('Image successfully rotated');
			        } else {
				        //print json_encode(array('success' => false, 'message' => 'Error: Could not rotate image'));
				        $render->setSuccess(false);
				        $render->setMessage('Error: Could not rotate image');
			        }
		        } else {
			        //print json_encode(array('success' => false, 'message' => 'Error: No image specified'));
			        $render->setSuccess(false);
			        $render->setMessage('Error: No image specified');
		        }
		        //exit();
		        return $render;
		        break;
	        case "crop_image" :
		        if ($_POST['image']) {
			        // Make sure we are editing our temp image, and not the original
			        $temp_image = str_replace(basename($_POST['image']), '_fm_' . basename($_POST['image']), $_POST['image']);
			
			        $image = new imageTransform();
			        $image->jpegOutputQuality = 80;
			        $image->sourceFile = DIRECTORY . $temp_image;
			        $image->targetFile = DIRECTORY . $temp_image;
			        if ($image->crop($_POST['crop_x'], $_POST['crop_y'], $_POST['crop_x'] + $_POST['crop_width'], $_POST['crop_y'] + $_POST['crop_height'])) {
				        //print json_encode(array('success' => true, 'message' => 'Image successfully cropped'));
				        $render->setSuccess(true);
				        $render->setMessage('Image successfully cropped');
			         } else {
				        //print json_encode(array('success' => false, 'message' => 'Error: Could not crop image'));
				        $render->setSuccess(false);
				        $render->setMessage('Error: Could not crop image');
			         }
		        } else {
			        //print json_encode(array('success' => false, 'message' => 'Error: No image specified'));
			        $render->setSuccess(false);
			        $render->setMessage('Error: No image specified');
		        }
		        //exit();
		        return $render;
		        break;
        }    
    }

    public function upload()
    {
        global $abs;
        $render = $this->getTemplate('dummy');        

        $path = @$_REQUEST['path'];			//for use with dir1.dir2.dir3        
        
		# no $path is specified, use the root upload folder;
        if (!$path) $path = config('root.upload_folder');
        else $path = config('root.upload_folder').$path;
        
		# relative path
        $relative_path = str_replace(".",DIRECTORY_SEPARATOR,$path);
        $relative_path = str_replace("/",DIRECTORY_SEPARATOR,$relative_path);
        
        # web relative path
        $http_relative_path = str_replace(DIRECTORY_SEPARATOR,"/",$path);
        # full http path
        # $http_relative_path = config('root.url').$http_relative_path;
		$upload_path = $abs.$relative_path.DIRECTORY_SEPARATOR;
        izlog($upload_path);
        if (!is_dir($upload_path))
        # path is not an existed directory, so return false
        {
            $render->setSuccess(false);
            $render->setMsg("Upload failed");
            return $render;
        }


		$failed = array();
		$uploaded = array();
		$success = true;
        foreach( $_FILES as $Fileid){
            $upload_name = @$_POST['upload_name'];
            # if no upload_name is specified, use the original name (converted to slug)
            if (!$upload_name) $upload_name = basename($Fileid['name']);
            $parts = pathinfo($upload_name);

            $extension = $parts['extension'];
            $basename = str_replace(".$extension", "", $parts['basename']);

            $upload_name = $this->slug($basename).".".$extension;

            # setup the target path
            $target_path = $upload_path . $upload_name;
            if( move_uploaded_file($Fileid['tmp_name'], $target_path) ) {
                $uploaded[] = array(					
					"name"		=> $basename.".".$extension,
					"size"		=> $Fileid['size'],
					"web_path"	=> '/'.$http_relative_path.'/'.$upload_name,
					"physical_path" => $relative_path	#for security, no full physical path here, only relative to the root of site
                );
                $msg = $basename.".".$extension." uploaded successfully";                
            }else{                                
                $failed[] = $upload_name;
                $msg = $basename.".".$extension." uploaded failed";
                $success = false;
            }
        }
		$render->setSuccess($success);
		$render->setFailed($failed);
		$render->setUploaded($uploaded);
		$render->setMsg($msg);
        return $render;
    }
    private function slug($str)
    {
	    $str = strtolower(trim($str));
	    $str = preg_replace('/[^a-z0-9-]/', '-', $str);
	    $str = preg_replace('/-+/', "-", $str);
	    return $str;
    }

    public function image()
    {
    	$render = $this->getTemplate('image');
		$image = str_replace(basename($_REQUEST['image']), '_fm_' . basename($_REQUEST['image']), $_REQUEST['image']);

		$render->setImageWebPath(WEB_DIRECTORY.$image);
    	return $render;
    }
}
?>
